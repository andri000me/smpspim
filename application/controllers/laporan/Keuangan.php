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
class Keuangan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_keuangan_modul_model' => 'keuangan',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
            'departemen_model' => 'dept',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
            'tagihan_model' => 'tagihan',
            'detail_tagihan_model' => 'detail_tagihan',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(12);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'CAWU' => $this->cawu->get_all(FALSE),
            'JENJANG' => $this->dept->get_all(FALSE),
            'TINGKAT' => $this->tingkat->get_all(FALSE),
            'TAGIHAN' => $this->tagihan->get_all(FALSE),
            'BULAN' => array(
                '-- Pilih Bulan --',
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'Nopember',
                'Desember',
            )
        );

        $this->generate->backend_view('laporan/keuangan/index', $data);
    }

    public function get_data() {
        $this->generate->set_header_JSON();

//        $pie_donut = $this->input->post('pie_donut');
        $ta = $this->input->post('ta');
        $jenjang = $this->input->post('jenjang');
        $tingkat = $this->input->post('tingkat');
        $kelas = $this->input->post('kelas');
        $akhir_tanggal = $this->input->post('akhir_tanggal');
        $mulai_tanggal = $this->input->post('mulai_tanggal');
        $pegawai = $this->input->post('pegawai');
        
        $pembayaran = $this->keuangan->get_data('Pembayaran', $ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        $pengembalian = $this->keuangan->get_data('Pengembalian', $ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        
        $data_source = array(
            $pembayaran,
            $pengembalian,
            $pengembalian,
        );
        
        $names = array(
            'data0' => 'Pembayaran',
            'data1' => 'Pengembalian',
            'data2' => 'Saldo',
        );
        $data = $this->chart_handler->format_output_multiple_date($data_source, $mulai_tanggal, $akhir_tanggal, 'Tanggal', 'Rupiah', $names);
        $data['type'] = 'money';
        
        unset($data['data']['data2']);
        for ($i = 0; $i < count($data['data']['data0']); $i++) {
            $data['data']['data2'][] = $data['data']['data0'][$i] - $data['data']['data1'][$i];
        }

        $this->generate->output_JSON($data);
    }

    public function ta_changed() {
        $this->generate->set_header_JSON();

        $ta = $this->input->post('ta');
        
        $data = array(
            'kelas' => $this->kelas->get_rows(array('TA_KELAS' => $ta)),
            'tagihan' => $this->tagihan->get_rows(array('TA_TAG' => $ta)),
        );

        $this->generate->output_JSON($data);
    }

    public function get_detail_tag() {
        $this->generate->set_header_JSON();

        $tagihan = $this->input->post('tagihan');
        
        $data = array(
            'detail_tagihan' => $this->detail_tagihan->get_rows_group(array('TAGIHAN_DT' => $tagihan)),
        );

        $this->generate->output_JSON($data);
    }

    public function export() {
        $ta = $this->input->get('ta');
        $jenjang = $this->input->get('jenjang');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');
        $akhir_tanggal = $this->input->get('akhir_tanggal');
        $mulai_tanggal = $this->input->post('mulai_tanggal');
        $pegawai = $this->input->get('pegawai');
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=data_keuangan_" . date('Y-m-d_H-i-s') . ".csv");

        echo $this->keuangan->export_data($ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
    }
    
    public function ajax_list() {
        $this->generate->set_header_JSON();
        
        $ta = $this->input->get('ta');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');
        $akhir_tanggal = $this->input->get('bulan');
        $mulai_tanggal = $this->input->get('tahun');
        $pegawai = $this->input->post('pegawai');

        $id_datatables = 'datatable1';
        $list = $this->keuangan->get_datatables($ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->CREATED_BAYAR;
            $row[] = $item->JENIS_BAYAR;
            $row[] = $item->NAMA_TA;
            $row[] = $item->NIS_SISWA_SHOW;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS_SHOW;
            $row[] = $item->NAMA_DT;
            $row[] = $this->money->format($item->NOMINAL_BAYAR);
            $row[] = $item->NAMA_PEG_SHOW;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->keuangan->count_all($ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai),
            "recordsFiltered" => $this->keuangan->count_filtered($ta, $jenjang, $tingkat, $kelas, $akhir_tanggal, $mulai_tanggal, $pegawai),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
