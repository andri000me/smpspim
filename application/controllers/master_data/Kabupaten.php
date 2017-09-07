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
class Kabupaten extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_KAB";

    public function __construct() {
        parent::__construct();
        $this->load->model('kabupaten_model', 'kabupaten');
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/kabupaten/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kabupaten->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_KAB;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;
            $row[] = $item->NAMA_NEGARA;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_KAB . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_KAB . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kabupaten->count_all(),
            "recordsFiltered" => $this->kabupaten->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kabupaten);
        
        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Provinsi',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'PROVINSI_KAB',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'value' => $data == NULL ? "" : $data->PROVINSI_KAB,
                    'label' => $data == NULL ? "" : $data->NAMA_PROV.' - '.$data->NAMA_NEGARA,
//                    'data'  => $this->kelompok->get_all(),                       // WAJIB
//                    'url'   => NULL
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/provinsi/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Nama Kabupaten',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_KAB',
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
            'NAMA_KAB' => $this->input->post('NAMA_KAB'),
            'PROVINSI_KAB' => $this->input->post('PROVINSI_KAB'),
        );
        $insert = $this->kabupaten->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_KAB'] = $this->input->post('NAMA_KAB');
        $data['PROVINSI_KAB'] = $this->input->post('PROVINSI_KAB');
        
        $affected_row = $this->kabupaten->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->kabupaten->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->kabupaten->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
