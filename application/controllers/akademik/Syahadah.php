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
class Syahadah extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_SISWA";
    
    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'syahadah_model' => 'syahadah',
            'siswa_model' => 'siswa',
            'kamus_model' => 'kamus',
        ));
        $this->load->library('translasi_handler');
        $this->auth->validation(array(2, 8));
    }

    public function index() {
        $this->generate->backend_view('akademik/syahadah/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->syahadah->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = '<h4>'.$this->translasi_handler->proses($item->NAMA_SISWA).'</h4>';
            $row[] = $item->AYAH_NAMA_SISWA;
            $row[] = '<h4>'.$this->translasi_handler->proses($item->AYAH_NAMA_SISWA).'</h4>';
            $row[] = $item->JK_SISWA;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = '<button type="button" class="btn btn-info btn-sm" onclick="update_data_'.$id_datatables.'(\''.$item->ID_SISWA.'\');"><i class="fa fa-pencil"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->syahadah->count_all(),
            "recordsFiltered" => $this->syahadah->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->siswa);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array();
        
        $text = trim($data->NAMA_SISWA);
        $text = strtoupper($text);
        $text = str_replace("`", "'", $text);
        $text = str_replace(".", " ", $text);

        $parsing_siswa = explode(" ", $text);
        
        $text = trim($data->AYAH_NAMA_SISWA);
        $text = strtoupper($text);
        $text = str_replace("`", "'", $text);
        $text = str_replace(".", " ", $text);

        $parsing_ayah = explode(" ", $text);
        
        $parsing = array_merge($parsing_ayah, $parsing_siswa);
        
        foreach ($parsing as $detail) {
            if($detail == '' || $detail == ' ') continue;
            
            $data_html[] = array(
                'label' => $detail,
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'text',
                    'name' => 'ARAB[]',
                    "placeholder" => " ",
                    'value' => $this->translasi_handler->proses($detail)
                )
            );
            $data_html[] = array(
                'hidden' => TRUE,                                               
                'data' => array(
                    'name' => 'LATIN[]',                                          
                    'value' => $detail                                    
                )
            );
        }
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }
    
    public function ajax_update() {
        $this->generate->set_header_JSON();
        
        $latin = $this->input->post('LATIN');
        $arab = $this->input->post('ARAB');
        
        foreach ($latin as $index => $value) {
            if($this->kamus->get_text($value) == NULL) 
                $this->kamus->save(array('LATIN_GK' => $value, 'ARAB_GK' => $arab[$index]));
            else
                $this->kamus->update(array('LATIN_GK' => $value), array('ARAB_GK' => $arab[$index]));
        }
        
        $this->generate->output_JSON(array('status' => 1));
    }

}
