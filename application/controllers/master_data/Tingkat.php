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
class Tingkat extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TINGK";

    public function __construct() {
        parent::__construct();
        $this->load->model(array('tingkat_model' => 'tingkat', 'departemen_model' => 'departemen'));
        $this->auth->validation(array(11, 2, 5));
    }

    public function index() {
        $this->generate->backend_view('master_data/tingkat/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->tingkat->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_DEPT;
            $row[] = $item->KETERANGAN_TINGK;

//            $row[] = '
//                <div class="btn-group">
//                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
//                    <ul class="dropdown-menu">
//                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TINGK . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
//                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_TINGK . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
//                    </ul>
//                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tingkat->count_all(),
            "recordsFiltered" => $this->tingkat->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->tingkat);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Nama',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_TINGK',
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->NAMA_TINGK
                )
            ),
            array(
                'label' => 'Departemen',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type'  => 'dropdown',                                      // WAJIB
                    'name'  => 'DEPT_TINGK',                                    // WAJIB
                    'value' => $data == NULL ? "" : $data->DEPT_TINGK,
                    'value_blank'  => '-- Pilih Departemen --',
                    'data'  => $this->departemen->get_all()                      // WAJIB
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_TINGK',
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->KETERANGAN_TINGK
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

//    public function ajax_add() {
//        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('add');
//
//        $data = $this->input->post();
//        unset($data['TOKEN']);
//        $insert = $this->tingkat->save($data);
//
//        $this->generate->output_JSON(array("status" => 1));
//    }
//
//    public function ajax_update() {
//        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('edit');
//        $data = array();
//        $post = $this->input->post()    ;
//        $data = $this->generate->filter_data_post($this->edit_id, $this->primary_key, $post);
//        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $post);
//        
//        $where = $cek['where'];
//        if (isset($cek['data'])) $data[$this->primary_key] = $cek['data'][$this->primary_key];
//        
//        $affected_row = $this->tingkat->update($where, $data);
//
//        $this->generate->output_JSON(array("status" => $affected_row));
//    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->tingkat->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->tingkat->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
