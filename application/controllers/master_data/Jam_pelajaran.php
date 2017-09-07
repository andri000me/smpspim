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
class Jam_pelajaran extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_MJP";

    public function __construct() {
        parent::__construct();
        $this->load->model('jam_pelajaran_model', 'jam_pelajaran');
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/jam_pelajaran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jam_pelajaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_MJP;
            $row[] = $item->NAMA_DEPT;
            $row[] = $item->NAMA_JK;
            $row[] = $item->NAMA_MJP;
            $row[] = $item->MULAI_MJP;
            $row[] = $item->BEL_MULAI_MJP;
            $row[] = $item->AKHIR_MJP;
            $row[] = $item->BEL_AKHIR_MJP;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MJP . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_MJP . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jam_pelajaran->count_all(),
            "recordsFiltered" => $this->jam_pelajaran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jam_pelajaran);
        
        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Jenjang',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'DEPT_MJP',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->DEPT_MJP,
                    'label' => $data == NULL ? "" : $data->NAMA_DEPT,
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/departemen/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Jenis Kelamin',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type'  => 'autocomplete',                                  // WAJIB
                    'name'  => 'JK_MJP',                                    // WAJIB
                    'multiple'  => FALSE,                                       // IF NEEDED
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->JK_MJP,
                    'label' => $data == NULL ? "" : $data->NAMA_JK,
                    'data'  => NULL,                                            // WAJIB
                    'url'   => base_url('master_data/jk/auto_complete')                      // WAJIB
                )
            ),
//            array(
//                'label' => 'Nama',
//                'required' => TRUE,
//                'keterangan' => 'Wajib diisi',
//                'length' => 4,
//                'data' => array(
//                    'type' => 'text',
//                    'name' => 'NAMA_MJP',
//                    "placeholder" => " ",
//                    'value' => $data == NULL ? "" : $data->NAMA_MJP
//                )
//            ),
            array(
                'label' => 'Nama',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'dropdown',
                    'name' => 'URUTAN_MJP',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->URUTAN_MJP,
                    'data' => array(
                        array('id' => '1', 'text' => "JAM KE-1"),
                        array('id' => '2', 'text' => "JAM KE-2"),
                        array('id' => '3', 'text' => "JAM KE-3"),
                        array('id' => '4', 'text' => "JAM KE-4"),
                        array('id' => '5', 'text' => "JAM KE-5"),
                        array('id' => '6', 'text' => "JAM KE-6"),
                        array('id' => '7', 'text' => "JAM KE-7"),
                        array('id' => '8', 'text' => "JAM KE-8"),
                        array('id' => '9', 'text' => "JAM KE-9"),
                        array('id' => '10', 'text' => "JAM KE-10"),
                        array('id' => '11', 'text' => "JAM KE-11"),
                        array('id' => '12', 'text' => "JAM KE-12"),
                    )
                )
            ),
            array(
                'label' => 'Jam Mulai',                    
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'clockpicker',               
                    'name' => 'MULAI_MJP',                 
//                    $data == NULL ? '' : 'readonly' => 'true',
                    'value' => $data == NULL ? "" : $data->MULAI_MJP
                )
            ),
            array(
                'label' => 'Jumlah Bel Mulai',                                        
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',                                           
                    'name' => 'BEL_MULAI_MJP',                                        
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->BEL_MULAI_MJP
                )
            ),
            array(
                'label' => 'Jam Akhir',                    
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'clockpicker',               
                    'name' => 'AKHIR_MJP',                 
//                    $data == NULL ? '' : 'readonly' => 'true',
                    'value' => $data == NULL ? "" : $data->AKHIR_MJP
                )
            ),
            array(
                'label' => 'Jumlah Bel Akhir',                                        
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',                                           
                    'name' => 'BEL_AKHIR_MJP',                                        
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->BEL_AKHIR_MJP
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'DEPT_MJP' => $this->input->post('DEPT_MJP'),
            'JK_MJP' => $this->input->post('JK_MJP'),
            'NAMA_MJP' => 'JAM KE-'.$this->input->post('URUTAN_MJP'),
            'URUTAN_MJP' => $this->input->post('URUTAN_MJP'),
            'MULAI_MJP' => $this->input->post('MULAI_MJP'),
            'BEL_MULAI_MJP' => $this->input->post('BEL_MULAI_MJP'),
            'AKHIR_MJP' => $this->input->post('AKHIR_MJP'),
            'BEL_AKHIR_MJP' => $this->input->post('BEL_AKHIR_MJP'),
        );
        $insert = $this->jam_pelajaran->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['DEPT_MJP'] = $this->input->post('DEPT_MJP');
        $data['JK_MJP'] = $this->input->post('JK_MJP');
        $data['NAMA_MJP'] = 'JAM KE-'.$this->input->post('URUTAN_MJP');
        $data['URUTAN_MJP'] = $this->input->post('URUTAN_MJP');
        $data['MULAI_MJP'] = $this->input->post('MULAI_MJP');
        $data['BEL_MULAI_MJP'] = $this->input->post('BEL_MULAI_MJP');
        $data['AKHIR_MJP'] = $this->input->post('AKHIR_MJP');
        $data['BEL_AKHIR_MJP'] = $this->input->post('BEL_AKHIR_MJP');
        
        $affected_row = $this->jam_pelajaran->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->jam_pelajaran->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->jam_pelajaran->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
