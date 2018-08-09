<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Aplikasi Sistem Informasi Akademik (SIAKAD)
 * MTS TBS KUDUS
 * Dibuat oleh Rohmad Eko Wahyudi
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 *
 */

class Db_handler {

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function get_data_tables($table, $posts, $columns, $orders, $order, $joins = NULL, $select = "*", $params = NULL) {
        // GET DATA
        $this->get_table($table, $joins, $params, $select);
        $this->get_query($posts, $columns, $orders, $order);
        if ($posts['length'] != -1)
            $this->CI->db->limit($posts['length'], $posts['start']);
        $query_data = $this->CI->db->get();
//        var_dump($this->CI->db->last_query());
        // GET COUNT
        $this->get_table($table, $joins, $params, $select);
        $this->get_query($posts, $columns, $orders, $order);
        $query_count_filtered = $this->CI->db->get();

        // GET COUNT ALL
        $this->get_table($table, $joins, $params, $select);
        $query_count_all = $this->CI->db->get();

        return array(
            "count_all" => $query_count_all->num_rows(),
            "count_filtered" => $query_count_filtered->num_rows(),
            "data" => $query_data->result(),
        );
    }

    public function get_data_tables_row($table, $posts, $columns, $orders, $order, $joins = NULL, $select = "*", $params = NULL) {
        $this->get_table($table, $joins, $params, $select);
        $this->get_query($posts, $columns, $orders, $order);

        return $this->CI->db->get()->row();
    }

    public function exec_query($sql, $single = FALSE, $result_object = TRUE) {
        $result = $this->CI->db->query($sql);

        if ($single) {
            if ($result_object) {
                return $result->row();
            } else {
                return $result->row_array();
            }
        } else {
            if ($result_object) {
                return $result->result();
            } else {
                return $result->result_array();
            }
        }
    }

    public function call_function($function, $params = array()) {
        $param = '';
        foreach ($params as $value) {
            if ($value == '' || $value == NULL)
                return NULL;

            if ($param == '')
                $param .= $value;
            else
                $param .= ',' . $value;
        }
        $result = $this->CI->db->query('SELECT ' . $function . '(' . $param . ') AS RESULT');
        $row = $result->row();

        if (isset($row))
            return $row->RESULT;
        else
            return NULL;
    }

    public function call_procedure($function, $params = array()) {
        $param = '';
        foreach ($params as $value) {
            if ($value == '' || $value == NULL)
                return NULL;

            if ($param == '')
                $param .= $value;
            else
                $param .= ',' . $value;
        }
        $this->CI->db->query('CALL ' . $function . '(' . $param . ')');
    }

    public function get_by_id($table, $primary_key, $id, $select = '*', $joins = NULL, $result_object = TRUE) {
        $params = array(
            'where' => array(
                $primary_key => $id
            )
        );

        return $this->get_row($table, $params, $select, $joins, $result_object);
    }

    public function get_row($table, $params = NULL, $select = '*', $joins = NULL, $result_object = TRUE) {
        $this->get_table($table, $joins, $params, $select);
        $this->CI->db->limit(1, 0);
        $query_data = $this->CI->db->get();
//        var_dump($this->CI->db->last_query());

        return $result_object ? $query_data->row() : $query_data->row_array();
    }

    public function get_rows($table, $params = NULL, $select = '*', $joins = NULL, $result_object = TRUE) {
        $this->get_table($table, $joins, $params, $select);
        $query_data = $this->CI->db->get();
//        var_dump($this->CI->db->last_query());

        return $result_object ? $query_data->result() : $query_data->result_array();
    }

    public function get_list($table, $params, $primary_key, $name_of_pk, $joins = NULL) {
        $select = $primary_key . " as id, " . $name_of_pk . " as text";

        return $this->get_rows($table, $params, $select, $joins, FALSE);
    }

    public function is_available($table, $field, $value = NULL) {
        if (is_array($field)) {
            $params = array(
                'where' => $field
            );
        } else {
            $params = array(
                'where' => array(
                    $field => $value
                )
            );
        }

        $result = $this->get_rows($table, $params);

        if (count($result) == 0)
            return TRUE;
        else
            return FALSE;
    }

