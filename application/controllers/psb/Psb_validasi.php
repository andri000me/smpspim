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
class Psb_validasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'psb_validasi_model' => 'psb_validasi',
            'aturan_denah_model' => 'aturan_denah',
        ));
        $this->auth->validation(3);
    }

    public function change_status() {
        $this->generate->set_header_JSON();

        $STATUS_PSB_V = $this->input->post('STATUS');

        if ($this->aturan_denah->is_um_dibuat() && ($STATUS_PSB_V == 0)) {
            $result = 0;
            $msg = 'Denah telah dibuat. Anda tidak diperbolehkan membuka PSB.';
        } else {
            if ($this->psb_validasi->is_status_ada()) {
                $result = $this->psb_validasi->update_status($STATUS_PSB_V);
            } else {
                $result = $this->psb_validasi->simpan_status($STATUS_PSB_V);
            }

            if ($result > 0) {
                $msg = 'Perubahan status berhasil';
            } else {
                $msg = 'Perubahan status gagal';
            }
        }

        $this->generate->output_JSON(array('status' => $result, 'msg' => $msg));
    }

}
