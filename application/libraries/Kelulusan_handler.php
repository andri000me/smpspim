<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class kelulusan_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'akad_siswa_model' => 'akad_siswa',
            'siswa_model' => 'siswa',
        ));
        $this->CI->load->library('mutasi_handler');
    }

    public function proses($ID_AS, $TA, $STATUS_KELULUSAN, $STATUS_TAG = null) {
        $data_siswa = $this->CI->akad_siswa->get_by_id($ID_AS);

        if ($STATUS_TAG != 0)
            $this->cek_tagihan($data_siswa->ID_SISWA);

        $data_akad = array(
            'LULUS_AS' => $STATUS_KELULUSAN,
        );
        $where_akad = array(
            'ID_AS' => $ID_AS,
        );

        $status = $this->CI->akad_siswa->update($where_akad, $data_akad);
        if ($status && ($STATUS_KELULUSAN == 'L')) {
            $this->CI->mutasi_handler->update_status_masterdata($data_siswa->ID_SISWA, 99);
        }

        return $status;
    }

    private function cek_tagihan($ID_SISWA) {
        $where = array(
            'TA_TAG' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 0,
        );
        $data_tag = $this->CI->assign_tagihan->get_rows($where);

        if (count($data_tag) > 0) {
            $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak dapat memproses siswa karena ada tagihan yang belum dibayar. Silahkan menghubungi pihak keuangan untuk pelunasan.'));
        }
    }

}
