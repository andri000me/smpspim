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
class Laporan_mutasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_mutasi_model' => 'laporan_mutasi',
            'pelanggaran_model' => 'pelanggaran',
        ));
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_mutasi/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_mutasi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
            $row[] = $item->NIS_NIS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->ALAMAT_SISWA;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_MUTASI;
            $row[] = $item->NO_SURAT_MUTASI_SISWA;
            $row[] = $item->TANGGAL_MUTASI_SISWA;
            $row[] = $item->JUMLAH_POIN_KSH;
            $row[] = $item->JUMLAH_LARI_KSH;
            
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="cetak(' . $item->ID_KSH . ');"><i class="fa fa-print"></i></button>&nbsp;';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_mutasi->count_all(),
            "recordsFiltered" => $this->laporan_mutasi->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function cetak_perkelas() {
        $input_kelas = $this->input->get('KELAS');
        $data = array();

        if ($input_kelas != "") {
            $kelas_exp = explode(',', $input_kelas);
            $where = '(';
            foreach ($kelas_exp as $id_kelas) {
                if ($where != '(')
                    $where .= ' OR ';
                $where .= ' ID_KELAS=' . $id_kelas;
            }
            $where .= ') AND (JUMLAH_LARI_KSH > 2)';

            $data['data'] = $this->laporan_mutasi->get_data_cetak($where);

            foreach ($data['data'] as $key => $detail) {
                $where_ksh = array(
                    'TA_KSH' => $detail->TA_KSH,
                    'SISWA_KSH' => $detail->SISWA_KSH,
                );
                $data_ksh = array(
                    'CETAK_LARI_KSH' => 1
                );
                $this->laporan_mutasi->update($where_ksh, $data_ksh);

                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                $data['DETAIL_PELANGGARAN'][$key] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        }

        $this->load->view('backend/komdis/laporan_mutasi/cetak_perkelas_multi', $data);
    }

    public function cetak($ID_KSH) {
        $where = array('ID_KSH' => $ID_KSH);
        $siswa = $this->laporan_mutasi->get_full_by_id($where);
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

        $this->load->view('backend/komdis/laporan_mutasi/cetak_persiswa', $data);
    }

}
