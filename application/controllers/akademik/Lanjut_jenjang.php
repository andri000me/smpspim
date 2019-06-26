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
class Lanjut_jenjang extends CI_Controller {

    var $jenis = 'DAUROH';
    var $dept_lanjut_jenjang = array(
        6 => 11,
        8 => 11,
        10 => 14,
        13 => 14
    );

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'lanjut_jenjang_model' => 'lanjut_jenjang',
            'tahun_ajaran_model' => 'ta',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
            'siswa_model' => 'siswa'
        ));
        $this->load->library(array(
            'pelanggaran_handler',
            'kenaikan_handler'
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(),
            'DEPT' => $this->tingkat->get_tingkat_dept()
        );
        $this->generate->backend_view('akademik/lanjut_jenjang/index', $data);
    }

    public function ajax_list($ID_KELAS, $ID_TA) {
        $this->generate->set_header_JSON();

        $kelas_next = $this->kelas->get_kelas_lanjut_jenjang($ID_KELAS, $ID_TA);

        $id_datatables = 'datatable1';
        $list = $this->lanjut_jenjang->get_datatables($ID_KELAS);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();

            $row[] = $item->NIS_NIS;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
//            $row[] = $item->KETERANGAN_TINGK;
//            $row[] = $item->NAMA_KELAS;
//            $row[] = $item->NAMA_PEG;

            $html_kelas = '<select class="form-control" onchange="changeKelas(this)"><option value="">-- Pilih Kelas Tujuan --</option>';
            foreach ($kelas_next as $detail) {
                $html_kelas .= '<option value="' . $detail->ID_KELAS . '">' . $detail->NAMA_KELAS . '</option>';
            }
            $html_kelas .= "</select>";

            $row[] = $html_kelas;
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="proses_siswa(this)" data-id="' . $item->ID_SISWA . '" data-siswa="' . $item->ID_AS . '" data-nama="' . $item->NAMA_SISWA . '"><i class="fa fa-arrow-circle-right"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->lanjut_jenjang->count_all($ID_KELAS),
            "recordsFiltered" => $this->lanjut_jenjang->count_filtered($ID_KELAS),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function proses_siswa() {
        $this->generate->set_header_JSON();

        $ID_AS = $this->input->post('ID_AS');
        $ID_SISWA = $this->input->post('ID_SISWA');
        $ID_TA = $this->input->post('ID_TA');
        $ID_KELAS = $this->input->post('ID_KELAS');
        $STATUS_KENAIKAN = 1;

        $kelas = $this->lanjut_jenjang->get_data_kelas($ID_KELAS);
        $ID_TINGK = $kelas->TINGKAT_KELAS;

        $result = $this->kenaikan_handler->proses($ID_AS, $ID_TA, $STATUS_KENAIKAN, $ID_TINGK);
var_dump($check);exit();
        if ($result) {
            $msg = 'Berhasil memasukan ke tingkat';
            $ID_AS = $result;
            $data = array(
                'AKTIF_SISWA' => 1,
                'ALUMNI_SISWA' => 0,
                'STATUS_ASAL_SISWA' => 1,
                'STATUS_MUTASI_SISWA' => NULL,
                'TANGGAL_MUTASI_SISWA' => NULL,
                'NO_SURAT_MUTASI_SISWA' => NULL,
                'NOMOR_IJASAH_SISWA' => NULL,
                'NOMOR_SYAHADAH_SISWA' => NULL,
                'USER_MUTASI_SISWA' => NULL, 
            );
            $where = array(
                'ID_SISWA' => $ID_SISWA
            );
            $result = $this->siswa->update($where, $data);
            if ($result) {
                $result = $this->proses_kelas($ID_KELAS, $ID_AS, $ID_TINGK, $kelas);
                if ($result) {
                    $this->pelanggaran_handler->proses_poin_tahun_lalu($ID_SISWA, $ID_TA);
                    $msg = 'Berhasil memasukan siswa kekelas';
                } else {
                    $msg = 'Gagal memasukan siswa kekelas';
                }
            } else {
                $msg = 'Gagal mengaktifkan siswa';
            }
        } else {
            $msg = 'Gagal memasukan ke tingkat';
        }

        $this->generate->output_JSON(array('status' => $result, 'msg' => $msg));
    }

    private function proses_kelas($KELAS_AS, $ID_AS, $TINGKAT_AS, $data_kelas) {
        if ($data_kelas->JUMLAH_SISWA_KELAS >= $data_kelas->KAPASITAS_RUANG)
            $this->generate->output_JSON(array('status' => 0, 'msg' => "Kelas sudah penuh. Silahkan pilih kelas lain."));

        // UPDATE JUMLAH SISWA DI AKAD_KELAS
        $data_akad_kelas = array('JUMLAH_SISWA_KELAS' => ($data_kelas->JUMLAH_SISWA_KELAS + 1));
        $where_akad_kelas = array('ID_KELAS' => $KELAS_AS);

        $this->kelas->update($where_akad_kelas, $data_akad_kelas);

        // SET KELAS SISWA
        $data_akad_siswa = array(
            'KELAS_AS' => $KELAS_AS,
            'TINGKAT_AS' => $TINGKAT_AS,
        );
        $where_akad_siswa = array('ID_AS' => $ID_AS);

        return $this->akad_siswa->update($where_akad_siswa, $data_akad_siswa);
    }

    public function get_kelas_ta_depan() {
        $this->generate->set_header_JSON();

        $ID_TA = $this->input->post('ID_TA');

        $this->generate->output_JSON(array('status' => $result));
    }

}
