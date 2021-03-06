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
class Laporan_surat_segera extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_surat_segera_model'=> 'laporan_surat_segera',
            'jenis_tindakan_model'=> 'jenis_tindakan',
            'laporan_tindakan_model'=> 'tindakan',
        ));
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_surat_segera/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $tindakan = $this->jenis_tindakan->get_all(FALSE);
        $list = $this->laporan_surat_segera->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PONDOK_MPS;
            $row[] = $item->JUMLAH_POIN_KSH;
            $row[] = $item->NAMA_KJT;
            $row[] = $item->POIN_KJT;
            
            $row[] = '<button type="button" class="btn btn-danger btn-sm" onclick="cetak_surat('.$item->ID_KJT.', \''.$item->URL_KJT.'\', '.$item->ID_KSH.', '.$item->KOLEKTIF_KJT.');"><i class="fa fa-print"></i>&nbsp;&nbsp;'.$item->NAMA_KJT.'</button>&nbsp;';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_surat_segera->count_all(),
            "recordsFiltered" => $this->laporan_surat_segera->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
}
