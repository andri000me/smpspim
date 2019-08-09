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
class User extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_USER";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'user_model' => 'user',
            'hakakses_model' => 'hakakses',
            'hakakses_user_model' => 'hakakses_user',
            'tagihan_model' => 'tagihan',
            'departemen_model' => 'dept',
            'jk_model' => 'jk',
        ));
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/user/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->user->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $data_level = $this->db_handler->get_row('md_hakakses_user', [
                'where' => [
                    'USER_HU' => $item->ID_USER
                ]
                    ], 'GROUP_CONCAT(NAME_HAKAKSES) AS LEVEL', [
                ['md_hakakses', 'HAKAKSES_HU=ID_HAKAKSES']
            ]);

            $no++;
            $row = array();
            $row[] = $item->ID_USER;
            $row[] = $item->NAME_USER;
            $row[] = $item->NAMA_PEG;
            $row[] = $data_level->LEVEL;
            $row[] = $item->STATUS_USER;
            $row[] = $item->LASTLOGIN_USER;

            if ($this->hakakses_user->cek_hakakses_user(4, $item->ID_USER))
                $aksi_keu = '<li><a href="javascript:void()" title="Atur Hakakses Keuangan" onclick="atur_keuangan_' . $id_datatables . '(\'' . $item->ID_USER . '\')"><i class="fa fa-money"></i>&nbsp;&nbsp;&nbsp;Atur Hakakses Keuangan</a></li>';
            else
                $aksi_keu = '';

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah Password" onclick="update_password_' . $id_datatables . '(\'' . $item->ID_USER . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Password</a></li>
                        <li><a href="javascript:void()" title="Ubah Status" onclick="update_status_' . $id_datatables . '(\'' . $item->ID_USER . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Status</a></li>
                        <li><a href="javascript:void()" title="Ubah Hakakses" onclick="update_hakakses_' . $id_datatables . '(\'' . $item->ID_USER . '\')"><i class="fa fa-unlock-alt"></i>&nbsp;&nbsp;&nbsp;Ubah Hakakses</a></li>
                        ' . $aksi_keu . '
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user->count_all(),
            "recordsFiltered" => $this->user->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form_password() {
        $data = $this->generate->set_header_form_JSON($this->user);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Username / NIP',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAME_USER',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAME_USER
                )
            ),
            array(
                'label' => 'Nama Pegawai',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PEG',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAMA_PEG
                )
            ),
            array(
                'label' => 'Password Baru',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'password',
                    'name' => 'PASSW0RD_BARU',
                    "placeholder" => " "
                )
            ),
            array(
                'label' => 'Ulangi Password Baru',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'password',
                    'name' => 'RE_PASSW0RD_BARU',
                    "placeholder" => " "
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function request_form_status() {
        $data = $this->generate->set_header_form_JSON($this->user);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Username / NIP',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAME_USER',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAME_USER
                )
            ),
            array(
                'label' => 'Nama Pegawai',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PEG',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAMA_PEG
                )
            ),
            array(
                'label' => 'Status',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'dropdown',
                    'name' => 'STATUS_USER',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->STATUS_USER,
                    'data' => array(
                        array('id' => 'ACTIVE', 'text' => "ACTIVE"),
                        array('id' => 'BLOCK', 'text' => "BLOCK"),
                    )
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function request_form_hakakses() {
        $data = $this->generate->set_header_form_JSON($this->user);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Username / NIP',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAME_USER',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAME_USER
                )
            ),
            array(
                'label' => 'Nama Pegawai',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PEG',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAMA_PEG
                )
            ),
            array(
                'label' => 'Hak Akses', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'checkbox_simple', // WAJIB, ex checkbox, radio
                    'name' => 'HAKAKSES_HU[]', // WAJIB
                    'data' => $this->hakakses->get_all_with_administrator()                       // WAJIB
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function request_form_keuangan() {
        $data = $this->generate->set_header_form_JSON($this->user);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_jenjang = $this->dept->get_all_checkbox();
        $data_jk = $this->jk->get_all();
        $data_jenjang_jk = array();
        foreach ($data_jenjang as $jenjang) {
            foreach ($data_jk as $jk) {
                $data_jenjang_jk[] = array(
                    'value' => $jenjang->value . '#' . $jk->id,
                    'label' => $jenjang->label . ' - ' . $jk->text
                );
            }
        }

        $data_html = array(
            array(
                'label' => 'Username / NIP',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAME_USER',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAME_USER
                )
            ),
            array(
                'label' => 'Nama Pegawai',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 6,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_PEG',
                    "placeholder" => " ",
                    "readonly" => TRUE,
                    'value' => $data == NULL ? "" : $data->NAMA_PEG
                )
            ),
            array(
                'label' => 'Tagihan', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'dropdown', // WAJIB, ex checkbox, radio
                    'name' => 'TAGIHAN', // WAJIB
                    'id' => 'tagihan', // WAJIB
                    'value_blank' => '-- Pilih Tagihan --',
                    'data' => $this->tagihan->get_all_ta_active_dropdown()                       // WAJIB
                )
            ),
            array(
                'label' => 'Jenjang', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'checkbox_simple', // WAJIB, ex checkbox, radio
                    'name' => 'JENJANG[]', // WAJIB
                    'data' => $data_jenjang_jk                       // WAJIB
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_update_password() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->input->post();

        if ($data['PASSW0RD_BARU'] != $data['RE_PASSW0RD_BARU']) {
            $status = FALSE;
            $msg = 'Password tidak sama';
        } elseif (strlen($data['PASSW0RD_BARU']) < 5) {
            $status = FALSE;
            $msg = 'Password harus minimal 5 karakter';
        } else {
            $data_user = array('PASSWORD_USER' => $this->crypt->encryptPassword($data['PASSW0RD_BARU']));
            $where = array('NAME_USER' => $data['NAME_USER']);
            $status = $this->user->update($where, $data_user);

            if ($status) {
                $status = TRUE;
                $msg = 'Password berhasil dirubah';
            } else {
                $status = FALSE;
                $msg = 'Password gagal dirubah. Cobalah dengan password lain.';
            }
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function ajax_update_status() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->input->post();

        $data_user = array(
            'STATUS_USER' => $data['STATUS_USER'],
            'SISA_PERCOBAAN_USER' => 5,
        );
        $where = array('NAME_USER' => $data['NAME_USER']);
        $status = $this->user->update($where, $data_user);

        if ($status) {
            $status = TRUE;
            $msg = 'Status berhasil dirubah';
        } else {
            $status = FALSE;
            $msg = 'Status gagal dirubah.';
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function ajax_update_hakakses() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $ID_USER = $this->input->post('ID_USER');
        $HAKAKSES_HU = $this->input->post('HAKAKSES_HU');

        $this->hakakses_user->delete_by_id($ID_USER);
        foreach ($HAKAKSES_HU as $value) {
            $this->hakakses_user->save(array(
                'USER_HU' => $ID_USER,
                'HAKAKSES_HU' => $value,
            ));
        }

        $status = TRUE;
        $msg = 'Hakakses berhasil diatur ulang';

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function ajax_update_keuangan() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $ID_USER = $this->input->post('ID_USER');
        $TAGIHAN = $this->input->post('TAGIHAN');
        $JENJANG_JK = $this->input->post('JENJANG');

        $this->tagihan->delete_user($ID_USER);
        if ($JENJANG_JK != null) {
            foreach ($JENJANG_JK as $value) {
                $JENJANG_JK_EXP = explode('#', $value);

                $this->tagihan->add_user(array(
                    'USER_MUK' => $ID_USER,
                    'TAGIHAN_MUK' => $TAGIHAN,
                    'DEPT_MUK' => $JENJANG_JK_EXP[0],
                    'JK_MUK' => $JENJANG_JK_EXP[1],
                ));
            }
        }

        $status = TRUE;
        $msg = 'Hakakses berhasil diatur ulang';

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->user->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->user->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

}
