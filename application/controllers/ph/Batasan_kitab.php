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
class Batasan_kitab extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_BATASAN";

    public function __construct() {
        parent::__construct();
        $this->load->model('batasan_kitab_model', 'batasan_kitab');
        $this->auth->validation(5);
    }

    public function index() {
        $this->generate->backend_view('ph/batasan_kitab/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->batasan_kitab->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_KITAB;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_JK;
            $row[] = $item->AWAL_BATASAN;
            $row[] = $item->AKHIR_BATASAN;
            $row[] = $item->NILAI_MAKS_BATASAN;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_BATASAN . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_BATASAN . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->batasan_kitab->count_all(),
            "recordsFiltered" => $this->batasan_kitab->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->batasan_kitab);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'TA',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'TA_BATASAN',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? $this->session->userdata('ID_TA_ACTIVE') : $data->TA_BATASAN,
                    'label' => $data == NULL ? $this->session->userdata('NAMA_TA_ACTIVE') : $data->NAMA_TA,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/tahun_ajaran/auto_complete')                      
                )
            ),
            array(
                'label' => 'Kitab',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'KITAB_BATASAN',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->KITAB_BATASAN,
                    'label' => $data == NULL ? "" : $data->NAMA_KITAB,
                    'data'  => NULL,                                            
                    'url'   => base_url('ph/kitab/auto_complete')                      
                )
            ),
            array(
                'label' => 'Tingkat',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'TINGKAT_BATASAN',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->TINGKAT_BATASAN,
                    'label' => $data == NULL ? "" : $data->KETERANGAN_TINGK,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/tingkat/auto_complete')                      
                )
            ),
            array(
                'label' => 'Jenis Kelamin',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'JK_BATASAN',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->JK_BATASAN,
                    'label' => $data == NULL ? "" : $data->NAMA_JK,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/jk/auto_complete')                      
                )
            ),
            array(
                'label' => 'Batasan Awal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'AWAL_BATASAN',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->AWAL_BATASAN
                )
            ),
            array(
                'label' => 'Batasan Akhir',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'AKHIR_BATASAN',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->AKHIR_BATASAN
                )
            ),
            array(
                'label' => 'Nilai Maksimal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NILAI_MAKS_BATASAN',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NILAI_MAKS_BATASAN
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TA_BATASAN' => $this->input->post('TA_BATASAN'),
            'KITAB_BATASAN' => $this->input->post('KITAB_BATASAN'),
            'TINGKAT_BATASAN' => $this->input->post('TINGKAT_BATASAN'),
            'JK_BATASAN' => $this->input->post('JK_BATASAN'),
            'AWAL_BATASAN' => $this->input->post('AWAL_BATASAN'),
            'AKHIR_BATASAN' => $this->input->post('AKHIR_BATASAN'),
            'NILAI_MAKS_BATASAN' => $this->input->post('NILAI_MAKS_BATASAN'),
            'USER_BATASAN' => $this->session->userdata('ID_USER')
        );
        $insert = $this->batasan_kitab->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['TA_BATASAN'] = $this->input->post('TA_BATASAN');
        $data['KITAB_BATASAN'] = $this->input->post('KITAB_BATASAN');
        $data['TINGKAT_BATASAN'] = $this->input->post('TINGKAT_BATASAN');
        $data['JK_BATASAN'] = $this->input->post('JK_BATASAN');
        $data['AWAL_BATASAN'] = $this->input->post('AWAL_BATASAN');
        $data['AKHIR_BATASAN'] = $this->input->post('AKHIR_BATASAN');
        $data['NILAI_MAKS_BATASAN'] = $this->input->post('NILAI_MAKS_BATASAN');
        $data['USER_BATASAN'] = $this->session->userdata('ID_USER');
        
        $affected_row = $this->batasan_kitab->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->batasan_kitab->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->batasan_kitab->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
