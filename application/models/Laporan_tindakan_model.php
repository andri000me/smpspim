<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_tindakan_model extends CI_Model {

    var $table = 'komdis_tindakan';
    var $column = array('IF(NIS_SISWA IS NULL, NIS_NIS, NIS_SISWA)', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_KJT', 'IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, ", ", NAMA_KEC, ", ", NAMA_KAB), CONCAT(NAMA_PONDOK_MPS, ", ", ALAMAT_MPS))', 'TANGGAL_KT', 'ID_KT');
    var $primary_key = "ID_KT";
    var $order = array("ID_KT" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('*, IF(NIS_SISWA IS NULL, NIS_NIS, NIS_SISWA) AS NIS_SHOW, CONCAT(NIP_PEG," - ", NAMA_PEG) AS NAMA_PEG, IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, ", ", NAMA_KEC, ", ", NAMA_KAB), CONCAT(NAMA_PONDOK_MPS, ", ", ALAMAT_MPS)) AS DOMISILI_SISWA');
        $this->db->from($this->table);
        $this->db->join('komdis_siswa_header ksh', $this->table . '.PELANGGARAN_HEADER_KT=ksh.ID_KSH');
        $this->db->join('md_tahun_ajaran mta', 'ksh.TA_KSH=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', 'ksh.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', 'ksh.SISWA_KSH=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('md_nis mn', 'ms.ID_SISWA=mn.SISWA_NIS AND mn.TA_NIS=' . $this->session->userdata("ID_TA_ACTIVE"), 'LEFT');
        $this->db->join('akad_siswa asw', 'ms.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS');
        $this->db->join('komdis_jenis_tindakan kjt', $this->table . '.TINDAKAN_KT=kjt.ID_KJT');
        $this->db->join('md_user mu', $this->table . '.PENANGGUNGJAWAB_KT=mu.ID_USER');
        $this->db->join('md_pegawai mp', 'mu.PEGAWAI_USER=mp.ID_PEG');
        $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
    }

    private function _get_table_detail($select = false) {
        $this->db->from($this->table);
        if ($select) {
            $this->db->select('ID_SISWA, NIS_SISWA, NAMA_SISWA,NAMA_KELAS, AYAH_NAMA_SISWA, WALI_NAMA_SISWA, ALAMAT_SISWA, NAMA_KEC, NAMA_KAB, PONDOK_SISWA, NOHP_SISWA, NAMA_PONDOK_MPS, ALAMAT_MPS, POIN_TAHUN_LALU_KSH, POIN_KSH, LARI_KSH, mp.NAMA_PEG AS NAMA_TANGGUNGJAWAB, mpk.GELAR_AWAL_PEG AS GELAR_AWAL_WALI_KELAS, mpk.NOHP_PEG AS NOMOR_HP_WALI_KELAS, mpk.NAMA_PEG AS WALI_KELAS, mpk.GELAR_AKHIR_PEG AS GELAR_AKHIR_WALI_KELAS, ID_DEPT, NAMA_DEPT, TA_KSH, SISWA_KSH, NAMA_KJT, JUMLAH_POIN_KSH, JUMLAH_LARI_KSH, DATA_KT, NOMOR_SURAT_KT, URL_KJT, TANGGAL_KT, NAMA_PROV, mp.GELAR_AWAL_PEG AS GELAR_AWAL_TANGGUNGJAWAB, mp.NAMA_PEG AS NAMA_TANGGUNGJAWAB, mp.GELAR_AKHIR_PEG AS GELAR_AKHIR_TANGGUNGJAWAB, mpk.NAMA_PEG AS WALI_KELAS, TANGGAL_KT');
            $this->db->join('(SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) ksh', $this->table . '.PELANGGARAN_HEADER_KT=ksh.ID_KSH', 'LEFT');
        } else {
            $this->db->select('*, mp.GELAR_AWAL_PEG AS GELAR_AWAL_TANGGUNGJAWAB, mp.NAMA_PEG AS NAMA_TANGGUNGJAWAB, mp.GELAR_AKHIR_PEG AS GELAR_AKHIR_TANGGUNGJAWAB, mpk.NAMA_PEG AS WALI_KELAS');
            $this->db->join('komdis_siswa_header ksh', $this->table . '.PELANGGARAN_HEADER_KT=ksh.ID_KSH', 'LEFT');
        }
        $this->db->join('md_tahun_ajaran mta', 'ksh.TA_KSH=mta.ID_TA', 'LEFT');
        $this->db->join('md_catur_wulan mcw', 'ksh.CAWU_KSH=mcw.ID_CAWU', 'LEFT');
        $this->db->join('md_siswa ms', 'ksh.SISWA_KSH=ms.ID_SISWA', 'LEFT');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa asw', 'ms.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0 AND asw.AKTIF_AS=1 ', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->join('md_departemen mdp', 'mtnow.DEPT_TINGK=mdp.ID_DEPT', 'LEFT');
        $this->db->join('md_pegawai mpk', 'ak.WALI_KELAS=mpk.ID_PEG', 'LEFT');
        $this->db->join('komdis_jenis_tindakan kjt', $this->table . '.TINDAKAN_KT=kjt.ID_KJT', 'LEFT');
//        $this->db->join('md_user mu', $this->table.'.PENANGGUNGJAWAB_KT=mu.ID_USER');
        $this->db->join('md_pegawai mp', $this->table . '.PENANGGUNGJAWAB_KT=mp.ID_PEG', 'LEFT');
    }

    private function _get_datatables_query() {
        $this->_get_table();
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
            $column[0] = 'NIS_SHOW';
            $column[4] = 'DOMISILI_SISWA';
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

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

    public function get_by_id($id) {
        $this->_get_table_detail();
        $this->db->where($this->primary_key, $id);
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $result = $this->db->get();

        return $result->row();
    }

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_data_tindakan_sp($id) {
        $where = array('PAKET_SP_KT' => $id);
        $this->_get_table_detail(true);
        $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
        $this->db->where($where);
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $this->db->group_by('SISWA_KSH');
        $result = $this->db->get();

        return $result->result_array();
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_KT as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KT as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function sudah_ditindak($ID_KSH, $ID_KJT) {
        $where = array(
            'PELANGGARAN_HEADER_KT' => $ID_KSH,
            'TINDAKAN_KT' => $ID_KJT
        );
        $this->db->from($this->table);
        $this->db->where($where);

        if ($this->db->count_all_results() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function get_kolektif($ID_KJT) {
        $this->db->from('komdis_siswa_header ksh');
        $this->db->join('komdis_jenis_tindakan kjt', 'ksh.POIN_KSH>=kjt.POIN_KJT AND ksh.POIN_KSH<=kjt.POIN_MAKS_KJT AND kjt.ID_KJT=' . $ID_KJT);
        $this->db->join('komdis_tindakan kt', 'ksh.ID_KSH=kt.PELANGGARAN_HEADER_KT AND kjt.ID_KJT=kt.TINDAKAN_KT', 'LEFT');

        return $this->db->get()->result();
    }

    public function get_detail_kolektif($ID_KJT, $NOMOR_SURAT) {
        $this->db->select('ID_SISWA, NIS_SISWA, NAMA_SISWA,NAMA_KELAS, AYAH_NAMA_SISWA, WALI_NAMA_SISWA, ALAMAT_SISWA, NAMA_KEC, NAMA_KAB, PONDOK_SISWA, NOHP_SISWA, NAMA_PONDOK_MPS, ALAMAT_MPS, POIN_TAHUN_LALU_KSH, POIN_KSH, LARI_KSH, mp.NAMA_PEG AS NAMA_TANGGUNGJAWAB, mpk.GELAR_AWAL_PEG AS GELAR_AWAL_WALI_KELAS, mpk.NOHP_PEG AS NOMOR_HP_WALI_KELAS, mpk.NAMA_PEG AS WALI_KELAS, mpk.GELAR_AKHIR_PEG AS GELAR_AKHIR_WALI_KELAS, ID_DEPT, NAMA_DEPT, TA_KSH, SISWA_KSH, NAMA_KJT, JUMLAH_POIN_KSH, JUMLAH_LARI_KSH, mpk.NOHP_PEG AS NOMOR_HP_WALI_KELAS');
        $this->db->from($this->table);
        $this->db->join('(SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) ksh', $this->table . '.PELANGGARAN_HEADER_KT=ksh.ID_KSH');
        $this->db->join('md_tahun_ajaran mta', 'ksh.TA_KSH=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', 'ksh.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', 'ksh.SISWA_KSH=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa asw', 'ms.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="' . $this->session->userdata("ID_TA_ACTIVE") . '" AND asw.KONVERSI_AS=0  AND asw.AKTIF_AS=1', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');
        $this->db->join('md_departemen mdp', 'mtnow.DEPT_TINGK=mdp.ID_DEPT', 'LEFT');
        $this->db->join('md_pegawai mpk', 'ak.WALI_KELAS=mpk.ID_PEG');
        $this->db->join('komdis_jenis_tindakan kjt', $this->table . '.TINDAKAN_KT=kjt.ID_KJT');
        $this->db->join('md_user mu', $this->table . '.PENANGGUNGJAWAB_KT=mu.ID_USER');
        $this->db->join('md_pegawai mp', 'mu.PEGAWAI_USER=mp.ID_PEG');
        $this->db->where(array(
            'TINDAKAN_KT' => $ID_KJT,
            'NOMOR_SURAT_KT' => $NOMOR_SURAT,
        ));
        $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
        $this->db->order_by('ID_TINGK', 'ASC');
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $this->db->order_by('NAMA_SISWA', 'ASC');
        $this->db->group_by('SISWA_KSH');

        $result = $this->db->get();

        return $result->result_array();
    }

    public function hapus_surat($ID_KT) {
        $sql = 'DELETE kt2 FROM komdis_tindakan kt1 INNER JOIN komdis_tindakan kt2 ON kt1.NOMOR_SURAT_KT=kt2.NOMOR_SURAT_KT WHERE kt1.ID_KT=' . $ID_KT;
        $result = $this->db->query($sql);

        return $result;
    }

    public function get_nomor_surat($TINDAKAN_KT, $NOMOR_SURAT_KT = NULL) {
        $this->db->from($this->table);
        $this->db->where('TINDAKAN_KT', $TINDAKAN_KT);
        if ($NOMOR_SURAT_KT != NULL)
            $this->db->where('NOMOR_SURAT_KT', $NOMOR_SURAT_KT);
        $this->db->group_by('NOMOR_SURAT_KT');

        $result = $this->db->get();

        if ($NOMOR_SURAT_KT == NULL)
            return $result->result();
        else
            return $result->row();
    }

}
