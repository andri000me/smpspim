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
class Amplop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'akad_siswa_model' => 'siswa',
            'jenis_tindakan_model' => 'jenis_tindakan',
            'laporan_tindakan_model' => 'tindakan',
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $data = array();

        $data['pd'] = array(
            array(
                'id' => 'H. Ahmad Ismail',
                'text' => 'H. Ahmad Ismail',
            ),
            array(
                'id' => 'H. Muhammad Mulin Niam',
                'text' => 'H. Muhammad Mulin Niam',
            )
        );

        $data['jenis_tindakan'][] = array(
            'id' => '',
            'text' => '-- Pilih Tindakan --',
        );

        $data_jenis_tindakan = $this->jenis_tindakan->get_all(false);
        foreach ($data_jenis_tindakan as $detail) {
            if ($detail->NAMA_KJT == 'SP')
                continue;

            $data['jenis_tindakan'][] = array(
                'id' => $detail->ID_KJT,
                'text' => $detail->NAMA_KJT,
            );
        }

        $this->generate->backend_view('akademik/amplop/index', $data);
    }

    public function cetak() {
        $post = $this->input->post();

        $where_siswa = array(
            'KELAS_AS' => $post['KELAS']
        );
        $data = array(
            'DATA' => $post,
            'SISWA' => $this->siswa->get_rows($where_siswa, TRUE)
        );

        $this->load->view('backend/akademik/amplop/cetak', $data);
    }

    public function cetak_komdis() {
        $post = $this->input->post();

        foreach ($post as $key => $value) {
            if ($value == '') {
                echo '<h1>DATA INPUTAN TIDAK LENGKAP</h1>';
                exit();
            }
        }

        $data_surat = $this->tindakan->get_nomor_surat($post['JENIS_TINDAKAN'], $post['NOMOR_SURAT']);

        if ($data_surat == NULL) {
            echo '<h1>NOMOR SURAT TIDAK DITEMUKAN PADA JENIS TINDAKAN TERSEBUT</h1>';
            exit();
        }

        $data = array(
            'post' => $post,
            'data' => json_decode($data_surat->DATA_KT, TRUE),
            'jenis_tindakan' => $this->jenis_tindakan->get_by_id($post['JENIS_TINDAKAN'])
        );

        $this->load->view('backend/akademik/amplop/cetak_komdis_' . $post['JENIS_TINDAKAN'], $data);
    }

}
