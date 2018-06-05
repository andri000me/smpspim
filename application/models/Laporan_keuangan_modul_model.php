<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_keuangan_modul_model extends CI_Model {

    var $table = 'keu_pembayaran';
    var $batasan = 'DATE(CREATED_BAYAR)';
    
    var $column = array('CREATED_BAYAR', 'JENIS_BAYAR', 'NAMA_TA','IF(NIS_SISWA IS NULL, "", NIS_SISWA)','NAMA_SISWA','KETERANGAN_TINGK','IF(NAMA_KELAS IS NULL, "", NAMA_KELAS)','NAMA_DT', 'NOMINAL_BAYAR', 'mpk.NAMA_PEG');
    var $order = array("CREATED_BAYAR" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        // $this->db->select('*, IF(NAMA_KELAS IS NULL, "", NAMA_KELAS) AS NAMA_KELAS_SHOW, IF(NIS_SISWA IS NULL, "", NIS_SISWA) AS NIS_SISWA_SHOW ');
        $this->db->from($this->table);
        $this->db->join('keu_setup ds',$this->table.'.SETUP_BAYAR=ds.ID_SETUP');
        $this->db->join('keu_detail dt','ds.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->join('md_tahun_ajaran ta','t.TA_TAG=ta.ID_TA');
        $this->db->join('md_siswa ms','ds.SISWA_SETUP=ms.ID_SISWA');
        $this->db->join('md_user mu',$this->table.'.USER_BAYAR=mu.ID_USER');
        $this->db->join('md_pegawai mpk','mu.PEGAWAI_USER=mpk.ID_PEG');
        $this->db->join('akad_siswa asw', 'ms.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="'.$ta.'" AND asw.KONVERSI_AS=0', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK', 'LEFT');

        if ($ta != "")
            $this->db->where('ID_TA', $ta);
        if ($tagihan != "")
            $this->db->where('ID_TAG', $tagihan);
        if ($detail_tagihan != "")
            $this->db->where('ID_DT', $detail_tagihan);
        if ($jenjang != "")
            $this->db->where('DEPT_TINGK', $jenjang);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($akhir_tanggal != 0)
            $this->db->where($this->batasan.' <=', $akhir_tanggal);
        if ($mulai_tanggal != "")
            $this->db->where($this->batasan.' >=', $mulai_tanggal);
        if ($pegawai != "")
            $this->db->where('mpk.ID_PEG', $pegawai);
    }

    public function get_data($label, $ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        if($label == 'Pembayaran') $this->db->select('SUM(NOMINAL_BAYAR) AS data, '.$this->batasan.' AS x_label');
        elseif($label == 'Pengembalian') $this->db->select('SUM(NOMINAL_BAYAR) AS data, '.$this->batasan.' AS x_label');
        
        $this->_get_table($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        
        if ($label == 'Pembayaran') 
            $this->db->where('JENIS_BAYAR', 'PEMBAYARAN');
        elseif ($label == 'Pengembalian') 
            $this->db->where('JENIS_BAYAR', 'PENGEMBALIAN');
        elseif ($label == 'Tunggakan') 
            $this->db->where('JENIS_BAYAR', 'PEMBAYARAN');
        
        $this->db->group_by($this->batasan);

        $this->db->order_by('x_label', 'ASC');

        return $this->db->get()->result();
    }

    public function export_data($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        $this->load->dbutil();

        $this->db->select(''
                . 'CREATED_BAYAR AS TANGGAL_WAKTU_BAYAR'
                . ', JENIS_BAYAR AS JENIS_AKSI'
                . ', NAMA_TA AS NAMA_TA'
                . ', NIS_SISWA AS NIS'
                . ', NAMA_SISWA AS NAMA_SISWA'
                . ', KETERANGAN_TINGK AS JENJANG_TINGKAT'
                . ', NAMA_KELAS AS KELAS'
                . ', mp.NAMA_PEG AS WALI_KELAS'
                . ', NAMA_DT AS NAMA_TAGIHAN'
                . ', NOMINAL_BAYAR AS NOMINAL'
                . ', mpk.NAMA_PEG AS USER_INPUT'
                . '');
        
        $this->_get_table($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        
        $this->db->order_by('CREATED_BAYAR', 'ASC');
        
        $sql = $this->db->get();

        return $this->dbutil->csv_from_result($sql);
    }

    private function _get_datatables_query($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        $this->db->select('*, IF(NAMA_KELAS IS NULL, "", NAMA_KELAS) AS NAMA_KELAS_SHOW, IF(NIS_SISWA IS NULL, "", NIS_SISWA) AS NIS_SISWA_SHOW, mpk.NAMA_PEG AS NAMA_PEG_SHOW');
        $this->_get_table($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
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

    function get_datatables($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        $this->_get_datatables_query($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        // var_dump($this->db->last_query());

        return $query->result();
    }

    function count_filtered($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        $this->_get_datatables_query($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai) {
        $this->db->select('*, IF(NAMA_KELAS IS NULL, "", NAMA_KELAS) AS NAMA_KELAS_SHOW, IF(NIS_SISWA IS NULL, "", NIS_SISWA) AS NIS_SISWA_SHOW, mpk.NAMA_PEG AS NAMA_PEG_SHOW');
        $this->_get_table($ta, $tagihan, $detail_tagihan, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        
        return $this->db->count_all_results();
    }

}
