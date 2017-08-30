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
class Laporan_peringatan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_peringatan_model'=> 'laporan_peringatan',
            'jenis_tindakan_model'=> 'jenis_tindakan',
            'laporan_tindakan_model'=> 'tindakan',
        ));
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_peringatan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $tindakan = $this->jenis_tindakan->get_all(FALSE);
        $list = $this->laporan_peringatan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->POIN_KSH;
            $row[] = $item->NAMA_KJT;
            $row[] = $item->POIN_KJT;
            
            $row[] = '<button type="button" class="btn btn-danger btn-sm" onclick="cetak_surat('.$item->ID_KJT.', \''.$item->URL_KJT.'\', '.$item->ID_KSH.', '.$item->KOLEKTIF_KJT.');"><i class="fa fa-print"></i>&nbsp;&nbsp;'.$item->NAMA_KJT.'</button>&nbsp;';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_peringatan->count_all(),
            "recordsFiltered" => $this->laporan_peringatan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
}
