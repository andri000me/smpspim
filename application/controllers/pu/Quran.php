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
class Quran extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_TW";
    var $jenis = 'QURAN';
    var $tugas = array('PENYEMAK 1', 'PENYEMAK 2', 'PENILAI 1', 'PENILAI 2', 'PEMBAGI WAKTU');

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'testing_quran_model' => 'quran',
            'peserta_testing_model' => 'peserta',
            'testing_nilai_model' => 'nilai'
        ));
        $this->auth->validation(6);
    }

    public function index($ATURAN = TRUE) {
        $jadwal = $this->quran->get_rows('testing_jadwal', array('TA_TW' => $this->session->userdata('ID_TA_ACTIVE')));
        
        if (count($jadwal) == 0)
            $this->form_aturan($ATURAN);
        else 
            $this->lihat_jadwal();
        
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->quran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->TANGGAL_TW;
            $row[] = $item->MULAI_TW;
            $row[] = $item->AKHIR_TW;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TW . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_TW . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->quran->count_all(),
            "recordsFiltered" => $this->quran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    private function get_data_jadwal() {
        $data = array(
            'WAKTU' => array(
                'L' => $this->quran->get_rows('testing_waktu', array('JK_TW' => 'L','TA_TW' => $this->session->userdata('ID_TA_ACTIVE'))),
                'P' => $this->quran->get_rows('testing_waktu', array('JK_TW' => 'P','TA_TW' => $this->session->userdata('ID_TA_ACTIVE')))
            ),
            'RUANG' => array(
                'L' => $this->quran->get_rows('testing_ruang', array('JK_TR' => 'L','TA_TR' => $this->session->userdata('ID_TA_ACTIVE'))),
                'P' => $this->quran->get_rows('testing_ruang', array('JK_TR' => 'P','TA_TR' => $this->session->userdata('ID_TA_ACTIVE')))
            ),
            'MAPEL' => $this->quran->get_rows('testing_mapel', array('TA_TM' => $this->session->userdata('ID_TA_ACTIVE'))),
            'JUMLAH' => array(
                'L' => $this->peserta->count_jk(false, 'L'),
                'P' => $this->peserta->count_jk(false, 'P'),
            ),
            'TUGAS' => $this->tugas
        );
        
        if(count($data['RUANG']['L']) > 0 && count($data['RUANG']['P']) > 0) {
            $data['PEMBAGIAN'] = array(
                'L' => $this->pembagian_peserta($data['JUMLAH']['L'], count($data['RUANG']['L']), count($data['WAKTU']['L'])),
                'P' => $this->pembagian_peserta($data['JUMLAH']['P'], count($data['RUANG']['P']), count($data['WAKTU']['P']))
            );
        }
        
        foreach ($data['RUANG'] as $JK_RUANG => $DATA_RUANG) {
            foreach ($DATA_RUANG as $DETAIL_RUANG) {
                foreach ($data['WAKTU'][$JK_RUANG] as $DETAIL_WAKTU) {
                    $data_jadwal = array(
                        'TA_TW' => $this->session->userdata('ID_TA_ACTIVE'),
                        'WAKTU_TP' => $DETAIL_WAKTU->ID_TW,
                        'RUANG_TP' => $DETAIL_RUANG->ID_TR,
                        'JENIS_TP' => $this->jenis,
                    );
                    $data['MAPEL_JADWAL'][$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW] = $this->quran->get_row('testing_jadwal', $data_jadwal);
                    for ($kl = 0; $kl < count($this->tugas); $kl++) {
                        $data_jadwal['TUGAS_TP'] = $this->tugas[$kl];
                        
                        $data['JADWAL'][$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$data_jadwal['TUGAS_TP']] = $this->quran->get_row('testing_jadwal', $data_jadwal);
                    }
                }
            }
        }
        
        return $data;
    }
    
    public function lihat_jadwal($cetak = FALSE) {
        $data = $this->get_data_jadwal();
        
        if($cetak) 
            $this->generate->backend_view('pu/quran/cetak', $data);
        else
            $this->generate->backend_view('pu/quran/view', $data);
    }
    
    public function form_aturan($ATURAN) {
        $data = $this->get_data_jadwal();
        
        if(count($data['WAKTU']['L']) == 0 || count($data['WAKTU']['P']) == 0 || count($data['RUANG']['L']) == 0 || count($data['RUANG']['P']) == 0 || count($data['MAPEL']) == 0) {
            $this->quran->reset_jadwal_ta();
        }
        
        if($ATURAN)
            $this->generate->backend_view('pu/quran/form', $data);
        else 
            $this->generate->backend_view('pu/quran/form_jadwal', $data);
    } 
    
    public function reset_jadwal() {
        $this->quran->reset_jadwal_ta();
        
        $this->form_aturan(1);
    }
    
    private function pembagian_peserta($jumlah, $ruang, $waktu) {
        $perkiraan = $jumlah/($ruang * $waktu);
        $peserta_maks_perruang = round($perkiraan, 0, PHP_ROUND_HALF_DOWN);
        $sisa_peserta = $jumlah - ($peserta_maks_perruang * $ruang * $waktu);
        
//        $jumlah_peserta_maks = $peserta_maks_perruang * (($ruang * $waktu) - $waktu);
//        $sisa_peserta = $jumlah - $jumlah_peserta_maks;
//        
//        if (($sisa_peserta%$waktu) == 0) $sisa_peserta /= 2;
//        else {
//            $sisa_peserta_maks = round($sisa_peserta/$waktu);
//            $temp_sisa_peserta = $sisa_peserta;
//            unset($sisa_peserta);
//            $sisa_peserta = array(
//                'MAKS' => $sisa_peserta_maks,
//                'SISA' => $temp_sisa_peserta - ($sisa_peserta_maks * ($waktu - 1))
//            );
//            
//        }
        
        return array('JUMLAH_PERRUANG' => $peserta_maks_perruang, 'JUMLAH_SISA' => $sisa_peserta);
    }
    
    public function tambah_aturan() {
        $this->generate->set_header_JSON();
        
        $data = $this->input->post();
        
        $data_waktu = array(
            'TA_TW' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TW' => $this->jenis,
            'JK_TW' => 'L',
            'USER_TW' => $this->session->userdata('ID_USER')
        );
        for ($i = 0; $i < $data['WAKTU_LK']; $i++) {
            $this->quran->save('testing_waktu',$data_waktu);
        }
        $data_waktu['JK_TW'] = 'P';
        for ($i = 0; $i < $data['WAKTU_PR']; $i++) {
            $this->quran->save('testing_waktu',$data_waktu);
        }
        
        $data_ruang = array(
            'TA_TR' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TR' => $this->jenis,
            'JK_TR' => 'L',
            'USER_TR' => $this->session->userdata('ID_USER')
        );
        for ($i = 0; $i < $data['RUANG_LK']; $i++) {
            $this->quran->save('testing_ruang',$data_ruang);
        }
        $data_ruang['JK_TR'] = 'P';
        for ($i = 0; $i < $data['RUANG_PR']; $i++) {
            $this->quran->save('testing_ruang',$data_ruang);
        }
        
        $data_mapel = array(
            'TA_TM' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TM' => $this->jenis,
            'USER_TM' => $this->session->userdata('ID_USER')
        );
        for ($i = 0; $i < $data['MAPEL']; $i++) {
            $this->quran->save('testing_mapel',$data_mapel);
        }
        
        $this->generate->output_JSON(array('status' => 1, 'url' => site_url('pu/quran')));
    }
    
    public function simpan_aturan() {
        $this->generate->set_header_JSON();
        
        $data = $this->input->post();
        
        $data_ruang_lk = $this->quran->get_rows('testing_ruang', array('JK_TR' => 'L','TA_TR' => $this->session->userdata('ID_TA_ACTIVE')));
        $temp_ruang = $data['RUANG_TR'];
        $temp_ruang_lk = array_slice($temp_ruang, 0, count($data_ruang_lk));
        $temp_ruang_pr = array_slice($temp_ruang, count($data_ruang_lk));
        
        if((count($data['MAPEL_TM']) != count(array_unique($data['MAPEL_TM']))) || (count($temp_ruang_lk) != count(array_unique($temp_ruang_lk))) || (count($temp_ruang_pr) != count(array_unique($temp_ruang_pr)))) {
            $this->generate->output_JSON(array('status' => 0, 'msg' => 'Matapelajaran atau ruangan tidak boleh sama.'));
        }
        
        for ($i = 0; $i < count($data['ID_TW']); $i++) {
            $data_waktu = array(
                'TANGGAL_TW' => $data['TANGGAL_TW'][$i],
                'MULAI_TW' => $data['MULAI_TW'][$i],
                'AKHIR_TW' => $data['AKHIR_TW'][$i],
            );
            $where_waktu = array(
                'ID_TW' => $data['ID_TW'][$i]
            );
            $this->quran->update('testing_waktu', $where_waktu, $data_waktu);
        }
        
        for ($i = 0; $i < count($data['ID_TM']); $i++) {
            $data_mapel= array(
                'MAPEL_TM' => $data['MAPEL_TM'][$i],
            );
            $where_mapel = array(
                'ID_TM' => $data['ID_TM'][$i]
            );
            $this->quran->update('testing_mapel', $where_mapel, $data_mapel);
        }
        
        for ($i = 0; $i < count($data['ID_TR']); $i++) {
            $data_ruang = array(
                'RUANG_TR' => $data['RUANG_TR'][$i],
            );
            $where_ruang = array(
                'ID_TR' => $data['ID_TR'][$i]
            );
            $this->quran->update('testing_ruang', $where_ruang, $data_ruang);
        }
        
        $this->generate->output_JSON(array('status' => 1, 'url' => site_url('pu/quran/index/0')));
    }
    
    public function simpan_denah() {
        $this->generate->set_header_JSON();
        
        $data = $this->input->post();
        
        $array_reset = array('');
        $offset = $data['JUMLAH_LK'] + 1;
        $temp_peg = $data['PEGAWAI_TP'];
        $temp_peg_lk = array_slice($temp_peg, 0, $offset);
        $temp_peg_pr = array_slice($temp_peg, $offset);
        $temp_peg_lk_full = array_diff($temp_peg_lk, $array_reset);
        $temp_peg_pr_full = array_diff($temp_peg_pr, $array_reset);
        
        $temp_non_peg = $data['NON_PEGAWAI_TP'];
        $temp_non_peg_lk = array_slice($temp_non_peg, 0, $offset);
        $temp_non_peg_pr = array_slice($temp_non_peg, $offset);
        $temp_non_peg_lk_full = array_diff($temp_non_peg_lk, $array_reset);
        $temp_non_peg_pr_full = array_diff($temp_non_peg_pr, $array_reset);
        
        if(count($temp_peg_lk_full) != count(array_unique($temp_peg_lk_full)))
            $this->generate->output_JSON(array('status' => 0, 'msg' => 'Pegawai laki-laki tidak boleh sama.'));
        if(count($temp_peg_pr_full) != count(array_unique($temp_peg_pr_full)))
            $this->generate->output_JSON(array('status' => 0, 'msg' => 'Pegawai perempuan tidak boleh sama.'));
        if(count($temp_non_peg_lk_full) != count(array_unique($temp_non_peg_lk_full)))
            $this->generate->output_JSON(array('status' => 0, 'msg' => 'Non pegawai laki-laki tidak boleh sama.'));
        if(count($temp_non_peg_pr_full) != count(array_unique($temp_non_peg_pr_full)))
            $this->generate->output_JSON(array('status' => 0, 'msg' => 'Non pegawai perempuan tidak boleh sama.'));
        
        for ($i = 0; $i < count($data['PEGAWAI_TP']); $i++) {
            if($data['PEGAWAI_TP'][$i] == '') {
                if($data['NON_PEGAWAI_TP'][$i] == '') {
                    $this->generate->output_JSON(array('status' => 0, 'msg' => 'Pegawai tidak boleh kosong.'));
                }
            }
        }
        
        $this->quran->delete_by_where('testing_jadwal', array('TA_TP' => $this->session->userdata('ID_TA_ACTIVE')));
        
        $j = 0;
        for ($i = 0; $i < count($data['RUANG_TP']); $i++) {
            for ($k = $j; $k < ($j + 5); $k++) {
                $z = $k - $j;
                $data_jadwal = array(
                    'TA_TP' => $this->session->userdata('ID_TA_ACTIVE'),
                    'WAKTU_TP' => $data['WAKTU_TP'][$i],
                    'RUANG_TP' => $data['RUANG_TP'][$i],
                    'MAPEL_TP' => $data['MAPEL_TP'][$i],
                    'PESERTA_TP' => $data['PESERTA_TP'][$i],
                    'JENIS_TP' => $this->jenis,
                    'TUGAS_TP' => $this->tugas[$z],
                    'PEGAWAI_TP' => ($data['PEGAWAI_TP'][$k] == '' ? NULL : $data['PEGAWAI_TP'][$k]),
                    'NON_PEGAWAI_TP' => ($data['NON_PEGAWAI_TP'][$k] == '' ? NULL : $data['NON_PEGAWAI_TP'][$k]),
                    'USER_TP' => $this->session->userdata('ID_USER'),
                );
                $this->quran->save('testing_jadwal', $data_jadwal);
            }
            
            $j = $k;
        }
        
        $this->generate->output_JSON(array('status' => 1, 'url' => site_url('pu/quran')));
    }
    
    public function cetak() {
        $data = array(
            'TA' => $this->session->userdata('NAMA_TA_ACTIVE'),
        );
        $jadwal = $this->get_data_jadwal();
        $result = array_merge($data, $jadwal);
        
        $this->load->view('backend/pu/quran/cetak', $result);
    }

    public function input_nilai() {
        $this->generate->backend_view('pu/quran/nilai');
    }

    public function ajax_list_quran() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable2';
        $list = $this->peserta->get_datatables(FALSE);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = '<input type="text" class="form-control input-sm" value="'.($item->NILAI_TN == NULL ? '' : $item->NILAI_TN).'" style="width: 50px;" data-siswa="'.$item->ID_SISWA.'" onchange="simpan_nilai(this);"/>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta->count_all(FALSE),
            "recordsFiltered" => $this->peserta->count_filtered(FALSE),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function simpan_nilai() {
        $this->generate->set_header_JSON();
        
        $SISWA_TN = $this->input->post('SISWA_TN');
        $NILAI_TN = $this->input->post('NILAI_TN');
        
        $data = array(
            'TA_TN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_TN' => $SISWA_TN,
            'JENIS_TN' => $this->jenis,
            'NILAI_TN' => $NILAI_TN,
            'USER_TN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_TN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_TN' => $SISWA_TN,
            'JENIS_TN' => $this->jenis,
        );
        $this->nilai->delete_by_where($where);
        $result = $this->nilai->save($data);
        
        $this->generate->output_JSON(array('status' => $result));
    }
}
