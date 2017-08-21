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
class Jenis_tindakan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_KJT";

    public function __construct() {
        parent::__construct();
        $this->load->model('jenis_tindakan_model', 'jenis_tindakan');
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/jenis_tindakan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jenis_tindakan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_KJT;
            $row[] = $item->POIN_KJT;
            $row[] = $item->POIN_MAKS_KJT;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_KJT . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jenis_tindakan->count_all(),
            "recordsFiltered" => $this->jenis_tindakan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jenis_tindakan);
        
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
                    'name' => 'NAMA_KJT',
                    'readonly' => 'true',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_KJT
                )
            ),
            array(
                'label' => 'Poin Min.',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'POIN_KJT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->POIN_KJT
                )
            ),
            array(
                'label' => 'Poin Maks.',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'POIN_MAKS_KJT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->POIN_MAKS_KJT
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['POIN_KJT'] = $this->input->post('POIN_KJT');
        $data['POIN_MAKS_KJT'] = $this->input->post('POIN_MAKS_KJT');
        
        $affected_row = $this->jenis_tindakan->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->jenis_tindakan->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_tindakan->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
