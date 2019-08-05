<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sekolah
 *
 * @author rohmad
 */
class Jadwal extends CI_Controller {

    var $edit_id = TRUE;
    var $primary_key = "ID_PEG";

    public function __construct() {
        parent::__construct();

        $this->load->model(array(
            'departemen_model' => 'dept',
            'jk_model' => 'jk',
            'jadwal_model' => 'jadwal',
            'guru_mapel_model' => 'guru_mapel',
            'kelas_model' => 'kelas',
            'hari_model' => 'hari',
            'jam_pelajaran_model' => 'jp',
            'guru_model' => 'guru'
        ));
        $this->load->library('timetables_hanlder');

        $this->auth->validation(array(2));
    }

    public function index() {
        $data = array(
            'JENJANG' => $this->dept->get_all(FALSE),
            'JK' => $this->jk->get_all(FALSE)
        );

        $this->generate->backend_view('akademik/jadwal/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jadwal->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_KELAS;
            
            $row[] = $item->NAMA_MAPEL;
            
            $row[] = $item->NIP_PEG;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->NAMA_HARI;
            $row[] = $item->JAM_PELAJARAN;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jadwal->count_all(),
            "recordsFiltered" => $this->jadwal->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function unduh_xml($jenjang, $jk) {
        $this->load->helper('download');

//        $this->timetables_hanlder->write_xml($jenjang);

        $name_file = $this->pengaturan->getXMLDownload();
        $filename = './files/xml/download_' . $name_file . '.xml';
        $this->pengaturan->setXMLDownload($name_file + 1);

        $xml = $this->timetables_hanlder->write_xml($jenjang, $jk);
        $xml->save($filename);

        header('Content-disposition: attachment; filename="' . $filename . '"');
        header('Content-type: "text/xml"; charset="utf8"');
        readfile($filename);

        exit;
    }

    public function unggah_xml() {
        $JENJANG = $this->input->post('JENJANG_UPLOAD');
//        $JK = $this->input->post('JK_UPLOAD');

        $urut = $this->pengaturan->getXMLUpload();
        $this->pengaturan->setXMLUpload($urut + 1);

        $file_element_name = 'file_xml';
        $config['upload_path'] = './files/xml/';
        $config['allowed_types'] = 'xml';
        $config['max_size'] = '10000';
        $config['overwrite'] = TRUE;
        $config['file_name'] = 'upload_' . $urut;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_element_name)) {
            $aa = $this->upload->data();

//            $this->read_xml($config['upload_path'] . $config['file_name'] . '.' . $config['allowed_types'], $JENJANG);

            $this->timetables_hanlder->read_xml($config['upload_path'] . $config['file_name'] . '.' . $config['allowed_types'], $JENJANG);

            $status = TRUE;
            $msg = "File berhasil diupload dan diimport ke database";
            @unlink($_FILES[$file_element_name]);
        } else {
            $status = FALSE;
            $msg = 'gagal diupload (ERROR: ' . $this->upload->display_errors('', '') . ')';
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

//    public function read_xml($filename = './files/xml/upload_19.xml', $JENJANG = 'MI') {
//        $this->timetables_hanlder->read_xml($filename, $JENJANG);
//        exit();
//    }

    public function cetak_jadwal_kelas() {
        $kelas = $this->kelas->get_all(FALSE);
        $hari = $this->hari->get_rows(array('LIBUR_HARI' => 0));

        $data = array(
            'kelas' => $kelas,
            'hari' => $hari,
            'jam_pelajaran' => array(),
            'jadwal' => array(),
        );

        foreach ($kelas as $index => $detail_kelas) {
            foreach ($hari as $detail_hari) {
                $data_jp = array(
                    'DEPT_MJP' => $detail_kelas->ID_DEPT,
                    'JK_MJP' => $detail_kelas->JK_KELAS,
                );
                $data['jam_pelajaran'][$detail_kelas->ID_KELAS][$detail_hari->ID_HARI] = $this->jp->get_rows($data_jp);
                foreach ($data['jam_pelajaran'][$detail_kelas->ID_KELAS][$detail_hari->ID_HARI] as $detail_jp) {
                    $result = $this->jadwal->get_jadwal_kelas($detail_kelas->ID_KELAS, $detail_hari->ID_HARI, $detail_jp->URUTAN_MJP);
                    if (count($result) > 0) {
                        $data['jadwal'][$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP] = $result;
                    }
                }
            }
        }
        
        $this->load->view('backend/akademik/jadwal/cetak_jadwal_kelas', $data);
    }

    public function cetak_kehadiran_guru() {
        $jk = $this->jk->get_all(FALSE);
        $hari = $this->hari->get_rows(array('LIBUR_HARI' => 0));
        $guru = $this->guru->get_all(FALSE);

        $data = array(
            'jk' => $jk,
            'hari' => $hari,
            'guru' => array(),
            'jam_pelajaran' => array(),
            'jadwal' => array(),
        );

        foreach ($jk as $detail_jk) {
            foreach ($hari as $detail_hari) {
                $data_jp = array(
                    'JK_MJP' => $detail_jk->ID_JK,
                );
                $jam_pelajaran = $this->jp->get_rows($data_jp);
                $data['jam_pelajaran'][$detail_hari->ID_HARI][$detail_jk->ID_JK] = 0;
                foreach ($guru as $detail_guru) {
                    $jumlah_jadwal_guru = 0;
                    $data['guru'][$detail_hari->ID_HARI][$detail_jk->ID_JK][$detail_guru->ID_PEG] = $detail_guru;
                    foreach ($jam_pelajaran as $detail_jp) {
                        $data_jadwal = array(
                            'HARI_AJ' => $detail_hari->ID_HARI,
                            'JAM_AJ' => $detail_jp->ID_MJP,
                            'GURU_AGM' => $detail_guru->ID_PEG,
                        );
                        $result = $this->jadwal->get_kehadiran_guru($data_jadwal);

                        if (count($result) > 0) {
                            if ($data['jam_pelajaran'][$detail_hari->ID_HARI][$detail_jk->ID_JK] < $detail_jp->URUTAN_MJP)
                                $data['jam_pelajaran'][$detail_hari->ID_HARI][$detail_jk->ID_JK] = $detail_jp->URUTAN_MJP;

                            $data['jadwal'][$detail_hari->ID_HARI][$detail_jk->ID_JK][$detail_guru->ID_PEG][$detail_jp->URUTAN_MJP] = $result;
                            $jumlah_jadwal_guru++;
                        }
                    }
                    if ($jumlah_jadwal_guru == 0)
                        unset($data['guru'][$detail_hari->ID_HARI][$detail_jk->ID_JK][$detail_guru->ID_PEG]);
                }
            }
        }

        $this->load->view('backend/akademik/jadwal/cetak_kehadiran_guru', $data);
    }

    public function cetak_jadwal_guru() {
        $guru = $this->guru->get_all(FALSE);

        $data = array(
            'guru' => $guru,
        );
        foreach ($guru as $detail_guru) {
            $result = $this->jadwal->get_jadwal_guru($detail_guru->ID_PEG);
            if (count($result) > 0)
                $data['jadwal'][$detail_guru->ID_PEG] = $result;
        }

        $this->load->view('backend/akademik/jadwal/cetak_guru', $data);
    }

    public function cetak_kelas_guru() {
        $guru = $this->guru->get_all(FALSE);

        $data = array(
            'guru' => $guru,
        );
        foreach ($guru as $detail_guru) {
            $result = $this->jadwal->get_jumlah_kelas_guru($detail_guru->ID_PEG);
            if (count($result) > 0)
                $data['jadwal'][$detail_guru->ID_PEG] = $result;
        }

        $this->load->view('backend/akademik/jadwal/cetak_kelas_guru', $data);
    }

    public function cetak_mapel_guru() {
        $guru = $this->guru->get_all(FALSE);

        $data = array(
            'guru' => $guru,
        );
        foreach ($guru as $detail_guru) {
            $result = $this->jadwal->get_jumlah_mapel_guru($detail_guru->ID_PEG);
            if (count($result) > 0)
                $data['jadwal'][$detail_guru->ID_PEG] = $result;
        }

        $this->load->view('backend/akademik/jadwal/cetak_mapel_guru', $data);
    }
    
    public function cetak_jurnal_kelas() {
        $data['hari'] = $this->hari->get_rows(array('LIBUR_HARI' => 0));
        
        $this->load->view('backend/akademik/jadwal/cetak_jurnal_kelas', $data);
    }

}
