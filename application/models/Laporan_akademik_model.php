<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_akademik_model extends CI_Model {

    var $table = 'akad_siswa';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($label = NULL, $data_ta = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        if ($label == NULL || $label == 'NAMA_TA' || $label == 'TA_AKTIF' || $label == 'TA_MUTASI')
            $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_AS=mta.ID_TA');
        if ($label == 'BULAN_AKTIF' || $label == 'BULAN_MUTASI') {
            $query1 = "(SELECT 
                            ID_AS, BULAN, STATUS_MUTASI_SISWA, IF(BULAN=TANGGAL_MUTASI,BULAN,NULL) AS BULAN_MUTASI
                        FROM
                            (SELECT 
                                CONCAT(" . $data_ta->ID_TA . ") AS TA_FALSE, DATE_FORMAT(m1, '%Y-%m') AS BULAN
                            FROM
                                (SELECT 
                                ('" . $data_ta->TANGGAL_MULAI_TA . "' - INTERVAL DAYOFMONTH('" . $data_ta->TANGGAL_MULAI_TA . "') - 1 DAY) + INTERVAL m MONTH AS m1
                            FROM
                                (SELECT 
                                @rownum:=@rownum + 1 AS m
                            FROM
                                (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t1, (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t2, (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t3, (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t4, (SELECT @rownum:=- 1) t0) d1) d2
                            WHERE
                                m1 <= '" . $data_ta->TANGGAL_AKHIR_TA . "'
                            ORDER BY m1) aa
                                JOIN
                            (SELECT 
                                ID_SISWA,
                                    NAMA_SISWA,
                                    STATUS_MUTASI_SISWA,
                                    TANGGAL_MUTASI_SISWA,
                                    DATE_FORMAT(TANGGAL_MUTASI_SISWA, '%Y-%m') AS TANGGAL_MUTASI,
                                    NAMA_TA,
                                    NAMA_CAWU,
                                    AWAL_TC,
                                    AKHIR_TC,
                                    ID_TA,
                                    ID_CAWU,
                                    ID_AS,
                                    TA_AS
                            FROM
                                simapes.md_siswa
                            JOIN akad_siswa ON ID_SISWA = SISWA_AS
                            LEFT JOIN (SELECT 
                                NAMA_TA, NAMA_CAWU, AWAL_TC, AKHIR_TC, ID_TA, ID_CAWU
                            FROM
                                md_tanggal_cawu
                            JOIN md_tahun_ajaran ON ID_TA = TA_TC
                            JOIN md_catur_wulan ON ID_CAWU = CAWU_TC) tc ON TANGGAL_MUTASI_SISWA >= AWAL_TC
                                AND TANGGAL_MUTASI_SISWA <= AKHIR_TC
                                AND TA_AS = ID_TA) bb ON TA_AS = TA_FALSE)";
            $this->db->join($query1 . ' zz', $this->table . '.ID_AS=zz.ID_AS');
        }
        if ($label == 'CAWU_AKTIF' || $label == 'CAWU_MUTASI') {
            $query1 = "(SELECT 
                            ID_AS,
                            yy.ID_SISWA AS PURE_ID_SISWA,
                            yy.NAMA_SISWA AS PURE_NAMA_SISWA,
                            ta.NAMA_TA AS PURE_NAMA_TA,
                            cw.NAMA_CAWU AS PURE_NAMA_CAWU,
                            ta.ID_TA AS PURE_ID_TA,
                            cw.ID_CAWU AS PURE_ID_CAWU,
                            CONCAT(ta.NAMA_TA, ' - ', cw.NAMA_CAWU) AS TA_CAWU,
                            xx.*
                        FROM
                            simapes.akad_siswa
                                JOIN
                            md_siswa yy ON SISWA_AS = yy.ID_SISWA
                                JOIN
                            md_tahun_ajaran ta ON ta.ID_TA = TA_AS
                                JOIN
                            md_tanggal_cawu tc ON tc.TA_TC = TA_AS
                                JOIN
                            md_catur_wulan cw ON cw.ID_CAWU = tc.CAWU_TC
                                LEFT JOIN
                            (SELECT 
                                ID_SISWA,
                                    NAMA_SISWA,
                                    STATUS_MUTASI_SISWA,
                                    TANGGAL_MUTASI_SISWA,
                                    NAMA_TA,
                                    NAMA_CAWU,
                                    AWAL_TC,
                                    AKHIR_TC,
                                    ID_TA,
                                    ID_CAWU
                            FROM
                                simapes.md_siswa
                            JOIN akad_siswa ON ID_SISWA = SISWA_AS
                            LEFT JOIN (SELECT 
                                NAMA_TA, NAMA_CAWU, AWAL_TC, AKHIR_TC, ID_TA, ID_CAWU
                            FROM
                                md_tanggal_cawu
                            JOIN md_tahun_ajaran ON ID_TA = TA_TC
                            JOIN md_catur_wulan ON ID_CAWU = CAWU_TC) tc ON TANGGAL_MUTASI_SISWA >= AWAL_TC
                                AND TANGGAL_MUTASI_SISWA <= AKHIR_TC
                                AND TA_AS = ID_TA) xx ON SISWA_AS = xx.ID_SISWA AND xx.ID_TA = TA_AS AND xx.ID_CAWU = cw.ID_CAWU
                        WHERE
                            KONVERSI_AS = 0
                            ORDER BY ta.ID_TA ASC, cw.ID_CAWU ASC)";
            $this->db->join($query1 . ' zz', $this->table . '.ID_AS=zz.ID_AS');
        }
        if ($label == NULL || $label == 'mt.KETERANGAN_TINGK')
            $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
        if ($label == NULL || $label == 'NAMA_KELAS')
            $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
        if ($label == NULL || $label == 'NAMA_SUKU')
            $this->db->join('md_suku msk', 'ms.SUKU_SISWA=msk.ID_SUKU', 'LEFT');
        if ($label == NULL || $label == 'NAMA_AGAMA')
            $this->db->join('md_agama mag', 'ms.AGAMA_SISWA=mag.ID_AGAMA', 'LEFT');
        if ($label == NULL || $label == 'NAMA_KONDISI')
            $this->db->join('md_kondisi mkd', 'ms.KONDISI_SISWA=mkd.ID_KONDISI', 'LEFT');
        if ($label == NULL || $label == 'mdt.KETERANGAN_TINGK') {
            $this->db->join('md_jenjang_sekolah mjs', 'ms.MASUK_JENJANG_SISWA=mjs.ID_JS', 'LEFT');
            $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD', 'LEFT');
            $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK=' . 'ms.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD', 'LEFT');
        }
        if ($label == NULL || $label == 'NAMA_JK')
            $this->db->join('md_jenis_kelamin mjk', 'ms.JK_SISWA=mjk.ID_JK', 'LEFT');
        if ($label == NULL || $label == 'NAMA_WARGA')
            $this->db->join('md_kewarganegaraan mkw', 'ms.WARGA_SISWA=mkw.ID_WARGA', 'LEFT');
        if ($label == NULL || $label == 'NAMA_DARAH')
            $this->db->join('md_golongan_darah mgd', 'ms.GOL_DARAH_SISWA=mgd.ID_DARAH', 'LEFT');
        if ($label == NULL || $label == 'NAMA_TEMTING')
            $this->db->join('md_tempat_tinggal mtt', 'ms.TEMPAT_TINGGAL_SISWA=mtt.ID_TEMTING', 'LEFT');
        if ($label == NULL || $label == 'NAMA_PONDOK_MPS')
            $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        if ($label == NULL || $label == 'moha.NAMA_SO')
            $this->db->join('md_ortu_hidup moha', 'ms.AYAH_HIDUP_SISWA=moha.ID_SO', 'LEFT');
        if ($label == NULL || $label == 'mjpa.NAMA_JP')
            $this->db->join('md_jenjang_pendidikan mjpa', 'ms.AYAH_PENDIDIKAN_SISWA=mjpa.ID_JP', 'LEFT');
        if ($label == NULL || $label == 'mjpa.NAMA_JENPEK')
            $this->db->join('md_pekerjaan mpka', 'ms.AYAH_PEKERJAAN_SISWA=mpka.ID_JENPEK', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_SO')
            $this->db->join('md_ortu_hidup mohi', 'ms.IBU_HIDUP_SISWA=mohi.ID_SO', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_JP')
            $this->db->join('md_jenjang_pendidikan mjpi', 'ms.IBU_PENDIDIKAN_SISWA=mjpi.ID_JP', 'LEFT');
        if ($label == NULL || $label == 'mohi.NAMA_JENPEK')
            $this->db->join('md_pekerjaan mpki', 'ms.IBU_PEKERJAAN_SISWA=mpki.ID_JENPEK', 'LEFT');
        if ($label == NULL || $label == 'NAMA_HASIL')
            $this->db->join('md_penghasilan mpg', 'ms.ORTU_PENGHASILAN_SISWA=mpg.ID_HASIL', 'LEFT');
        if ($label == NULL || $label == 'NAMA_MUTASI')
            $this->db->join('md_status_mutasi msm', 'ms.STATUS_MUTASI_SISWA=msm.ID_MUTASI', 'LEFT');
        if ($label == NULL || $label == 'NAMA_KEC' || $label == 'NAMA_KAB' || $label == 'NAMA_PROV') {
            $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
            $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
            $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        }
    }

    public function get_data($label, $ta, $tingkat, $kelas, $jk) {
        if ($ta == null)
            $id_ta = $this->session->userdata('ID_TA_ACTIVE');
        else
            $id_ta = $ta;

        $data_ta = $this->db_handler->get_row('md_tahun_ajaran', [
            'where' => [
                'ID_TA' => $id_ta
            ]
        ]);

        if ($label == NULL || $label == 'AKTIF_AS') {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), IF(' . $label . ' = 1, "AKTIF", CONCAT("TIDAK", " ", "AKTIF")) ) AS x_label');
        }

        if ($label == 'KONVERSI_AS') {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), IF(' . $label . ' = 1, "KONVERSI", CONCAT("TIDAK", " ", "KONVERSI")) ) AS x_label');
        } elseif ($label == 'TANGGAL_LAHIR_SISWA') {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), (YEAR(CURDATE()) - LEFT(' . $label . ', 4))) AS x_label');
        } elseif ($label == 'TA_AKTIF' || $label == 'TA_MUTASI') {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(NAMA_TA IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), NAMA_TA) AS x_label');
        } elseif ($label == 'CAWU_AKTIF' || $label == 'CAWU_MUTASI') {
            $this->db->select('COUNT(' . $this->table . '.ID_AS) AS data, IF(TA_CAWU IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), TA_CAWU) AS x_label');
        } elseif ($label == 'BULAN_AKTIF' || $label == 'BULAN_MUTASI') {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(BULAN IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), BULAN) AS x_label');
        } else {
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), ' . $label . ') AS x_label');
        }

        $this->_get_table($label, $data_ta);

        $this->db->where('KONVERSI_AS', 0);
        if ($ta != "") {
            $this->db->where('TA_AS', $ta);
            $this->db->where('AKTIF_AS', 1);
        }
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($jk != "")
            $this->db->where('JK_SISWA', $jk);
        if ($label != 'KONVERSI_AS')
            $this->db->where('KONVERSI_AS', 0);

        if ($label == 'TANGGAL_LAHIR_SISWA') {
            $this->db->group_by('LEFT(' . $label . ', 4)');
        } elseif ($label == 'TA_AKTIF') {
            $this->db->where('(STATUS_MUTASI_SISWA IS NULL)', null, FALSE);
            $this->db->group_by('NAMA_TA');
        } elseif ($label == 'TA_MUTASI') {
            $this->db->where('TANGGAL_MUTASI_SISWA IS NOT NULL', null, false);
            $this->db->where('STATUS_MUTASI_SISWA <> 99', null, false);
            $this->db->group_by('NAMA_TA');
        } elseif ($label == 'BULAN_AKTIF') {
            $this->db->where('(zz.BULAN_MUTASI IS NULL)', null, FALSE);
            $this->db->group_by('BULAN');
        } elseif ($label == 'BULAN_MUTASI') {
            $this->db->where('BULAN_MUTASI IS NOT NULL', null, FALSE);
            $this->db->where(' zz.STATUS_MUTASI_SISWA <> 99', null, FALSE);
            $this->db->group_by('BULAN');
        } elseif ($label == 'CAWU_AKTIF') {
            $this->db->where('(zz.ID_SISWA IS NULL)', null, FALSE);
            $this->db->group_by('TA_CAWU');
        } elseif ($label == 'CAWU_MUTASI') {
            $this->db->where('zz.ID_SISWA IS NOT NULL', NULL, FALSE);
            $this->db->where(' zz.STATUS_MUTASI_SISWA <> 99', null, FALSE);
            $this->db->group_by('TA_CAWU');
        } else {
            $this->db->group_by($label);
        }

        $this->db->order_by('x_label', 'ASC');
        $result = $this->db->get();
