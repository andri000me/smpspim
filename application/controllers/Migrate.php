<?php

class Migrate extends CI_Controller {

    public function index() {
        $this->auth->validation();
        $this->load->library('migration');

        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo '<h1>Migration Success</h1>';
        }
    }

}
