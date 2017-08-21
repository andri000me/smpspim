<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model', 'user');
        $this->auth->validation(1);
    }

    public function index($ID_USER = 0) {
        $data = array();
        if ($ID_USER != 0) {
            $data['EDIT_USER'] = TRUE;
            $data['ID_USER'] = $ID_USER;
        }
        $this->load->view('layout/admin/header');
        $this->load->view('layout/admin/main_header');
        $this->load->view('layout/admin/sidebar');
        $this->load->view('user/index', $data);
        $this->load->view('layout/admin/footer');
    }

    public function ajax_list() {
        if ($this->input->is_ajax_request()) {
            $list = $this->user->get_datatables();
            $hakakses = $this->user->get_hakakses();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $user) {
                $no++;
                $row = array();
                $row[] = $user->NAME_USER;
                $row[] = $user->FULLNAME_USER;

                $option_status = '<select class="form-control" id="STATUS_USER' . $user->ID_USER . '" onchange="change_status(\'' . $user->ID_USER . '\')">';
                $option_status .= '<option value="ACTIVE" ';
                if ($user->STATUS_USER == "ACTIVE")
                    $option_status .= 'selected';
                $option_status .= '>ACTIVE</option>';
                $option_status .= '<option value="PENDING" ';
                if ($user->STATUS_USER == "PENDING")
                    $option_status .= 'selected';
                $option_status .= '>PENDING</option>';
                $option_status .= '<option value="BLOCK" ';
                if ($user->STATUS_USER == "BLOCK")
                    $option_status .= 'selected';
                $option_status .= '>BLOCK</option>';
                $option_status .= '</select>';

                $row[] = $option_status;

                $option_hakakses = '<select class="form-control" id="HAKAKSES' . $user->ID_USER . '" onchange="change_hakakses(\'' . $user->ID_USER . '\')">';
                foreach ($hakakses as $value) {
                    $option_hakakses .= '<option value="' . $value->ID_HAKAKSES . '" ';
                    if ($value->ID_HAKAKSES == $user->HAKAKSES_USER)
                        $option_hakakses .= 'selected';
                    $option_hakakses .= '>' . $value->NAME_HAKAKSES . '</option>';
                }
                $option_hakakses .= '</select>';

                $row[] = $option_hakakses;
                $row[] = $user->NAMA_KECAMATAN . "<br>" . $user->NAMA_KABUPATEN . "<br>" . $user->NAMA_PROVINSI;
                $row[] = $user->LASTLOGIN_USER;

                //add html for action
                $row[] = '<a class="btn btn-sm btn-primary btn-flat" href="javascript:void()" title="Edit" onclick="edit_data(' . "'" . $user->ID_USER . "'" . ')"><i class="glyphicon glyphicon-pencil"></i></a>
                      <a class="btn btn-sm btn-danger btn-flat" href="javascript:void()" title="Hapus" onclick="delete_data(' . "'" . $user->ID_USER . "'" . ')"><i class="glyphicon glyphicon-trash"></i></a>
                      <a class="btn btn-sm btn-info btn-flat" href="javascript:void()" title="Lihat" onclick="view_data(' . "'" . $user->ID_USER . "'" . ')"><i class="glyphicon glyphicon-eye-open"></i></a>';

                $data[] = $row;
            }

            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->user->count_all(),
                "recordsFiltered" => $this->user->count_filtered(),
                "data" => $data,
            );
            //output to json format
            echo json_encode($output);
        }
    }

    public function ajax_edit($id) {
        if ($this->input->is_ajax_request()) {
            $data = $this->user->get_by_id($id);
            $data->CREATED_USER = ($data->CREATED_USER == '0000-00-00') ? '' : $data->CREATED_USER; // if 0000-00-00 set tu empty for datepicker compatibility
            echo json_encode($data);
        }
    }

    public function ajax_add() {
        if ($this->input->is_ajax_request()) {
            $this->_validate();
            $data = array(
                'NAME_USER' => $this->input->post('NAME_USER'),
                'PASSWORD_USER' => $this->crypt->encryptPassword($this->input->post('PASSWORD_USER')),
                'FULLNAME_USER' => $this->input->post('FULLNAME_USER'),
                'KECAMATAN_USER' => $this->input->post('KECAMATAN_USER')
            );
            $insert = $this->user->save($data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_update() {
        if ($this->input->is_ajax_request()) {
            $this->_validate();
            $data = array(
                'PASSWORD_USER' => $this->crypt->encryptPassword($this->input->post('PASSWORD_USER')),
                'FULLNAME_USER' => $this->input->post('FULLNAME_USER'),
                'KECAMATAN_USER' => $this->input->post('KECAMATAN_USER')
            );
            $this->user->update(array('ID_USER' => $this->input->post('ID_USER')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_change_status($id, $status) {
        if ($this->input->is_ajax_request()) {
            $data = array(
                'STATUS_USER' => $status
            );
            $this->user->update(array('ID_USER' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_change_hakakses($id, $status) {
        if ($this->input->is_ajax_request()) {
            $data = array(
                'HAKAKSES_USER' => $status
            );
            $this->user->update(array('ID_USER' => $id), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_delete($id) {
        if ($this->input->is_ajax_request()) {
            $this->user->delete_by_id($id);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_check_username($id) {
        if ($this->input->is_ajax_request()) {
            $result = $this->user->get_status_login($id);
            if ($result)
                echo json_encode(array('status' => FALSE));
            else
                echo json_encode(array('status' => TRUE));
            exit();
        }
    }

    private function _validate() {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('NAME_USER') == '') {
            $data['inputerror'][] = 'NAME_USER';
            $data['error_string'][] = 'First name is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('FULLNAME_USER') == '') {
            $data['inputerror'][] = 'FULLNAME_USER';
            $data['error_string'][] = 'Nama lengkap is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('PASSWORD_USER') == '') {
            $data['inputerror'][] = 'PASSWORD_USER';
            $data['error_string'][] = 'Password is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('REPASSWORD_USER') == '') {
            $data['inputerror'][] = 'REPASSWORD_USER';
            $data['error_string'][] = 'Repassword is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('KECAMATAN_USER') == '') {
            $data['inputerror'][] = 'KECAMATAN_USER';
            $data['error_string'][] = 'Kecamatan is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('PASSWORD_USER') != $this->input->post('REPASSWORD_USER')) {
            $data['inputerror'][] = 'REPASSWORD_USER';
            $data['error_string'][] = 'Password tidak sama.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function edit($ID_USER) {
        $this->index($ID_USER);
    }

}
