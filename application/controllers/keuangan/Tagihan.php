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
class Tagihan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TAG";

    public function __construct() {
        parent::__construct();
        $this->load->model(array('tagihan_model' => 'tagihan', 'tahun_ajaran_model' => 'tahun_ajaran'));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/tagihan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->tagihan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_TAG;
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_TAG;
            
            if ($item->PSB_TAG) $row[] = '<i class="fa fa-check" style="cursor: pointer;" onclick="return change_active(0, \''.$item->ID_TAG.'\');"></i>';
            else $row[] = '<i class="fa fa-close" style="cursor: pointer;" onclick="return change_active(1, \''.$item->ID_TAG.'\');"></i>';

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TAG . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_TAG . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tagihan->count_all(),
            "recordsFiltered" => $this->tagihan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->tagihan);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'TA',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type'  => 'dropdown',                                      // WAJIB
                    'name'  => 'TA_TAG',                                    // WAJIB
                    'value' => $data == NULL ? "" : $data->TA_TAG,
                    'value_blank'  => '-- Pilih TA --',
                    'data'  => $this->tahun_ajaran->get_all()                       // WAJIB
                )
            ),
            array(
                'label' => 'Nama Tagihan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_TAG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_TAG
                )
            ),
            array(
                'label' => 'PSB',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type'  => 'radio',                                      // WAJIB, ex checkbox, radio
                    'name'  => 'PSB_TAG',                                    // WAJIB
                    'inline'=> true,                                           // IF NEEDED
                    'value' => $data == NULL ? '0' : $data->PSB_TAG,
                    'data'  => array(
                        array('value' => '0', 'label' => 'TIDAK'),
                        array('value' => '1', 'label' => 'YA'),
                    )
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TA_TAG' => $this->input->post('TA_TAG'),
            'NAMA_TAG' => $this->input->post('NAMA_TAG'),
            'PSB_TAG' => $this->input->post('PSB_TAG'),
        );
        $insert = $this->tagihan->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['TA_TAG'] = $this->input->post('TA_TAG');
        $data['NAMA_TAG'] = $this->input->post('NAMA_TAG');
        $data['PSB_TAG'] = $this->input->post('PSB_TAG');
        
        $affected_row = $this->tagihan->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->tagihan->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->tagihan->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
