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
class Konversi_handler {
    
    public function __construct() {
        $this->CI = & get_instance();
        
        $this->CI->load->model(array(
            'akad_siswa_model' => 'akad_siswa',
            'nis_model' => 'nis',
            'assign_tagihan_model' => 'assign_tagihan'
        ));
        $this->CI->load->library('nis_handler');
    }
    
    public function proses($ID_AS, $ID_SISWA, $ANGKATAN_SISWA, $ID_TINGKAT, $ID_KELAS) {
        if($this->CI->akad_siswa->konversi_tersedia($ID_SISWA, $ID_TINGKAT, $ID_KELAS)) {
            $data_siswa = $this->CI->nis->get_siswa($ID_SISWA);
            $status_konversi = $this->CI->nis_handler->konversi_nis_diperbolehkan($data_siswa, $ID_TINGKAT);
            
            if($status_konversi) $this->cek_tagihan ($ID_SISWA);
            
            if ($this->tambah_data($ID_SISWA, $ID_TINGKAT, $ID_KELAS)) {
                $this->update_status_konversi($ID_AS);
                if($status_konversi) {
                    $this->CI->nis_handler->hapus_nis($data_siswa);
                    $this->CI->nis_handler->buat_nis($ID_SISWA, $ANGKATAN_SISWA, $ID_TINGKAT);
                }
            } else {
                $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak dapat meng-konversi data ke tingkat dan kelas tujuan.'));
            }
        } else {
            $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak dapat meng-konversi data ke tingkat dan kelas yang telah dikonversi.'));
        }
        
        return TRUE;
    }
    
    private function cek_tagihan($ID_SISWA) {
        $where = array(
            'TA_TAG' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 1,
        );
        $data_tag = $this->CI->assign_tagihan->get_rows($where);
        
        if (count($data_tag) > 0) {
            $this->CI->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tidak dapat meng-konversi siswa karena terdapat tagihan yang belum dikembalikan. Silahkan menghubungi pihak keuangan untuk pengembalian uang tagihan.'));
        } else {
            $data_update = array(
                'KADALUARSA_SETUP' => 1
            );
            $where_update = array(
                'SISWA_SETUP' => $ID_SISWA,
                'STATUS_SETUP' => 0
            );
            
            $this->CI->assign_tagihan->update($where_update, $data_update);
        }
    }
    
    private function tambah_data($ID_SISWA, $ID_TINGKAT, $ID_KELAS) {
        $data = array(
            'TA_AS' => $this->CI->session->userdata("ID_TA_ACTIVE"),
            'SISWA_AS' => $ID_SISWA,
            'TINGKAT_AS' => $ID_TINGKAT,
            'KELAS_AS' => $ID_KELAS,
            'USER_AS' => $this->CI->session->userdata("ID_USER"),
        );
        
        return $this->CI->akad_siswa->save($data);
    }
    
    private function update_status_konversi($ID_AS) {
        $data = array(
            'AKTIF_AS' => 0,
            'KONVERSI_AS' => 1,
            'USER_KONVERSI_AS' => $this->CI->session->userdata("ID_USER"),
        );
        $where = array('ID_AS' => $ID_AS);
        
        return $this->CI->akad_siswa->update($where, $data);
    }
}
