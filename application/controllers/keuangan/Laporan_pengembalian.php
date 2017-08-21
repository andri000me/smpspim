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
class Laporan_pengembalian extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_DT";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_pengembalian_model' => 'laporan_pengembalian',
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/laporan_pengembalian/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_pengembalian->get_datatables();
        $nominal = $this->laporan_pengembalian->nominal_all();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_TAG;
            $row[] = $item->NAMA_DT;
            $row[] = $item->DEPT_DT;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $this->money->format($item->NOMINAL_DT);
            $row[] = $item->KETERANGAN_BAYAR;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = $item->CREATED_BAYAR;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="'. site_url('keuangan/pengembalian/ajax_cetak/'.$item->KODE_BAYAR).'" title="Cetak" target="_blank"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kwitansi</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_pengembalian->count_all(),
            "recordsFiltered" => $this->laporan_pengembalian->count_filtered(),
            "data" => $data,
            "nominal" => $nominal
        );

        $this->generate->output_JSON($output);
    }
}
