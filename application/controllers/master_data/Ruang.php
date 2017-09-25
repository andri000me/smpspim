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
class Ruang extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "KODE_RUANG";

    public function __construct() {
        parent::__construct();
        $this->load->model('ruang_model', 'ruang');
        $this->auth->validation(array(11, 2, 6));
    }

    public function index() {
        $this->generate->backend_view('master_data/ruang/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->ruang->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->KODE_RUANG;
            $row[] = $item->NAMA_RUANG;
            $row[] = $item->KAPASITAS_RUANG;

            if ($item->UJIAN_RUANG) $row[] = '<i class="fa fa-check"></i>';
            else $row[] = '<i class="fa fa-close" style="cursor: pointer;" onclick="return change_active(1, \''.$item->KODE_RUANG.'\');"></i>';
            
            $row[] = $item->KAPASITAS_UJIAN_RUANG;
            
            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->KODE_RUANG . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->KODE_RUANG . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->ruang->count_all(),
            "recordsFiltered" => $this->ruang->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->ruang);
        
        $input_id = TRUE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Nama',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_RUANG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_RUANG
                )
            ),
            array(
                'label' => 'Ujian',                                     // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type'  => 'dropdown',                                      // WAJIB
                    'name'  => 'UJIAN_RUANG',                                    // WAJIB
                    'value' => $data == NULL ? "1" : $data->UJIAN_RUANG,
                    'data'  => array(
                        array('id' => '1', 'text' => 'YA'),
                        array('id' => '0', 'text' => 'TIDAK'),
                    )
                )
            ),
            array(
                'label' => 'Kapasitas',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KAPASITAS_RUANG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "40" : $data->KAPASITAS_RUANG
                )
            ),
            array(
                'label' => 'Kapasitas Ujian',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KAPASITAS_UJIAN_RUANG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "40" : $data->KAPASITAS_UJIAN_RUANG
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        
        $this->ruang->save($data);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_RUANG'] = $this->input->post('NAMA_RUANG');
        $data['KAPASITAS_RUANG'] = $this->input->post('KAPASITAS_RUANG');
        $data['KAPASITAS_UJIAN_RUANG'] = $this->input->post('KAPASITAS_UJIAN_RUANG');
        $data['UJIAN_RUANG'] = $this->input->post('UJIAN_RUANG');
        
        $affected_row = $this->ruang->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->ruang->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->ruang->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
