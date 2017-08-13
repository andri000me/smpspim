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
class Jenis_absensi extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_MJK";

    public function __construct() {
        parent::__construct();
        $this->load->model('jenis_absensi_model', 'jenis_absensi');
        $this->auth->validation(array(11, 2));
    }

    public function index() {
        $this->generate->backend_view('master_data/jenis_absensi/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jenis_absensi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_MJK;
            $row[] = $item->NAMA_MJK;
            $row[] = $item->ID_KJP_A;
            $row[] = $item->NAMA_KJP_A;
            $row[] = $item->POIN_KJP_A;
            $row[] = $item->ID_KJP_T;
            $row[] = $item->NAMA_KJP_T;
            $row[] = $item->POIN_KJP_T;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MJK . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jenis_absensi->count_all(),
            "recordsFiltered" => $this->jenis_absensi->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jenis_absensi);
        
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
                    'name' => 'NAMA_MJK',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_MJK
                )
            ),
            array(
                'label' => 'Pelanggaran Alpha',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'PELANGGARAN_ALPHA_MJK',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 1,                                       
                    'value' => $data == NULL ? "" : $data->PELANGGARAN_ALPHA_MJK,
                    'label' => $data == NULL ? "" : $data->ID_KJP.' '.$data->NAMA_KJP.' [POIN: '.$data->POIN_KJP.']',
//                    'data'  => $this->kelompok->get_all(),                       
//                    'url'   => NULL
                    'data'  => NULL,                                            
                    'url'   => site_url('komdis/jenis_pelanggaran/auto_complete_pelanggaran')                    
                )
            ),
            array(
                'label' => 'Pelanggaran Terlambat',                                     
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'autocomplete',                                  
                    'name'  => 'PELANGGARAN_TERLAMBAT_MJK',                                    
                    'multiple'  => FALSE,                                       
                    'minimum'  => 1,                                       
                    'value' => $data == NULL ? "" : $data->PELANGGARAN_TERLAMBAT_MJK,
                    'label' => $data == NULL ? "" : $data->ID_KJP.' '.$data->NAMA_KJP.' [POIN: '.$data->POIN_KJP.']',
//                    'data'  => $this->kelompok->get_all(),                       
//                    'url'   => NULL
                    'data'  => NULL,                                            
                    'url'   => site_url('komdis/jenis_pelanggaran/auto_complete_pelanggaran')                    
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
        
        $data['NAMA_MJK'] = $this->input->post('NAMA_MJK');
        $data['PELANGGARAN_ALPHA_MJK'] = $this->input->post('PELANGGARAN_ALPHA_MJK');
        $data['PELANGGARAN_TERLAMBAT_MJK'] = $this->input->post('PELANGGARAN_TERLAMBAT_MJK');
        
        $affected_row = $this->jenis_absensi->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_absensi->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
