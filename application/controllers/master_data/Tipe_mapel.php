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
class Tipe_mapel extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_MTM";

    public function __construct() {
        parent::__construct();
        $this->load->model('tipe_mapel_model', 'tipe_mapel');
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/tipe_mapel/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->tipe_mapel->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_MTM;
            $row[] = $item->NAMA_MTM;
            $row[] = $item->LULUS_MTM;
            $row[] = $item->HER_MTM;
            $row[] = $item->TAMAT_MTM;
            $row[] = $item->TIDAK_TAMAT_MTM;
            $row[] = $item->NAIK_MTM;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MTM . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tipe_mapel->count_all(),
            "recordsFiltered" => $this->tipe_mapel->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->tipe_mapel);
        
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
                    'name' => 'NAMA_MTM',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_MTM
                )
            ),
            array(
                'label' => 'Nilai Minimal Lulus',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'LULUS_MTM',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->LULUS_MTM
                )
            ),
            array(
                'label' => 'Nilai Minimal HER',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'HER_MTM',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->HER_MTM
                )
            ),
            array(
                'label' => 'Nilai Minimal Tamat',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'TAMAT_MTM',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->TAMAT_MTM
                )
            ),
            array(
                'label' => 'Nilai Minimal Naik Kelas',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAIK_MTM',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAIK_MTM
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'NAMA_MTM' => $this->input->post('NAMA_MTM'),
            'LULUS_MTM' => $this->input->post('LULUS_MTM'),
            'HER_MTM' => $this->input->post('HER_MTM'),
            'TAMAT_MTM' => $this->input->post('TAMAT_MTM'),
            'NAIK_MTM' => $this->input->post('NAIK_MTM'),
        );
//        $insert = $this->tipe_mapel->save($data);
        $insert = 0;

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_MTM'] = $this->input->post('NAMA_MTM');
        $data['LULUS_MTM'] = $this->input->post('LULUS_MTM');
        $data['HER_MTM'] = $this->input->post('HER_MTM');
        $data['TAMAT_MTM'] = $this->input->post('TAMAT_MTM');
        $data['NAIK_MTM'] = $this->input->post('NAIK_MTM');
        
        $affected_row = $this->tipe_mapel->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
//        $affected_row = $this->tipe_mapel->delete_by_id($id);
        $affected_row = 0;

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->tipe_mapel->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
