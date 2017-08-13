<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Db_log {

    function __construct() {
        // Anything except exit() :P
    }

    // Name of function same as mentioned in Hooks Config
    function logQueries() {
        $CI = & get_instance();
        
        $times = $CI->db->query_times;                   // Get execution time of all the queries executed by controller
        foreach ($CI->db->queries as $key => $query) {
            $data = array(
                'FROM_LOG' => 'HOOK',
                'IP_LOG' => $CI->input->ip_address(),
                'PATH_LOG' => $CI->router->directory,
                'CONTROLLER_LOG' => $CI->router->class,
                'METHOD_LOG' => $CI->router->method,
                'SESSION_LOG' => json_encode($CI->session->all_userdata()),
                'QUERY_LOG' => $query,
                'EXECUTION_TIME_LOG' => $times[$key]
            );
            $CI->db->insert('gen_log', $data);
        }
    }

}
