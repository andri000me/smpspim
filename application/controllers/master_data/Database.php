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
class Database extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth->validation(11);
    }

    public function index() {
        $this->generate->backend_view('master_data/database/index');
    }

    public function backup_db() {
        $filename = './files/database/' . date('Y-m-d_H-i-s') . '.sql.gz';

        $prefs = array(
            'ignore' => array('gen_log'), 
        );

        $this->load->dbutil();
        $backup = $this->dbutil->backup($prefs);

        $this->load->helper('file');
        write_file($filename, $backup);

        $this->load->helper('download');
        force_download($filename, $backup);
    }

}
