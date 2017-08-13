<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jenis_pelanggaran_model extends CI_Model {

    var $table = 'komdis_jenis_pelanggaran';
    var $column = array('NAMA_TA','CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", "."),IF(ANAK_KJP IS NULL, "", ANAK_KJP))', 'NAMA_KJP','POIN_KJP', 'ID_KJP');
    var $primary_key = "ID_KJP";
    var $order = array("ID_KJP" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('*, CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", "."),IF(ANAK_KJP IS NULL, "", ANAK_KJP)) AS NO_KJP');
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_KJP=mta.ID_TA');
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

    public function get_poin($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->POIN_KJP;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_KJP as value, NAMA_KJP as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KJP as id, CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, '', '.'),IF(ANAK_KJP IS NULL, '', ANAK_KJP), ' ',NAMA_KJP, ' [POIN: ',POIN_KJP,']') as text");
        $this->db->from($this->table);
        $this->db->like('NAMA_KJP', $where);

        return $this->db->get()->result();
    }

    public function get_all_ac_pelanggaran($where) {
        $this->db->select("ID_KJP as id, CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, '', '.'),IF(ANAK_KJP IS NULL, '', ANAK_KJP), ' ',NAMA_KJP, ' [POIN: ',POIN_KJP,']') as text");
        $this->db->from($this->table);
        $this->db->where('TA_KJP', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->like('NAMA_KJP', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function cek_no($INDUK_KJP, $ANAK_KJP) {
        $this->db->from($this->table);
        $this->db->where(array(
            'INDUK_KJP' => $INDUK_KJP, 
            'ANAK_KJP' => $ANAK_KJP
        ));

        if($this->db->count_all_results() == 0) return TRUE;
        else return FALSE;
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

}
