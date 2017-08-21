<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_siswa_model extends CI_Model {

    var $table = 'md_siswa';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($label = NULL) {
        $this->db->from($this->table);
        if ($label == NULL || $label == 'NAMA_SUKU')
            $this->db->join('md_suku msk', $this->table . '.SUKU_SISWA=msk.ID_SUKU', 'LEFT');
        if ($label == NULL || $label == 'NAMA_AGAMA')
            $this->db->join('md_agama mag', $this->table . '.AGAMA_SISWA=mag.ID_AGAMA', 'LEFT');
        if ($label == NULL || $label == 'NAMA_KONDISI')
            $this->db->join('md_kondisi mkd', $this->table . '.KONDISI_SISWA=mkd.ID_KONDISI', 'LEFT');
        if ($label == NULL || $label == 'KETERANGAN_TINGK') {
            $this->db->join('md_jenjang_sekolah mjs', $this->table . '.MASUK_JENJANG_SISWA=mjs.ID_JS', 'LEFT');
            $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD', 'LEFT');
            $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK=' . $this->table . '.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD', 'LEFT');
        }
        if ($label == NULL || $label == 'NAMA_JK')
            $this->db->join('md_jenis_kelamin mjk', $this->table . '.JK_SISWA=mjk.ID_JK', 'LEFT');
        if ($label == NULL || $label == 'NAMA_WARGA')
            $this->db->join('md_kewarganegaraan mkw', $this->table . '.WARGA_SISWA=mkw.ID_WARGA', 'LEFT');
        if ($label == NULL || $label == 'NAMA_DARAH')
            $this->db->join('md_golongan_darah mgd', $this->table . '.GOL_DARAH_SISWA=mgd.ID_DARAH', 'LEFT');
        if ($label == NULL || $label == 'NAMA_TEMTING')
            $this->db->join('md_tempat_tinggal mtt', $this->table . '.TEMPAT_TINGGAL_SISWA=mtt.ID_TEMTING', 'LEFT');
        if ($label == NULL || $label == 'NAMA_PONDOK_MPS')
            $this->db->join('md_pondok_siswa mps', $this->table . '.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        if ($label == NULL || $label == 'moha.NAMA_SO')
            $this->db->join('md_ortu_hidup moha', $this->table . '.AYAH_HIDUP_SISWA=moha.ID_SO', 'LEFT');
        if ($label == NULL || $label == 'mjpa.NAMA_JP')
            $this->db->join('md_jenjang_pendidikan mjpa', $this->table . '.AYAH_PENDIDIKAN_SISWA=mjpa.ID_JP', 'LEFT');
        if ($label == NULL || $label == 'mjpa.NAMA_JENPEK')
            $this->db->join('md_pekerjaan mpka', $this->table . '.AYAH_PEKERJAAN_SISWA=mpka.ID_JENPEK', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_SO')
            $this->db->join('md_ortu_hidup mohi', $this->table . '.IBU_HIDUP_SISWA=mohi.ID_SO', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_JP')
            $this->db->join('md_jenjang_pendidikan mjpi', $this->table . '.IBU_PENDIDIKAN_SISWA=mjpi.ID_JP', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_JENPEK')
            $this->db->join('md_pekerjaan mpki', $this->table . '.IBU_PEKERJAAN_SISWA=mpki.ID_JENPEK', 'LEFT');
        if ($label == NULL || $label == 'NAMA_HASIL')
            $this->db->join('md_penghasilan mpg', $this->table . '.ORTU_PENGHASILAN_SISWA=mpg.ID_HASIL', 'LEFT');
        if ($label == NULL || $label == 'NAMA_MUTASI')
            $this->db->join('md_status_mutasi msm', $this->table . '.STATUS_MUTASI_SISWA=msm.ID_MUTASI', 'LEFT');
        if ($label == NULL || $label == 'NAMA_KEC' || $label == 'NAMA_KAB' || $label == 'NAMA_PROV') {
            $this->db->join('md_kecamatan kec', $this->table . '.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
            $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
            $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        }
    }

    public function get_data($label, $keaktifan) {
        if ($label == 'AKTIF_SISWA')
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), IF(' . $label . ' = 1, "AKTIF", CONCAT("TIDAK", " ", "AKTIF")) ) AS x_label');
        elseif ($label == 'TANGGAL_LAHIR_SISWA')
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), (YEAR(CURDATE()) - LEFT(' . $label . ', 4))) AS x_label');
        elseif ($label == 'NAMA_MUTASI') {
            $this->db->select('COUNT(ID_SISWA) AS data, ' . $label . ' AS x_label');
            $this->db->from('md_status_mutasi msm');
            $this->db->join($this->table, 'STATUS_MUTASI_SISWA=ID_MUTASI', 'LEFT');
        } else
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), ' . $label . ') AS x_label');

        if ($label != 'NAMA_MUTASI') 
            $this->_get_table($label);

        if ($keaktifan != "")
            $this->db->where('AKTIF_SISWA', $keaktifan);

        if ($label == 'TANGGAL_LAHIR_SISWA')
            $this->db->group_by('LEFT(' . $label . ', 4)');
        else
            $this->db->group_by($label);

        $this->db->order_by('x_label', 'ASC');
        $result = $this->db->get();

        return $result->result();
    }

    public function export_data($keaktifan) {
        $this->load->dbutil();

        $this->db->select(''
                . 'NIS_SISWA AS NIS'
                . ',NISN_SISWA AS NISN'
                . ',NIK_SISWA AS NIK'
                . ',NO_UN_SISWA AS NOMOR_UN'
                . ',NO_UM_SISWA AS NOMOR_URUT_UJIAN_MASUK'
                . ',NAMA_SISWA AS NAMA'
                . ',PANGGILAN_SISWA AS PANGGILAN'
                . ',AKTIF_SISWA AS STATUS_KEAKTIFAN'
                . ',ANGKATAN_SISWA AS ANGKATAN'
                . ',SUKU_SISWA AS KODE_SUKU'
                . ',NAMA_SUKU AS NAMA_SUKU'
                . ',AGAMA_SISWA AS KODE_AGAMA'
                . ',NAMA_AGAMA AS NAMA_AGAMA'
                . ',KONDISI_SISWA AS KODE_KONDISI'
                . ',NAMA_KONDISI AS NAMA_KONDISI'
                . ',JK_SISWA AS KODE_JENIS_KELAMIN'
                . ',NAMA_JK AS NAMA_JENIS_KELAMIN'
                . ',TEMPAT_LAHIR_SISWA AS TEMPAT_LAHIR'
                . ',TANGGAL_LAHIR_SISWA AS TANGGAL_LAHIR'
                . ',WARGA_SISWA AS KODE_KEWARGANEGARAAN'
                . ',NAMA_WARGA AS NAMA_KEWARGANEGARAAN'
                . ',ANAK_KE_SISWA AS ANAK_KE'
                . ',JUMLAH_SDR_SISWA AS JUMLAH_SAUDARA'
                . ',BERAT_SISWA AS BERAT_BADAN'
                . ',TINGGI_SISWA AS TINGGI_BADAN'
                . ',NAMA_DARAH AS GOLONGAN_DARAH'
                . ',ALAMAT_SISWA AS ALAMAT'
                . ',kec.ID_KEC AS KODE_KECAMATAN'
                . ',kec.NAMA_KEC AS NAMA_KECAMATAN'
                . ',kab.ID_KAB AS KODE_KABUPATEN'
                . ',kab.NAMA_KAB AS NAMA_KABUPATEN'
                . ',prov.ID_PROV AS KODE_PROVINSI'
                . ',prov.NAMA_PROV AS NAMA_PROVINSI'
                . ',KODE_POS_SISWA AS KODEPOS'
                . ',ID_TEMTING AS KODE_TEMPAT_TINGGAL'
                . ',NAMA_TEMTING AS NAMA_TEMPAT_TINGGAL'
                . ',NAMA_PONDOK_MPS AS NAMA_PONDOK'
                . ',PENGASUH_MPS AS PENGASUH_PONDOK'
                . ',ALAMAT_MPS AS ALAMAT_PONDOK'
                . ',TELP_MPS AS TELP_PONDOK'
                . ',NOHP_SISWA AS NO_HP'
                . ',EMAIL_SISWA AS EMAIL'
                . ',RIWAYAT_KESEHATAN_SISWA AS RIWAYAT_KESEHATAN'
                . ',mjsas.NAMA_JS AS JENJANG_ASAL_SEKOLAH'
                . ',KECAMATAN_AS AS KECAMATAN_ASAL_SEKOLAH'
                . ',kabas.NAMA_KAB AS KABUPATEN_ASAL_SEKOLAH'
                . ',provas.NAMA_PROV AS PROVINSI_ASAL_SEKOLAH'
                . ',mdt.DEPT_TINGK AS JENJANG_MASUK'
                . ',mdt.KETERANGAN_TINGK AS PILIHAN_MASUK'
                . ',NO_IJASAH_SISWA AS NO_IJASAH'
                . ',TANGGAL_IJASAH_SISWA AS TANGGAL_IJASAH'
                . ',AYAH_NIK_SISWA AS NIK_AYAH'
                . ',AYAH_NAMA_SISWA AS NAMA_AYAH'
                . ',AYAH_HIDUP_SISWA AS STATUS_HIDUP_AYAH'
                . ',AYAH_TEMPAT_LAHIR_SISWA AS TEMPAT_LAHIR_AYAH'
                . ',AYAH_TANGGAL_LAHIR_SISWA AS TANGGAL_LAHIR_AYAH'
                . ',mjpa.ID_JP AS KODE_JENJANG_PENDIDIKAN_AYAH'
                . ',mjpa.NAMA_JP AS NAMA_JENJANG_PENDIDIKAN_AYAH'
                . ',mpka.ID_JENPEK AS KODE_JENIS_PEKERJAAN_AYAH'
                . ',mpka.NAMA_JENPEK AS NAMA_JENIS_PEKERJAAN_AYAH'
                . ',IBU_NIK_SISWA AS NIK_IBU'
                . ',IBU_NAMA_SISWA AS NAMA_IBU'
                . ',IBU_HIDUP_SISWA AS STATUS_HIDUP_IBU'
                . ',IBU_TEMPAT_LAHIR_SISWA AS TEMPAT_LAHIR_IBU'
                . ',IBU_TANGGAL_LAHIR_SISWA AS TANGGAL_LAHIR_IBU'
                . ',mjpi.ID_JP AS KODE_JENJANG_PENDIDIKAN_IBU'
                . ',mjpi.NAMA_JP AS NAMA_JENJANG_PENDIDIKAN_IBU'
                . ',mpki.ID_JENPEK AS KODE_JENIS_PEKERJAAN_IBU'
                . ',mpki.NAMA_JENPEK AS NAMA_JENIS_PEKERJAAN_IBU'
                . ',WALI_NIK_SISWA AS NIK_WALI'
                . ',WALI_NAMA_SISWA AS NAMA_WALI'
                . ',mhb.ID_HUB AS KODE_HUBUNGAN_WALI'
                . ',mhb.NAMA_HUB AS NAMA_HUBUNGAN_WALI'
                . ',mjpw.ID_JP AS KODE_JENJANG_PENDIDIKAN_WALI'
                . ',mjpw.NAMA_JP AS NAMA_JENJANG_PENDIDIKAN_WALI'
                . ',mpkw.ID_JENPEK AS KODE_JENIS_PEKERJAAN_WALI'
                . ',mpkw.NAMA_JENPEK AS NAMA_JENIS_PEKERJAAN_WALI'
                . ',ORTU_ALAMAT_SISWA AS ALAMAT_ORANG_TUA'
                . ',keco.ID_KEC AS KODE_KECAMATAN_ORANG_TUA'
                . ',keco.NAMA_KEC AS NAMA_KECAMATAN_ORANG_TUA'
                . ',kabo.ID_KAB AS KODE_KABUPATEN_ORANG_TUA'
                . ',kabo.NAMA_KAB AS NAMA_KABUPATEN_ORANG_TUA'
                . ',provo.ID_PROV AS KODE_PROVINSI_ORANG_TUA'
                . ',provo.NAMA_PROV AS NAMA_PROVINSI_ORANG_TUA'
                . ',mpg.ID_HASIL AS KODE_PENGHASILAN_ORANG_TUA'
                . ',mpg.NAMA_HASIL AS NAMA_PENGHASILAN_ORANG_TUA'
                . ',ORTU_NOHP1_SISWA AS NO_HP_ORANG_TUA_1'
                . ',ORTU_NOHP2_SISWA AS NO_HP_ORANG_TUA_2'
                . ',ORTU_NOHP3_SISWA AS NO_HP_ORANG_TUA_3'
                . ',ORTU_EMAIL_SISWA AS EMAIL_ORANG_TUA'
                . ',ID_HOBI AS KODE_HOBI'
                . ',NAMA_HOBI AS NAMA_HOBI'
                . ',msmt.ID_MUTASI AS KODE_MUTASI'
                . ',msmt.NAMA_MUTASI AS NAMA_MUTASI'
                . ',TANGGAL_MUTASI_SISWA AS TANGGAL_MUTASI'
                . ',masan.MD_ASSAN AS KODE_ASAL_SANTRI'
                . ',masan.NAMA_ASSAN AS NAMA_ASAL_SANTRI'
                . ',ak.NAMA_KELAS AS KELAS_SEKARANG'
                . ',asw.NO_ABSEN_AS AS NOMOR_ABSEN'
                . ',mtnow.KETERANGAN_TINGK AS JENJANG_TINGKAT_SEKARANG'
                . ',mp.NIP_PEG AS NIP_WALI_KELAS'
                . ',mp.NAMA_PEG AS NAMA_WALI_KELAS'
                . '');
        $this->_get_table();
        $this->db->join('md_hobi mhi', $this->table . '.HOBI_SISWA=mhi.ID_HOBI', 'LEFT');
        $this->db->join('md_status_mutasi msmt', $this->table . '.STATUS_MUTASI_SISWA=msmt.ID_MUTASI', 'LEFT');
        $this->db->join('md_asal_santri masan', $this->table . '.STATUS_ASAL_SISWA=masan.MD_ASSAN', 'LEFT');
        $this->db->join('md_asal_sekolah as', $this->table . '.ASAL_SEKOLAH_SISWA=as.ID_AS', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjsas', 'as.JENJANG_AS=mjsas.ID_JS', 'LEFT');
        $this->db->join('md_kecamatan kecas', 'as.KECAMATAN_AS=kecas.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabas', 'kecas.KABUPATEN_KEC=kabas.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provas', 'kabas.PROVINSI_KAB=provas.ID_PROV', 'LEFT');
        $this->db->join('md_hubungan mhb', $this->table . '.WALI_HUBUNGAN_SISWA=mhb.ID_HUB', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpw', $this->table . '.WALI_PENDIDIKAN_SISWA=mjpw.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpkw', $this->table . '.WALI_PEKERJAAN_SISWA=mpkw.ID_JENPEK', 'LEFT');
        $this->db->join('md_kecamatan keco', $this->table . '.ORTU_KECAMATAN_SISWA=keco.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabo', 'keco.KABUPATEN_KEC=kabo.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provo', 'kabo.PROVINSI_KAB=provo.ID_PROV', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_tahun_ajaran mta', 'ak.TA_KELAS=mta.ID_TA', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->join('md_departemen mdp', 'mtnow.DEPT_TINGK=mdp.ID_DEPT', 'LEFT');

        if ($keaktifan != "")
            $this->db->where('AKTIF_SISWA', $keaktifan);

        $sql = $this->db->get();

        return $this->dbutil->csv_from_result($sql);
    }

}
