<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_hafalan_kelas_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NO_ABSEN_AS', 'IF(NIS_SISWA IS NULL, "KELUAR", NIS_SISWA)','NAMA_SISWA', 'ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS','ID_AS');
    var $primary_key = "ID_PNH";
    var $order = array("ID_PNH" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ID_KELAS) {
        $this->db->select('*, IF(NIS_SISWA IS NULL, "KELUAR", NIS_SISWA) AS NIS_SISWA');
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA', 'LEFT');
        $this->db->join('ph_nilai_header pnh', $this->table . '.SISWA_AS=pnh.SISWA_PNH AND TA_PNH=TA_AS', 'LEFT');
        $this->db->where('KELAS_AS', $ID_KELAS);
        $this->db->where('KONVERSI_AS', 0);
//        $this->db->where('AKTIF_AS', 1);
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('NO_ABSEN_AS', 'ASC');
    }

    private function _get_datatables_query($ID_KELAS) {
        $this->_get_table($ID_KELAS);
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

    function get_datatables($ID_KELAS) {
        $this->_get_datatables_query($ID_KELAS);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($ID_KELAS) {
        $this->_get_datatables_query($ID_KELAS);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }
    
    public function get_nilai_siswa($TA_PNH, $SISWA_PHN, $BATASAN_PHN) {
        $this->db->from('ph_nilai');
        $this->db->where(array(
            'TA_PHN' => $TA_PNH,
            'SISWA_PHN' => $SISWA_PHN,
            'BATASAN_PHN' => $BATASAN_PHN
        ));
        $query = $this->db->get();

        if ($query == NULL)
            return NULL;
        else
            return $query->row();
    }

}
