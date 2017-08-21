<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Time_format {
    
    public function jam_menit($data) {
        return date("H:i", strtotime($data));
    }
    
}