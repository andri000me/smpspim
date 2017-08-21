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
class Kalender_akademik extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_AK";

    public function __construct() {
        parent::__construct();
        $this->load->model('kalender_akademik_model', 'kalender');
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/kalender_akademik/index');
    }
    
    public function event_calendar() {
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        
        $where = array(
            'TGL_MULAI_AK > ' => $start,
            'TGL_SELESAI_AK < ' => $end
        );
        $event = $this->kalender->get_rows($where);
        
        $this->generate->output_JSON($event);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kalender);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Nama',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_AK',
                    'id' => 'NAMA_AK',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_AK
                )
            ),
            array(
                'label' => 'Background',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type'  => 'dropdown',                                      
                    'name'  => 'BACKGROUND_AK',                                    
                    'id'  => 'BACKGROUND_AK',                                    
                    'value' => $data == NULL ? "#34495e" : $data->BACKGROUND_AK,
                    'data'  => array(
                        array('id' => '#34495e', 'text' => "Primary"),
                        array('id' => '#3498db', 'text' => "Info"),
                        array('id' => '#62cb31', 'text' => "Success"),
                        array('id' => '#ffb606', 'text' => "Warning"),
                        array('id' => '#e74c3c', 'text' => "Danger"),
                    )
                )
            ),
            array(
                'label' => 'Libur',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type'  => 'dropdown',                                      
                    'name'  => 'LIBUR_AK',                                    
                    'id'  => 'LIBUR_AK',                                    
                    'value' => $data == NULL ? 1 : $data->LIBUR_AK,
                    'data'  => array(
                        array('id' => '0', 'text' => "Tidak"),
                        array('id' => '1', 'text' => "Ya"),
                    )
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = $this->input->post();
        $data['BORDER_AK'] = $data['BACKGROUND_AK'];
        $data['LIBUR_AK'] = $data['LIBUR_AK'];
        $data['TA_AK'] = $this->session->userdata('ID_TA_ACTIVE');
        $data['USER_AK'] = $this->session->userdata('ID_USER');
        
        $insert = $this->kalender->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');
        
        $data = array(
            'TGL_MULAI_AK' => $this->input->post('TGL_MULAI_AK'),
            'TGL_SELESAI_AK' => $this->input->post('TGL_SELESAI_AK'),
        );
        $where = array(
            'ID_AK' => $this->input->post('ID_AK')
        );
        
        $affected_row = $this->kalender->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID_AK");
        $affected_row = $this->kalender->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
}