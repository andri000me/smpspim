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
class Pondok_siswa extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_MPS";

    public function __construct() {
        parent::__construct();
        $this->load->model('pondok_siswa_model', 'pondok_siswa');
        $this->auth->validation(array(11, 3, 2, 7));
    }

    public function index() {
        $this->generate->backend_view('master_data/pondok_siswa/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->pondok_siswa->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_PONDOK_MPS;
            $row[] = $item->PENGASUH_MPS;
            $row[] = $item->ALAMAT_MPS;
            $row[] = $item->JARAK_MPS;
            $row[] = $item->TELP_MPS;
            $row[] = $item->EMAIL_MPS;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MPS . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_MPS . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pondok_siswa->count_all(),
            "recordsFiltered" => $this->pondok_siswa->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->pondok_siswa);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Nama Pondok',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PONDOK_MPS',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_PONDOK_MPS
                )
            ),
            array(
                'label' => 'Pengasuh',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type' => 'text',
                    'name' => 'PENGASUH_MPS',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->PENGASUH_MPS
                )
            ),
            array(
                'label' => 'Alamat',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'ALAMAT_MPS',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->ALAMAT_MPS
                )
            ),
            array(
                'label' => 'Jarak',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'JARAK_MPS',
                    "placeholder" => "Dalam meter",
                    'value' => $data == NULL ? "" : $data->JARAK_MPS
                )
            ),
            array(
                'label' => 'Telp.',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'text',
                    'name' => 'TELP_MPS',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->TELP_MPS
                )
            ),
            array(
                'label' => 'Email',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'text',
                    'name' => 'EMAIL_MPS',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->EMAIL_MPS
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
        $data['USER_MPS'] = $this->session->userdata('ID_USER');
        
        $insert = $this->pondok_siswa->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());
        
        $where = $cek['where'];
        
        if (isset($cek['data'])) $data = $cek['data'];
        else $data = array();
        
        $data['NAMA_PONDOK_MPS'] = $this->input->post('NAMA_PONDOK_MPS');
        
        $affected_row = $this->pondok_siswa->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->pondok_siswa->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->pondok_siswa->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
