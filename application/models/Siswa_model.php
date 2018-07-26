<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Siswa_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NO_ABSEN_AS','NIS_SISWA', 'NAMA_SISWA', 'ANGKATAN_SISWA', 'JK_SISWA', 'TEMPAT_LAHIR_SISWA', 'TANGGAL_LAHIR_SISWA', 'CONCAT(ALAMAT_SISWA, CONCAT(", ","Kec "), NAMA_KEC, ", ", NAMA_KAB, CONCAT(", ","Prov "), NAMA_PROV)', 'IF(NAMA_PONDOK_MPS IS NULL, "-", NAMA_PONDOK_MPS)', 'IF(NAMA_KELAS IS NULL, "-", NAMA_KELAS)', 'ID_SISWA');
    var $primary_key = "ID_SISWA";
    var $order = array("ID_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ALL = FALSE) {
        $this->db->select('*, moha.NAMA_SO AS NAMA_SO_AYAH, mohi.NAMA_SO AS NAMA_SO_IBU, mjpa.NAMA_JP AS NAMA_JP_AYAH, mjpi.NAMA_JP AS NAMA_JP_IBU, , mjpw.NAMA_JP AS NAMA_JP_WALI, mpka.NAMA_JENPEK AS NAMA_JENPEK_AYAH, mpki.NAMA_JENPEK AS NAMA_JENPEK_IBU, mpkw.NAMA_JENPEK AS NAMA_JENPEK_WALI, kec.NAMA_KEC AS NAMA_KEC_SISWA, kab.NAMA_KAB AS NAMA_KAB_SISWA, prov.NAMA_PROV AS NAMA_PROV_SISWA, keco.NAMA_KEC AS NAMA_KEC_ORTU, kabo.NAMA_KAB AS NAMA_KAB_ORTU, provo.NAMA_PROV AS NAMA_PROV_ORTU, mtnow.ID_TINGK AS ID_TINGK_NOW, mtnow.DEPT_TINGK AS DEPT_TINGK_NOW, mtnow.NAMA_TINGK AS NAMA_TINGK_NOW, mtnow.KETERANGAN_TINGK AS KETERANGAN_TINGK_NOW, mdp.NAMA_DEPT AS NAMA_DEPT_NOW, mdt.ID_TINGK AS ID_TINGK_PSB, mdt.DEPT_TINGK AS DEPT_TINGK_PSB, mdt.NAMA_TINGK AS NAMA_TINGK_PSB, mdt.KETERANGAN_TINGK AS KETERANGAN_TINGK_PSB, mdpp.NAMA_DEPT AS NAMA_DEPT_PSB');
        $this->db->from($this->table);
        $this->db->join('md_asal_sekolah as', $this->table . '.ASAL_SEKOLAH_SISWA=as.ID_AS', 'LEFT');
        $this->db->join('md_suku msk', $this->table . '.SUKU_SISWA=msk.ID_SUKU', 'LEFT');
        $this->db->join('md_agama mag', $this->table . '.AGAMA_SISWA=mag.ID_AGAMA', 'LEFT');
        $this->db->join('md_kondisi mkd', $this->table . '.KONDISI_SISWA=mkd.ID_KONDISI', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjs', $this->table . '.MASUK_JENJANG_SISWA=mjs.ID_JS', 'LEFT');
        $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD', 'LEFT');
        $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK=' . $this->table . '.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD', 'LEFT');
        $this->db->join('md_departemen mdpp', 'mdt.DEPT_TINGK=mdpp.ID_DEPT', 'LEFT');
        $this->db->join('md_jenis_kelamin mjk', $this->table . '.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kewarganegaraan mkw', $this->table . '.WARGA_SISWA=mkw.ID_WARGA', 'LEFT');
        $this->db->join('md_golongan_darah mgd', $this->table . '.GOL_DARAH_SISWA=mgd.ID_DARAH', 'LEFT');
        $this->db->join('md_asal_santri mas', $this->table . '.STATUS_ASAL_SISWA=mas.MD_ASSAN', 'LEFT');
        $this->db->join('md_status_kk mskk', $this->table . '.STATUS_KK_SISWA=mskk.ID_SKK', 'LEFT');
        $this->db->join('md_hobi mhi', $this->table . '.HOBI_SISWA=mhi.ID_HOBI', 'LEFT');
        $this->db->join('md_cita mct', $this->table . '.CITA_SISWA=mct.ID_CITA', 'LEFT');
        $this->db->join('md_tempat_tinggal mtt', $this->table . '.TEMPAT_TINGGAL_SISWA=mtt.ID_TEMTING', 'LEFT');
        $this->db->join('md_pondok_siswa mps', $this->table . '.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('md_ortu_hidup moha', $this->table . '.AYAH_HIDUP_SISWA=moha.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpa', $this->table . '.AYAH_PENDIDIKAN_SISWA=mjpa.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpka', $this->table . '.AYAH_PEKERJAAN_SISWA=mpka.ID_JENPEK', 'LEFT');
        $this->db->join('md_ortu_hidup mohi', $this->table . '.IBU_HIDUP_SISWA=mohi.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpi', $this->table . '.IBU_PENDIDIKAN_SISWA=mjpi.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpki', $this->table . '.IBU_PEKERJAAN_SISWA=mpki.ID_JENPEK', 'LEFT');
        $this->db->join('md_hubungan mhb', $this->table . '.WALI_HUBUNGAN_SISWA=mhb.ID_HUB', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpw', $this->table . '.WALI_PENDIDIKAN_SISWA=mjpw.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpkw', $this->table . '.WALI_PEKERJAAN_SISWA=mpkw.ID_JENPEK', 'LEFT');
        $this->db->join('md_penghasilan mpg', $this->table . '.ORTU_PENGHASILAN_SISWA=mpg.ID_HASIL', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table . '.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_kecamatan keco', $this->table . '.ORTU_KECAMATAN_SISWA=keco.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabo', 'keco.KABUPATEN_KEC=kabo.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provo', 'kabo.PROVINSI_KAB=provo.ID_PROV', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0 AND asw.AKTIF_AS=1 ', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_tahun_ajaran mta', 'ak.TA_KELAS=mta.ID_TA', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->join('md_departemen mdp', 'mtnow.DEPT_TINGK=mdp.ID_DEPT', 'LEFT');
        $this->db->join('md_status_mutasi msmt', $this->table . '.STATUS_MUTASI_SISWA=msmt.ID_MUTASI', 'LEFT');
        if ($ALL)
            $this->db->where(array(
                'STATUS_MUTASI_SISWA' => NULL
            ));
        else
            $this->db->where(array(
                'AKTIF_SISWA' => 1
            ));
    }

    private function _get_table_simple($ALL = FALSE, $select = true) {
        if ($select)
            $this->db->select('*, IF(NAMA_KELAS IS NULL, "-", NAMA_KELAS) AS NAMA_KELAS_SHOW, IF(NAMA_PONDOK_MPS IS NULL, "-", NAMA_PONDOK_MPS) AS NAMA_PONDOK_MPS_SHOW, CONCAT(ALAMAT_SISWA, CONCAT(", ","Kec "), NAMA_KEC, ", ", NAMA_KAB, CONCAT(", ","Prov "), NAMA_PROV) AS ALAMAT_SISWA_SHOW');
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table . '.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table . '.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_pondok_siswa mps', $this->table . '.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0 AND asw.AKTIF_AS=1 ', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        if ($ALL)
            $this->db->where(array(
                'STATUS_MUTASI_SISWA' => NULL
            ));
        else
            $this->db->where(array(
                'AKTIF_SISWA' => 1
            ));
    }

    private function _get_datatables_query() {
        $this->_get_table_simple();

        $i = 0;
        $search_value = $_POST['search']['value'];
        $search_columns = $_POST['columns'];
        foreach ($this->column as $item) {
            if ($search_value || $search_columns) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }
        $i = 0;
        foreach ($this->column as $item) {
            if ($search_columns) {
                if ($i === 0)
                    $this->db->group_start();
                $this->db->like($item, $search_columns[$i]['search']['value']);
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }

        if (isset($_POST['order'])) {
            $column[6] = 'ALAMAT_SISWA_SHOW';
            $column[7] = 'NAMA_PONDOK_MPS_SHOW';
            $column[8] = 'NAMA_KELAS_SHOW';
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    // MODEL UNTUK SISWA
    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_by_id($id, $simple = FALSE) {
        if ($simple)
            $this->db->from($this->table);
        else
            $this->_get_table(TRUE);

        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_by_id_simple($id) {
        $this->db->from($this->table);
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_tahun_ajaran mta', 'ak.TA_KELAS=mta.ID_TA', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_by_id_mutasi($id) {
        $this->db->from($this->table);
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function count_all($check = NULL) {
        $this->db->from($this->table);

        if ($check !== NULL)
            $this->db->where($check['name'], $check['value']);

        return $this->db->count_all_results();
    }

    public function get_data_kartu($ID_SISWA, $ID_KELAS = NULL) {
        $this->db->select('*, kec.NAMA_KEC AS NAMA_KEC_SISWA, kab.NAMA_KAB AS NAMA_KAB_SISWA, prov.NAMA_PROV AS NAMA_PROV_SISWA');
        $this->db->from($this->table);
        $this->db->join('md_kecamatan kec', $this->table . '.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table . '.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0 AND asw.AKTIF_AS=1 ', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->where('AKTIF_SISWA', 1);
        if ($ID_SISWA != NULL)
            $this->db->where('ID_SISWA', $ID_SISWA);
        if ($ID_KELAS != NULL)
            $this->db->where('ID_KELAS', $ID_KELAS);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_SISWA as id, CONCAT(IF(NIS_SISWA IS NULL, 'BELUM ADA NIS' , NIS_SISWA), ' - ', NAMA_SISWA) as text");
        $this->db->from($this->table);
        $this->db->like('CONCAT(NIS_SISWA," ",NAMA_SISWA)', $where);
        $this->db->where('STATUS_MUTASI_SISWA', NULL);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_ac_pembayaran($where) {
        $this->db->select("ID_SISWA as id, CONCAT(IF(NIS_SISWA IS NULL, 'BELUM ADA NIS' , NIS_SISWA), ' - ', NAMA_SISWA) as text");
        $this->db->from($this->table);
        $this->db->join('akad_siswa as', $this->table . '.ID_SISWA=as.SISWA_AS', 'LEFT');
        $this->db->join('md_tingkat mt', 'as.TINGKAT_AS=mt.ID_TINGK', 'LEFT');
        $this->db->like('CONCAT(IF(NIS_SISWA IS NULL, \'\' , NIS_SISWA)," ",NAMA_SISWA)', $where);
        $this->db->where(array(
            'STATUS_MUTASI_SISWA' => NULL,
            'STATUS_PSB_SISWA' => 1
        ));

        if ($this->session->userdata('TAGIHAN') !== NULL) {
            $keu = json_decode($this->session->userdata('TAGIHAN'));
            $i = 0;
            foreach ($keu as $detail) {
                if ($i == 0) {
                    $this->db->group_start();
                    $this->db->where('(DEPT_TINGK="' . $detail->DEPT_DT . '" AND JK_SISWA="' . $detail->JK_MUK . '")');
                } else {
                    $this->db->or_where('(DEPT_TINGK="' . $detail->DEPT_DT . '" AND JK_SISWA="' . $detail->JK_MUK . '")');
                }

                if ($i == (count($keu) - 1)) {
                    $this->db->group_end();
                }

                $i++;
            }
        }
        $this->db->order_by('NAMA_SISWA', 'ASC');
        $this->db->group_by('ID_SISWA');
        $result = $this->db->get();

        return $result->result();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function update_excel($where, $data) {
        $this->db->update($this->table, $data, $where);
        $result = $this->db->affected_rows();

        if ($result)
            return NULL;
        else
            return $this->db->last_query();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function get_rows_aktif($where, $sortir = NULL) {
        $this->_get_table();
        $this->db->where($where);
        if (is_array($sortir)) {
            foreach ($sortir as $field => $value) {
                $this->db->order_by($field, $value);
            }
        }

        return $this->db->get()->result();
    }

    public function ac_siswa_kelas($where) {
        $this->db->select("ID_SISWA as id, CONCAT('SISWA: ',NIS_SISWA, ' - ', NAMA_SISWA, ' | KELAS: ', NAMA_KELAS) as text");
        $this->db->from('md_siswa ms');
        $this->db->join('akad_siswa as', 'ms.ID_SISWA=as.SISWA_AS AND as.TA_AS=' . $this->session->userdata('ID_TA_ACTIVE') . ' AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1 AND as.KELAS_AS IS NOT NULL');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->like('CONCAT(NIS_SISWA," ",NAMA_SISWA)', $where);
        $this->db->where(array(
            'STATUS_MUTASI_SISWA' => NULL,
            'NIS_SISWA <> ' => NULL,
        ));
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_data_simple($jenjang, $tingkat, $kelas) {
        $this->db->select($this->table . '.*, asw.NO_ABSEN_AS, ak.NAMA_KELAS');
        $this->_get_table_simple(FALSE, FALSE);
        $this->db->join('md_tingkat mting', 'ak.TINGKAT_KELAS=mting.ID_TINGK', 'LEFT');

        if ($jenjang != "")
            $this->db->where('DEPT_TINGK', $jenjang);
        if ($tingkat != "")
            $this->db->where('ID_TINGK', $tingkat);
        if ($kelas != "")
            $this->db->where('ID_KELAS', $kelas);

        return $this->db->get()->result_array();
    }

}
