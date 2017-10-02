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
class Kelas extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_KELAS";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kelas_model' => 'kelas',
            'hakakses_user_model' => 'hakakses',
        ));
        $this->auth->validation(array(2, 13, 5, 4, 7, 8));
    }

    public function index() {
        $this->generate->backend_view('akademik/kelas/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kelas->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
//            $row[] = $item->NAMA_TA;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->KODE_RUANG;
            $row[] = $item->NAMA_RUANG;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->KAPASITAS_RUANG;
            $row[] = $item->JUMLAH_SISWA_KELAS;
            $row[] = ($item->AKTIF_KELAS == 1) ? 'YA' : 'TIDAK';

            $row[] = ($item->ID_TA == $this->session->userdata('ID_TA_ACTIVE')) ? '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_KELAS . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_KELAS . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>' : '-';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kelas->count_all(),
            "recordsFiltered" => $this->kelas->count_filtered(),
            "data" => $data,
        );
        
        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kelas);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'TA',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'TA_KELAS',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->TA_KELAS,
                    'label' => $data == NULL ? "" : $data->NAMA_TA,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/tahun_ajaran/auto_complete')                      
                )
            ),
            array(
                'label' => 'Tingkat',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'TINGKAT_KELAS',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->TINGKAT_KELAS,
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
                    'name'  => 'JK_KELAS',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->JK_KELAS,
                    'label' => $data == NULL ? "" : $data->NAMA_JK,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/jk/auto_complete')                      
                )
            ),
            array(
                'label' => 'Ruang',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'RUANG_KELAS',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->RUANG_KELAS,
                    'label' => $data == NULL ? "" : $data->NAMA_RUANG,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/ruang/auto_complete')                      
                )
            ),
            array(
                'label' => 'Nama Kelas',                                        
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'text',                                           
                    'name' => 'NAMA_KELAS',                   
                    'value' => $data == NULL ? "" : $data->NAMA_KELAS
                )
            ),
            array(
                'label' => 'Wali Kelas',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'WALI_KELAS',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 0,                                       
                    'value' => $data == NULL ? "" : $data->WALI_KELAS,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
                    'data'  => NULL,                                            
                    'url'   => base_url('master_data/pegawai/auto_complete')                      
                )
            ),
            array(
                'hidden' => TRUE,
                'data' => array(
                    'name' => 'TEMP_WALI_KELAS',
                    'value' => $data == NULL ? "" : $data->WALI_KELAS
                )
            ),
            array(
                'label' => 'Aktif',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'radio',
                    'name'  => 'AKTIF_KELAS',                                    
                    'inline'=> true,                                           
                    'value' => $data == NULL ? 1 : $data->AKTIF_KELAS,
                    'data'  => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )   
                )
            ),
            array(
                'label' => 'Keterangan',                                        
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',                                           
                    'name' => 'KETERANGAN_KELAS',                   
                    'value' => $data == NULL ? "" : $data->KETERANGAN_KELAS
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }
    
    public function unset_hakakses($NIP_PEG) {
        $data = array(
            'USER_HU' => $NIP_PEG,
            'HAKAKSES_HU' => 10
        );
        
        $this->hakakses->delete_by_where($data);
    }
     
    public function set_hakakses($NIP_PEG) {
        $data = array(
            'USER_HU' => $NIP_PEG,
            'HAKAKSES_HU' => 10
        );
        
        $this->hakakses->save($data);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        unset($data['TEMP_WALI_KELAS']);
        $data['USER_KELAS'] = $this->session->userdata('ID_USER');
        $data['CREATED_KELAS'] = date("Y-m-d H:i:s");
        $insert = $this->kelas->save($data);
        
        if ($insert) $this->set_hakakses($data['WALI_KELAS']);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        
        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        $wali_kelas_lama = $data['TEMP_WALI_KELAS'];
        unset($data['TEMP_WALI_KELAS']);
        $data['USER_KELAS'] = $this->session->userdata('ID_USER');
        
        $where = array('ID_KELAS' => $data['ID_KELAS']);
        
        $affected_row = $this->kelas->update($where, $data);
        
        if($affected_row) {
            $this->unset_hakakses($wali_kelas_lama);
            $this->set_hakakses($data['WALI_KELAS']);
        }

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->kelas->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->kelas->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function get_all() {
        $this->generate->set_header_JSON();
        
        $data = $this->kelas->get_all();
        
        $this->generate->output_JSON($data);
    }

}
