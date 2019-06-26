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
class Nilai_um extends CI_Controller {
    
    var $tipe = 'UM';

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'nilai_um_model' => 'nilai_um',
            'jadwal_pu_model' => 'jadwal_pu',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'tingkat_model' => 'tingkat',
            'siswa_model' => 'siswa',
            'aturan_denah_model' => 'aturan_denah',
            'akad_siswa_model' => 'akad_siswa',
            'kelas_model' => 'kelas'
        ));
        $this->auth->validation(array(3, 6));
    }
    
    private function get_tingkat() {
        $data_jenjang_tingkat = json_decode($this->pengaturan->getUjianPSB(), TRUE);
        
        foreach ($data_jenjang_tingkat as $jenjang => $value) {
            foreach ($value as $tingkat) {
                $data_relasi = $this->jenjang_sekolah->relasi_jenjang_departemen_tingkat($jenjang, $tingkat);
                
                $data[] = array(
                    'VALUE' => $data_relasi->ID_TINGK,
                    'TEXT' => $data_relasi->DEPT_TINGK.' KELAS: '.$data_relasi->NAMA_TINGK,
                );
            }
        }
        
        return $data;
    }
    
    private function option_tingkat($data, $id, $ID_TINGK) {
       $tag = '<select class="form-control">';
        // $tag = "-";
        foreach ($data as $detail) {
           $tag .= '<option value="'.$detail->ID_TINGK.'" ';
           if ($id == $detail->ID_TINGK) $tag .= 'selected';
           $tag .= '>'.$detail->DEPT_TINGK.' - '.$detail->NAMA_TINGK.'</option>';
           if ($ID_TINGK == $detail->ID_TINGK) break;
            // if ($id == $detail->ID_TINGK) $tag = $detail->DEPT_TINGK.' - '.$detail->NAMA_TINGK;
        }
       $tag .= '</select>';
        
        return $tag;
    }
    
    private function terima_siswa($ID_SISWA) {
        return '<button class="ladda-button btn btn-success btn-sm" data-style="zoom-in" onclick="proses_lulus(this);" data-siswa="'.$ID_SISWA.'"><i class="fa fa-check-circle"></i></button>';
    }

    public function index() {
        $data['validasi_denah'] = $this->aturan_denah->is_um_validasi();
        $data['STATUS_JADWAL'] = ($this->jadwal_pu->count_all($this->tipe) > 0) ? TRUE : FALSE;
        $data['JENJANG_DEPT'] = $this->get_tingkat();
        
        $this->generate->backend_view('pu/nilai_um/index', $data);
    }

    public function ajax_list($ID_TINGK, $JK_SISWA) {
        $this->generate->set_header_JSON();
        
        $data_mapel = $this->jadwal_pu->get_mapel_by_tingkat($this->tipe, $ID_TINGK);
        $data_tingkat = $this->tingkat->get_all_urut();
        $nilai_lulus_psb = json_decode($this->pengaturan->getNilaiLulusPSB(), TRUE);

        $id_datatables = 'datatable1';
        $list = $this->nilai_um->get_datatables($ID_TINGK, $JK_SISWA);
        $temp_id = $ID_TINGK;
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NO_UM_SISWA;
            $row[] = $item->NAMA_SISWA;
            
            $jumlah_mapel_lisan = 0;
            $total_nilai_lisan = 0;
            $jumlah_mapel_tulis = 0;
            $total_nilai_tulis = 0;
            foreach ($data_mapel as $value) {
                $nilai = $this->nilai_um->get_data($value['ID_PUJ'], $item->ID_SISWA, $value['MAPEL_PUM']);
                
                if($value['JENIS_PUM'] == 'LISAN') {
                    $jumlah_mapel_lisan++;
                    $total_nilai_lisan += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
                } else {
                    $jumlah_mapel_tulis++;
                    $total_nilai_tulis += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
                }

                $row[] = '<input type="text" class="form-control input-sm input-nilai" style="width:50px" maxlength="5" data-id="'.$value['ID_PUJ'].'" data-mapel="'.$value['MAPEL_PUM'].'" data-siswa="'.$item->ID_SISWA.'" onchange="simpan_nilai(this);" value="'.($nilai == NULL ? "" : $nilai->NILAI_PNU).'"/>';
            }

            $rata_tulis = number_format($jumlah_mapel_tulis > 0 ? $total_nilai_tulis/$jumlah_mapel_tulis : 0, 1, '.', ',');
            $rata_lisan = number_format($jumlah_mapel_lisan > 0 ? $total_nilai_lisan/$jumlah_mapel_lisan : 0, 1, '.', ',');

            $row[] = $rata_tulis;
            $row[] = $rata_lisan;

            // CEK KELULUSAN PSB
            $temp_id = $this->check_kelulusan($ID_TINGK, $nilai_lulus_psb, $rata_tulis, $rata_lisan);
            
            if ($nilai == NULL) {
                $row[] = $this->option_tingkat($data_tingkat, $temp_id, $ID_TINGK);
                $row[] = $this->terima_siswa($item->ID_SISWA);
            } elseif($nilai->LOLOS_TINGKAT_PNU == NULL) {
                $row[] = $this->option_tingkat($data_tingkat, $temp_id, $ID_TINGK);
                $row[] = $this->terima_siswa($item->ID_SISWA);
            } else {
                $tingkat = $this->tingkat->get_by_id($nilai->LOLOS_TINGKAT_PNU);
                $row[] = $tingkat->NAMA_DEPT.' KELAS: '.$tingkat->NAMA_TINGK;
                $row[] = '';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai_um->count_all($ID_TINGK, $JK_SISWA),
            "recordsFiltered" => $this->nilai_um->count_filtered($ID_TINGK, $JK_SISWA),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    private function check_kelulusan($ID_TINGK, $nilai_lulus_psb, $rata_tulis, $rata_lisan) {
        $temp_id = $ID_TINGK;
        $temp_lulus = $nilai_lulus_psb[$temp_id];
        $temp_lisan = $temp_lulus['LISAN'];
        unset($temp_lulus['LISAN']);
        $i = 0;
        $log = '';
        if($temp_lulus == null)
            return $temp_id;
            
        foreach ($temp_lulus as $temp_detail) {
            if(!isset($temp_detail['MAX'])) {
                $temp_id = $temp_detail['TINGK'];
                break;
            }

            if (($rata_tulis > $temp_detail['MAX']) || (($rata_tulis <= $temp_detail['MAX']) && ($rata_tulis > $temp_detail['MIN']) && ($rata_lisan >= $temp_lisan))) {
                $temp_id = $temp_detail['TINGK'];
                break;
            } elseif(($rata_tulis <= $temp_detail['MAX']) && ($rata_tulis > $temp_detail['MIN']) && ($rata_lisan < $temp_lisan)) {
                $temp_id = isset($temp_lulus[$i + 1]['TINGK']) ? $temp_lulus[$i + 1]['TINGK'] : $temp_lulus[$i]['TINGK'];
                break;
            }

            $i++;
        }

        return $temp_id;
    }
    
    public function get_mapel() {
        $this->generate->set_header_JSON();
        
        $data = $this->jadwal_pu->get_mapel_by_tingkat($this->tipe, $this->input->post('ID_TINGK'));
        
        $this->generate->output_JSON(array('DATA' => $data));
    }
    
    public function simpan_nilai() {
        $this->generate->set_header_JSON();
        
        $data_input = $this->input->post();
        
        $data['NILAI_PNU'] = $data_input['NILAI_PNU'];
        unset($data_input['NILAI_PNU']);
        $where = $data_input;
        
        if ($this->nilai_um->is_stored($where)) {
            $status = $this->nilai_um->update($where, $data);
        } else {
            $where['NILAI_PNU'] = $data['NILAI_PNU'];
            $status = $this->nilai_um->save($where);
        }
        
        $this->generate->output_JSON(array('status' => $status));
    }
    
    // public function proses_kelulusan_all() {
    //     $this->generate->set_header_JSON();
        
    //     $ID_TINGK = $this->input->post('ID_TINGK');
    //     $JK_SISWA = $this->input->post('JK_SISWA');
        
    //     $data_siswa = $this->nilai_um->get_all($ID_TINGK, $JK_SISWA);
        
    //     foreach ($data_siswa as $detail) {
    //         $this->proses_kelulusan($detail->ID_SISWA);
    //     }
        
    //     $this->generate->output_JSON(array('status' => 1));
    // }
    
    public function proses_kelulusan($ID_SISWA = NULL) {
        $this->generate->set_header_JSON();
        
        if ($ID_SISWA == NULL) {
            $FROM_POST = TRUE;
            $ID_SISWA = $this->input->post('ID_SISWA');
            $ID_TINGK = $this->input->post('ID_TINGK');
            $ID_TINGK_FILTER = $this->input->post('ID_TINGK_FILTER');
        } else {
            $FROM_POST = FALSE;
        }
        
        $data_mapel = $this->jadwal_pu->get_mapel_by_tingkat($this->tipe, $ID_TINGK_FILTER);
        
        $jumlah_mapel_lisan = 0;
        $total_nilai_lisan = 0;
        $jumlah_mapel_tulis = 0;
        $total_nilai_tulis = 0;
        foreach ($data_mapel as $value) {
            $nilai = $this->nilai_um->get_data($value['ID_PUJ'], $ID_SISWA, $value['MAPEL_PUM']);
            
            if($value['JENIS_PUM'] == 'LISAN') {
                $jumlah_mapel_lisan++;
                $total_nilai_lisan += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
            } else {
                $jumlah_mapel_tulis++;
                $total_nilai_tulis += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
            }
        }
        $rata_tulis = $jumlah_mapel_tulis > 0 ? ($total_nilai_tulis/$jumlah_mapel_tulis) : 0;
        $rata_lisan = $jumlah_mapel_lisan > 0 ? ($total_nilai_lisan/$jumlah_mapel_lisan) : 0;
        
        $data = array('AKTIF_SISWA' => 1);
        $where = array('ID_SISWA' => $ID_SISWA);
        
        $status = $this->siswa->update($where, $data);
        
        // MEMASUKAN SISWA KE AKADEMIK
        $data_akad = array(
            'TA_AS' => $this->session->userdata('ID_PSB_ACTIVE'),
            'SISWA_AS' => $ID_SISWA,
            'TINGKAT_AS' => $ID_TINGK,
            'NILAI_1_AS' => $rata_tulis,
            'NILAI_2_AS' => $rata_lisan,
            'USER_AS' => $this->session->userdata("ID_USER")
        );

        if($status) $this->akad_siswa->save($data_akad);

        if ($FROM_POST) $this->generate->output_JSON(array('status' => $status));
    }
    
    public function cetak_surat() {
        $ID_TINGK = $this->input->get('ID_TINGK');
        $JK_SISWA = $this->input->get('JK_SISWA');
        $MILADIYAH = $this->input->get('MILADIYAH');
        $HIJRIYAH = $this->input->get('HIJRIYAH');
        
        $where = array(
            'NIS_SISWA' => NULL,
            'AKTIF_SISWA' => 1,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
            'JK_SISWA' => $JK_SISWA,
            'mdt.ID_TINGK' => $ID_TINGK
        );
        
        $data = array(
            'SISWA' => $this->siswa->get_rows_aktif($where),
            'TINGKAT' => $this->tingkat->get_by_id($ID_TINGK),
            'TANGGAL' => $this->jadwal_pu->get_tanggal_aktif($this->tipe),
            'KETUA' => $this->pengaturan->getDataKetuaPU(),
            'MILADIYAH' => $MILADIYAH,
            'HIJRIYAH' => $HIJRIYAH
        );
        
        $this->load->view('backend/pu/nilai_um/cetak', $data);
    }
    
    public function cetak_daftar_nilai() {
        $data['siswa'] = $this->nilai_um->get_data_all();

        $this->load->view('backend/pu/nilai_um/cetak_daftar_nilai', $data);
    }

    public function cetak_pengumuman($pdf) {
        $where = array(
            'NIS_SISWA' => NULL,
            'AKTIF_SISWA' => 1,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
        );
       if($pdf) 
           $sortir = array(
               'DEPT_TINGK_PSB' => 'ASC',
               'NAMA_TINGK_PSB' => 'ASC',
               'NO_UM_SISWA' => 'ASC',
           );
       else 
            $sortir = array(
                'DEPT_TINGK_PSB' => 'ASC',
                'NAMA_TINGK_PSB' => 'ASC',
                'JK_SISWA' => 'ASC',
                'NAMA_SISWA' => 'ASC',
            );
        $data['siswa'] = $this->siswa->get_rows_aktif($where, $sortir);
        
        $this->load->view('backend/pu/nilai_um/cetak_pengumuman'.($pdf ? '' : '_excel'), $data);
    }

    public function get_kapasitas_kelas() {
        $kelas = $this->kelas->get_kapasitas_kelas();
        $jumlah_siswa = array();
        foreach ($kelas as $detail) {
            $jumlah_siswa[$detail['ID_TINGK']] = $this->akad_siswa->get_kapasitas_kelas($detail['ID_TINGK']);
        }
        
        $data = array(
            'kelas' => $kelas,
            'jumlah_siswa' => $jumlah_siswa
        );

        $this->generate->output_JSON($data);
    }

    public function export_csv($ID_TINGK, $JK_SISWA) {
        $data_nilai = array();

        $data_mapel = $this->jadwal_pu->get_mapel_by_tingkat($this->tipe, $ID_TINGK);
        $data_siswa = $this->nilai_um->get_all($ID_TINGK, $JK_SISWA);
        $data_tingkat = $this->tingkat->get_all_urut();
        
        $data = array(
            'mapel' => $data_mapel,
            'siswa' => $data_siswa,
            'nilai' => $data_nilai,
            'tingkat' => $data_tingkat,
            'ID_TINGK' => $ID_TINGK,
            'tahun' => date('Y')
        );

        $this->load->view('backend/pu/nilai_um/excel_nilai.php', $data);
    }
}