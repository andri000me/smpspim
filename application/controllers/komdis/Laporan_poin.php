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
class Laporan_poin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model(array(
            'pelanggaran_header_model' => 'laporan_poin',
            'pelanggaran_model' => 'pelanggaran',
            'jenis_tindakan_model' => 'jenis_tindakan',
            'laporan_tindakan_model' => 'tindakan',
            'departemen_model' => 'dept',
            'kelas_model' => 'kelas',
            'pondok_siswa_model' => 'pondok_siswa'
        ));
        $this->load->library('pelanggaran_handler');
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_poin/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $tindakan = $this->jenis_tindakan->get_all(FALSE);
        $list = $this->laporan_poin->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
//            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->POIN_KSH;
            $row[] = $item->LARI_KSH;
            $row[] = $item->SURAT;

//            foreach ($tindakan as $detail) {
//                if($this->tindakan->sudah_ditindak($item->ID_KSH, $detail->ID_KJT) AND $item->POIN_KSH $item $detail->POIN_KJT) { 
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="cetak(' . $item->ID_KSH . ');"><i class="fa fa-print"></i></button>&nbsp;';
//                }
//            }
//
//            $row[] = $aksi;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_poin->count_all(),
            "recordsFiltered" => $this->laporan_poin->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_JSON();

        $data_html = array(
            array(
                'label' => 'Penanggungjawab',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'PENANGGUNGJAWAB_KT',
                    'multiple' => FALSE,
                    'minimum' => 1,
                    'value' => $data == NULL ? "" : $data->PENANGGUNGJAWAB_KT,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
                    'data' => NULL,
                    'url' => base_url('master_data/pegawai/auto_complete')
                )
            )
        );

        $this->generate->output_JSON(array('STATUS' => TRUE, 'DATA' => $data_html));
    }

    public function ajax_add() {
//        $this->generate->set_header_JSON();

        $data = $this->input->get();

        $nomor_surat = $this->pengaturan->getNomorSuratKomdis($data['URL_KJT']);
        $this->pengaturan->setNomorSuratKomdis($data['URL_KJT'], $nomor_surat + 1);

        $input_kolektif = $data['KOLEKTIF_KJT'];
        $data_kolektif = $this->tindakan->get_kolektif($data['TINDAKAN_KT']);

        $data['NOMOR_SURAT_KT'] = $nomor_surat;
        $data['USER_KT'] = $this->session->userdata('ID_USER');
        $data['TANGGAL_KT'] = date("Y-m-d");

        unset($data['URL_KJT']);
        unset($data['KOLEKTIF_KJT']);

        if ($input_kolektif) {
            foreach ($data_kolektif as $detail) {
                $data['PELANGGARAN_HEADER_KT'] = $detail->ID_KSH;

                $id = $this->tindakan->save($data);
            }
        } else {
            $id = $this->tindakan->save($data);
        }

        redirect(site_url('komdis/laporan_poin/cetak_surat/' . $id));
    }

    public function cetak($ID_KSH) {
        $siswa = $this->laporan_poin->get_full_by_id($ID_KSH);
        $data = array();

        if (count($siswa) == 1) {
            foreach ($siswa as $detail) {
                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                $data['data'][] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        } 

        $this->load->view('backend/komdis/laporan_poin/cetak', $data);
    }

    public function cetak_pertindakan($ID_KJT) {
        $data = array();
        
        if ($ID_KJT != 0) {

            $siswa = $this->laporan_poin->get_full_by_id($ID_KJT, TRUE);
            foreach ($siswa as $detail) {
                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                $data['data'][] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        }

        $this->load->view('backend/komdis/laporan_poin/cetak', $data);
    }

    public function hapus_surat($ID_KT) {
        $this->generate->set_header_JSON();
        
        $result = $this->tindakan->hapus_surat($ID_KT);
        
        $this->generate->output_JSON(array('status' => $result));
    }
    
    public function cetak_surat($ID_KT) {
        $data = array(
            'nama_panitia' => 'KOMISI DISIPLIN SISWA'
        );

        $data_tindakan = $this->tindakan->get_by_id($ID_KT);

        if ($data_tindakan->KOLEKTIF_KJT) {
            $data['NAMA_TANGGUNGJAWAB'] = $data_tindakan->NAMA_TANGGUNGJAWAB;

            $data['JENJANG'] = array();
            $data['data'] = array();

            if ($data_tindakan->POIN_KSH >= 100) {
                $data_siswa = $this->tindakan->get_detail_kolektif($data_tindakan->ID_KJT, $data_tindakan->NOMOR_SURAT_KT);
                foreach ($data_siswa as $data_kolektif) {
                    $data_syariah = $this->pelanggaran_handler->cek_pelanggaran_syariah($data_kolektif->ID_SISWA);

                    if ($data_syariah == NULL) {
                        $data['JENJANG']['NON-SYARIAH'][$data_kolektif->ID_DEPT] = $data_kolektif->NAMA_DEPT;
                        $data['data']['NON-SYARIAH'][$data_kolektif->ID_DEPT][] = $data_kolektif;
                    } else {
                        $data['JENJANG']['SYARIAH'][$data_kolektif->ID_DEPT] = $data_kolektif->NAMA_DEPT;
                        $data['JENIS_PELANGGARAN'] = $data_syariah;

                        $data['data']['SYARIAH'][$data_kolektif->ID_DEPT][] = $data_kolektif;
                    }
                }
            } else {
                $data_siswa = $this->tindakan->get_detail_kolektif($data_tindakan->ID_KJT, $data_tindakan->NOMOR_SURAT_KT);
                foreach ($data_siswa as $data_kolektif) {
                    $data['JENJANG'][$data_kolektif->ID_DEPT] = $data_kolektif->NAMA_DEPT;
                    $data['data'][$data_kolektif->ID_DEPT][] = $data_kolektif;
                }
            }
        } else {
            $data['data'] = $data_tindakan;
        }

        if ($data['data'] == NULL) {
            echo '<h1>DATA SISWA TIDAK LENGKAP. PERIKSA TERLEBIH DAHULU KELAS SISWA DAN ALAMAT SISWA</h1>';
            exit();
        }

        $data['tanggal'] = $this->date_format->to_view($data_tindakan->TANGGAL_KT);
        $data['nomor_surat'] = $this->pengaturan->getFormatSurat($data_tindakan->NOMOR_SURAT_KT, $data_tindakan->URL_KJT, $data_tindakan->TANGGAL_KT, 'KOMDIS');

        $this->load->view('backend/komdis/laporan_poin/cetak_' . $data_tindakan->URL_KJT, $data);
    }

    public function cetak_perkelas($ID_KELAS) {
        if ($ID_KELAS != 0) {
            $data = array(
                'KELAS' => $this->kelas->get_by_id($ID_KELAS),
                'DATA' => $this->laporan_poin->get_data_perkelas($ID_KELAS),
                'TANGGAL' => $this->laporan_poin->get_terakhir_input()
            );
        }

        $this->load->view('backend/komdis/laporan_poin/cetak_perkelas', $data);
    }

    public function cetak_perpondok($ID_PONDOK) {
        if ($ID_PONDOK != 0) {
            $data = array(
                'PONDOK' => $this->pondok_siswa->get_by_id($ID_PONDOK),
                'DATA' => $this->laporan_poin->get_data_perkelas($ID_PONDOK, FALSE),
                'TANGGAL' => $this->laporan_poin->get_terakhir_input()
            );
        }

        $this->load->view('backend/komdis/laporan_poin/cetak_perpondok', $data);
    }

}