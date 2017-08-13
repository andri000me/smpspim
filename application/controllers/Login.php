<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model', 'user');
    }

    public function index() {
        $this->load->view('layout/main/header');
        $this->load->view('user/login');
    }

    public function log_out() {
        $this->generate->set_header_JSON();

        $this->auth->log_out();

        $this->generate->output_JSON(array("status" => 1, "link" => site_url()));
    }

    public function ajax_login() {
        if ($this->input->is_ajax_request()) {
            $this->_validate();
            $data_login = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password')
            );
            $result = $this->auth->check_login($data_login);
            
            echo json_encode($result);
        }
    }

    private function _validate() {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Username is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password is required';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function request_content() {
        
    }

    public function option_hakakses() {
        $this->auth->validation();
        $this->auth->unregistration_hakakses();

        $this->load->model('hakakses_user_model', 'hakakses_user');
        $data['data'] = $this->hakakses_user->get_all();
        $data['count'] = $this->hakakses_user->count_all();

        $this->load->view('layout/main/header');
        $this->load->view('backend/user/option_hakakses', $data);
    }

    public function chooseHakAkses() {
        $this->auth->validation_simple();

        $ID_HAKAKSES = $this->input->post('ID_HAKAKSES');

        $data['status'] = $this->auth->registration_hakakses($ID_HAKAKSES);

        if ($data['status']) {
            $data['msg'] = 'Berhasil mendaftarkan ke modul';
            $data['link'] = site_url('login/welcome');
        } else {
            $data['msg'] = 'Gagal mendaftarkan ke modul';
        }

        echo json_encode($data);
        exit();
    }

    public function change_password() {
        $this->generate->set_header_JSON();

        if ($this->session->userdata('ID_USER') !== NULL) {
            $data_html = array(
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

            $this->generate->output_form_JSON(NULL, $this->session->userdata('ID_USER'), $data_html, FALSE, FALSE, FALSE, TRUE);
        }
    }

    public function proccess_change_password() {
        $this->generate->set_header_JSON();

        if ($this->session->userdata('ID_USER') !== NULL) {
            $data = $this->input->post();

            if ($data['PASSW0RD_BARU'] != $data['RE_PASSW0RD_BARU']) {
                $status = FALSE;
                $msg = 'Password tidak sama';
            } elseif (strlen($data['PASSW0RD_BARU']) < 5) {
                $status = FALSE;
                $msg = 'Password harus minimal 5 karakter';
            } else {
                $data_user = array('PASSWORD_USER' => $this->crypt->encryptPassword($data['PASSW0RD_BARU']));
                $where = array('ID_USER' => $this->session->userdata('ID_USER'));
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
    }

    public function welcome() {
        $this->auth->validation();

        $data = array();

        if ($this->session->userdata('ID_HAKAKSES') == 3) {
            $this->load->model('psb_validasi_model', 'psb_validasi');

            $data['STATUS_PSB'] = $this->psb_validasi->is_psb_tutup();
        }

        $this->generate->backend_view('user/welcome', $data);
    }

}
