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
class Kenaikan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kenaikan_model' => 'kenaikan',
            'kenaikan_rekap_model' => 'kenaikan_rekap',
            'kelas_model' => 'kelas',
            'tipe_mapel_model' => 'tipe_mapel',
            'nilai_hafalan_model' => 'nilai_hafalan',
        ));
        $this->auth->validation(array(2, 10));
        
        $this->load->library('kenaikan_handler');
    }

    public function index() {
        $this->generate->backend_view('akademik/kenaikan/index');
    }
    
    public function ajax_list_rekap($kelas) {
        $this->generate->set_header_JSON();
        
        $id_datatables = 'datatable2';
        $list = $this->kenaikan_rekap->get_datatables($kelas);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $row = array();
            
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = '<strong>'.($item->NAIK_AS ? 'NAIK' : 'TIDAK NAIK').'</strong>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kenaikan_rekap->count_all($kelas),
            "recordsFiltered" => $this->kenaikan_rekap->count_filtered($kelas),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function ajax_list($kelas) {
        $this->generate->set_header_JSON();
        
        $id_datatables = 'datatable1';
        $tipe_mapel = $this->tipe_mapel->get_all_array(FALSE);
        $list = $this->kenaikan->get_datatables($kelas);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $row = array();
            
            $row[] = '<strong>'.$item->NO_ABSEN_AS.'</strong>';
            $row[] = $item->NIS_SISWA;
            $row[] = '<strong>'.$item->NAMA_SISWA.'</strong>';
            
            $nilai_umum_1 = $this->kenaikan->get_nilai($item->ID_SISWA, 1, 1);
            if($nilai_umum_1->JUMLAH_MAPEL > 0) {
                $rata_umum_1 = $nilai_umum_1->TOTAL_NILAI/$nilai_umum_1->JUMLAH_MAPEL;
//                $row[] = ($nilai_umum_1->TOTAL_NILAI == '') ? '-' : number_format($rata_umum_1, 2, ',', '.');
            } else {
                $rata_umum_1 = 0;
//                $row[] = '-';
            }
            
            $nilai_umum_2 = $this->kenaikan->get_nilai($item->ID_SISWA, 2, 1);
            if($nilai_umum_2->JUMLAH_MAPEL > 0) {
                $rata_umum_2 = $nilai_umum_2->TOTAL_NILAI/$nilai_umum_2->JUMLAH_MAPEL;
//                $row[] = ($nilai_umum_2->TOTAL_NILAI == '') ? '-' : number_format($rata_umum_2, 2, ',', '.');
            } else {
                $rata_umum_2 = 0;
//                $row[] = '-';
            }
            
            $nilai_umum_3 = $this->kenaikan->get_nilai($item->ID_SISWA, 3, 1);
            if($nilai_umum_3->JUMLAH_MAPEL > 0) {
                $rata_umum_3 = $nilai_umum_3->TOTAL_NILAI/$nilai_umum_3->JUMLAH_MAPEL;
//                $row[] = ($nilai_umum_3->TOTAL_NILAI == '') ? '-' : number_format($rata_umum_3, 2, ',', '.');
            } else {
                $rata_umum_3 = 0;
//                $row[] = '-';
            }
            
            $rata_umum = ($rata_umum_1 + $rata_umum_2 + $rata_umum_3) / 3;
            $naik_umum = ($rata_umum < $tipe_mapel[0]['NAIK_MTM']) ? FALSE : TRUE;
            $show_umum = '<p class="text-'.($naik_umum ? 'default' : 'danger').'"><strong>'.$rata_umum.'</strong></p>';
            $row[] = ($nilai_umum_1->TOTAL_NILAI == '' || $nilai_umum_2->TOTAL_NILAI == '' || $nilai_umum_3->TOTAL_NILAI == '' ? '-' : $show_umum);
            
            $nilai_agama_1 = $this->kenaikan->get_nilai($item->ID_SISWA, 1, 2);
            if($nilai_agama_1->JUMLAH_MAPEL > 0) {
                $rata_agama_1 = $nilai_agama_1->TOTAL_NILAI/$nilai_agama_1->JUMLAH_MAPEL;
//                $row[] = ($nilai_agama_1->TOTAL_NILAI == '') ? '-' : number_format($rata_agama_1, 2, ',', '.');
            } else {
                $rata_agama_1 = 0;
//                $row[] = '-';
            }
            
            $nilai_agama_2 = $this->kenaikan->get_nilai($item->ID_SISWA, 2, 2);
            if($nilai_agama_2->JUMLAH_MAPEL > 0) {
                $rata_agama_2 = $nilai_agama_2->TOTAL_NILAI/$nilai_agama_2->JUMLAH_MAPEL;
//                $row[] = ($nilai_agama_2->TOTAL_NILAI == '') ? '-' : number_format($rata_agama_2, 2, ',', '.');
            } else {
                $rata_agama_2 = 0;
//                $row[] = '-';
            }
            
            $nilai_agama_3 = $this->kenaikan->get_nilai($item->ID_SISWA, 3, 2);
            if($nilai_agama_3->JUMLAH_MAPEL > 0) {
                $rata_agama_3 = $nilai_agama_3->TOTAL_NILAI/$nilai_agama_3->JUMLAH_MAPEL;
//                $row[] = ($nilai_agama_3->TOTAL_NILAI == '') ? '-' : number_format($rata_agama_3, 2, ',', '.');
            } else {
                $rata_agama_3 = 0;
//                $row[] = '-';
            }
            
            $rata_agama = ($rata_agama_1 + $rata_agama_2 + $rata_agama_3) / 3;
            $naik_agama = ($rata_agama < $tipe_mapel[1]['NAIK_MTM']) ? FALSE : TRUE;
            $show_agama = '<p class="text-'.($naik_agama ? 'default' : 'danger').'"><strong>'.$rata_agama.'</strong></p>';
            $row[] = ($nilai_agama_1->TOTAL_NILAI == '' || $nilai_agama_2->TOTAL_NILAI == '' || $nilai_agama_3->TOTAL_NILAI == '' ? '-' : $show_agama);
            
            $row[] = '<strong>'.$this->kenaikan->get_dauroh($item->ID_SISWA).'</strong>';
            $nilai_hafalan = $this->nilai_hafalan->get_nilai($item->ID_SISWA);
            $row[] = ($nilai_hafalan == NULL) ? '-' : '<strong>'.$nilai_hafalan.'</strong>';
            $row[] = $this->kenaikan->get_absensi($item->ID_SISWA, 'SAKIT');
            $row[] = $this->kenaikan->get_absensi($item->ID_SISWA, 'IZIN');
            $row[] = '<strong>'.$this->kenaikan->get_absensi($item->ID_SISWA, 'ALPHA').'</strong>';
            $row[] = '<strong>'.$this->kenaikan->get_poin($item->ID_SISWA).'</strong>';
            
            $rata = ($rata_agama_1 + $rata_agama_2 + $rata_agama_3 + $rata_umum_1 + $rata_umum_2 + $rata_umum_3)/6;
            $naik = $naik_agama * $naik_umum;
            
            $row[] = '<select class="form-control"><option value="1" selected>Naik</option><option value="0">Tidak Naik</option></select>';
//            $row[] = '<select class="form-control"><option value="1" '.($naik ? 'selected' : '').'>Naik</option><option value="0" '.($naik ? '' : 'selected').'>Tidak Naik</option></select>';
            $row[] = '<button class="ladda-button btn-proses btn btn-'.($naik ? 'success' : 'danger').' btn-sm" data-style="zoom-in" data-all="0" onclick="proses_naik(this);" data-id="'.$item->ID_AS.'" data-siswa="'.$item->NIS_SISWA.' - '.$item->NAMA_SISWA.'" data-rata="'.($naik ? 1 : 0).'"><i class="fa fa-check-circle"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kenaikan->count_all($kelas),
            "recordsFiltered" => $this->kenaikan->count_filtered($kelas),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function list_wali_kelas() {
        $this->generate->set_header_JSON();
        
        $data = $this->kelas->get_wali_kelas($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function list_kelas() {
        $this->generate->set_header_JSON();
        
        $data = $this->kelas->get_kelas_kenaikan($this->input->post('ID_PEG'));
        
        $this->generate->output_JSON($data);
    }

    public function proses_naik() {
        $this->generate->set_header_JSON();
        
        $ID_AS = $this->input->post('ID_AS');
        $TA = $this->input->post('NEXT_TA_FILTER');
        $STATUS_KENAIKAN = $this->input->post('STATUS_KENAIKAN');
        $STATUS_TAG = $this->input->post('STATUS_TAG');
        
        $status = $this->kenaikan_handler->proses($ID_AS, $TA, $STATUS_KENAIKAN, NULL, $STATUS_TAG);
        
        $this->generate->output_JSON(array('status' => $status));
    }
}
