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
class Mutasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'assign_tagihan_model' => 'assign_tagihan',
            'mutasi_model' => 'mutasi',
            'siswa_model' => 'siswa',
            'kelas_model' => 'kelas',
            'tingkat_model' => 'tingkat',
            'status_mutasi_model' => 'status_mutasi'
        ));
        $this->load->library('mutasi_handler');
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/mutasi/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->mutasi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_NIS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->ALAMAT_SISWA;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->DEPT;
            $row[] = $item->TINGK;
            $row[] = $item->NAMA_MUTASI;
            $row[] = $item->NO_SURAT_MUTASI_SISWA;
            $row[] = $item->TANGGAL_MUTASI_SISWA;
            $row[] = ($item->ID_MUTASI != 99) ? '<a href="'. site_url('akademik/mutasi/cetak/'.$item->ID_SISWA).'" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print"></i></a>' : '';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->mutasi->count_all(),
            "recordsFiltered" => $this->mutasi->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form() {
        $data['MUTASI'] = $this->status_mutasi->get_all(FALSE);
        $this->generate->backend_view('akademik/mutasi/form', $data);
    }

    public function proses() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');
        
        $ID_AS = $this->input->post('ID_AS');
        $ID_SISWA = $this->input->post('ID_SISWA');
        $STATUS_MUTASI = $this->input->post('STATUS_MUTASI');
        
        $status = $this->mutasi_handler->proses($ID_AS, $ID_SISWA, $STATUS_MUTASI);

        $this->generate->output_JSON(array("status" => $status, 'msg' => 'Berhasil meng-mutasi siswa.'));
    }
    
    public function ac_siswa() {
        $this->generate->set_header_JSON();
        
        $data = $this->mutasi->get_ac_siswa($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
    
    public function get_data_siswa() {
        $this->generate->set_header_JSON();
        
        $data_siswa = $this->siswa->get_by_id($this->input->post('ID_SISWA'));

        if (file_exists('files/siswa/' . $data_siswa->NIS_SISWA . '.jpg')) {
            $data_siswa->FOTO_SISWA = $data_siswa->NIS_SISWA . '.jpg';
        } elseif (file_exists('files/siswa/' . $data_siswa->ID_SISWA . '.png') || $data_siswa->FOTO_SISWA != NULL) {
            $data_siswa->FOTO_SISWA = $data_siswa->ID_SISWA . '.png';
        }

        $data = array(
            'siswa' => $data_siswa,
            'tingkat' => $this->tingkat->get_all_except_id($data_siswa->TINGKAT_AS)
        );
        
        $this->generate->output_JSON($data);
    }
    
    public function get_data_tagihan() {
        $this->generate->set_header_JSON();
        
        $data = $this->assign_tagihan->get_tagihan_siswa_mutasi($this->input->post('ID_SISWA'));
        
        $this->generate->output_JSON($data);
    }
    
    public function cetak($ID_SISWA) {
        $data['siswa'] = $this->mutasi->get_detail_mutasi_siswa($ID_SISWA);
                
        $this->load->view('backend/akademik/mutasi/cetak', $data);
    }
}
