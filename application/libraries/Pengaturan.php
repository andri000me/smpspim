<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pengaturan {

    function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model('pengaturan_model', 'data_pengaturan');
    }

    public function getNamaApp() {
        return $this->CI->data_pengaturan->get_by_id('nama_aplikasi');
    }

    public function getMotto() {
        return $this->CI->data_pengaturan->get_by_id('motto');
    }

    public function getNamaYayasan() {
        return $this->CI->data_pengaturan->get_by_id('nama_yayasan');
    }

    public function getNamaLembaga() {
        return $this->CI->data_pengaturan->get_by_id('nama_lembaga');
    }

    public function getNamaLembagaSingk() {
        return $this->CI->data_pengaturan->get_by_id('nama_lembaga_singkatan');
    }

    public function getAlamat() {
        return $this->CI->data_pengaturan->get_by_id('alamat');
    }

    public function getDesa() {
        return $this->CI->data_pengaturan->get_by_id('desa');
    }

    public function getKecamatan() {
        return $this->CI->data_pengaturan->get_by_id('kecamatan');
    }

    public function getKabupaten() {
        return $this->CI->data_pengaturan->get_by_id('kabupaten');
    }

    public function getProvinsi() {
        return $this->CI->data_pengaturan->get_by_id('provinsi');
    }

    public function getNegara() {
        return $this->CI->data_pengaturan->get_by_id('negara');
    }

    public function getKodepos() {
        return $this->CI->data_pengaturan->get_by_id('kode_pos');
    }

    public function getTelp() {
        return $this->CI->data_pengaturan->get_by_id('telp');
    }

    public function getFax() {
        return $this->CI->data_pengaturan->get_by_id('fax');
    }

    public function getWebsite() {
        return $this->CI->data_pengaturan->get_by_id('website');
    }

    public function getEmail() {
        return $this->CI->data_pengaturan->get_by_id('email');
    }

    public function getLogo() {
        return $this->CI->data_pengaturan->get_by_id('logo');
    }

    public function getUjianPSB() {
        return $this->CI->data_pengaturan->get_by_id('psb_ujian');
    }

    public function getTahunBerdiri() {
        return $this->CI->data_pengaturan->get_by_id('tahun_berdiri');
    }

    public function getUjianCawu() {
        return $this->CI->data_pengaturan->get_by_id('cawu_ujian');
    }

    public function getJedaPercobaanLogin() {
        return $this->CI->data_pengaturan->get_by_id('jeda_percobaan_login');
    }

    public function getLamaLogTersimpan() {
        return $this->CI->data_pengaturan->get_by_id('lama_log_tersimpan');
    }

    public function getBanyakPercobaanLogin() {
        return $this->CI->data_pengaturan->get_by_id('banyak_percobaan_login');
    }

    public function getNilaiMinimalHafal() {
        return $this->CI->data_pengaturan->get_by_id('nilai_minimal_hafal');
    }

    public function getMaksimalLariHafalan() {
        return $this->CI->data_pengaturan->get_by_id('maksimal_lari_hafalan');
    }

    public function getNilaiLulusPSB() {
        return $this->CI->data_pengaturan->get_by_id('nilai_lulus_psb');
    }

    public function getNomorInduk($dept) {
        $data = json_decode($this->CI->data_pengaturan->get_by_id('nomor_induk_terakhir'), TRUE);
        
        foreach ($data as $jenjang => $nomor) {
            if ($dept == $jenjang) return $nomor;
        }
    }
    
    public function getNomorPokok($jenjang, $angkatan, $nomor) {
        if (strlen($nomor) > 4) return $jenjang.$angkatan.$nomor;
        else return $jenjang.$angkatan.'-'.$nomor;
    }
    
    public function setNomorTerakhir($dept, $nomor_terakhir) {
        $data = json_decode($this->CI->data_pengaturan->get_by_id('nomor_induk_terakhir'), TRUE);
        $data_tingkat = array();
        
        foreach ($data as $jenjang => $nomor) {
            if ($dept == $jenjang) $data_tingkat[$jenjang] = $nomor_terakhir;
            else $data_tingkat[$jenjang] = $nomor;
        }
        
        return $this->CI->data_pengaturan->update('nomor_induk_terakhir', json_encode($data_tingkat));
    }

    public function getJumlahSiswaPerbaris() {
        return $this->CI->data_pengaturan->get_by_id('denah_siswa_perbaris');
    }

    public function getJumlahSiswaPerruang() {
        return $this->CI->data_pengaturan->get_by_id('denah_siswa_perruang');
    }

    public function getDataKetuaPU() {
        $this->CI->load->model('pegawai_model', 'pegawai');
        
        $data = $this->CI->pegawai->get_by_id($this->CI->data_pengaturan->get_by_id('ketua_pu'));
        
        return $data;
    }

    public function getPDTUKeuangan1() {
        $this->CI->load->model('pegawai_model', 'pegawai');
        
        $data = $this->CI->pegawai->get_by_id($this->CI->data_pengaturan->get_by_id('pd_tu_dan_keuangan_1'));
        
        return $data;
    }

    public function getPDTUKeuangan2() {
        $this->CI->load->model('pegawai_model', 'pegawai');
        
        $data = $this->CI->pegawai->get_by_id($this->CI->data_pengaturan->get_by_id('pd_tu_dan_keuangan_2'));
        
        return $data;
    }

    public function getDataKetuaP3H($jk) {
        $this->CI->load->model('pegawai_model', 'pegawai');
        
        $data = $this->CI->pegawai->get_by_id($this->CI->data_pengaturan->get_by_id('ketua_p3h_'.($jk == 'L' ? 'banin' : 'banat')));
        
        return $data;
    }

    public function getDataKetuaKomdis() {
        $this->CI->load->model('pegawai_model', 'pegawai');
        
        $data = $this->CI->pegawai->get_by_id($this->CI->data_pengaturan->get_by_id('ketua_komdis'));
        
        return $data;
    }
    
    public function setJumlahSiswaPerruang($value) {
        return $this->CI->data_pengaturan->update('denah_siswa_perruang', $value);
    }

    public function getStatusUjianPSB($jenjang, $tingkat) {
        $data = json_decode($this->CI->data_pengaturan->get_by_id('psb_ujian'));
        
        foreach ($data as $key => $detail) {
            if ($key == $jenjang) {
                foreach ($detail as $value) {
                    if ($value == $tingkat)
                        return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function getDataUjianPSB() {
        return json_decode($this->CI->data_pengaturan->get_by_id('psb_ujian'));
    }

    public function getDataUjianCawu() {
        return json_decode($this->CI->data_pengaturan->get_by_id('cawu_ujian'));
    }

    // MENDAPATKAN DATA JENJANG PADA CALON SISWA YANG MASUK KETINGKAT 1
    public function getDataJenjangUM($data) {
        $this->CI->load->model(array(
            'asal_sekolah_model' => 'asal_sekolah',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
        ));
        
        $jenjang = array();
        $data_asal_sekolah = $this->CI->asal_sekolah->get_by_id($data['ASAL_SEKOLAH_SISWA']);
        $jenjang['old'] = $data_asal_sekolah->NAMA_JS;
        $max_tingkat = $this->CI->jenjang_sekolah->get_jumlah_kelas($jenjang['old']);

        if ($max_tingkat >= $data['MASUK_TINGKAT_SISWA']) {
            $jenjang_sekolah = $data_asal_sekolah->ID_JS;
            
            if ($data['MASUK_TINGKAT_SISWA'] == 1 && $jenjang_sekolah < 4) {
                $data_asal_sekolah_new = $this->CI->jenjang_sekolah->get_by_id($data_asal_sekolah->ID_JS + 1);
                $jenjang['new'] = $data_asal_sekolah_new->NAMA_JS;
            } elseif ($jenjang_sekolah < 4) {
                $jenjang['new'] = $jenjang['old'];
            }
        }

        return $jenjang;
    }

    // MENDAPATKAN STATUS UJIAM MASUK CALON SISWA
    public function getStatusTingkat($jenjang, $tingkat) {
        $this->CI->load->model(array(
            'jenjang_sekolah_model' => 'jenjang_sekolah',
        ));
        
        $max_tingkat = $this->CI->jenjang_sekolah->get_jumlah_kelas($jenjang);

        if ($max_tingkat >= $tingkat) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getJenjangSebelumnya($jenjang) {
        $this->CI->load->model('jenjang_sekolah_model', 'jenjang_sekolah');

        return $this->CI->jenjang_sekolah->jenjang_sebelumnya($jenjang);
    }
    
    public function getJenjangSelanjutnya($jenjang) {
        $this->CI->load->model('jenjang_sekolah_model', 'jenjang_sekolah');

        return $this->CI->jenjang_sekolah->jenjang_selanjutnya($jenjang);
    }

    public function getKodeUM($SISWA) {
        $no_urut = $SISWA->NO_UM_SISWA;

        if($no_urut == NULL) return 'TIDAK UM';
        
        if (strlen($no_urut) == 1)
            $no_urut = '00' . $no_urut;
        elseif (strlen($no_urut) == 2)
            $no_urut = '0' . $no_urut;

        return 'PSB/' . $SISWA->DEPT_MJD . '/' . $SISWA->MASUK_TINGKAT_SISWA . '/' . $no_urut;
    }
    
    public function getKelipatanDenah($jumlah_perbaris) {
        $kelipatan = 0;
        $data = json_decode($this->CI->data_pengaturan->get_by_id('denah_kelipatan'));
        
        foreach ($data as $key => $value) {
            if ($key == $jumlah_perbaris) $kelipatan = $value;
        }
        
        return $kelipatan;
    }
    
    public function getTahunTAAwal() {
        $ta_explode = explode("/", $this->CI->session->userdata("NAMA_TA_ACTIVE"));
        
        return $ta_explode[0];
    }
    
    public function getTahunPSBAwal() {
        $ta_explode = explode("/", $this->CI->session->userdata("NAMA_PSB_ACTIVE"));
        
        return $ta_explode[0];
    }

    public function isPengecualianTagihan($tipe, $jenjang, $tingkat = NULL) {
        $data = $this->CI->data_pengaturan->get_by_id('pengecualian_tagihan_psb_'.$tipe);
        $data_array = json_decode($data, TRUE);
        
        if(array_key_exists($jenjang, $data_array)) {
            if($tingkat == NULL) {
                return TRUE;
            } else {
                if(in_array($tingkat, $data_array[$jenjang])) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }
    
    public function getPelanggaranAbsensi() {
        return $this->CI->data_pengaturan->get_by_id('pelanggaran_absensi');
    }
    
    public function getXMLUpload() {
        return $this->CI->data_pengaturan->get_by_id('xml_upload');
    }
    
    public function setXMLUpload($value) {
        return $this->CI->data_pengaturan->update('xml_upload', $value);
    }
    
    public function getXMLDownload() {
        return $this->CI->data_pengaturan->get_by_id('xml_download');
    }
    
    public function setXMLDownload($value) {
        return $this->CI->data_pengaturan->update('xml_download', $value);
    }
    
    public function getNomorPaketSP() {
        return $this->CI->data_pengaturan->get_by_id('no_paket_sp');
    }
    
    public function setNomorPaketSP($value) {
        return $this->CI->data_pengaturan->update('no_paket_sp', $value);
    }
    
    public function getNomorSuratKomdis($tindakan) {
        return $this->CI->data_pengaturan->get_by_id('nomor_surat_komdis_'.$tindakan);
    }
    
    public function getPoinPelanggaranSyariat() {
        return $this->CI->data_pengaturan->get_by_id('poin_pelanggaran_syariat');
    }
    
    public function setNomorSuratKomdis($tindakan, $value) {
        return $this->CI->data_pengaturan->update('nomor_surat_komdis_'.$tindakan, $value);
    }
    
    public function getFormatSurat($nomor, $jenis_surat, $tanggal, $bidang) {
        $bulan = date('n', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        
        return $nomor.'/'. strtoupper($jenis_surat).'/'.$bidang.'-PIM/'.$this->CI->date_format->toRomawi($bulan).'/'. $tahun;
    }
    
    public function getKKM() {
        return $this->CI->data_pengaturan->get_by_id('kkm');
    }
    
    public function getNomorSuratMutasi() {
        return $this->CI->data_pengaturan->get_by_id('nomor_surat_mutasi');
    }
    
    public function setNomorSuratMutasi($value) {
        return $this->CI->data_pengaturan->update('nomor_surat_mutasi', $value);
    }
    
    public function getNomorIjasah() {
        return $this->CI->data_pengaturan->get_by_id('nomor_ijasah');
    }
    
    public function setNomorIjasah($value) {
        return $this->CI->data_pengaturan->update('nomor_ijasah', $value);
    }
    
    public function getNomorSyahadah() {
        return $this->CI->data_pengaturan->get_by_id('nomor_syahadah');
    }
    
    public function setNomorSyahadah($value) {
        return $this->CI->data_pengaturan->update('nomor_syahadah', $value);
    }

    public function getBulanHijriyah() {
        $bulan = array(
            '1' => 'Muharrom',
            '2' => 'Shofar',
            '3' => 'Rabiul Awal',
            '4' => 'Rabiul Akhir',
            '5' => 'Jumadil Awal',
            '6' => 'Jumadil Akhir',
            '7' => 'Rojab',
            '8' => 'Sya\'ban',
            '9' => 'Romadhon',
            '10' => 'Syawal',
            '11' => 'Dzulqo\'dah',
            '12' => 'Dzulhijjah',
        );

        return $bulan;
    }
}
