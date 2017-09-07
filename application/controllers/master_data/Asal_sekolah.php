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
class Asal_sekolah extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kecamatan_model' => 'kecamatan',
            'asal_sekolah_model' => 'asal_sekolah',
            'jenjang_sekolah_model' => 'jenjang',
        ));
        $this->auth->validation(array(3, 11, 2));
    }

    public function index() {
        $this->generate->backend_view('master_data/asal_sekolah/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->asal_sekolah->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_JS;
            $row[] = $item->NAMA_AS;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_AS . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->asal_sekolah->count_all(),
            "recordsFiltered" => $this->asal_sekolah->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->asal_sekolah);
        
        $input_id = FALSE;
        $show_id = FALSE;
        $field_id = 'ID_AS';

        $data_html = array(
            array(
                'label' => 'Jenjang',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type'  => 'dropdown',                                      // WAJIB
                    'name'  => 'JENJANG_AS',                                    // WAJIB
                    'value' => $data == NULL ? "" : $data->ID_JS,
//                    'value_blank'  => '-- Pilih Jenjang --',
                    'data'  => $this->jenjang->get_all_add()                       // WAJIB
                )
            ),
            array(
                'label' => 'Nama Sekolah',                                        // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',                                           // WAJIB
                    'name' => 'NAMA_AS',                                        // WAJIB
                    'value' => $data == NULL ? "" : $data->NAMA_AS
                )
            ),
            array(
                'label' => 'Alamat',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'KECAMATAN_AS',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'value' => $data == NULL ? "" : $data->KECAMATAN_AS,
                    'label' => $data == NULL ? "" : $data->NAMA_KEC,
//                    'data'  => $this->kelompok->get_all(),                       // WAJIB
//                    'url'   => NULL
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/kecamatan/auto_complete')                      // WAJIB
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $field_id, $data_html, $input_id, $show_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');
        
        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        $insert = $this->asal_sekolah->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');

        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        
        $where = array(
            'ID_AS' => $data['ID_AS']
        );
        unset($data['ID_AS']);

        $affected_row = $this->asal_sekolah->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->asal_sekolah->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
}
