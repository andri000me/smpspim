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
class Nilai extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_PNH";

    public function __construct() {
        parent::__construct();
        $this->load->model('nilai_hafalan_model', 'nilai');
        $this->auth->validation(5);
    }

    public function index() {
        $this->generate->backend_view('ph/nilai/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->nilai->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->NILAI_PNH;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <!--<li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_PNH . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>-->
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai->count_all(),
            "recordsFiltered" => $this->nilai->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function form($ID = NULL) {
        $data = array();
        
        if($ID == NULL) {
            $data['ADD'] = TRUE;
        } else {
            $data = array(
                'ADD' => FALSE,
                'DATA' => $this->nilai->get_detail_nilai($ID)
            );
        }
        
        $this->generate->backend_view('ph/nilai/form', $data);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $this->input->post('ID_SISWA'),
            'USER_PHN' => $this->session->userdata('ID_USER'),
        );
        
        $ID_KITAB = $this->input->post('ID_KITAB');
        $NILAI_PHN = $this->input->post('NILAI_PHN');
        $BATASAN_PHN = $this->input->post('BATASAN_PHN');
        $NILAI_MAKS_PNH = $this->input->post('NILAI_MAKS_BATASAN');
        $PENYEMAK_PHN = $this->input->post('PENYEMAK');
        
        $NILAI_PNH = array();
        for ($i = 0; $i < count($NILAI_PHN); $i++) {
            $data['BATASAN_PHN'] = $BATASAN_PHN[$i];
            $data['NILAI_PHN'] = $NILAI_PHN[$i];
            $data['PENYEMAK_PHN'] = $PENYEMAK_PHN[$i];
            $NILAI_PNH[$ID_KITAB[$i]][] = $NILAI_PHN[$i];
            
            $insert = $this->nilai->simpan_nilai($data);
        }
        
        $this->hitung_nilai($data['SISWA_PHN'], $NILAI_MAKS_PNH, $NILAI_PHN);

        $this->generate->output_JSON(array("status" => 1));
    }
    
    private function hitung_nilai($ID_SISWA, $NILAI_MAKS_PNH, $NILAI_PHN) {
        $NILAI_AKHIR = array();
        $NILAI = array_combine($NILAI_MAKS_PNH, $NILAI_PHN);
        $TEMP_NILAI_MAKS = 0;
        $TEMP = 0;
        ksort($NILAI);
        foreach ($NILAI as $NILAI_MAKS => $NILAI_SISWA) {
            $TEMP_NILAI_MAKS += $NILAI_MAKS;
            if($TEMP_NILAI_MAKS > 100) {
                $TEMP++;
                $TEMP_NILAI_MAKS = $NILAI_MAKS;
                $NILAI_AKHIR[$TEMP] = $NILAI_SISWA;
            } else {
                $NILAI_AKHIR[$TEMP] = $NILAI_SISWA + (isset($NILAI_AKHIR[$TEMP]) ? $NILAI_AKHIR[$TEMP] : 0);
            }
        }
        
        $NILAI_TOTAL = 0;
        foreach ($NILAI_AKHIR as $NILAI) {
            $NILAI_TOTAL += $NILAI;
        }
        
        $NILAI_RATA = $NILAI_TOTAL/count($NILAI_AKHIR);
        
        $this->nilai->reset_nilai_header($ID_SISWA, $NILAI_RATA);
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = array(
            'USER_PHN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $this->input->post('ID_SISWA'),
        );
        
        $ID_KITAB = $this->input->post('ID_KITAB');
        $NILAI_PHN = $this->input->post('NILAI_PHN');
        $NILAI_MAKS_PNH = $this->input->post('NILAI_MAKS_BATASAN');
        $BATASAN_PHN = $this->input->post('BATASAN_PHN');
        $PENYEMAK_PHN = $this->input->post('PENYEMAK');
        
        $NILAI_PNH = array();
        for ($i = 0; $i < count($NILAI_PHN); $i++) {
            $where['BATASAN_PHN'] = $BATASAN_PHN[$i];
            $data['NILAI_PHN'] = $NILAI_PHN[$i];
            $data['PENYEMAK_PHN'] = $PENYEMAK_PHN[$i];
            $NILAI_PNH[$ID_KITAB[$i]][] = $NILAI_PHN[$i];
            
            $insert = $this->nilai->ubah_nilai($where, $data);
        }
        
        $this->hitung_nilai($where['SISWA_PHN'], $NILAI_MAKS_PNH, $NILAI_PHN);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->nilai->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->nilai->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
    
    public function get_batasan() {
        $this->generate->set_header_JSON();
        
        $data = $this->nilai->get_batasan($this->input->post('ID_SISWA'));
        
        $this->generate->output_JSON(array('data' => $data));
    }
    
    public function get_penyemak() {
        $this->generate->set_header_JSON();
        
        $data = $this->nilai->get_penyemak();
        
        $this->generate->output_JSON(array('data' => $data));
    }

    
    private function hitung_nilai_backup($ID_SISWA, $DATA) {
        $NILAI_RATA = 0;
        $NILAI_AKHIR = array();
        
        foreach ($DATA as $KITAB => $NILAI) {
            $temp = 0;
            foreach ($NILAI as $DETAIL_NILAI) {
                $temp += $DETAIL_NILAI;
            }
            $NILAI_AKHIR[$KITAB] = $temp;
        }
        
        $temp = 0;
        foreach ($NILAI_AKHIR as $DETAIL_NILAI) {
            $temp += $DETAIL_NILAI;
        }
        
        $NILAI_RATA = $temp/count($DATA);
        
        var_dump($DATA);
        echo '<br>';
        echo json_encode($DATA);
        exit();
        $this->nilai->reset_nilai_header($ID_SISWA, $NILAI_RATA);
    }
}
