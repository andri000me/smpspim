<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jenjang_sekolah_model extends CI_Model {

    var $table = 'md_jenjang_sekolah';
    var $column = array('ID_JS', 'NAMA_JS', 'ID_JS');
    var $primary_key = "ID_JS";
    var $order = array("ID_JS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->where('ID_JS<>', 1);
        $this->db->where('ID_JS<>', 7);
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

    public function get_jumlah_kelas($data) {
        $this->_get_table();
        $this->db->where('ID_JS', $data);

        return $this->db->get()->row()->JUMLAH_KELAS_JS;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_JS as id, NAMA_JS as text");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_add($for_html = true) {
        if ($for_html) $this->db->select("ID_JS as id, NAMA_JS as text");
        $this->db->from($this->table);
        $this->db->where('ID_JS<>', 1);

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_JS as id, NAMA_JS as text");
        $this->_get_table();
        $this->db->like('NAMA_JS', $where);

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
    
    public function get_nama_dept($id) {
        $this->_get_table();
        $this->db->join('md_jenjang_departemen', 'JENJANG_MJD=ID_JS');
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->DEPT_MJD;
    }
    
    public function get_gedung_dept($id, $tingkat, $jk) {
        $this->_get_table();
        $this->db->join('md_jenjang_departemen', 'JENJANG_MJD=ID_JS');
        $this->db->join('md_tingkat', 'DEPT_MJD=DEPT_TINGK');
        $this->db->where(array(
            $this->primary_key => $id,
            'NAMA_TINGK' => $tingkat
        ));
        $result = $this->db->get()->row()->GEDUNG_UJIAN_TINGK;
        $data = json_decode($result, TRUE);

        if ($data == null) {
            $this->CI->generate->output_JSON(array(
                'status' => false,
                'msg' => 'Ada kesalahan dalam penulian gedung ujian jenjang '.$data['NAMA_DEPT'][$key].' tingkat '.$data['TINGKAT'][$key].' jenis kelamin  di database. Silahkan atur gedung ujian di datatabase pada tabel md_tingkat field GEDUNG_UJIAN_TINGK.'
            ));
        }

        return $data[$jk];
    }
    
    public function get_nama_jenjang($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->NAMA_JS;
    }
    
    public function get_warna_jenjang($nama) {
        $this->_get_table();
        $this->db->where('NAMA_JS', $nama);

        return $this->db->get()->row()->WARNA_JS;
    }
    
    public function jenjang_selanjutnya($jenjang) {
        $this->_get_table();
        $this->db->where('NAMA_JS', $jenjang);
        $id_jenjang_selanjutnya = $this->db->get()->row()->ID_JS + 1;
        
        $this->_get_table();
        $this->db->where($this->primary_key, $id_jenjang_selanjutnya);

        return $this->db->get()->row()->NAMA_JS;
    }
    
    public function jenjang_sebelumnya($jenjang) {
        $this->_get_table();
        $this->db->where('NAMA_JS', $jenjang);
        $id_jenjang_selanjutnya = $this->db->get()->row()->ID_JS - 1;
        
        $this->_get_table();
        $this->db->where($this->primary_key, $id_jenjang_selanjutnya);

        return $this->db->get()->row()->NAMA_JS;
    }
    
    public function relasi_jenjang_departemen() {
        $this->db->from('md_jenjang_departemen');
        
        return $this->db->get()->result_array();
    }
    
    public function relasi_jenjang_departemen_tingkat($jenjang, $tingkat = NULL) {
        $this->db->from('md_jenjang_departemen mjd');
        $this->db->join('md_jenjang_sekolah mjs', 'mjs.ID_JS=mjd.JENJANG_MJD');
        $this->db->join('md_tingkat mt', 'mt.DEPT_TINGK=mjd.DEPT_MJD');
        if ($tingkat == NULL) {
            $this->db->where(array(
                'ID_JS' => $jenjang,
            ));
            
            return $this->db->get()->result();
        } else {  
            $this->db->where(array(
                'NAMA_TINGK' => $tingkat,
                'ID_JS' => $jenjang,
            ));
        
            return $this->db->get()->row();
        }
    }
    
    public function relasi_jenjang_sekolah($jenjang) {
        $this->db->from('md_jenjang_departemen mjd');
        $this->db->join('md_jenjang_sekolah mjs', 'mjs.ID_JS=mjd.JENJANG_MJD');
        
        $this->db->where(array(
            'ID_JS' => $jenjang
        ));

        return $this->db->get()->row();
    }

}
 