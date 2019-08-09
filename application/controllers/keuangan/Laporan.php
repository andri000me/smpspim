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
class Laporan extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_DT";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_keuangan_model' => 'laporan',
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/laporan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan->get_datatables();
        $nominal = $this->laporan->nominal_all();
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
            $row[] = $this->money->format($item->NOMINAL_DT);
            $row[] = $item->KETERANGAN_BAYAR;

            $row[] = $item->NAMA_PEG;

            $row[] = $item->CREATED_BAYAR;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="' . site_url('keuangan/pembayaran/ajax_cetak/' . $item->KODE_BAYAR) . '" title="Cetak" target="_blank"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kwitansi</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan->count_all(),
            "recordsFiltered" => $this->laporan->count_filtered(),
            "data" => $data,
            "nominal" => $nominal
        );

        $this->generate->output_JSON($output);
    }

    public function harian() {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        $data = [
            'start' => $start,
            'end' => $end
        ];

        $pembayaran = $this->db_handler->get_rows('keu_pembayaran', [
            'where' => [
                'USER_BAYAR' => $this->session->userdata('ID_USER'),
                'LEFT(CREATED_BAYAR, 10) >' => date('Y-m-d', strtotime($start . "-1 days")),
                'LEFT(CREATED_BAYAR, 10) <' => date('Y-m-d', strtotime($end . "+1 days")),
            ],
            'group_by' => [
                "LEFT(CREATED_BAYAR, 10)"
            ],
            'order_by' => [
                'CREATED_BAYAR' => 'DESC'
            ]
                ], 'LEFT(CREATED_BAYAR, 10) AS TANGGAL, SUM(NOMINAL_BAYAR) AS NOMINAL');

        foreach ($pembayaran as $detail) {
            $data['pembayaran'][$detail->TANGGAL] = $detail->NOMINAL;
        }

        $this->load->view('backend/keuangan/laporan/cetak', $data);
    }

    public function laporan_tagihan() {
        if ($this->session->userdata('ADMINISTRATOR')) {
            $pembayaran = $this->db_handler->get_rows('keu_tagihan', [
                'where' => [
                    'TA_TAG' => $this->session->userdata('ID_TA_ACTIVE'),
                    'PSB_TAG' => 0,
                    'JENIS_BAYAR' => 'PEMBAYARAN'
                ],
                'order_by' => [
                    'NAMA_PEG' => 'ASC',
                    'URUT_DEPT' => 'ASC',
                    'ID_DT' => 'ASC'
                ],
                'group_by' => [
                    'ID_PEG',
                    'ID_DEPT',
                    'ID_DT'
                ]
                    ], 'ID_TAG, NAMA_TAG, ID_DT, NAMA_DT, ID_DEPT, SUM(NOMINAL_BAYAR) AS NOMINAL, md_pegawai.*', [
                ['keu_detail', 'ID_TAG=TAGIHAN_DT'],
                ['keu_setup', 'DETAIL_SETUP=ID_DT'],
                ['keu_pembayaran', 'SETUP_BAYAR=ID_SETUP'],
                ['md_user', 'USER_BAYAR=ID_USER'],
                ['md_pegawai', 'ID_PEG=PEGAWAI_USER'],
                ['md_departemen', 'ID_DEPT=DEPT_DT']
            ]);

            $id_tag = null;
            $data_pegawai = array();
            $data_pembayaran = array();
            foreach ($pembayaran as $detail) {
                $id_tag = $detail->ID_TAG;
                $data_pegawai[$detail->ID_PEG] = $detail;
                $data_pembayaran[$detail->ID_PEG][$detail->ID_DEPT][$detail->ID_DT] = $detail;
            }

            $dt = $this->db_handler->get_rows('keu_detail', ['where' => ['TAGIHAN_DT' => $id_tag], 'order_by' => ['ID_DT' => 'ASC', 'URUT_DEPT' => 'ASC']], '*', [['md_departemen', 'ID_DEPT=DEPT_DT']]);

            $detail_tag = array();
            foreach ($dt as $detail_dt) {
                $detail_tag[$detail_dt->DEPT_DT][$detail_dt->ID_DT] = $detail_dt;
            }

            $data = array(
                'jenjang' => $this->db_handler->get_rows('md_departemen', ['order_by' => ['URUT_DEPT' => 'ASC']]),
                'pembayaran' => $data_pembayaran,
                'pegawai' => $data_pegawai,
                'detail_tag' => $detail_tag
            );

            $this->load->view('backend/keuangan/laporan/laporan_tagihan', $data);
        } else {
            echo '<h1>MENU HANYA AKTIF PADA USER YANG MEMILIKI HAKAKSES ADMINISTRATOR</h1>';
        }
    }

}