    public function get_auto_complete($table, $query, $id, $text, $joins = NULL, $as_text = NULL, $param = NULL) {
        $params = array(
            'group_start' => true,
            'like' => array(
                $id => $query
            ),
        );

        if (is_array($text)) {
            foreach ($text as $detail) {
                $params_temp = array(
                    'or_like' => array(
                        $detail => $query
                    )
                );
                $params = array_merge($params, $params_temp);
            }
        } else {
            $params_temp = array(
                'or_like' => array(
                    $text => $query
                )
            );
            $params = array_merge($params, $params_temp);
        }

        $params['group_end'] = true;

        if ($param != NULL) {
            if (is_array($param)) {
                $params = array_merge($params, $param);
            } else {
                $params['inline'] = $param;
            }
        }

        if ($as_text != NULL) {
            if (is_array($as_text)) {
                $text = 'CONCAT(';
                $start = TRUE;

                foreach ($as_text as $field) {
                    $text .= ($start ? '' : ', " - " ,') . $field;
                    $start = false;
                }

                $text .= ')';
            } else {
                $text = $as_text;
            }
        }

        $select = $id . " as id, " . $text . " as text";

        return $this->get_rows($table, $params, $select, $joins);
    }

    public function insert($table, $data) {
        foreach ($data as $key => $value) {
            if ($value == '')
                $data[$key] = NULL;
        }
        $this->CI->db->insert($table, $data);

        return $this->CI->db->insert_id();
    }

    public function insert_datatables($table, $data) {
        $data = $this->CI->generate->clear_token($data);

        return $this->insert($table, $data);
    }

    public function update($table, $where, $data) {
        foreach ($data as $key => $value) {
            if ($value == '')
                $data[$key] = NULL;
        }
        $this->CI->db->update($table, $data, $where);

        return $this->CI->db->affected_rows();
    }

    public function update_datatables($table, $primary_key, $posts, $edit_id) {
        $cek = $this->CI->generate->cek_update_id($edit_id, $primary_key, $posts);

        $where = $cek['where'];

        $data = array();
        if (isset($cek['data']))
            $data = $cek['data'];

        $posts = $this->CI->generate->clear_temp_post($posts, $primary_key);
        $data = array_merge($data, $posts);

        return $this->update($table, $where, $data);
    }

    public function delete($table, $where) {
        $this->CI->db->delete($table, $where);

        return $this->CI->db->affected_rows();
    }

    public function delete_datatables($table, $primary_key, $id) {
        $where = array(
            $primary_key => $id
        );

        return $this->delete($table, $where);
    }

    public function set_uniqe_value($table, $primary_key, $id, $field, $value_set = 1, $value_unset = 0) {
        $data_reset = array(
            $field => $value_unset
        );
        $where_reset = array(
            $field => $value_set
        );
        $status_reset = $this->update($table, $where_reset, $data_reset);

        if ($status_reset) {
            $data_set = array(
                $field => $value_set
            );
            $where_set = array(
                $primary_key => $id
            );
            $status_set = $this->update($table, $where_set, $data_set);
        } else {
            $status_set = FALSE;
        }

        return $status_set;
    }

