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
class Kelulusan_tt extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kelulusan_tt_model' => 'kelulusan_tt',
            'kelulusan_model' => 'kelulusan',
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/kelulusan_tt/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kelulusan_tt->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_KELAS;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = $item->LULUS_AS;
            $row[] = '<strong>'.number_format($this->kelulusan->get_testing($item->ID_SISWA, 'KITAB', $item->ID_TA), 2, ',', '.').'</strong>';
            $row[] = '<strong>'.number_format($this->kelulusan->get_testing($item->ID_SISWA, 'QURAN', $item->ID_TA), 2, ',', '.').'</strong>';
            $row[] = '<select class="form-control">'
                    . '<option value=""> - </option>'
                    . '<option value="L">L</option>'
//                    . '<option value="T">T</option>'
//                    . '<option value="TT">TT</option>'
                    . '<option value="TQ">TQ</option>'
                    . '<option value="TK">TK</option>'
                    . '<option value="TTK">TTK</option>'
                    . '<option value="TTQ">TTQ</option>'
                    . '<option value="TQTK">TQ+TK</option>'
                    . '<option value="TQTTK">TQ+TTK</option>'
                    . '<option value="TTQTK">TTQ+TK</option>'
                    . '<option value="TTQTTK">TTQ+TTK</option>'
                    . '</select>';
            $row[] = '<button class="ladda-button btn-sm btn-primary" data-style="zoom-in" onclick="proses_lulus(this);" data-id="'.$item->ID_AS.'" data-siswa="'.$item->NIS_SISWA.' - '.$item->NAMA_SISWA.'"><i class="fa fa-check-circle"></i></button>';
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kelulusan_tt->count_all(),
            "recordsFiltered" => $this->kelulusan_tt->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
