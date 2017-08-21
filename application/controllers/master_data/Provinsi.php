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
class Provinsi extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_PROV";

    public function __construct() {
        parent::__construct();
        $this->load->model('provinsi_model', 'provinsi');
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/provinsi/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->provinsi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_PROV;
            $row[] = $item->NAMA_PROV;
            $row[] = $item->NAMA_NEGARA;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PROV . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_PROV . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->provinsi->count_all(),
            "recordsFiltered" => $this->provinsi->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->provinsi);
        
        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Negara',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'NEGARA_PROV',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'value' => $data == NULL ? "" : $data->NEGARA_PROV,
                    'label' => $data == NULL ? "" : $data->NAMA_NEGARA,
//                    'data'  => $this->kelompok->get_all(),                       // WAJIB
//                    'url'   => NULL
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/negara/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Nama Provinsi',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PROV',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_PROV
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'NAMA_PROV' => $this->input->post('NAMA_PROV'),
            'NEGARA_PROV' => $this->input->post('NEGARA_PROV'),
        );
        $insert = $this->provinsi->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_PROV'] = $this->input->post('NAMA_PROV');
        $data['NEGARA_PROV'] = $this->input->post('NEGARA_PROV');
        
        $affected_row = $this->provinsi->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->provinsi->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->provinsi->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
