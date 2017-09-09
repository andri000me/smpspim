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
class Laporan_lari extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_lari_model' => 'laporan_lari',
            'pelanggaran_model' => 'pelanggaran',
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_lari/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_lari->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->JUMLAH_POIN_KSH;
            $row[] = $item->JUMLAH_LARI_KSH;
            
            $row[] = '<input type="checkbox" class="checkbox" onchange="check_cetak_siswa(this)" value="' . $item->ID_KSH . '">';
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="cetak(' . $item->ID_KSH . ');"><i class="fa fa-print"></i></button>&nbsp;';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_lari->count_all(),
            "recordsFiltered" => $this->laporan_lari->count_filtered(),
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
            
            $data['data'] = $this->laporan_lari->get_data_cetak($where);
            
            foreach ($data['data'] as $key => $detail) {
//                $where_ksh = array(
//                    'TA_KSH' => $detail->TA_KSH,
//                    'SISWA_KSH' => $detail->SISWA_KSH,
//                );
//                $data_ksh = array(
//                    'CETAK_LARI_KSH' => 1
//                );
//                $this->laporan_lari->update($where_ksh, $data_ksh);
                
                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);
                var_dump($detail);
                $data['data'][$key]['DETAIL'] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        }

        $this->load->view('backend/komdis/laporan_lari/cetak_perkelas_multi', $data);
    }

}
