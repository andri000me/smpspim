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
class Siswa_kelas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'akad_siswa_model' => 'akad_siswa',
            'kelas_model' => 'kelas',
            'tingkat_model' => 'tingkat'
        ));
        $this->load->library('nis_handler');
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/siswa_kelas/index');
    }
    
    private function option_tingkat($data, $id, $jk, $tag_open) {
        $tag = '<select class="form-control" onchange="change_tingkat(this, \''.$jk.'\');" >';
        foreach ($data as $detail) {
            $tag .= '<option value="'.$detail->ID_TINGK.'" ';
            if ($id == $detail->ID_TINGK) {
                $tag .= 'selected';
                if($tag_open == "") {
                    $tag = $detail->DEPT_TINGK.' - '.$detail->NAMA_TINGK;
                    break;
                }
            }
            $tag .= '>'.$detail->DEPT_TINGK.' - '.$detail->NAMA_TINGK.'</option>';
            if ($id == $detail->ID_TINGK) break;
        }
        $tag .= '</select>';
        
        return $tag;
    }
    
    private function option_kelas($data) {
        $tag = '<select class="form-control">';
        $tag .= '<option value="" >-- Pilih Kelas --</option>';
        foreach ($data as $detail) {
            $tag .= '<option value="'.$detail->ID_KELAS.'" >'.$detail->NAMA_KELAS.'</option>';
        }
        $tag .= '</select>';
        
        return $tag;
    }
    
    private function terima_siswa($ID_AS, $tag_open) {
        if ($tag_open) 
            return '<button class="ladda-button btn btn-success btn-sm" data-style="zoom-in" onclick="proses_lulus(this);" data-id="'.$ID_AS.'" '.(($tag_open == "") ? "disabled" : "").'><i class="fa fa-check-circle"></i></button>';
        else
            return '<button class="ladda-button btn btn-danger btn-sm" data-style="zoom-in" onclick="hapus_kelas(this);" data-id="'.$ID_AS.'" '.(($tag_open != "") ? "disabled" : "").'><i class="fa fa-remove"></i></button>';
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();
        
        $data_tingkat = $this->tingkat->get_all_urut();

        $id_datatables = 'datatable1';
        $list = $this->akad_siswa->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            
            if($item->NAMA_PEG == NULL) {
                $tag_open = "<strong>";
                $tag_close = "</strong>";
            } else {
                $tag_open = "";
                $tag_close = "";
            }
            
            $where_kelas = array(
                'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
                'TINGKAT_KELAS' => $item->ID_TINGK,
                'JK_KELAS' => $item->JK_SISWA,
                'AKTIF_KELAS' => 1,
            );
            
            $data_kelas = $this->kelas->get_rows($where_kelas);
            
//            $row[] = $tag_open.$item->NAMA_TA.$tag_close;
            $row[] = $tag_open.$item->NIS_SISWA_SHOW.$tag_close;
            $row[] = $tag_open.$item->NO_ABSEN_AS_SHOW.$tag_close;
            $row[] = $tag_open.$item->NAMA_SISWA.$tag_close;
            $row[] = $tag_open.$item->ANGKATAN_SISWA.$tag_close;
            $row[] = $tag_open.$item->JK_SISWA.$tag_close;
            $row[] = $this->option_tingkat($data_tingkat, $item->ID_TINGK, $item->JK_SISWA, $tag_open);
            $row[] = ($tag_open == "") ? $item->NAMA_KELAS_SHOW : $this->option_kelas($data_kelas);
            $row[] = $item->NAMA_PEG_SHOW;
            $row[] = $item->AKTIF_AS ? (($item->ID_TA == $this->session->userdata("ID_TA_ACTIVE")) ? $this->terima_siswa($item->ID_AS, $tag_open) : "-") : '<strong>KELUAR</strong>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->akad_siswa->count_all(),
            "recordsFiltered" => $this->akad_siswa->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function proses_kelas() {
        $this->generate->set_header_JSON();
        
        $data_tingkat = $this->tingkat->get_all(false);
        foreach ($data_tingkat as $tingkat) {
            $this->proses_distribusi_kelas($tingkat->ID_TINGK, 'L');
            $this->proses_distribusi_kelas($tingkat->ID_TINGK, 'P');
        }
        
        $this->generate->output_JSON(array('status' => 1));
    }
    
    private function proses_distribusi_kelas($tingkat, $jk) {
        $data_kelas = $this->kelas->get_rows_array(array(
            'TINGKAT_KELAS' => $tingkat,
            'JK_KELAS' => $jk,
            'AKTIF_KELAS' => 1
        ));
        $data_siswa = $this->akad_siswa->get_data_sort_nilai($tingkat, $jk);
        
        $i = 0;
        foreach ($data_siswa as $detail_siswa) {
            if(!isset($data_kelas[$i])) $i = 0;
            
            $loop = FALSE;
            while($data_kelas[$i]['KAPASITAS_RUANG'] < $data_kelas[$i]['JUMLAH_SISWA_KELAS']) {
                unset($data_kelas[$i]);
                array_values($data_kelas);
                
                if(!isset($data_kelas[$i])) {
                    if($loop || (count($data_kelas) == 0)) {
                        break 2;
                    } else {
                        $i = 0;
                        $loop = TRUE;
                    }
                }
            }
//            echo $i.'=>'.$data_kelas[$i]['ID_KELAS'].', '.$data_kelas[$i]['JUMLAH_SISWA_KELAS'].', '.$data_kelas[$i]['KAPASITAS_RUANG'].', '.$detail_siswa['ID_AS'].'<br>';
            // SET KELAS SISWA
            $data_akad_siswa = array('KELAS_AS' => $data_kelas[$i]['ID_KELAS']);
            $where_akad_siswa = array('ID_AS' => $detail_siswa['ID_AS']);

            $this->akad_siswa->update($where_akad_siswa, $data_akad_siswa);

            // UPDATE JUMLAH SISWA DI AKAD_KELAS
            $data_kelas[$i]['JUMLAH_SISWA_KELAS']++;
            $data_akad_kelas = array('JUMLAH_SISWA_KELAS' => $data_kelas[$i]['JUMLAH_SISWA_KELAS']);
            $where_akad_kelas = array('ID_KELAS' => $data_kelas[$i]['ID_KELAS']);

            $this->kelas->update($where_akad_kelas, $data_akad_kelas);
            
            $i++;
        }
    }

//    public function random_siswa() {
//        $this->generate->set_header_JSON();
//
//        $data_tingkat = $this->tingkat->get_all(false);
//        foreach ($data_tingkat as $tingkat) {
//            $this->proses_random('L', $tingkat->ID_TINGK);
//            $this->proses_random('P', $tingkat->ID_TINGK);
//        }
//
//        $this->generate->output_JSON(array('status' => 1));
//    }

    private function proses_random($jk, $tingkat) {
        $data_kelas = $this->kelas->get_rows(array(
            'TINGKAT_KELAS' => $tingkat,
            'JK_KELAS' => $jk
        ));
        
        $data_siswa = $this->akad_siswa->get_rows_array(array(
            'JK_SISWA' => $jk,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KELAS_AS' => NULL,
            'TINGKAT_AS' => $tingkat
        ));
        
        shuffle($data_siswa);
        
        $index = 0;
        foreach ($data_kelas as $kelas) {
            for ($i = $kelas->JUMLAH_SISWA_KELAS; $i < $kelas->KAPASITAS_RUANG; $i++) {
                if (!isset($data_siswa[$index])) break 2;
                
                // SET KELAS SISWA
                $data_akad_siswa = array('KELAS_AS' => $kelas->ID_KELAS);
                $where_akad_siswa = array('ID_AS' => $data_siswa[$index]['ID_AS']);

                $this->akad_siswa->update($where_akad_siswa, $data_akad_siswa);
                
                // UPDATE JUMLAH SISWA DI AKAD_KELAS
                $data_akad_kelas = array('JUMLAH_SISWA_KELAS' => ($i + 1));
                $where_akad_kelas = array('ID_KELAS' => $kelas->ID_KELAS);

                $this->kelas->update($where_akad_kelas, $data_akad_kelas);
                
                $index++;
            }
        }
    }
    
    public function proses_lulus() {
        $this->generate->set_header_JSON();

        $KELAS_AS = $this->input->post("KELAS_AS");
        $ID_AS = $this->input->post("ID_AS");
        $TINGKAT_AS = $this->input->post("TINGKAT_AS");
        
        $data_kelas = $this->kelas->get_by_id($KELAS_AS);
        
        if($data_kelas->JUMLAH_SISWA_KELAS >= $data_kelas->KAPASITAS_RUANG) 
            $this->generate->output_JSON(array('status' => 0, 'msg' => "Kelas sudah penuh. Silahkan pilih kelas lain."));
        
        // UPDATE JUMLAH SISWA DI AKAD_KELAS
        $data_akad_kelas = array('JUMLAH_SISWA_KELAS' => ($data_kelas->JUMLAH_SISWA_KELAS + 1));
        $where_akad_kelas = array('ID_KELAS' => $KELAS_AS);

        $this->kelas->update($where_akad_kelas, $data_akad_kelas);
        
        // SET KELAS SISWA
        $data_akad_siswa = array(
            'KELAS_AS' => $KELAS_AS,
            'TINGKAT_AS' => $TINGKAT_AS,
        );
        $where_akad_siswa = array('ID_AS' => $ID_AS);

        $this->akad_siswa->update($where_akad_siswa, $data_akad_siswa);

        $this->generate->output_JSON(array('status' => 1));
    }
    
    public function change_tingkat() {
        $this->generate->set_header_JSON();
        
        $ID_TINGK = $this->input->post('ID_TINGK');
        $JK_SISWA = $this->input->post('JK_SISWA');
        
        $where_kelas = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
            'TINGKAT_KELAS' => $ID_TINGK,
            'JK_KELAS' => $JK_SISWA,
            'AKTIF_KELAS' => 1,
        );

        $data_kelas = $this->kelas->get_rows($where_kelas);
        
        $this->generate->output_JSON(array('kelas' => $data_kelas));
    }
    
    public function proses_nis() {
        $this->generate->set_header_JSON();
        
        $result = $this->nis_handler->proses();
        
        $this->generate->output_JSON(array('count' => $result));
    }
    
    public function proses_absen() {
        $this->generate->set_header_JSON();
        
        $result = $this->nis_handler->proses_absen();
        
        $this->generate->output_JSON(array('count' => $result));
    }
    
    public function hapus_kelas() {
        $this->generate->set_header_JSON();
        
        $ID_AS = $this->input->post("ID_AS");
        
        $data_akad_siswa = (array) $this->akad_siswa->get_by_id_simple($ID_AS);
        
        $ID_KELAS = $data_akad_siswa['KELAS_AS'];
        
        $data_akad_siswa['KELAS_AS'] = NULL;
        $data_akad_siswa['NO_ABSEN_AS'] = NULL;
        $where_akad_siswa = array('ID_AS' => $ID_AS);
        $result = $this->akad_siswa->update($where_akad_siswa, $data_akad_siswa);
        
        if($result) {
            $data_kelas = (array) $this->kelas->get_by_id_simple($ID_KELAS);

            $data_kelas['JUMLAH_SISWA_KELAS']--;
            $where_kelas = array('ID_KELAS' => $ID_KELAS);
            $this->kelas->update($where_kelas, $data_kelas);
        }
        
        $this->generate->output_JSON(array('status' => $result));
    }

}