//        echo $this->db->last_query();exit();

        return $result->result();
    }

    public function export_data($ta, $tingkat, $kelas, $jk) {
        $this->load->dbutil();
        $this->_get_table();
        $this->db->join('md_hobi mhi', 'ms.HOBI_SISWA=mhi.ID_HOBI', 'LEFT');
        $this->db->join('md_status_mutasi msmt', 'ms.STATUS_MUTASI_SISWA=msmt.ID_MUTASI', 'LEFT');
        $this->db->join('md_asal_santri masan', 'ms.STATUS_ASAL_SISWA=masan.MD_ASSAN', 'LEFT');
        $this->db->join('md_asal_sekolah as', 'ms.ASAL_SEKOLAH_SISWA=as.ID_AS', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjsas', 'as.JENJANG_AS=mjsas.ID_JS', 'LEFT');
        $this->db->join('md_kecamatan kecas', 'as.KECAMATAN_AS=kecas.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabas', 'kecas.KABUPATEN_KEC=kabas.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provas', 'kabas.PROVINSI_KAB=provas.ID_PROV', 'LEFT');
        $this->db->join('md_hubungan mhb', 'ms.WALI_HUBUNGAN_SISWA=mhb.ID_HUB', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpw', 'ms.WALI_PENDIDIKAN_SISWA=mjpw.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpkw', 'ms.WALI_PEKERJAAN_SISWA=mpkw.ID_JENPEK', 'LEFT');
        $this->db->join('md_kecamatan keco', 'ms.ORTU_KECAMATAN_SISWA=keco.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabo', 'keco.KABUPATEN_KEC=kabo.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provo', 'kabo.PROVINSI_KAB=provo.ID_PROV', 'LEFT');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG', 'LEFT');

        $select_nis = '';
        if ($ta != "") {
            $this->db->where('TA_AS', $ta);
            $this->db->join('md_nis mnis', 'mnis.SISWA_NIS=ID_SISWA AND TA_NIS=' . $ta, 'LEFT');
            $select_nis = ',NIS_NIS AS NIS_LAMA';
        }
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($jk != "")
            $this->db->where('JK_SISWA', $jk);


        $this->db->select(''
                . 'NIS_SISWA AS NIS'
                . ',NIS_EMIS_SISWA AS NIS_EMIS'
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
                . ',NO_ABSEN_AS AS NOMOR_ABSEN'
                . ',mt.KETERANGAN_TINGK AS JENJANG_TINGKAT_SEKARANG'
                . ',mp.NIP_PEG AS NIP_WALI_KELAS'
                . ',mp.NAMA_PEG AS NAMA_WALI_KELAS'
                . $select_nis
                . '');

        $sql = $this->db->get();

        return $this->dbutil->csv_from_result($sql);
    }

}
