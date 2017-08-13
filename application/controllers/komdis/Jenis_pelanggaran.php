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
class Jenis_pelanggaran extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_KJP";

    public function __construct() {
        parent::__construct();
        $this->load->model('jenis_pelanggaran_model', 'jenis_pelanggaran');
        $this->auth->validation(array(7, 11));
    }

    public function index() {
        $this->generate->backend_view('komdis/jenis_pelanggaran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jenis_pelanggaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NO_KJP;
            $row[] = $item->NAMA_KJP;
            $row[] = $item->POIN_KJP;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_KJP . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_KJP . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jenis_pelanggaran->count_all(),
            "recordsFiltered" => $this->jenis_pelanggaran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jenis_pelanggaran);
        
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Induk',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'INDUK_KJP',
                    'id' => 'INDUK_KJP',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->INDUK_KJP
                )
            ),
            array(
                'label' => 'Anak',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'ANAK_KJP',
                    'id' => 'ANAK_KJP',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->ANAK_KJP
                )
            ),
            array(
                'label' => 'Jenis Pelanggaran',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_KJP',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_KJP
                )
            ),
            array(
                'label' => 'Poin',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'POIN_KJP',
                    'id' => 'POIN_KJP',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->POIN_KJP
                )
            ),
        );
        
        if($data != NULL) {
            $data_html[0]['data']['readonly'] = TRUE;
            $data_html[1]['data']['readonly'] = TRUE;
        }
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');
        
        $data = array(
            'TA_KJP' => $this->session->userdata("ID_TA_ACTIVE"),
            'INDUK_KJP' => $this->input->post('INDUK_KJP'),
            'ANAK_KJP' => ($this->input->post('ANAK_KJP') == "") ? NULL : $this->input->post('ANAK_KJP'),
            'NAMA_KJP' => $this->input->post('NAMA_KJP'),
            'POIN_KJP' => $this->input->post('POIN_KJP'),
            'USER_KJP' => $this->session->userdata("ID_USER"),
        );
        $insert = $this->jenis_pelanggaran->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');
        
        $data = array(
            'NAMA_KJP' => $this->input->post('NAMA_KJP'),
            'POIN_KJP' => $this->input->post('POIN_KJP'),
        );
        
        $where = array(
            'ID_KJP' => $this->input->post('ID_KJP')
        );
        
        $affected_row = $this->jenis_pelanggaran->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->jenis_pelanggaran->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function cek_no() {
        $this->generate->set_header_JSON();
        
        $INDUK_KJP = $this->input->post('INDUK_KJP');
        $ANAK_KJP = ($this->input->post('ANAK_KJP') == "") ? NULL : $this->input->post('ANAK_KJP');
        
        $status = $this->jenis_pelanggaran->cek_no($INDUK_KJP, $ANAK_KJP);
        
        $this->generate->output_JSON(array('status' => $status));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_pelanggaran->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function auto_complete_pelanggaran() {
        $this->generate->set_header_JSON();
        
        $data = $this->jenis_pelanggaran->get_all_ac_pelanggaran($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
