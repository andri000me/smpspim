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
class Jenis_kelompok extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TJK";

    public function __construct() {
        parent::__construct();
        $this->load->model('jenis_kelompok_model', 'jenis_kelompok');
        $this->auth->validation(13);
    }

    public function index() {
//        $data['COUNT'] = $this->jenis_kelompok->count_kelompok_ta_active();
        $data['COUNT'] = 1;
        $this->generate->backend_view('tuk/jenis_kelompok/index', $data);
    }
    
    public function prepare() {
//        if($this->jenis_kelompok->count_kelompok_ta_active() == 0) {
//            $data = array(
//                'TA_TJK' => $this->session->userdata('ID_TA_ACTIVE'),
//                'JENIS_TJK' => 'PEMASUKAN',
//                'NAMA_TJK' => 'TAGIHAN',
//            );
//            $this->jenis_kelompok->save($data);
//        }
        
        redirect('tuk/jenis_kelompok');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jenis_kelompok->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->JENIS_TJK;
            $row[] = $item->NAMA_TJK;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TJK . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jenis_kelompok->count_all(),
            "recordsFiltered" => $this->jenis_kelompok->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jenis_kelompok);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Jenis',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'dropdown',
                    'name' => 'JENIS_TJK',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->JENIS_TJK,
                    'data'  => array(
                        array('id' => 'PEMASUKAN', 'text' => "PEMASUKAN"),
                        array('id' => 'PENGELUARAN', 'text' => "PENGELUARAN"),
                    )
                )
            ),
            array(
                'label' => 'Nama Kelompok',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_TJK',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_TJK,
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TA_TJK' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TJK' => $this->input->post('JENIS_TJK'),
            'NAMA_TJK' => $this->input->post('NAMA_TJK'),
        );
        $insert = $this->jenis_kelompok->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['JENIS_TJK'] = $this->input->post('JENIS_TJK');
        $data['NAMA_TJK'] = $this->input->post('NAMA_TJK');
        
        $affected_row = $this->jenis_kelompok->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_kelompok->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function auto_complete_pemasukan() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_kelompok->get_all_ac($this->input->post('q'), 'PEMASUKAN');
        
        $this->generate->output_JSON($data);
    }

    public function auto_complete_pengeluaran() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_kelompok->get_all_ac($this->input->post('q'), 'PENGELUARAN');
        
        $this->generate->output_JSON($data);
    }

}
