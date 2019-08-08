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
class Laporan_tunggakan extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_DT";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_tunggakan_model' => 'laporan_tunggakan',
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/laporan_tunggakan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_tunggakan->get_datatables();
        $nominal = $this->laporan_tunggakan->nominal_all();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_TAG;
            $row[] = $item->NAMA_DT;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_tunggakan->count_all(),
            "recordsFiltered" => $this->laporan_tunggakan->count_filtered(),
            "data" => $data,
            "nominal" => $nominal
        );

        $this->generate->output_JSON($output);
    }

    public function laporan_tagihan() {
        $data = [
            'ta' => $this->db_handler->get_rows('md_tahun_ajaran', ['order_by' => ['NAMA_TA' => 'DESC']]),
            'dept' => $this->db_handler->get_rows('md_departemen', ['order_by' => ['URUT_DEPT' => 'ASC']]),
            'tagihan' => $this->get_laporan_tagihan([
                'STATUS_SETUP' => 0,
                'KADALUARSA_SETUP' => 0,
                'PSB_TAG' => 0
                    ], 'ID_SETUP IS NOT NULL'),
            'non_tagihan' => $this->get_laporan_tagihan([
                'ID_SETUP' => null,
                'PSB_TAG' => 0
                    ], 'ID_DEPT IS NOT NULL'),
        ];

        $this->load->view('backend/keuangan/laporan_tunggakan/cetak', $data);
    }

    private function get_laporan_tagihan($where, $inline = null) {
        $params = [
            'where' => $where,
            'group_by' => [
                'ID_TA',
                'ID_DEPT'
            ],
            'order_by' => [
                'NAMA_TA' => 'DESC',
                'URUT_DEPT' => 'ASC'
            ]
        ];

        if ($inline != null)
            $params['inline'] = $inline;

        $data = $this->db_handler->get_rows('akad_siswa', $params, 'ID_TA, NAMA_TA, ID_DEPT, NAMA_DEPT, COUNT(DISTINCT(SISWA_AS)) AS JUMLAH_SISWA, SUM(NOMINAL_DT) AS JUMLAH_NOMINAL', [
            ['md_siswa', 'SISWA_AS=ID_SISWA', 'left'],
            ['md_tahun_ajaran', 'TA_AS=ID_TA', 'left'],
            ['akad_kelas', 'KELAS_AS=ID_KELAS', 'left'],
            ['md_tingkat', 'TINGKAT_KELAS=ID_TINGK', 'left'],
            ['md_departemen', 'DEPT_TINGK=ID_DEPT', 'left'],
            ['keu_tagihan', 'TA_TAG=ID_TA', 'left'],
            ['keu_detail', 'TAGIHAN_DT=ID_TAG AND DEPT_DT=ID_DEPT', 'left'],
            ['keu_setup', 'DETAIL_SETUP=ID_DT AND SISWA_SETUP=ID_SISWA', 'left']
        ]);

        $return = array();
        foreach ($data as $detail) {
            $return[$detail->ID_TA][$detail->ID_DEPT] = $detail;
        }

        return $return;
    }

}
