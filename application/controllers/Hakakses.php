<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hakakses
 *
 * @author Rohmad Eko Wahyudi
 */
class Hakakses extends CI_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
        $this->load->model(array('hakakses_model' => 'hakakses', 'menu_model' => 'menu'));
        $this->auth->validation(1);
    }

    public function index() {
        $this->generate->backend_view('hakakses/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $list = $this->hakakses->get_datatables();
        $hakakses = $this->hakakses->get_hakakses();
        $levelmenu = $this->hakakses->get_levelmenu();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $menu) {
            $no++;
            $row = array();
            $row[] = $menu->NAME_MENU;

            foreach ($hakakses as $ha) {
                $checked = '';
                foreach ($levelmenu as $value) {
                    if (($value->HAKAKSES_LEVELMENU == $ha->ID_HAKAKSES) && ($value->MENU_LEVELMENU == $menu->ID_MENU))
                        $checked = 'checked="TRUE"';
                }
                $row[] = '<div class="checkbox checkbox-primary"><input type="checkbox" ' . $checked . ' id="checked_' . $ha->ID_HAKAKSES . '_' . $menu->ID_MENU . '" onchange="change_role(' . $ha->ID_HAKAKSES . ', \'' . $menu->ID_MENU . '\')"><label for="checked_' . $ha->ID_HAKAKSES . '_' . $menu->ID_MENU . '">&nbsp;&nbsp;' . $ha->NAME_HAKAKSES . '</label></div>';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->hakakses->count_all(),
            "recordsFiltered" => $this->hakakses->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function change_role() {
        $this->generate->set_header_JSON();
        
        $data = array(
            "HAKAKSES_LEVELMENU" => $this->input->post('ID_HAKAKSES'),
            "MENU_LEVELMENU" => $this->input->post('ID_MENU')
        );
        $this->hakakses->change_role($this->input->post('STATUS'), $data);
        
        $this->session->unset_userdata("MENU_USER");
        $this->session->set_userdata('MENU_USER', json_encode($this->menu->get_menu($this->session->userdata("ID_HAKAKSES"), JSON_PRETTY_PRINT)));
        
        $output = array("status" => TRUE);

        $this->generate->output_JSON($output);
    }

}
