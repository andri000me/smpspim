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
class Pegawai extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_PEG";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'pegawai_model' => 'pegawai',
            'user_model' => 'user',
            'hakakses_user_model' => 'hakakses',
        ));
        $this->auth->validation(array(11, 6, 2, 7, 5, 12, 13));
    }

    public function index() {
        $this->generate->backend_view('master_data/pegawai/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->pegawai->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIP_PEG;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->JK_PEG;
            $row[] = $item->ALAMAT_PEG;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = ($item->AKTIF_PEG == 1) ? '<i style="cursor: pointer" onclick="change_status(0, \'' . $item->ID_PEG . '\');" class="fa fa-check"></i>' : '<i style="cursor: pointer" onclick="change_status(1, \'' . $item->ID_PEG . '\');" class="fa fa-close"></i>';

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PEG . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_PEG . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pegawai->count_all(),
            "recordsFiltered" => $this->pegawai->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form($ID_PEG = NULL, $view = FALSE) {
        if ($ID_PEG !== NULL)
            $data['data'] = $this->pegawai->get_by_id($ID_PEG);
        else
            $data['data'] = NULL;

        if ($view)
            $data['mode_view'] = TRUE;

        $this->generate->backend_view('master_data/pegawai/form', $data);
    }

    private function selection_form($data) {
        $data['TANGGAL_LAHIR_PEG'] = $this->date_format->to_store_db($data['TANGGAL_LAHIR_PEG']);
        unset($data['validasi']);
        foreach ($data as $key => $value) {
            if ($value == '')
                unset($data[$key]);
        }

        return $data;
    }

    private function save_photobooth($ID_PEG, $data_image) {
        list($type, $data_image) = explode(';', $data_image);
        list(, $data_image) = explode(',', $data_image);
        $data_image = base64_decode($data_image);
        $name_file = 'files/pegawai/' . $ID_PEG . '.png';

        file_put_contents($name_file, $data_image);

        $data['FOTO_PEG'] = $ID_PEG . '.png';
        $where['ID_PEG'] = $ID_PEG;

        return $this->pegawai->update($where, $data);
    }

    public function selection_form_photo($post) {
        unset($post['from_upload']);
        unset($post['UPLOAD_FOTO_PEG']);

        return $post;
    }

    public function save_photo() {
        $ID_PEG = $this->input->post('ID_PEG');
        $file_element_name = 'UPLOAD_FOTO_PEG';
        $config['upload_path'] = './files/pegawai/';
        $config['allowed_types'] = 'png';
        $config['max_size'] = '2000';
        $config['max_width'] = '2400';
        $config['max_height'] = '2400';
        $config['overwrite'] = TRUE;
        $config['file_name'] = $ID_PEG;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_element_name)) {
            $aa = $this->upload->data();

            $data['FOTO_PEG'] = $ID_PEG . '.png';
            $where['ID_PEG'] = $ID_PEG;
            $this->pegawai->update($where, $data);

            $status = TRUE;
            $msg = "berhasil diupload";
            @unlink($_FILES[$file_element_name]);
        } else {
            $status = FALSE;
            $msg = 'gagal diupload (ERROR: ' . $this->upload->display_errors('', '') . ')';
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }
    
    private function add_user($NIP_PEG, $insert) {
        $data_user = array(
            'NAME_USER' => $NIP_PEG,
            'PASSWORD_USER' => $this->crypt->encryptDefaultPassword(),
            'PEGAWAI_USER' => $insert
        );
        
        $insert = $this->user->save($data_user);
        
        $this->set_hakakses_guru($insert);
    }
    
    private function set_hakakses_guru($insert) {
        $data = array(
            'USER_HU' => $insert,
            'HAKAKSES_HU' => 9
        );
        
        $this->hakakses->save($data);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $msg = '';
        $data = $this->selection_form($this->input->post());
        $data = $this->selection_form_photo($data);

        if (isset($data['TAKE_FOTO_PEG'])) {
            $data_image = $data['TAKE_FOTO_PEG'];
            unset($data['TAKE_FOTO_PEG']);
        }
        
        $insert = $this->pegawai->save($data);
        
        if($insert > 0) $this->add_user($data['NIP_PEG'],$insert);
        
        if ($insert > 0 and isset($data_image)) {
            $this->save_photobooth($insert, $data_image);
        }
        
        $this->generate->output_JSON(array("status" => $insert, "msg" => $msg));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->selection_form($this->input->post());
        $data = $this->selection_form_photo($data);

        if (isset($data['TAKE_FOTO_PEG'])) {
            $affected_row_image = $this->save_photobooth($data['ID_PEG'], $data['TAKE_FOTO_PEG']);
            unset($data['TAKE_FOTO_PEG']);
        }

        $where = array(
            'ID_PEG' => $data['ID_PEG']
        );
        
        unset($data['ID_PEG']);
        
        $affected_row = $this->pegawai->update($where, $data);
        if($affected_row) $this->user->update(array('PEGAWAI_USER' => $where['ID_PEG']), array('NAME_USER' => $data['NIP_PEG']));

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->pegawai->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function change_status() {
        $this->generate->set_header_JSON();
        
        $where["ID_PEG"] = $this->input->post("ID_PEG");
        $data["AKTIF_PEG"] = $this->input->post("AKTIF_PEG");
        
        $affected_row = $this->pegawai->update($where, $data);
        
        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->pegawai->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
    
    public function get_all_select2() {
        $this->generate->set_header_JSON();
        
        $data = $this->pegawai->get_all_ac();
        
        $this->generate->output_JSON($data);
    }
    
    public function auto_complete_guru() {
        $this->generate->set_header_JSON();
        
        $data = $this->pegawai->get_all_ac_guru($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function check_data() {
        $this->generate->set_header_JSON();

        $data['name'] = $this->input->post('name');
        $data['value'] = $this->input->post('value');

        if ($this->pegawai->count_all($data) == 0)
            $this->generate->output_JSON(array("status" => TRUE));
        else
            $this->generate->output_JSON(array("status" => FALSE));
    }

}
