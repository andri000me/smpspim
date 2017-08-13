<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth {

    public function __construct() {

        $this->CI = & get_instance();
    }

    public function validation($ID_HAKAKSES = '') {
        if (!$this->CI->session->userdata('ID_USER'))
            redirect('login');
        else {
            if ($this->CI->session->userdata('ID_HAKAKSES') == NULL and $this->CI->router->fetch_method() != 'option_hakakses' and $this->CI->router->fetch_class() != 'pencarian') {
                redirect('login/option_hakakses');
            }

            if (is_array($ID_HAKAKSES)) {
                if (!in_array($this->CI->session->userdata('ID_HAKAKSES'), $ID_HAKAKSES))
                    show_error('Anda tidak memiliki akses pada halaman ini', '403', 'Kesalahan Hak Akses');
            } elseif (($this->CI->session->userdata('ID_HAKAKSES') != $ID_HAKAKSES) && ($ID_HAKAKSES != ''))
                show_error('Anda tidak memiliki akses pada halaman ini', '403', 'Kesalahan Hak Akses');
        }
    }

    public function validation_simple() {
        if (!$this->CI->session->userdata('ID_USER'))
            redirect('login');
    }

    public function check_login($data) {
        $this->CI->load->model(array('user_model' => 'user', 'menu_model' => 'menu', 'tahun_ajaran_model' => 'tahun_ajaran', 'catur_wulan_model' => 'cawu', 'login_model' => 'login'));

        $result_check_login = '';

        if ($this->CI->login->login_diperbolehkan()) {
            $result_username = $this->CI->user->get_status_login_username($data);
            
            if ($result_username) {
                if ($result_username->STATUS_USER == 'ACTIVE') {
                    $result = $this->CI->user->get_status_login($data);
                    
                    if($result) {
                        $data_ta = $this->CI->tahun_ajaran->get_ta_active();
                        $data_psb = $this->CI->tahun_ajaran->get_psb_active();
                        $data_cawu = $this->CI->cawu->get_cawu_active();

                        $data = array(
                            'ID_USER' => $result->ID_USER,
                            'NAME_USER' => $result->NAME_USER,
                            'ID_PEG' => $result->ID_PEG,
                            'FULLNAME_USER' => $result->NAMA_PEG,
                            'PHOTO_USER' => $result->FOTO_PEG,
                            'CREATED_USER' => $result->CREATED_USER,
                            'ID_TA_ACTIVE' => $data_ta->ID_TA,
                            'NAMA_TA_ACTIVE' => $data_ta->NAMA_TA,
                            'ID_PSB_ACTIVE' => $data_psb->ID_TA,
                            'NAMA_PSB_ACTIVE' => $data_psb->NAMA_TA,
                            'ID_CAWU_ACTIVE' => $data_cawu->ID_CAWU,
                            'NAMA_CAWU_ACTIVE' => $data_cawu->NAMA_CAWU,
                        );
                        $this->CI->user->update(array('ID_USER' => $result->ID_USER), array('LASTLOGIN_USER' => date('Y-m-d H:i:s')));

                        $this->CI->session->set_userdata($data);

                        $this->CI->login->login_benar($data);
                        
                        $result_check_login = $result->STATUS_USER;
                    } elseif ($result_username->SISA_PERCOBAAN_USER > 0) {
                        $this->CI->user->update(array('ID_USER' => $result_username->ID_USER), array('SISA_PERCOBAAN_USER' => $result_username->SISA_PERCOBAAN_USER - 1));
                        
                        $this->CI->login->login_salah($data);
                        
                        $result_check_login = 'WRONG_PASSWORD#'.($result_username->SISA_PERCOBAAN_USER - 1);
                    } else {
                        $this->CI->user->update(array('ID_USER' => $result_username->ID_USER), array('STATUS_USER' => 'BLOCK'));
                        
                        $this->CI->login->login_salah($data);
                        
                        $result_check_login = 'BLOCK';
                    }
                } else {
                    $this->CI->login->login_salah($data);

                    $result_check_login = $result_username->STATUS_USER;
                }
            } else {
                $this->CI->login->login_salah($data);

                $result_check_login = 'WRONG_USERNAME';
            }
        } else {
            $this->CI->login->login_salah($data);
                
            $result_check_login = 'TIMEOUT';
        }

        $return = $this->status_login($result_check_login);

        return $return;
    }

    private function status_login($results) {
        $data = array();

        $results_ex = explode('#', $results);
        $result = $results_ex[0];
        $count = isset($results_ex[1]) ? $results_ex[1] : NULL;
        
        if ($result == 'ACTIVE') {
            // $this->clear_log();
            
            $data['success_string'] = 'Selamat, Login berhasil.';
            $data['link'] = site_url('login/option_hakakses');
            $data['status'] = TRUE;
        } elseif ($result == 'BLOCK') {
            $data['error_string'] = 'Akun Anda telah diblock. Silahkan hubungi Admin untuk keterangan lebih lanjut.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'PENDING') {
            $data['error_string'] = 'Akun Anda masih berstatus PENDING. Tunggu sampai kami mengaktifkan akun Anda.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'DELETE') {
            $data['error_string'] = 'Akun Anda telah dihapus oleh Admin. Silahkan hubungi admin untuk keterangan lebih lanjut.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'WRONG_USERNAME') {
            $data['error_string'] = 'Username tidak cocok dengan database.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'WRONG_PASSWORD') {
            $data['error_string'] = 'Password tidak cocok dengan database. Anda memiliki sebanyak '.$count.' lagi untuk mecoba masuk. Jika gagal, maka akun Anda akan diblokir.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'WRONG') {
            $data['error_string'] = 'Username atau Password tidak cocok dengan database.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == 'TIMEOUT') {
            $data['error_string'] = 'Waktu percobaan login Anda telah melampaui batas. Silahkan coba lagi dalam '.$this->CI->pengaturan->getJedaPercobaanLogin().' menit kedepan.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        } elseif ($result == '') {
            $data['error_string'] = 'Error tidak diketahui.';
            $data['inputerror'] = 'login';
            $data['status'] = FALSE;
        }

        return $data;
    }
    
    private function clear_log() {
        $this->CI->load->model(array('log_query_model' => 'log_query'));
        
        $this->CI->log_query->clear_log();
    }

    public function unregistration_hakakses() {
        $data = array(
            'ID_HAKAKSES',
            'NAME_HAKAKSES',
            'MENU_USER'
        );

        $this->CI->session->unset_userdata($data);
    }

    public function registration_hakakses($ID_HAKAKSES) {
        $this->CI->load->model(array('hakakses_user_model' => 'hakakses_user', 'menu_model' => 'menu'));
        $result = $this->CI->hakakses_user->get_by_id($ID_HAKAKSES);

        if ($result) {
            $data = array(
                'ID_HAKAKSES' => $result->ID_HAKAKSES,
                'NAME_HAKAKSES' => $result->NAME_HAKAKSES,
                'MENU_USER' => json_encode($this->CI->menu->get_menu($ID_HAKAKSES, JSON_PRETTY_PRINT))
            );

            if ($result->ID_HAKAKSES == 4) {
                $keu = $this->CI->hakakses_user->get_keuangan_user();

                if (count($keu) > 0)
                    $data['TAGIHAN'] = json_encode($keu);
            }

            $this->CI->session->set_userdata($data);

            return TRUE;
        } else {
            return FALSE;
        }
    }

    // $type = 'add', 'edit', 'delete', 'view', 'export'
    public function crud_validation($type) {
        $MENUS = json_decode($this->CI->session->userdata('MENU_USER'));

        $result = FALSE;
        foreach ($MENUS as $MENU) {
            $menu = $MENU->CONTROLLER_MENU;
            if (strpos($menu, '/')) {
                $menu_ex = explode("/", $menu);
                $menu = $menu_ex[1];
            }

            if (strtolower($menu) == strtolower($this->CI->router->fetch_class()) && (strtolower($type) == strtolower($MENU->FUNCTION_MENU)))
                $result = TRUE;
        }

        return $result;
    }

    public function log_out() {
        $this->CI->session->sess_destroy();
    }

    public function generate_token() {
        $generate_token = $this->CI->crypt->randomString();

        $this->CI->session->unset_userdata('TOKEN');
        $this->CI->session->set_userdata('TOKEN', $this->CI->crypt->encryptPassword($generate_token));

        return $generate_token;
    }

}
