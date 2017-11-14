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
class Alarm extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_MA";

    public function __construct() {
        parent::__construct();
        $this->load->model('alarm_model', 'alarm');
        $this->auth->validation(array(11));
    }

    public function index() {
        $this->generate->backend_view('master_data/alarm/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->alarm->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->JENIS_MA;
            $row[] = $item->NAMA_JK;
            $row[] = $item->JAM_MA;
            $row[] = $item->FILE_MA;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_MA . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->alarm->count_all(),
            "recordsFiltered" => $this->alarm->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->alarm);
        
        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'JK',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'JK_MA',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->JK_MA,
                    'label' => $data == NULL ? "" : $data->NAMA_JK,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/jk/auto_complete')                      
                )
            ),
            array(
                'label' => 'Jenis',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type'  => 'dropdown',                                      
                    'name'  => 'JENIS_MA',                                    
                    'value' => $data == NULL ? "" : $data->JENIS_MA,
                    'value_blank'  => '-- Pilih Jenis --',
                    'data'  => array(
                        array('id' => 'KBM', 'text' => "KBM"),
                        array('id' => 'UJIAN', 'text' => "UJIAN"),
                    )
                )
            ),
            array(
                'label' => 'Jam',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'clockpicker',
                    'name' => 'JAM_MA',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->JAM_MA
                )
            ),
            array(
                'label' => 'Nama File',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'text',
                    'name' => 'FILE_MA',
                    "placeholder" => "file_alarm_1.mp3",
                    'value' => $data == NULL ? "" : $data->FILE_MA
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'JK_MA' => $this->input->post('JK_MA'),
            'JENIS_MA' => $this->input->post('JENIS_MA'),
            'JAM_MA' => $this->input->post('JAM_MA'),
            'FILE_MA' => $this->input->post('FILE_MA'),
        );
        $insert = $this->alarm->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['JK_MA'] = $this->input->post('JK_MA');
        $data['JENIS_MA'] = $this->input->post('JENIS_MA');
        $data['JAM_MA'] = $this->input->post('JAM_MA');
        $data['FILE_MA'] = $this->input->post('FILE_MA');
        
        $affected_row = $this->alarm->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->alarm->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->alarm->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
