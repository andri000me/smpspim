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
class Taqdir extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TAQDIR";

    public function __construct() {
        parent::__construct();
        $this->load->model('taqdir_model', 'taqdir');
        $this->auth->validation(8);
    }

    public function index() {
        $this->generate->backend_view('lpba/taqdir/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->taqdir->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_TAQDIR;
            $row[] = $item->NAMA_TAQDIR;
            $row[] = $item->NILAI_MIN_TAQDIR;
            $row[] = $item->NILAI_MAKS_TAQDIR;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TAQDIR . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <!--<li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_TAQDIR . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>-->
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->taqdir->count_all(),
            "recordsFiltered" => $this->taqdir->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->taqdir);
        
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
                    'name' => 'NAMA_TAQDIR',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_TAQDIR
                )
            ),
            array(
                'label' => 'Nilai Min',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NILAI_MIN_TAQDIR',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NILAI_MIN_TAQDIR
                )
            ),
            array(
                'label' => 'Nilai Maks',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NILAI_MAKS_TAQDIR',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NILAI_MAKS_TAQDIR
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'NAMA_TAQDIR' => $this->input->post('NAMA_TAQDIR'),
            'NILAI_MIN_TAQDIR' => $this->input->post('NILAI_MIN_TAQDIR'),
            'NAMA_TAQDIR' => $this->input->post('NAMA_TAQDIR'),
            'NILAI_MAKS_TAQDIR' => $this->input->post('NILAI_MAKS_TAQDIR'),
        );
        $insert = $this->taqdir->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_TAQDIR'] = $this->input->post('NAMA_TAQDIR');
        $data['NILAI_MIN_TAQDIR'] = $this->input->post('NILAI_MIN_TAQDIR');
        $data['NILAI_MAKS_TAQDIR'] = $this->input->post('NILAI_MAKS_TAQDIR');
        
        $affected_row = $this->taqdir->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->taqdir->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->taqdir->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
