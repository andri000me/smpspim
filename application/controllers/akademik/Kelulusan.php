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
class Kelulusan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kelulusan_model' => 'kelulusan',
            'kenaikan_model' => 'kenaikan',
            'kelas_model' => 'kelas',
            'tipe_mapel_model' => 'tipe_mapel',
            'nilai_hafalan_model' => 'nilai_hafalan',
        ));
        $this->auth->validation(array(2, 10));
        
        $this->load->library('kelulusan_handler');
    }

    public function index() {
        $this->generate->backend_view('akademik/kelulusan/index');
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
            
            $row[] = '<strong>'.number_format($this->kelulusan->get_testing($item->ID_SISWA, 'KITAB'), 2, ',', '.').'</strong>';
            $row[] = '<strong>'.number_format($this->kelulusan->get_testing($item->ID_SISWA, 'QURAN'), 2, ',', '.').'</strong>';
            
            $kta = $this->kelulusan->get_kta($item->ID_SISWA);
            $display_kta = '<select class="form-control input-sm" style="width: 60px;" data-siswa="'.$item->ID_SISWA.'" onchange="simpan_nilai_kta(this);">'
                . '<option value="" '.($kta == '-' ? "selected" : '').'>-</option>'
                . '<option value="م" '.($kta == 'م' ? "selected" : '').'>م</option>'
                . '<option value="ج ج" '.($kta == 'ج ج' ? "selected" : '').'>ج ج</option>'
                . '<option value="ج" '.($kta == 'ج' ? "selected" : '').'>ج</option>'
                . '<option value="ر" '.($kta == 'ر' ? "selected" : '').'>ر</option>'
                . '</select>';
            $row[] = '<strong>'.$display_kta.'</strong>';
            
            $row[] = '<strong>'.$this->kenaikan->get_dauroh($item->ID_SISWA).'</strong>';
            $nilai_hafalan = $this->nilai_hafalan->get_nilai($item->ID_SISWA);
            $row[] = ($nilai_hafalan == NULL) ? '-' : '<strong>'.$nilai_hafalan.'</strong>';
            $row[] = $this->kenaikan->get_absensi($item->ID_SISWA, 'SAKIT');
            $row[] = $this->kenaikan->get_absensi($item->ID_SISWA, 'IZIN');
            $row[] = '<strong>'.$this->kenaikan->get_absensi($item->ID_SISWA, 'ALPHA').'</strong>';
            $row[] = '<strong>'.$this->kenaikan->get_poin($item->ID_SISWA).'</strong>';
            
            $rata = ($rata_agama_1 + $rata_agama_2 + $rata_agama_3 + $rata_umum_1 + $rata_umum_2 + $rata_umum_3)/6;
            $naik = $naik_agama * $naik_umum;
            
            $row[] = ($item->LULUS_AS == NULL) ? '<select class="form-control">'
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
                    . '</select>' : '<strong>'.$item->LULUS_AS.'</strong>';
            
            $row[] = ($item->LULUS_AS == NULL) ? '<button class="ladda-button btn-sm btn-primary" data-style="zoom-in" onclick="proses_lulus(this);" data-id="'.$item->ID_AS.'" data-siswa="'.$item->NIS_SISWA.' - '.$item->NAMA_SISWA.'"><i class="fa fa-check-circle"></i></button>' : '-';

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
        
        $data = $this->kelas->get_kelas_kelulusan($this->input->post('ID_PEG'));
        
        $this->generate->output_JSON($data);
    }

    public function proses_lulus() {
        $this->generate->set_header_JSON();
        
        $ID_AS = $this->input->post('ID_AS');
        $TA = $this->input->post('NEXT_TA_FILTER');
        $STATUS_KELULUSAN = $this->input->post('STATUS_KELULUSAN');
        
        $status = $this->kelulusan_handler->proses($ID_AS, $TA, $STATUS_KELULUSAN);
        
        $this->generate->output_JSON(array('status' => $status));
    }

    public function simpan_nilai_kta() {
        $this->generate->set_header_JSON();
        
        $SISWA_LN = $this->input->post('SISWA_LN');
        $NILAI_LN = $this->input->post('NILAI_LN');
        
        $status = $this->kelulusan->simpan_nilai_kta($SISWA_LN, $NILAI_LN);
        
        $this->generate->output_JSON(array('status' => $status));
    }
}
