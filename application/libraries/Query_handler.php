<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Aplikasi Sistem Informasi Akademik (SIAKAD)
 * MTS TBS KUDUS
 * Dibuat oleh Rohmad Eko Wahyudi
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 *
 */

class Query_handler {

    public function query_ayah_wali() {
        return 'IFNULL(AYAH_NAMA_SISWA, WALI_NAMA_SISWA)';
    }

    public function query_jenjang_pim() {
        return 'CONCAT(TINGKAT_PIM_JENJANG," ",NAMA_JS)';
    }

    public function query_mapel_dept() {
        return 'CONCAT(NAMA_DEPT," - ",KODE_MAPEL, " - ", NAMA_MAPEL)';
    }

    public function query_pegawai_nama_gelar() {
        return 'CONCAT(IF(((GELAR_AWAL_PEG IS NULL) OR (GELAR_AWAL_PEG = "")), "", CONCAT(GELAR_AWAL_PEG, ". ")), NAMA_PEG, IF(((GELAR_AKHIR_PEG IS NULL) OR (GELAR_AKHIR_PEG = "")), "", CONCAT(". ", GELAR_AKHIR_PEG)))';
    }

    public function query_pegawai_nip_nama_gelar() {
        return 'CONCAT(NIP_PEG, " - ", IF(((GELAR_AWAL_PEG IS NULL) OR (GELAR_AWAL_PEG = "")), "", CONCAT(GELAR_AWAL_PEG, ". ")), NAMA_PEG, IF(((GELAR_AKHIR_PEG IS NULL) OR (GELAR_AKHIR_PEG = "")), "", CONCAT(". ", GELAR_AKHIR_PEG)))';
    }

    public function query_alamat_lengkap($field_alamat) {
        return 'CONCAT(IF(((' . $field_alamat . ' IS NULL) OR (' . $field_alamat . ' = "")), "", CONCAT(' . $field_alamat . ', ", ")), "Kec. ", NAMA_KEC, ", ", NAMA_KAB, ", ", "Prov. ", NAMA_PROV)';
    }
}
