<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Db_log {
 
    function __construct() {
        
    }
 
    function logQueries($query) {
        $CI = & get_instance();
 
        $filepath = APPPATH . 'logs/Query-log-' . date('Y-m-d') . '.log'; 
        $handle = fopen($filepath, "a+");                 
           
        $sql = "Time: ". date("d-m-Y H:i:s") 
            ."\nUser: ".$CI->session->userdata("ID_USER") 
            ."\n----------------------------------------------------------------------------------" 
            ."\n".$query
            ."\n----------------------------------------------------------------------------------" 
            ."\n\n==================================================================================";
        
        fwrite($handle, $sql . "\n\n");  
 
        fclose($handle);
    }
 
}