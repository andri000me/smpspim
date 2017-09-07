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
class Departemen extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_DEPT";

    public function __construct() {
        parent::__construct();
        $this->load->model('departemen_model', 'departemen');
        $this->auth->validation(array(11, 5));
    }

    public function index() {
        $this->generate->backend_view('master_data/departemen/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->departemen->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_DEPT;
            $row[] = $item->NAMA_DEPT;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->TELP_DEPT;
            $row[] = $item->EMAIL_DEPT;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_DEPT . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_DEPT . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->departemen->count_all(),
            "recordsFiltered" => $this->departemen->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->departemen);
        
        $input_id = TRUE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Nama Departemen',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_DEPT',
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->NAMA_DEPT
                )
            ),
            array(
                'label' => 'Direktur',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'DIREKTUR_DEPT',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'value' => $data == NULL ? "" : $data->DIREKTUR_DEPT,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
//                    'data'  => $this->kelompok->get_all(),                       // WAJIB
//                    'url'   => NULL
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/pegawai/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Alamat',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'ALAMAT_DEPT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->ALAMAT_DEPT
                )
            ),
            array(
                'label' => 'Kecamatan',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'KECAMATAN_DEPT',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'value' => $data == NULL ? "" : $data->KECAMATAN_DEPT,
                    'label' => $data == NULL ? "" : $data->NAMA_KEC.', '.$data->NAMA_KAB.', '.$data->NAMA_PROV.', '.$data->NAMA_NEGARA,
//                    'data'  => $this->kelompok->get_all(),                       // WAJIB
//                    'url'   => NULL
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/kecamatan/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Telepon',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'TELP_DEPT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->TELP_DEPT
                )
            ),
            array(
                'label' => 'Faximile',
                'required' => FALSE,
                'keterangan' => '',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'FAX_DEPT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->FAX_DEPT
                )
            ),
            array(
                'label' => 'Website',
                'required' => FALSE,
                'keterangan' => '',
                'length' => 4,
                'data' => array(
                    'type' => 'text',
                    'name' => 'WEBSITE_DEPT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->WEBSITE_DEPT
                )
            ),
            array(
                'label' => 'Email',
                'required' => FALSE,
                'keterangan' => '',
                'length' => 4,
                'data' => array(
                    'type' => 'text',
                    'name' => 'EMAIL_DEPT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->EMAIL_DEPT
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
//        $insert = $this->departemen->save($data);
//
//        $this->generate->output_JSON(array("status" => 1));
//    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $data = array();
        $post = $this->input->post();
        $data = $this->generate->filter_data_post($this->edit_id, $this->primary_key, $post);
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $post);
        
        $where = $cek['where'];
        if (isset($cek['data'])) $data[$this->primary_key] = $cek['data'][$this->primary_key];
        
        $affected_row = $this->departemen->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->departemen->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->departemen->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
