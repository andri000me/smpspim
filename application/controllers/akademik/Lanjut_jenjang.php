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
class Lanjut_jenjang extends CI_Controller {
    
    var $jenis = 'DAUROH';
    var $dept_lanjut_jenjang = array(
        6 => 11,
        8 => 11,
        10 => 14,
        13 => 14
    );

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'lanjut_jenjang_model' => 'lanjut_jenjang',
            'tahun_ajaran_model' => 'ta',
            'tingkat_model' => 'tingkat',
            'siswa_model' => 'siswa'
        ));
        $this->load->library('kenaikan_handler');
        $this->auth->validation(2);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'DEPT' => $this->tingkat->get_tingkat_dept()
        );
        $this->generate->backend_view('akademik/lanjut_jenjang/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $dept = $this->tingkat->get_tingkat_dept();
        
        $id_datatables = 'datatable1';
        $list = $this->lanjut_jenjang->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            
            $row_dept = '<select class="form-control">';
            foreach ($dept as $detail) {
                $row_dept .= '<option value="'.$detail->ID_TINGK.'" '.(isset($this->dept_lanjut_jenjang[$item->TINGKAT_AS]) ? ($this->dept_lanjut_jenjang[$item->TINGKAT_AS] == $detail->ID_TINGK ? 'selected' : '') : '').'>'.$detail->DEPT_TINGK.'</option>';
            }
            $row_dept .= '</select>';
            
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = $row_dept;
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="proses_siswa(this)" data-id="'.$item->ID_SISWA.'" data-siswa="'.$item->ID_AS.'" data-nama="'.$item->NAMA_SISWA.'"><i class="fa fa-arrow-circle-right"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->lanjut_jenjang->count_all(),
            "recordsFiltered" => $this->lanjut_jenjang->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function proses_siswa() {
        $this->generate->set_header_JSON();
        
        $ID_AS = $this->input->post('ID_AS');
        $ID_SISWA = $this->input->post('ID_SISWA');
        $ID_TA = $this->input->post('ID_TA');
        $ID_TINGK = $this->input->post('ID_TINGK');
        $STATUS_KENAIKAN = 1;
        
        $result = $this->kenaikan_handler->proses($ID_AS, $ID_TA, $STATUS_KENAIKAN, $ID_TINGK);
        
        if($result) {
            $data = array(
                'AKTIF_SISWA' => 1,
                'ALUMNI_SISWA' => 0,
                'STATUS_ASAL_SISWA' => 1
            );
            $where = array(
                'ID_SISWA' => $ID_SISWA
            );
            $this->siswa->update($where, $data);
        }
        
        $this->generate->output_JSON(array('status' => $result));
    }

}
