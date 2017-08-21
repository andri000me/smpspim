<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sekolah
 *
 * @author rohmad
 */
class Log_query extends CI_Controller {

    var $edit_id = TRUE;
    var $primary_key = "ID_AGAMA";

    public function __construct() {
        parent::__construct();
        $this->load->model('log_query_model', 'log_query');
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/log_query/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->log_query->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $SESSION = json_decode($item->SESSION_LOG, TRUE);
            $SESSION_SHOW = json_encode(array(
                'ID_USER' => $SESSION['ID_USER'],
                'NAME_USER' => $SESSION['NAME_USER'],
                'FULLNAME_USER' => $SESSION['FULLNAME_USER'],
                'NAME_HAKAKSES' => isset($SESSION['NAME_HAKAKSES']) ? $SESSION['NAME_HAKAKSES'] : NULL,
                'ID_PSB_ACTIVE' => $SESSION['ID_PSB_ACTIVE'],
                'ID_TA_ACTIVE' => $SESSION['ID_TA_ACTIVE'],
                'ID_CAWU_ACTIVE' => $SESSION['ID_CAWU_ACTIVE'],
            ));

            $no++;
            $row = array();
            $row[] = $item->ID_LOG;
            $row[] = $item->DATE_LOG;
            $row[] = $item->IP_LOG;
            $row[] = $item->URI_LOG;
            $row[] = $SESSION_SHOW;
            $row[] = $item->QUERY_LOG;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->log_query->count_all(),
            "recordsFiltered" => $this->log_query->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function clear_log() {
        $this->generate->set_header_JSON();

        $this->auth->clear_log();

        $this->generate->output_JSON(array('status' => TRUE));
    }

}
