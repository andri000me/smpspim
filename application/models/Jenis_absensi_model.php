<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jenis_absensi_model extends CI_Model {

    var $table = 'md_jenis_kehadiran';
    var $column = array('ID_MJK', 'NAMA_MJK', 'kjpa.ID_KJP','kjpa.NAMA_KJP','kjpa.POIN_KJP', 'kjpt.ID_KJP','kjpt.NAMA_KJP','kjpt.POIN_KJP','ID_MJK');
    var $primary_key = "ID_MJK";
    var $order = array("ID_MJK" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($req_select = true) {
        if($req_select) $this->db->select('*, kjpa.ID_KJP AS ID_KJP_A, kjpa.NAMA_KJP AS NAMA_KJP_A, kjpa.POIN_KJP AS POIN_KJP_A, kjpt.ID_KJP AS ID_KJP_T, kjpt.NAMA_KJP AS NAMA_KJP_T, kjpt.POIN_KJP AS POIN_KJP_T');
        $this->db->from($this->table);
//        $this->db->join('komdis_jenis_pelanggaran kjp',$this->table.'.PELANGGARAN_MJK=kjp.ID_KJP');
        $this->db->join('komdis_jenis_pelanggaran kjpa',$this->table.'.PELANGGARAN_ALPHA_MJK=kjpa.ID_KJP');
        $this->db->join('komdis_jenis_pelanggaran kjpt',$this->table.'.PELANGGARAN_TERLAMBAT_MJK=kjpt.ID_KJP');
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

    public function get_id_pelanggaran($id, $alasan) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);
        
        if($alasan == 'ALPHA')
            return $this->db->get()->row()->PELANGGARAN_ALPHA_MJK;
        elseif($alasan == 'TERLAMBAT')
            return $this->db->get()->row()->PELANGGARAN_TERLAMBAT_MJK;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_MJK as value, NAMA_MJK as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_MJK as id, CONCAT(NAMA_MJK, ' - ALPHA POIN: ', kjpa.POIN_KJP, ' - TERLAMBAT POIN: ', kjpt.POIN_KJP) as text");
        $this->_get_table(FALSE);
        $this->db->like('NAMA_MJK', $where);

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

}
