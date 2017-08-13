<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_handler {

    public function __construct() {
        $this->CI = & get_instance();
    }
    
    public function create($file_name, $text) {
//        if (!file_exists($file_name)) {
            $this->CI->load->library('zend');
            $this->CI->zend->load('Zend/Barcode');
            $img_barcode = Zend_Barcode::draw('code39', 'image', array('text' => $text, 'drawText' => false), array());
            imagepng($img_barcode, $file_name);
//        }
    }
}
