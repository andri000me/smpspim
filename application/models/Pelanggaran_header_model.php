<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pelanggaran_header_model extends CI_Model {

    var $table = 'komdis_siswa_header';
    var $column = array('NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, CONCAT(", ","Kec "), NAMA_KEC, ", ", NAMA_KAB, CONCAT(", ","Prov "), NAMA_PROV), NAMA_PONDOK_MPS)', 'JUMLAH_POIN_KSH', 'JUMLAH_LARI_KSH', 'SURAT', 'ID_KSH', 'ID_KSH');
    var $primary_key = "ID_KSH";
    var $order = array("ID_KSH" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($datatables = true, $option_jk = true) {
        if ($datatables) {
            $this->table = '(SELECT *, IF(NAMA_KJT IS NULL, "", NAMA_KJT) AS SURAT FROM (SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) komdis_siswa_header LEFT OUTER JOIN komdis_jenis_tindakan ON JUMLAH_POIN_KSH >= POIN_KJT AND JUMLAH_POIN_KSH <= POIN_MAKS_KJT) komdis_siswa_header';
            if ($option_jk)
                $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
        }
        $this->db->select('*, IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, CONCAT(", ","Kec "), NAMA_KEC, ", ", NAMA_KAB, CONCAT(", ","Prov "), NAMA_PROV), NAMA_PONDOK_MPS) AS DOMISILI');
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', 'komdis_siswa_header.TA_KSH=mta.ID_TA');
//        $this->db->join('md_catur_wulan mcw', $this->table.'.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', 'komdis_siswa_header.SISWA_KSH=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_pondok_siswa mps', 'PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa as', 'komdis_siswa_header.SISWA_KSH=as.SISWA_AS AND komdis_siswa_header.TA_KSH=as.TA_AS');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mpw', 'ak.WALI_KELAS=mpw.ID_PEG');
        $this->db->where('TA_KSH', $this->session->userdata('ID_TA_ACTIVE'));
//        $this->db->order_by('CAWU_KSH', 'DESC');
//        $this->db->order_by('NAMA_KELAS', 'ASC');
//        $this->db->order_by('NO_ABSEN_AS', 'ASC');
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
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_full_by_id($where, $order_by = NULL) {
        $this->_get_table(TRUE, FALSE);
        $this->db->join('komdis_jenis_tindakan kjt', 'komdis_siswa_header.JUMLAH_POIN_KSH>=kjt.POIN_KJT AND komdis_siswa_header.JUMLAH_POIN_KSH<=kjt.POIN_MAKS_KJT', 'LEFT');
//        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
//        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
//        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        if ($order_by != NULL)
            $this->db->order_by($order_by, 'ASC');
        $this->db->where($where);
        $result = $this->db->get();

        return $result->result();
    }

    public function get_data_pemanggilan($where) {
        $this->table = '(SELECT *, IF(NAMA_KJT IS NULL, "", NAMA_KJT) AS SURAT FROM (SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) komdis_siswa_header LEFT OUTER JOIN komdis_jenis_tindakan ON JUMLAH_POIN_KSH >= POIN_KJT AND JUMLAH_POIN_KSH <= POIN_MAKS_KJT) komdis_siswa_header';

        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', 'komdis_siswa_header.TA_KSH=mta.ID_TA');
//        $this->db->join('md_catur_wulan mcw', $this->table.'.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', 'komdis_siswa_header.SISWA_KSH=ms.ID_SISWA');
        $this->db->join('akad_siswa as', 'komdis_siswa_header.SISWA_KSH=as.SISWA_AS AND komdis_siswa_header.TA_KSH=as.TA_AS');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mpw', 'ak.WALI_KELAS=mpw.ID_PEG');

        $this->db->join('komdis_jenis_tindakan kjt', 'komdis_siswa_header.JUMLAH_POIN_KSH>=kjt.POIN_KJT AND komdis_siswa_header.JUMLAH_POIN_KSH<=kjt.POIN_MAKS_KJT', 'LEFT');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');

        $this->db->where($where);
        $result = $this->db->get();
//        echo $this->db->last_query();
//        exit();

        return $result->result();
    }

    public function get_poin_siswa($TA_KSH, $CAWU_KSH, $SISWA_KSH) {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_KSH' => $TA_KSH,
            'CAWU_KSH' => $CAWU_KSH,
            'SISWA_KSH' => $SISWA_KSH,
        ));

        return $this->db->get()->row();
    }

    public function get_total_poin_siswa($TA_KSH, $SISWA_KSH) {
        $this->db->select_sum('POIN_KSH');
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_KSH' => $TA_KSH,
            'SISWA_KSH' => $SISWA_KSH,
        ));

        return $this->db->get()->row()->POIN_KSH;
    }

    public function get_total_lari_siswa($TA_KSH, $SISWA_KSH) {
        $this->db->select_sum('LARI_KSH');
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_KSH' => $TA_KSH,
            'SISWA_KSH' => $SISWA_KSH,
        ));

        return $this->db->get()->row()->LARI_KSH;
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_KSH as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KSH as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function save_lanjut_jenjang($data) {
        $check = array(
            'TA_KSH' => $data['TA_KSH'],
            'CAWU_KSH' => $data['CAWU_KSH'],
            'SISWA_KSH' => $data['SISWA_KSH'],
        );
        if ($this->db_handler->is_available($this->table, $check)) {
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        } else {
            return null;
        }
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function reset_taqlik_mutasi($data_siswa) {
        $sql_update = 'UPDATE komdis_siswa_header SET PROSES_TAKLIQ_KSH=0, PROSES_MUTASI_KSH=0 WHERE TA_KSH=' . $data_siswa->TA_KS . ' AND CAWU_KSH=' . $data_siswa->CAWU_KS . ' AND SISWA_KSH=' . $data_siswa->SISWA_KS;
        $status_update = $this->db->query($sql_update);

        $sql_update_taqlik = 'UPDATE komdis_siswa_header ksh INNER JOIN komdis_jenis_tindakan kjt ON ksh.POIN_KSH >= kjt.POIN_KJT AND ksh.POIN_KSH <= kjt.POIN_MAKS_KJT AND kjt.ID_KJT=4 SET PROSES_TAKLIQ_KSH=1 WHERE TA_KSH=' . $data_siswa->TA_KS . ' AND CAWU_KSH=' . $data_siswa->CAWU_KS . ' AND SISWA_KSH=' . $data_siswa->SISWA_KS;
        $status_takliq = $this->db->query($sql_update_taqlik);

        $sql_update_mutasi = 'UPDATE komdis_siswa_header ksh INNER JOIN komdis_jenis_tindakan kjt ON ksh.POIN_KSH >= kjt.POIN_KJT AND ksh.POIN_KSH <= kjt.POIN_MAKS_KJT AND kjt.ID_KJT=5 SET PROSES_MUTASI_KSH=1 WHERE TA_KSH=' . $data_siswa->TA_KS . ' AND CAWU_KSH=' . $data_siswa->CAWU_KS . ' AND SISWA_KSH=' . $data_siswa->SISWA_KS;
        $status_mutasi = $this->db->query($sql_update_mutasi);

        return ($status_update || $status_takliq || $status_mutasi);
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function get_data_perkelas($ID_KELAS, $KELAS = TRUE, $ORDER_BY = NULL) {
        $where_kelas = '';
        $where_pondok = '';

        if ($ORDER_BY == NULL)
            $order = 'ORDER BY NAMA_KELAS, NO_ABSEN_AS ASC';
        else
            $order = $ORDER_BY;

        if ($KELAS)
            $where_kelas = 'KELAS_AS=' . $ID_KELAS . ' AND';
        else
            $where_pondok = 'WHERE PONDOK_SISWA=' . $ID_KELAS;

        $temp_bulan_mulai = $this->pengaturan->getKomdisBulanMulai();

        $q_s = "";
        $q_t = "";
        $bulan_mulai = $temp_bulan_mulai;
        for ($aa = 1; $aa <= 12; $aa++) {
            $q_s .= " MAX(CASE WHEN BULAN = " . $bulan_mulai . " THEN TOTAL_POIN END) AS 'B" . ($bulan_mulai < 10 ? '0' . $bulan_mulai : $bulan_mulai) . "', ";
            if ($bulan_mulai == 12)
                $bulan_mulai = 0;
            $bulan_mulai++;
        }

        $bulan_mulai = $temp_bulan_mulai;
        for ($ab = 1; $ab <= 3; $ab++) {
            $bulan_1 = $bulan_mulai;
            if ($bulan_1 > 12)
                $bulan_1 = 1;
            $bulan_2 = $bulan_1 + 1;
            if ($bulan_2 > 12)
                $bulan_2 = 1;
            $bulan_3 = $bulan_2 + 1;
            if ($bulan_3 > 12)
                $bulan_3 = 1;
            $bulan_4 = $bulan_3 + 1;
            if ($bulan_4 > 12)
                $bulan_4 = 1;

            $q_t .= "(
        IF(MAX(CASE WHEN BULAN = " . ($bulan_1) . " THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = " . ($bulan_1) . " THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = " . ($bulan_2) . " THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = " . ($bulan_2) . " THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = " . ($bulan_3) . " THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = " . ($bulan_3) . " THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = " . ($bulan_4) . " THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = " . ($bulan_4) . " THEN TOTAL_POIN END))
    ) AS 'CAWU_" . $ab . "' ";
            if ($ab < 3)
                $q_t .= ",";
            $bulan_mulai = $bulan_4 + 1;
        }

//    MAX(CASE WHEN BULAN = 7 THEN TOTAL_POIN END) AS 'B07',
//    MAX(CASE WHEN BULAN = 8 THEN TOTAL_POIN END) AS 'B08',
//    MAX(CASE WHEN BULAN = 9 THEN TOTAL_POIN END) AS 'B09',
//    MAX(CASE WHEN BULAN = 10 THEN TOTAL_POIN END) AS 'B10',
//    MAX(CASE WHEN BULAN = 11 THEN TOTAL_POIN END) AS 'B11',
//    MAX(CASE WHEN BULAN = 12 THEN TOTAL_POIN END) AS 'B12',
//    MAX(CASE WHEN BULAN = 1 THEN TOTAL_POIN END) AS 'B01',
//    MAX(CASE WHEN BULAN = 2 THEN TOTAL_POIN END) AS 'B02',
//    MAX(CASE WHEN BULAN = 3 THEN TOTAL_POIN END) AS 'B03',
//    MAX(CASE WHEN BULAN = 4 THEN TOTAL_POIN END) AS 'B04',
//    MAX(CASE WHEN BULAN = 5 THEN TOTAL_POIN END) AS 'B05',
//    MAX(CASE WHEN BULAN = 6 THEN TOTAL_POIN END) AS 'B06',

        $sql = "SELECT 
    NO_ABSEN_AS, NIS_SISWA, NAMA_SISWA, POIN_TAHUN_LALU_KSH, TOTAL_LARI, AKTIF_AS, NAMA_KJT, ID_KJT, NAMA_KELAS, ID_MPS, NAMA_PONDOK_MPS, ALAMAT_MPS,
    " . $q_s . " 
    " . $q_t . " 
FROM 
    (
        SELECT *, MONTH(TANGGAL_KS) AS BULAN, SUM(POIN_KJP) AS TOTAL_POIN
        FROM akad_siswa
        INNER JOIN akad_kelas ON KELAS_AS=ID_KELAS
        LEFT OUTER JOIN komdis_siswa ON SISWA_KS=SISWA_AS AND TA_KS=TA_AS
        LEFT OUTER JOIN komdis_jenis_pelanggaran ON PELANGGARAN_KS=ID_KJP 
        WHERE " . $where_kelas . " TA_AS=" . $this->session->userdata('ID_TA_ACTIVE') . " AND KONVERSI_AS=0
        GROUP BY SISWA_AS, BULAN 
    ) AS tabel_komdis
    LEFT OUTER JOIN md_siswa ON ID_SISWA=SISWA_AS 
    LEFT OUTER JOIN (SELECT *, SUM(LARI_KSH) AS TOTAL_LARI FROM komdis_siswa_header WHERE TA_KSH=" . $this->session->userdata('ID_TA_ACTIVE') . " GROUP BY SISWA_KSH) komdis_header ON SISWA_AS=komdis_header.SISWA_KSH
    LEFT OUTER JOIN (SELECT SISWA_KSH, NAMA_KJT, ID_KJT FROM komdis_tindakan
    INNER JOIN (
    SELECT komdis_siswa_header.*, MAX(ID_KT) AS ID_KT_MAX FROM komdis_tindakan 
    INNER JOIN komdis_siswa_header ON ID_KSH=PELANGGARAN_HEADER_KT
    WHERE TA_KSH=" . $this->session->userdata('ID_TA_ACTIVE') . " 
    GROUP BY SISWA_KSH
    ) ktt ON ktt.ID_KT_MAX=ID_KT
    INNER JOIN komdis_jenis_tindakan ON ID_KJT=TINDAKAN_KT) komdis_tindak ON komdis_tindak.SISWA_KSH=SISWA_AS
    LEFT OUTER JOIN md_pondok_siswa ON ID_MPS=PONDOK_SISWA
    " . $where_pondok . "
    GROUP BY SISWA_AS
    " . $order;
        $query = $this->db->query($sql);
//        echo $this->db->last_query();
//        exit();

        return $query->result_array();
    }

    public function get_terakhir_input() {
        $sql = 'SELECT ID_KS, CREATED_KS FROM komdis_siswa ORDER BY ID_KS DESC LIMIT 0,1';
        $query = $this->db->query($sql);

        return $query->row()->CREATED_KS;
    }

    public function get_hari_aktif() {
        $start_date = '(SELECT TANGGAL_MULAI_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)';
        $end_date = '(SELECT TANGGAL_AKHIR_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)';

        $sql = "SELECT 
    (COUNT(*) - (SELECT 
            SUM(DATEDIFF(TGL_SELESAI_AK, TGL_MULAI_AK) + 1) AS JUMLAH_LIBUR
        FROM
            akad_kalender
        WHERE
            TGL_MULAI_AK >= " . $start_date . "
                AND TGL_SELESAI_AK <= " . $end_date . "
                AND LIBUR_AK = 1)) AS JUMLAH_HARI_AKTIF
FROM
    (SELECT 
        DAYNAME(DATE_ADD(" . $start_date . ", INTERVAL (UNITS.i + TENS.i * 10 + HUNDREDS.i * 100) DAY)) AS HARI
    FROM
        (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS UNITS
    CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS TENS
    CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS HUNDREDS
    WHERE
        DATE_ADD(" . $start_date . ", INTERVAL (UNITS.i + TENS.i * 10 + HUNDREDS.i * 100) DAY) <= " . $end_date . ") LIST_HARI
WHERE
    HARI <> 'Friday'
";
        $query = $this->db->query($sql);

        return $query->row()->JUMLAH_HARI_AKTIF;
    }

    public function get_group_kelas($where = NULL) {
        $this->db->select('*, CONCAT(INDUK_KJP, ".", ANAK_KJP) AS KODE_KJP, COUNT(ID_KS) AS JUMLAH_PELANGGARAN, COUNT(DISTINCT SISWA_KS) AS JUMLAH_PELANGGAR, SUM(POIN_KJP) AS JUMLAH_POIN');
        $this->db->from('komdis_siswa');
        $this->db->join('akad_siswa', 'SISWA_AS = SISWA_KS AND TA_KS = TA_AS');
        $this->db->join('komdis_jenis_pelanggaran', 'PELANGGARAN_KS = ID_KJP');
        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
        if ($where != NULL)
            $this->db->where($where);
        $this->db->group_by('KELAS_AS , KODE_KJP');
        $sql = $this->db->get_compiled_select();

        $this->db->from('akad_kelas');
        $this->db->join('md_tingkat', 'ID_TINGK = TINGKAT_KELAS', 'LEFT');
        $this->db->join('md_departemen', 'ID_DEPT= DEPT_TINGK', 'LEFT');
        $this->db->join('(' . $sql . ') asq', 'ID_KELAS = KELAS_AS AND TA_KELAS=TA_AS', 'LEFT');
        $this->db->where('TA_KELAS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('TINGKAT_KELAS, JK_KELAS, NAMA_KELAS, KODE_KJP', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function get_group_pelanggaran_kelas($where = NULL) {
        $this->db->select('*, CONCAT(INDUK_KJP, ".", ANAK_KJP) AS KODE_KJP');
        $this->db->from('komdis_jenis_pelanggaran');
//        $this->db->from('komdis_siswa');
//        $this->db->join('komdis_jenis_pelanggaran', 'PELANGGARAN_KS = ID_KJP');
        $this->db->join('md_jenis_kehadiran', 'PELANGGARAN_ALPHA_MJK = ID_KJP', 'LEFT');
//        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
//        $this->db->group_by('KODE_KJP');
        $this->db->where('TA_KJP', $this->session->userdata('ID_TA_ACTIVE'));
        if ($where != NULL)
            $this->db->where($where);
        $this->db->order_by('KODE_KJP', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function get_group_pelanggar($khusus, $where = NULL) {
        $this->db->from('komdis_siswa');
        $this->db->join('md_siswa', 'SISWA_KS = ID_SISWA');
        $this->db->join('akad_siswa', 'SISWA_AS = ID_SISWA AND TA_KS = TA_AS');
        $this->db->join('komdis_jenis_pelanggaran', 'PELANGGARAN_KS = ID_KJP');
        if ($khusus)
            $this->db->join('md_jenis_kehadiran', 'PELANGGARAN_ALPHA_MJK = ID_KJP');
        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
        if ($where != NULL)
            $this->db->where($where);
        $this->db->group_by('SISWA_KS');
        $sql = $this->db->get_compiled_select();

        $this->db->select('*, COUNT(SISWA_KS) AS JUMLAH_PELANGGAR');
        $this->db->from('(' . $sql . ') ks');
        $this->db->group_by('KELAS_AS');
        $query = $this->db->get();
//        var_dump($this->db->last_query());exit();

        return $query->result();
    }

    public function get_group_tindakan($where = NULL) {
//        $this->db->select('*, MAX(TINDAKAN_KT) AS TINDAKAN_KT_MAKS');
//        $this->db->from('komdis_tindakan');
//        $this->db->join('komdis_siswa_header', 'PELANGGARAN_HEADER_KT=ID_KSH');
//        $this->db->where('TA_KSH', $this->session->userdata('ID_TA_ACTIVE'));
//        if ($where != NULL)
//            $this->db->where($where);
//        $this->db->group_by('SISWA_KSH');
//        $sql = $this->db->get_compiled_select();
//        
//        $this->db->select('*, SUM(IF(TINDAKAN_KT_MAKS = 1, 1, 0)) AS TINDAKAN_1,SUM(IF(TINDAKAN_KT_MAKS = 2, 1, 0)) AS TINDAKAN_2,SUM(IF(TINDAKAN_KT_MAKS = 3, 1, 0)) AS TINDAKAN_3,SUM(IF(TINDAKAN_KT_MAKS = 4, 1, 0)) AS TINDAKAN_4,SUM(IF(TINDAKAN_KT_MAKS = 5, 1, 0)) AS TINDAKAN_5');
//        $this->db->from('('.$sql.') t');
//        $this->db->join('md_siswa', 'SISWA_KSH=ID_SISWA');
//        $this->db->join('akad_siswa', 'ID_SISWA=SISWA_AS AND TA_AS=TA_KSH AND KONVERSI_AS=0');
//        if ($where != NULL)
//            $this->db->where($where);
//        $this->db->group_by('KELAS_AS');
//        $query = $this->db->get();

        $this->db->select('*, SUM(IF(TINDAKAN_KT = 1, 1, 0)) AS TINDAKAN_1,SUM(IF(TINDAKAN_KT = 2, 1, 0)) AS TINDAKAN_2,SUM(IF(TINDAKAN_KT = 3, 1, 0)) AS TINDAKAN_3,SUM(IF(TINDAKAN_KT = 4, 1, 0)) AS TINDAKAN_4,SUM(IF(TINDAKAN_KT = 5, 1, 0)) AS TINDAKAN_5');
        $this->db->from('komdis_tindakan');
        $this->db->join('komdis_siswa_header', 'PELANGGARAN_HEADER_KT=ID_KSH');
        $this->db->join('md_siswa', 'SISWA_KSH=ID_SISWA');
        $this->db->join('akad_siswa', 'ID_SISWA=SISWA_AS AND TA_AS=TA_KSH AND KONVERSI_AS=0');
        $this->db->where('TA_KSH', $this->session->userdata('ID_TA_ACTIVE'));
        if ($where != NULL)
            $this->db->where($where);
        $this->db->group_by('KELAS_AS');
        $query = $this->db->get();

        return $query->result_array();
    }

    public function fix_kehadiran_komdis() {
        $sql = "INSERT INTO komdis_siswa (TA_KS, CAWU_KS, SISWA_KS, PELANGGARAN_KS, TANGGAL_KS, SUMBER_KS, KETERANGAN_KS, KEHADIRAN_KS, USER_KS)
            SELECT TA_AKH, CAWU_AKH, SISWA_AKH, PELANGGARAN_ALPHA_MJK, TANGGAL_AKH, PEGAWAI_USER, KETERANGAN_AKH, ID_AKH, USER_AKH
            FROM akad_kehadiran
            INNER JOIN md_jenis_kehadiran ON ID_MJK = JENIS_AKH
            INNER JOIN md_user ON USER_AKH = ID_USER
            LEFT JOIN komdis_siswa ON KEHADIRAN_KS = ID_AKH
            WHERE KEHADIRAN_KS IS NULL
            AND ALASAN_AKH='ALPHA'
            AND TA_AKH=" . $this->session->userdata('ID_TA_ACTIVE');
        $this->db->query($sql);

        $sql = "UPDATE komdis_siswa_header ksh
                    INNER JOIN
                (SELECT 
                    komdis_siswa_header.*, COUNT(ID_KS) AS JUMLAH_LARI
                FROM
                    komdis_siswa
                INNER JOIN komdis_siswa_header ON SISWA_KSH = SISWA_KS
                    AND TA_KS = TA_KSH 
                    AND CAWU_KS = CAWU_KSH
                INNER JOIN akad_kehadiran ON KEHADIRAN_KS = ID_AKH
                WHERE
                    JENIS_AKH = 1 AND ALASAN_AKH = 'ALPHA'
                GROUP BY SISWA_KSH , TA_KSH , CAWU_KSH) ks ON ks.SISWA_KSH = ksh.SISWA_KSH
                    AND ks.TA_KSH = ksh.TA_KSH
                    AND ks.CAWU_KSH = ksh.CAWU_KSH
            SET 
                ksh.LARI_KSH = ks.JUMLAH_LARI
            WHERE
                ks.SISWA_KSH = ksh.SISWA_KSH
                    AND ks.TA_KSH = ksh.TA_KSH
                    AND ks.CAWU_KSH = ksh.CAWU_KSH
                    AND ksh.TA_KSH = " . $this->session->userdata('ID_TA_ACTIVE');
        $this->db->query($sql);

        $this->fix_poin();
    }

    public function fix_lari_dan_poin() {
        for ($cawu = 1; $cawu <= 3; $cawu++) {
            $sql = "INSERT INTO komdis_siswa_header (TA_KSH, CAWU_KSH, SISWA_KSH, POIN_TAHUN_LALU_KSH, USER_KSH)
SELECT " . $this->session->userdata('ID_TA_ACTIVE') . ", " . $cawu . ", ID_SISWA, (SELECT SUM(POIN_KSH) FROM komdis_siswa_header WHERE SISWA_KSH=ID_SISWA) AS JUMLAH_POIN_LALU, " . $this->session->userdata('ID_USER') . "
FROM md_siswa
LEFT JOIN (SELECT * FROM komdis_siswa_header WHERE TA_KSH=" . $this->session->userdata('ID_TA_ACTIVE') . " AND CAWU_KSH=" . $cawu . ") ksh ON SISWA_KSH=ID_SISWA
WHERE TA_KSH IS NULL";
            $this->db->query($sql);
        }

        $sql = "UPDATE komdis_siswa_header ksh
                    INNER JOIN
                (SELECT 
                    komdis_siswa_header.*, COUNT(ID_KS) AS JUMLAH_LARI
                FROM
                    komdis_siswa
                INNER JOIN komdis_siswa_header ON SISWA_KSH = SISWA_KS
                    AND TA_KS = TA_KSH 
                    AND CAWU_KS = CAWU_KSH
                INNER JOIN akad_kehadiran ON KEHADIRAN_KS = ID_AKH
                WHERE
                    JENIS_AKH = 1 AND ALASAN_AKH = 'ALPHA'
                GROUP BY SISWA_KSH , TA_KSH , CAWU_KSH) ks ON ks.SISWA_KSH = ksh.SISWA_KSH
                    AND ks.TA_KSH = ksh.TA_KSH
                    AND ks.CAWU_KSH = ksh.CAWU_KSH
            SET 
                ksh.LARI_KSH = ks.JUMLAH_LARI
            WHERE
                ks.SISWA_KSH = ksh.SISWA_KSH
                    AND ks.TA_KSH = ksh.TA_KSH
                    AND ks.CAWU_KSH = ksh.CAWU_KSH
                    AND ksh.TA_KSH = " . $this->session->userdata('ID_TA_ACTIVE');

        $this->db->query($sql);

        $sql = "UPDATE komdis_siswa_header ksh
                INNER JOIN
            (SELECT 
                komdis_siswa_header.*, SUM(POIN_KJP) AS JUMLAH_POIN
            FROM
                komdis_siswa
            INNER JOIN komdis_siswa_header ON SISWA_KSH = SISWA_KS AND TA_KS = TA_KSH
                AND CAWU_KS = CAWU_KSH
            INNER JOIN komdis_jenis_pelanggaran ON ID_KJP = PELANGGARAN_KS
            GROUP BY SISWA_KSH , TA_KSH , CAWU_KSH) ks ON ksh.SISWA_KSH = ks.SISWA_KSH
                AND ksh.TA_KSH = ks.TA_KSH
                AND ksh.CAWU_KSH = ks.CAWU_KSH 
        SET 
            ksh.POIN_KSH = ks.JUMLAH_POIN
        WHERE
            ks.JUMLAH_POIN <> ksh.POIN_KSH
                AND ksh.SISWA_KSH = ks.SISWA_KSH
                AND ksh.TA_KSH = ks.TA_KSH
                AND ksh.CAWU_KSH = ks.CAWU_KSH 
                AND ksh.TA_KSH = " . $this->session->userdata('ID_TA_ACTIVE');

        $this->db->query($sql);
    }

}
