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
class Cetak_kartu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kartu_hafalan_model' => 'kartu',
            'tahun_ajaran_model' => 'tahun_ajaran',
            'nilai_hafalan_model' => 'nilai_hafalan',
            'siswa_model' => 'siswa',
        ));
        $this->auth->validation(array(5, 2));
    }

    public function index() {
        $this->generate->backend_view('ph/cetak_kartu/form');
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

    public function cetak_all() {
        $input = $this->input->get();

        $bulan = NULL;
        if (!isset($input['tingkat'])) {
            $input['ta'] = $this->session->userdata('ID_TA_ACTIVE');

            $data_siswa = $this->kartu->get_rows(array('ID_KELAS' => $input['kelas']));

            if ($input['blanko'] == 2)
                $bulan = json_decode($input['bulan'], TRUE);
        } else {
            $data_siswa = $this->kartu->get_by_siswa($input['id_siswa']);
        }

        foreach ($data_siswa as $detail_siswa) {
            $data['BULAN'] = $bulan;
            $data['KELAS'] = isset($input['tingkat']) ? '-' : $detail_siswa;
            $data['JENJANG'] = isset($input['tingkat']) ? '-' : $detail_siswa->DEPT_TINGK;
            $data['KITAB'] = $input['blanko'] == 3 ? $this->kartu->get_batasan($this->session->userdata('ID_TA_ACTIVE'), $detail_siswa->TINGKAT_AS, $detail_siswa->JK_SISWA) : NULL;
            $data['SISWA'][] = array(
                'DETAIL' => $detail_siswa,
                'KELAS' => isset($input['tingkat']) ? '-' : $detail_siswa->NAMA_KELAS,
                'JK_KELAS' => isset($input['tingkat']) ? '-' : $detail_siswa->JK_KELAS,
                'TINGKAT_KELAS' => isset($input['tingkat']) ? '-' : $detail_siswa->TINGKAT_KELAS,
                'TA' => isset($input['ta']) ? $this->tahun_ajaran->get_nama($input['ta']) : $this->session->userdata('NAMA_TA_ACTIVE'),
                'KITAB' => $input['blanko'] == 0 ? $this->kartu->get_batasan($this->session->userdata('ID_TA_ACTIVE'), $detail_siswa->TINGKAT_AS, $detail_siswa->JK_SISWA) : NULL,
//                'NILAI' => $input['blanko'] == 3 ? $this->nilai_hafalan->get_nilai_validasi($detail_siswa->ID_SISWA) : NULL,
            );
        }

        if ($input['blanko'] == 0)
            $view = 'cetak_bukti';
        elseif ($input['blanko'] == 1)
            $view = 'cetak_blanko';
        elseif ($input['blanko'] == 2)
            $view = 'cetak_monitoring';
        elseif ($input['blanko'] == 3)
            $view = 'cetak_validasi';

        $this->load->view('backend/ph/cetak_kartu/' . $view, $data);
    }

    public function ac_siswa() {
        $this->generate->set_header_JSON();

        $data = $this->kartu->get_siswa($this->input->post('q'));

        $this->generate->output_JSON($data);
    }
    
    public function get_siswa_perkelas() {
        $this->generate->set_header_JSON();
        
        $data = $this->siswa->get_rows_aktif_simple(array(
            'ID_KELAS' => $this->input->post('ID_KELAS')
        ));
        
        $this->generate->output_JSON($data);
    }

}
