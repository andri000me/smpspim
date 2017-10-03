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
class Denah_us extends CI_Controller {
    var $mode = 'US';
    var $title = 'Ujian Sekolah';
    var $status_validasi = FALSE;

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'aturan_denah_model' => 'aturan_denah',
            'denah_model' => 'denah',
            'denah_us_model' => 'denah_us',
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(6);
        $this->status_validasi = $this->aturan_denah->is_us_validasi();
    }

    public function index() {
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;
        
        $this->generate->backend_view('pu/denah_us/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->denah_us->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            
            $aksi = '';
            if ($item->DATA_DENAH != NULL) {
                $aksi .= '<li><a href="javascript:void()" title="Lihat Denah" onclick="view_data_' . $id_datatables . '(\'' . $item->ID_PUD . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Denah</a></li>';
            } 
            if (!$item->READY_DENAH) { 
                $aksi .= '<li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PUD . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Denah</a></li>';
            }
            
            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">'.$aksi
                    .'</ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->denah_us->count_all(),
            "recordsFiltered" => $this->denah_us->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function cek_denah() {
        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
            
        $status = $this->aturan_denah->is_us_dibuat();
        
        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah telah dibuat. Anda tidak boleh membuat denah baru pada tahun ajaran aktif.'));
    }
    
    public function buat_denah() {
        if($this->status_validasi) redirect ('pu/denah_us/show_denah');
        
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;
        
        $this->generate->backend_view('pu/denah_us/form', $data);
    }
    
    public function proses_aturan() {
        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        $status = $this->aturan_denah->is_us_dibuat();
        
        $output = $this->denah_handler->proses_aturan($status, $this->mode);
            
        $this->generate->output_JSON($output);
    }
    
    public function simpan_denah() {
         $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        $result = $this->denah_handler->proses_buat_denah($this->mode);
        
        $this->generate->output_JSON(array('status' => 1, 'data' => 'Berhasil membuat denah'));
    }
    
    public function show_denah() {
        $this->session->set_userdata("TOKEN_DENAH_READY", $this->crypt->randomString());
        
        $data['data'] = json_decode($this->aturan_denah->get_denah_cawu(), TRUE);
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;
        $data['TOKEN'] = $this->session->userdata("TOKEN_DENAH_READY");
        $data['STATUS_VALIDASI'] = $this->status_validasi;
        
        $this->generate->backend_view('pu/denah_us/view', $data);
    }
    
    public function request_denah() {
        $this->generate->set_header_JSON();
        
        $result = $this->denah_handler->show_denah($this->mode);
        
        $this->generate->output_JSON(array('status' => 1, 'data' => $result));
    }
    
    public function atur_ulang_denah() {
        $this->generate->set_header_JSON();
        
        $result = $this->denah_handler->atur_ulang_denah($this->mode);
        
        $this->generate->output_JSON(array('status' => $result, 'msg' => 'Gagal memproses data. Denah tidak dapat disimpan. Halaman akan dimuat ulang otomatis.'));
    }
    
    public function proses_sisa() {
//        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        $result = $this->denah_handler->proses_buat_denah($this->mode, TRUE);
        
        $this->generate->output_JSON(array('status' => 1, 'data' => $result));
    }
    
    public function proses_sisa_longgar() {
        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        $result = $this->denah_handler->proses_buat_denah($this->mode, TRUE, TRUE);
        
        $this->generate->output_JSON(array('status' => 1, 'data' => $result));
    }
    
    public function denah_ready() {
        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        if ($this->input->post("TOKEN") == $this->session->userdata("TOKEN_DENAH_READY")) $this->aturan_denah->ready_denah_us();
        
        $this->generate->output_JSON(array('status' => 1, 'data' => ''));
    }
    
    public function validasi_denah() {
        $this->generate->set_header_JSON();
        
        if($this->status_validasi) $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));
        
        $result = 1;//$this->denah_handler->validasi_denah($this->mode);
        
        if (($this->input->post("TOKEN") == $this->session->userdata("TOKEN_DENAH_READY")) && $this->aturan_denah->validasi_denah_us() && $result) {
            $status = 1;
            $msg = 'Berhasil memvalidasi denah. Anda akan diarahkan pada menu jadwal ujian sekolah';
            $link = site_url('pu/jadwal_us');
        } else {
            $status = 0;
            $msg = 'Denah gagal divalidasi';
            $link = '';
        }
        
        $this->generate->output_JSON(array('status' => $status, 'msg' => $msg, 'link' => $link));
    }
    
    public function ruang_fix() {
        $this->generate->set_header_JSON();
        
        $result = $this->denah_handler->ruang_fix($this->mode);
        
        $this->generate->output_JSON(array('status' => $result, 'msg' => 'Gagal memproses data. Halaman akan dimuat ulang otomatis.'));
    }
    
    public function gabung_ruang() {
        $this->generate->set_header_JSON();
        
        $result = $this->denah_handler->gabung_ruang($this->mode);
        
        $this->generate->output_JSON(array('status' => $result, 'msg' => 'Gagal memproses data. Halaman akan dimuat ulang otomatis.'));
    }

}
