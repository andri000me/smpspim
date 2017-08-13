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
class Cetak_kelulusan extends CI_Controller {
    
    var $jenis = 'DAUROH';
    var $dept_cetak_kelulusan = array(
        6 => 11,
        8 => 11,
        10 => 14,
        13 => 14
    );

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'cetak_kelulusan_model' => 'cetak_kelulusan',
            'siswa_model' => 'siswa',
            'nilai_siswa_model' => 'nilai',
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/cetak_kelulusan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();
        
        $id_datatables = 'datatable1';
        $list = $this->cetak_kelulusan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->AYAH_NAMA_SISWA;
            $row[] = $item->ALAMAT_SISWA;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;
            $row[] = $item->NAMA_MUTASI;
            $row[] = '<div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Cetak Ijasah" onclick="cetak_ijasah(\'' . $item->ID_SISWA . '\', null)"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Ijasah</a></li>
                        <li><a href="javascript:void()" title="Cetak Transkrip" onclick="cetak_transkrip(\'' . $item->ID_SISWA . '\', null)"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Transkrip</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->cetak_kelulusan->count_all(),
            "recordsFiltered" => $this->cetak_kelulusan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function cetak_ijasah() {
        $this->load->library('translasi_handler');
        
        $ID_SISWA = $this->input->get('ID_SISWA');
        $ARABIC = $this->input->get('ARABIC');
        
        if($ID_SISWA == 'null') $ID_SISWA = NULL;
        if($ARABIC == 'null') $ARABIC = NULL;
        
        $data = array(
            'data' => $this->cetak_kelulusan->get_data_akademik($ID_SISWA, $ARABIC),
            'post' => $this->input->get()
        );
        
        if(count($data['data']) == 0) {
            echo 'TIDAK ADA IJASAH YANG DITAMPILKAN';
            
            exit();
        }
        
        $this->load->view('backend/akademik/cetak_kelulusan/cetak_ijasah', $data);
    }
    
    public function cetak_transkrip() {
        $this->load->library('translasi_handler');
        
        $ID_SISWA = $this->input->get('ID_SISWA');
        $ARABIC = $this->input->get('ARABIC');
        
        if($ID_SISWA == 'null') $ID_SISWA = NULL;
        if($ARABIC == 'null') $ARABIC = NULL;
        
        $siswa = $this->cetak_kelulusan->get_data_akademik($ID_SISWA, $ARABIC);
        
        if(count($siswa) == 0) {
            echo 'TIDAK ADA TRANSKRIP YANG DITAMPILKAN';
            
            exit();
        }
        
        $data = array(
            'post' => $this->input->get()
        );
        foreach ($siswa as $detail) {
            $data['data'][$detail->ID_AS] = array(
                'DATA' => $detail,
                'NILAI_LATIN' => $this->cetak_kelulusan->get_nilai($detail->ID_AS, TRUE),
                'NILAI_ARAB' => $this->cetak_kelulusan->get_nilai($detail->ID_AS, FALSE)
            );
        }
        
        $this->load->view('backend/akademik/cetak_kelulusan/cetak_transkrip', $data);
    }
}
