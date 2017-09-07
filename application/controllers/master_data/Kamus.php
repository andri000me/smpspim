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
class Kamus extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_GK";

    public function __construct() {
        parent::__construct();
        $this->load->model('kamus_model', 'kamus');
        $this->auth->validation(array(11, 2, 8));
    }

    public function index() {
        $this->generate->backend_view('master_data/kamus/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kamus->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->LATIN_GK;
            $row[] = $item->ARAB_GK;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_GK . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kamus->count_all(),
            "recordsFiltered" => $this->kamus->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kamus);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Text Latin',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'LATIN_GK',
                    'id' => 'LATIN_GK',
                    $data == NULL ? '' : 'readonly' => 'true',
                    'value' => $data == NULL ? "" : $data->LATIN_GK
                )
            ),
            array(
                'label' => 'Text Arab',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'ARAB_GK',
                    'id' => 'ARAB_GK',
                    $data != NULL ? '' : 'readonly' => 'true',
                    'value' => $data == NULL ? "" : $data->ARAB_GK
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'LATIN_GK' => $this->input->post('LATIN_GK'),
            'ARAB_GK' => $this->input->post('ARAB_GK'),
        );
        $insert = $this->kamus->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['LATIN_GK'] = $this->input->post('LATIN_GK');
        $data['ARAB_GK'] = $this->input->post('ARAB_GK');
        
        $affected_row = $this->kamus->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->kamus->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function cek_kata() {
        $this->generate->set_header_JSON();
        
        $status = $this->kamus->cek_kata($this->input->post('kata'));
        
        $this->generate->output_JSON(array('status' => $status));
    }

}
