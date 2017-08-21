<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nis_handler {
    
    public function __construct() {
        $this->CI = & get_instance();
        
        $this->CI->load->model(array(
            'tingkat_model' => 'tingkat',
            'siswa_model' => 'siswa',
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'nis_model' => 'nis',
            'kelas_model' => 'kelas',
            'akad_siswa_model' => 'akad_siswa',
        ));
        $this->CI->load->library('tagihan_handler');
    }
    
    public function proses() {
        $count = 0;
        $siswa = $this->CI->nis->get_nis_null();
        
        $siswa_lama = array();
        $siswa_baru = array();
        // MEMILAH SISWA BARU DAN LAMA
        foreach ($siswa as $detail) {
            if($detail['ID_NIS'] == NULL) $siswa_baru[] = $detail;
            else $siswa_lama[] = $detail;
        }
        
        // MEMBUAT NIS UNTUK SISWA LULUSAN DARI PIM
        foreach ($siswa_lama as $detail) {
            $status_proses = $this->buat_nis($detail['ID_SISWA'], $detail['ANGKATAN_SISWA'], $detail['TINGKAT_AS']);
            
            if ($status_proses) $count++;
        }
        
        // MEMBUAT NIS UNTUK SISWA BARU
        foreach ($siswa_baru as $detail) {
            $status_proses = $this->buat_nis($detail['ID_SISWA'], $detail['ANGKATAN_SISWA'], $detail['TINGKAT_AS']);
            
            if ($status_proses) $count++;
        }
        
        return $count;
    }
    
    public function buat_nis($ID_SISWA, $ANGKATAN_SISWA, $ID_TINGK) {
//        if(!$this->CI->nis->nisNULL()) 
//            $this->CI->generate->output_JSON(array('status' => FALSE, 'msg' => 'Siswa dengan ID = '.$ID_SISWA.' telah mempunyai NIS.'));
        
        $data_tingkat = $this->CI->tingkat->get_by_id($ID_TINGK);
        $no_urut = $this->CI->pengaturan->getNomorInduk($data_tingkat->DEPT_TINGK) + 1;
        $nomor_induk = $this->CI->pengaturan->getNomorPokok($data_tingkat->DEPT_TINGK, $ANGKATAN_SISWA, $no_urut);
        
        $data = array('NIS_SISWA' => $nomor_induk);
        $where = array('ID_SISWA' => $ID_SISWA);
        $status = $this->CI->siswa->update($where, $data);
        
        if($status) {
            $this->CI->pengaturan->setNomorTerakhir($data_tingkat->DEPT_TINGK, $no_urut);
            
            // SET TAGIHAN SISWA
            $this->CI->tagihan_handler->assign_tagihan($data_tingkat->DEPT_TINGK, $ID_SISWA);
        }
        
        return $status;
    }
    
    public function hapus_nis($data_siswa) {
        $data_simpan = array(
            'NIS_NIS' => $data_siswa->NIS_SISWA,
            'NO_UM_NIS' => $data_siswa->NO_UM_SISWA,
            'SISWA_NIS' => $data_siswa->ID_SISWA,
            'TA_NIS' => $this->CI->session->userdata('ID_TA_ACTIVE'),
            'SISWA_NIS' => $data_siswa->ID_SISWA,
            'DEPT_NIS' => $data_siswa->DEPT_TINGK == NULL ? substr($data_siswa->NIS_SISWA, 0, 2) : $data_siswa->DEPT_TINGK,
            'ASAL_SEKOLAH_NIS' => $data_siswa->ASAL_SEKOLAH_SISWA,
            'MASUK_JENJANG_NIS' => $data_siswa->MASUK_JENJANG_SISWA,
            'MASUK_TINGKAT_NIS' => $data_siswa->MASUK_TINGKAT_SISWA,
            'NO_IJASAH_NIS' => $data_siswa->NO_IJASAH_SISWA,
            'TANGGAL_IJASAH_NIS' => $data_siswa->TANGGAL_IJASAH_SISWA,
            'STATUS_MUTASI_NIS' => $data_siswa->STATUS_MUTASI_SISWA,
            'TANGGAL_MUTASI_NIS' => $data_siswa->TANGGAL_MUTASI_SISWA,
            'NOMOR_IJASAH_NIS' => $data_siswa->NOMOR_IJASAH_SISWA,
            'NOMOR_SYAHADAH_NIS' => $data_siswa->NOMOR_SYAHADAH_SISWA,
            'NO_SURAT_MUTASI_NIS' => $data_siswa->NO_SURAT_MUTASI_SISWA,
            'STATUS_ASAL_NIS' => $data_siswa->STATUS_ASAL_SISWA,
            'USER_NIS' => $this->CI->session->userdata('ID_USER'),
        );
        
        if($this->CI->nis->save($data_simpan)) {
            $data_update = array(
                'NIS_SISWA' => NULL,
                'NO_UM_SISWA' => NULL,
            );
            $where_update = array('ID_SISWA' => $data_siswa->ID_SISWA);
            
            $this->CI->siswa->update($where_update, $data_update);
        } else {
            $this->CI->generate->output_JSON(array('status' => FALSE, 'msg' => 'Gagal menyimpan data NIS Siswa.'));
        }
    }
    
    public function konversi_nis_diperbolehkan($data_siswa, $ID_TINGKAT) {
        $data_tingkat = $this->CI->tingkat->get_by_id($ID_TINGKAT);
        
        if($data_siswa->DEPT_TINGK == $data_tingkat->DEPT_TINGK)
            return FALSE;
        else 
            return TRUE;
    }
    
    public function proses_absen() {
        $data_kelas = $this->CI->akad_siswa->get_kelas_absen_null();
        
        $i = 0;
        
        foreach ($data_kelas as $detail_kelas) {
            $where_as = array(
                'TA_AS' => $this->CI->session->userdata('ID_TA_ACTIVE'),
                'KELAS_AS' => $detail_kelas->KELAS_AS
            );
            $data_siswa = $this->CI->akad_siswa->get_rows($where_as);
            
            $nomor_absen = 1;
            foreach ($data_siswa as $detail_siswa) {
                $i++;
                
                $data_update = array(
                    'NO_ABSEN_AS' => $nomor_absen++
                );
                $where_update = array(
                    'ID_AS' => $detail_siswa->ID_AS,
                );
                $this->CI->akad_siswa->update($where_update, $data_update);
            }
        }
        
        return $i;
    }
}