<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kenaikan_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'akad_siswa_model' => 'akad_siswa',
        ));
        $this->CI->load->library(array(
            'tagihan_handler',
            'pelanggaran_handler'
        ));
    }

    public function proses($ID_AS, $TA, $STATUS_KENAIKAN, $TINGKAT = NULL, $STATUS_TAG = NULL) {
        $data_siswa = $this->CI->akad_siswa->get_by_id($ID_AS);

        if ($STATUS_TAG != 0)
            $this->cek_tagihan($data_siswa->ID_SISWA);

        if ($TINGKAT == NULL)
            $TINGKAT = ($STATUS_KENAIKAN) ? ($data_siswa->ID_TINGK + 1) : $data_siswa->ID_TINGK;

        $data = array(
            'TA_AS' => $TA,
            'SISWA_AS' => $data_siswa->ID_SISWA,
            'TINGKAT_AS' => $TINGKAT,
            'USER_AS' => $this->CI->session->userdata('ID_USER')
        );

        $insert = $this->CI->akad_siswa->save($data);
        if ($insert) {
            $this->CI->tagihan_handler->assign_tagihan($data_siswa->DEPT_TINGK, $data_siswa->ID_SISWA, $TA);

            $data_update = array(
                'NAIK_AS' => $STATUS_KENAIKAN
            );
            $where_update = array(
                'ID_AS' => $ID_AS
            );
            $this->CI->akad_siswa->update($where_update, $data_update);

            $this->CI->pelanggaran_handler->proses_poin_tahun_lalu($data_siswa->ID_SISWA, $TA);
        }

        return $insert;
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