    public function get_table($table, $joins, $params, $select = "*") {
        if (is_array($select)) {
            $temp_select = '';

            $start = true;
            foreach ($select as $field) {
                $temp_select .= ($start ? "" : ", ") . $field;
                $start = false;
            }

            $this->CI->db->select($temp_select);
        } else {
            $this->CI->db->select($select);
        }

        $this->CI->db->from($table);

        if ($joins != NULL) {
            foreach ($joins as $detail) {
                if (isset($detail[0]))
                    $detail['table'] = $detail[0];
                if (isset($detail[1]))
                    $detail['param'] = $detail[1];
                if (isset($detail[2]))
                    $detail['method'] = $detail[2];

                if (isset($detail['method']))
                    $this->CI->db->join($detail['table'], $detail['param'], $detail['method']);
                else
                    $this->CI->db->join($detail['table'], $detail['param']);
            }
        }

        if ($params != NULL) {
            foreach ($params as $method => $detail) {
                if ($method == 'where') {
                    foreach ($detail as $field => $value) {
                        $this->CI->db->where($field, $value);
                    }
                } elseif ($method == 'or_where') {
                    foreach ($detail as $field => $value) {
                        if (is_array($value)) {
                            foreach ($value as $value1) {
                                $this->CI->db->or_where($field, $value1);
                            }
                        } else {
                            $this->CI->db->or_where($field, $value);
                        }
                    }
                } elseif ($method == 'like') {
                    foreach ($detail as $field => $value) {
                        $this->CI->db->like('IFNULL(' . $field . ', "")', $value);
                    }
                } elseif ($method == 'or_like') {
                    foreach ($detail as $field => $value) {
                        if (is_array($value)) {
                            foreach ($value as $value1) {
                                $this->CI->db->or_like('IFNULL(' . $field . ', "")', $value1);
                            }
                        } else {
                            $this->CI->db->or_like('IFNULL(' . $field . ', "")', $value);
                        }
                    }
                } elseif ($method == 'order_by') {
                    foreach ($detail as $field => $value) {
                        $this->CI->db->order_by($field, $value);
                    }
                } elseif ($method == 'having') {
                    foreach ($detail as $field => $value) {
                        $this->CI->db->having($field, $value);
                    }
                } elseif ($method == 'group_by') {
                    foreach ($detail as $value) {
                        $this->CI->db->group_by($value);
                    }
                } elseif ($method == 'group_start') {
                    $this->CI->db->group_start();
                } elseif ($method == 'group_end') {
                    $this->CI->db->group_end();
                } elseif ($method == 'limit') {
                    $this->CI->db->limit($detail['length'], $detail['start']);
                } elseif ($method == 'inline') {
                    $this->CI->db->where($detail, NULL, FALSE);
                }
            }
        }
    }

    private function check_having_query($method, $field, $value) {
        if (!((strpos($field, 'SUM') !== false) || (strpos($field, 'MAX') !== false) || (strpos($field, 'MIN') !== false))) {
//            $this->CI->db->having('IFNULL(' . $field . ', "") = ' . $value);
            if ($method == 'like')
                $this->CI->db->like('IFNULL(' . $field . ', "")', $value);
            elseif ($method == 'or_like')
                $this->CI->db->or_like('IFNULL(' . $field . ', "")', $value);
        }
    }

    private function get_query($posts, $columns, $orders) {
        $i = 0;
        $search_value = $posts['search']['value'];
        $search_columns = $posts['columns'];
        foreach ($columns as $item) {
            if ($search_value || $search_columns) {
                if ($i === 0) {
                    $this->CI->db->group_start();
                    $this->check_having_query('like', $item, $search_value);
                } else {
                    $this->check_having_query('or_like', $item, $search_value);
                }
                if (count($search_columns) - 1 == $i) {
                    $this->CI->db->group_end();
                    break;
                }
            }
            $columns[$i] = $item;
            $i++;
        }

        $i = 0;
        foreach ($columns as $item) {
            if ($search_columns) {
                if ($i === 0)
                    $this->CI->db->group_start();
                $this->check_having_query('like', $item, $search_columns[$i]['search']['value']);
                if (count($search_columns) - 1 == $i) {
                    $this->CI->db->group_end();
                    break;
                }
            }
            $columns[$i] = $item;
            $i++;
        }

        if (isset($posts['order'])) {
            $this->CI->db->order_by($orders[$posts['order']['0']['column']], $posts['order']['0']['dir']);
        } else if (isset($orders)) {
            $this->CI->db->order_by(key($orders), $orders[key($orders)]);
        }
    }

    public function get_hari_aktif($start_date = NULL, $end_date = NULL) {
        if ($start_date == NULL)
            $start_date = '(SELECT TANGGAL_MULAI_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)';
        else
            $start_date = '"' . $start_date . '"';

        if ($end_date == NULL)
            $end_date = '(SELECT TANGGAL_AKHIR_TA FROM md_tahun_ajaran WHERE AKTIF_TA = 1)';
        else
            $end_date = '"' . $end_date . '"';

        $sql = "SELECT
    (COUNT(*) - (SELECT
            IFNULL(SUM(DATEDIFF(TGL_SELESAI_AK, TGL_MULAI_AK) + 1), 0) AS JUMLAH_LIBUR
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
        $query = $this->CI->db->query($sql);

        return $query->row()->JUMLAH_HARI_AKTIF;
    }

}
