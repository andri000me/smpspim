<?php

class Migrate extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->auth->validation();
        $this->load->library('migration');
    }

    public function index() {
        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo '<h1>Migration Success</h1>';
        }
    }
    
    public function version($version) {
        if ($this->migration->version($version) === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo '<h1>Migration Success</h1>';
        }
    }

}
