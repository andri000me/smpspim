<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Konversi_handler
 *
 * @author rohmad
 */
class Mutasi_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'akad_siswa_model' => 'akad_siswa',
            'siswa_model' => 'siswa',
            'assign_tagihan_model' => 'assign_tagihan'
        ));
        $this->CI->load->library('nis_handler');
    }

    public function proses($ID_AS, $ID_SISWA, $STATUS_MUTASI) {
//        $this->cek_tagihan($ID_SISWA);
        
        if($this->update_status_masterdata($ID_SISWA, $STATUS_MUTASI)) {
            $insert = $this->update_status_akademik($ID_AS);
        } else {
            $insert = 0;
        }

        return $insert;
    }

    private function cek_tagihan($ID_SISWA) {
        $where = array(
            'TA_TAG' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 1,
        );
        $data_tag = $this->CI->assign_tagihan->get_rows($where);

        if (count($data_tag) > 0) {
            $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak dapat memproses siswa karena ada tagihan yang belum dibayar. Silahkan menghubungi pihak keuangan untuk pelunasan.'));
        }
    }

    public function update_status_masterdata($ID_SISWA, $STATUS_MUTASI_SISWA) {
        $no = $this->CI->pengaturan->getNomorSuratMutasi();
        if($STATUS_MUTASI_SISWA == 99) {
            $no_ijasah = $this->CI->pengaturan->getNomorIjasah();
            $no_syahadah = $this->CI->pengaturan->getNomorSyahadah();
        } else {
            $no_ijasah = NULL;
            $no_syahadah = NULL;
        }
        $data = array(
            'AKTIF_SISWA' => 0,
            'ALUMNI_SISWA' => 1,
            'STATUS_MUTASI_SISWA' => $STATUS_MUTASI_SISWA,
            'TANGGAL_MUTASI_SISWA' => date('Y-m-d'),
            'NO_SURAT_MUTASI_SISWA' => $no,
            'NOMOR_IJASAH_SISWA' => $no_ijasah,
            'NOMOR_SYAHADAH_SISWA' => $no_syahadah,
            'USER_MUTASI_SISWA' => $this->CI->session->userdata('ID_USER'),
        );
        $where = array('ID_SISWA' => $ID_SISWA);
        $insert = $this->CI->siswa->update($where, $data);
        if($insert) {
            $this->CI->pengaturan->setNomorSuratMutasi($no + 1);
            if($no_ijasah != NULL && $no_syahadah != NULL) {
                $this->CI->pengaturan->setNomorIjasah($no_ijasah + 1);
                $this->CI->pengaturan->setNomorSyahadah($no_syahadah + 1);
            }
            
            $data_siswa = $this->CI->siswa->get_by_id_mutasi($ID_SISWA);
            $this->CI->nis_handler->hapus_nis($data_siswa);
        }
        
        return $insert;
    }

    private function update_status_akademik($ID_AS) {
        $data = array(
            'AKTIF_AS' => 0,
        );
        $where = array('ID_AS' => $ID_AS);

        return $this->CI->akad_siswa->update($where, $data);
    }

}
