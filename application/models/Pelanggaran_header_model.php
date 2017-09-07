<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pelanggaran_header_model extends CI_Model {

    var $table = 'komdis_siswa_header';
    var $column = array('NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'JUMLAH_POIN_KSH', 'JUMLAH_LARI_KSH', 'SURAT', 'ID_KSH');
    var $primary_key = "ID_KSH";
    var $order = array("ID_KSH" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($datatables = true) {
        if ($datatables) {
            $this->table = '(SELECT *, IF(NAMA_KJT IS NULL, "", NAMA_KJT) AS SURAT FROM (SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH='.$this->session->userdata('ID_TA_ACTIVE').' GROUP BY SISWA_KSH) komdis_siswa_header LEFT OUTER JOIN komdis_jenis_tindakan ON JUMLAH_POIN_KSH >= POIN_KJT AND JUMLAH_POIN_KSH <= POIN_MAKS_KJT) komdis_siswa_header';
            $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
        }
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', 'komdis_siswa_header.TA_KSH=mta.ID_TA');
//        $this->db->join('md_catur_wulan mcw', $this->table.'.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', 'komdis_siswa_header.SISWA_KSH=ms.ID_SISWA');
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

    public function get_full_by_id($where) {
        $this->_get_table();
        $this->db->join('komdis_jenis_tindakan kjt', 'komdis_siswa_header.JUMLAH_POIN_KSH>=kjt.POIN_KJT AND komdis_siswa_header.JUMLAH_POIN_KSH<=kjt.POIN_MAKS_KJT', 'LEFT');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');

        $this->db->where($where);

        return $this->db->get()->result();
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

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }
    
    public function reset_taqlik_mutasi($data_siswa) {
        $sql_update = 'UPDATE komdis_siswa_header SET PROSES_TAKLIQ_KSH=0, PROSES_MUTASI_KSH=0 WHERE TA_KSH='.$data_siswa->TA_KS.' AND CAWU_KSH='.$data_siswa->CAWU_KS.' AND SISWA_KSH='.$data_siswa->SISWA_KS;
        $status_update = $this->db->query($sql_update);
        
        $sql_update_taqlik = 'UPDATE komdis_siswa_header ksh INNER JOIN komdis_jenis_tindakan kjt ON ksh.POIN_KSH >= kjt.POIN_KJT AND ksh.POIN_KSH <= kjt.POIN_MAKS_KJT AND kjt.ID_KJT=4 SET PROSES_TAKLIQ_KSH=1 WHERE TA_KSH='.$data_siswa->TA_KS.' AND CAWU_KSH='.$data_siswa->CAWU_KS.' AND SISWA_KSH='.$data_siswa->SISWA_KS;
        $status_takliq = $this->db->query($sql_update_taqlik);
        
        $sql_update_mutasi = 'UPDATE komdis_siswa_header ksh INNER JOIN komdis_jenis_tindakan kjt ON ksh.POIN_KSH >= kjt.POIN_KJT AND ksh.POIN_KSH <= kjt.POIN_MAKS_KJT AND kjt.ID_KJT=5 SET PROSES_MUTASI_KSH=1 WHERE TA_KSH='.$data_siswa->TA_KS.' AND CAWU_KSH='.$data_siswa->CAWU_KS.' AND SISWA_KSH='.$data_siswa->SISWA_KS;
        $status_mutasi = $this->db->query($sql_update_mutasi);
        
        return ($status_update || $status_takliq || $status_mutasi);
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function get_data_perkelas($ID_KELAS, $KELAS = TRUE) {
        $where_kelas = '';
        $where_pondok = '';

        if ($KELAS)
            $where_kelas = 'KELAS_AS=' . $ID_KELAS . ' AND';
        else
            $where_pondok = 'WHERE PONDOK_SISWA=' . $ID_KELAS;

        $sql = "SELECT 
    NO_ABSEN_AS, NIS_SISWA, NAMA_SISWA, POIN_TAHUN_LALU_KSH, TOTAL_LARI, AKTIF_AS, NAMA_KJT, ID_KJT,
    MAX(CASE WHEN BULAN = 7 THEN TOTAL_POIN END) AS 'B07',
    MAX(CASE WHEN BULAN = 8 THEN TOTAL_POIN END) AS 'B08',
    MAX(CASE WHEN BULAN = 9 THEN TOTAL_POIN END) AS 'B09',
    MAX(CASE WHEN BULAN = 10 THEN TOTAL_POIN END) AS 'B10',
    MAX(CASE WHEN BULAN = 11 THEN TOTAL_POIN END) AS 'B11',
    MAX(CASE WHEN BULAN = 12 THEN TOTAL_POIN END) AS 'B12',
    MAX(CASE WHEN BULAN = 1 THEN TOTAL_POIN END) AS 'B01',
    MAX(CASE WHEN BULAN = 2 THEN TOTAL_POIN END) AS 'B02',
    MAX(CASE WHEN BULAN = 3 THEN TOTAL_POIN END) AS 'B03',
    MAX(CASE WHEN BULAN = 4 THEN TOTAL_POIN END) AS 'B04',
    MAX(CASE WHEN BULAN = 5 THEN TOTAL_POIN END) AS 'B05',
    MAX(CASE WHEN BULAN = 6 THEN TOTAL_POIN END) AS 'B06',
    (
        IF(MAX(CASE WHEN BULAN = 7 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 7 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 8 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 8 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 9 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 9 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 10 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 10 THEN TOTAL_POIN END))
    ) AS 'CAWU_1',
    (
        IF(MAX(CASE WHEN BULAN = 11 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 11 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 12 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 12 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 1 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 1 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 2 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 2 THEN TOTAL_POIN END))
    ) AS 'CAWU_2',
    (
        IF(MAX(CASE WHEN BULAN = 3 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 3 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 4 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 4 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 5 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 5 THEN TOTAL_POIN END)) + 
        IF(MAX(CASE WHEN BULAN = 6 THEN TOTAL_POIN END) IS NULL, 0, MAX(CASE WHEN BULAN = 6 THEN TOTAL_POIN END))
    ) AS 'CAWU_3'
FROM 
    (
        SELECT *, MONTH(TANGGAL_KS) AS BULAN, SUM(POIN_KJP) AS TOTAL_POIN
        FROM akad_siswa
        LEFT OUTER JOIN komdis_siswa ON SISWA_KS=SISWA_AS AND TA_KS=TA_AS
        LEFT OUTER JOIN komdis_jenis_pelanggaran ON PELANGGARAN_KS=ID_KJP 
        WHERE " . $where_kelas . " TA_AS=" . $this->session->userdata('ID_TA_ACTIVE') . " AND KONVERSI_AS=0
        GROUP BY SISWA_AS, BULAN 
    ) AS tabel_komdis
LEFT OUTER JOIN md_siswa ON ID_SISWA=SISWA_AS 
LEFT OUTER JOIN (SELECT *, SUM(LARI_KSH) AS TOTAL_LARI FROM komdis_siswa_header WHERE TA_KSH=" . $this->session->userdata('ID_TA_ACTIVE') . " GROUP BY SISWA_KSH) komdis_header ON TA_KSH=TA_KS AND SISWA_KS=komdis_header.SISWA_KSH
LEFT OUTER JOIN (SELECT SISWA_KSH, NAMA_KJT, ID_KJT FROM 
(SELECT * FROM komdis_tindakan ORDER BY TINDAKAN_KT DESC) komdis
INNER JOIN komdis_siswa_header ON ID_KSH=PELANGGARAN_HEADER_KT
INNER JOIN komdis_jenis_tindakan ON ID_KJT=TINDAKAN_KT
GROUP BY SISWA_KSH) komdis_tindak ON komdis_tindak.SISWA_KSH=SISWA_AS
" . $where_pondok . "
GROUP BY SISWA_AS
ORDER BY NO_ABSEN_AS ASC
";
        $query = $this->db->query($sql);

        return $query->result();
    }

    public function get_terakhir_input() {
        $sql = 'SELECT ID_KS, CREATED_KS FROM komdis_siswa ORDER BY ID_KS DESC LIMIT 0,1';
        $query = $this->db->query($sql);

        return $query->row()->CREATED_KS;
    }
    
    public function get_hari_aktif() {
        $sql = "SELECT 
    (COUNT(*) - (SELECT 
            SUM(DATEDIFF(TGL_SELESAI_AK, TGL_MULAI_AK) + 1) AS JUMLAH_LIBUR
        FROM
            akad_kalender
        WHERE
            TGL_MULAI_AK >= (SELECT TANGGAL_MULAI_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)
                AND TGL_SELESAI_AK <= (SELECT TANGGAL_AKHIR_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)
                AND LIBUR_AK = 1)) AS JUMLAH_HARI_AKTIF
FROM
    (SELECT 
        DAYNAME(DATE_ADD((SELECT TANGGAL_MULAI_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1), INTERVAL (UNITS.i + TENS.i * 10 + HUNDREDS.i * 100) DAY)) AS HARI
    FROM
        (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS UNITS
    CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS TENS
    CROSS JOIN (SELECT 0 AS i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS HUNDREDS
    WHERE
        DATE_ADD((SELECT TANGGAL_MULAI_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1), INTERVAL (UNITS.i + TENS.i * 10 + HUNDREDS.i * 100) DAY) <= (SELECT TANGGAL_AKHIR_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)) LIST_HARI
WHERE
    HARI <> 'Friday'
";
        $query = $this->db->query($sql);

        return $query->row()->JUMLAH_HARI_AKTIF;
    }
    
    public function get_group_kelas() {
        $this->db->select('*, CONCAT(INDUK_KJP, ".", ANAK_KJP) AS KODE_KJP, COUNT(ID_KS) AS JUMLAH_PELANGGAR, SUM(POIN_KJP) AS JUMLAH_POIN');
        $this->db->from('komdis_siswa');
        $this->db->join('md_siswa', 'SISWA_KS = ID_SISWA');
        $this->db->join('akad_siswa', 'SISWA_AS = ID_SISWA AND TA_KS = TA_AS');
        $this->db->join('akad_kelas', 'KELAS_AS = ID_KELAS');
        $this->db->join('md_pegawai','WALI_KELAS = ID_PEG');
        $this->db->join('komdis_jenis_pelanggaran', 'PELANGGARAN_KS = ID_KJP');
        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->group_by('ID_KELAS , KODE_KJP');
        $this->db->order_by('NAMA_KELAS, KODE_KJP', 'ASC');
        
        $query = $this->db->get();

        return $query->result();
    }
    
    public function get_group_pelanggaran_kelas() {
        $this->db->select('*, CONCAT(INDUK_KJP, ".", ANAK_KJP) AS KODE_KJP');
        $this->db->from('komdis_siswa');
        $this->db->join('komdis_jenis_pelanggaran', 'PELANGGARAN_KS = ID_KJP');
        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->group_by('KODE_KJP');
        $this->db->order_by('KODE_KJP', 'ASC');
        
        $query = $this->db->get();

        return $query->result();
    }

}
