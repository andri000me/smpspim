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
class Laporan_tindakan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_KSH";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_tindakan_model'=> 'laporan_tindakan',
        ));
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_tindakan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_tindakan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
            $row[] = $item->NIS_SHOW;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_KJT;
            $row[] = $item->DOMISILI_SISWA;
            $row[] = $item->TANGGAL_KT;

            $row[] = '<a href="'. site_url('komdis/laporan_poin/cetak_surat/'.($item->ID_KJT > 1 ? $item->ID_KT : $item->PAKET_SP_KT).'/'.$item->ID_KJT).'" target="_blank"><button type="button" class="btn btn-'.($item->NIS_SISWA == NULL ? 'danger' : 'success').' btn-sm"><i class="fa fa-print"></i>&nbsp;&nbsp;'.$item->NAMA_KJT.'</button></a>';
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_tindakan->count_all(),
            "recordsFiltered" => $this->laporan_tindakan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
}
