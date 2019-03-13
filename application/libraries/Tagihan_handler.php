<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'siswa_model' => 'siswa',
        ));
    }

    // SET TAGIHAN SISWA
    public function assign_tagihan($DEPT_TINGK, $ID_SISWA, $TA = NULL) {
        if ($this->siswa_dari_kajen($ID_SISWA))
            return 0;

        if ($TA == NULL)
            $TA = $this->CI->session->userdata('ID_TA_ACTIVE');
        
        $tagihan = $this->CI->detail_tagihan->get_all_active($DEPT_TINGK, $TA, $this->CI->siswa->get_jk());

        foreach ($tagihan as $tag) {
            $data_assign = array(
                'SISWA_SETUP' => $ID_SISWA,
                'DETAIL_SETUP' => $tag->ID_DT,
                'KETERANGAN_SETUP' => 'TAGIHAN TAHUN ' . date('Y'),
            );
            $where = array(
                'SISWA_SETUP' => $ID_SISWA,
                'DETAIL_SETUP' => $tag->ID_DT,
                'KADALUARSA_SETUP' => 0,
            );
            $cek = $this->CI->assign_tagihan->get_rows($where);
            if (count($cek) == 0)
                $this->CI->assign_tagihan->save($data_assign);
        }
    }

    private function siswa_dari_kajen($ID_SISWA) {
        $data_siswa = $this->CI->siswa->get_by_id($ID_SISWA, TRUE);

        if ($data_siswa->ALAMAT_SISWA == NULL)
            return FALSE;

        $id_kecamatan_margoyoso = 1172;
        $alamat_siswa = strtoupper($data_siswa->ALAMAT_SISWA);

        if (strpos($alamat_siswa, 'KAJEN') !== FALSE)
            return TRUE;
        else
            return FALSE;
    }

    public function pengunduran_diri($ID_SISWA, $FORCE_PROCCESS) {
        $data_tagihan = $this->CI->assign_tagihan->pembayaran_pengembalian($ID_SISWA);

        $tagihan = "";
        if ((count($data_tagihan) == 0) || (count($data_tagihan) > 0 && $FORCE_PROCCESS)) {
            $data_update = array(
                'KADALUARSA_SETUP' => 1
            );
            $where_update = array(
                'SISWA_SETUP' => $ID_SISWA,
                'STATUS_SETUP' => 0
            );

            $this->CI->assign_tagihan->update($where_update, $data_update);
        } else {
            foreach ($data_tagihan as $detail) {
                $tagihan .= $detail->NAMA_DT . ", ";
            }
        }

        return $tagihan;
    }

    public function unsign_tagihan($ID_SETUP) {
        foreach ($ID_SETUP as $DETAIL) {
            $this->CI->assign_tagihan->delete_by_id($DETAIL);
        }
    }

}
