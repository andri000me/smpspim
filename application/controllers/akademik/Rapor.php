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
class Rapor extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'rapor_model' => 'rapor',
            'nilai_siswa_model' => 'nilai',
            'kelas_model' => 'kelas',
            'siswa_model' => 'siswa',
            'matapelajaran_model' => 'mapel'
        ));
        $this->auth->validation(array(2, 10));
    }

    public function index() {
        $this->generate->backend_view('akademik/rapor/index');
    }

    public function ajax_list($kelas) {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $data_mapel = $this->rapor->get_mapel($kelas);
        $jumlah_mapel = count($data_mapel);
        $kkm = $this->pengaturan->getKKM();

        $list = $this->rapor->get_datatables($jumlah_mapel, $kelas);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $nilai_total = 0;
            $row = array();
            $row[] = $item->AKTIF_SISWA ? $item->NIS_SISWA : '<b>KELUAR</b>';
            $row[] = $item->NAMA_SISWA;

            foreach ($data_mapel as $detail) {
                $nilai = $this->rapor->get_nilai($detail->ID_AGM, $item->ID_AS);
                $row[] = ($nilai < $kkm ? '<strong>' : '') . $nilai . ($nilai < $kkm ? '</strong>' : '');
                $nilai_total += $nilai;
            }

            $row[] = $nilai_total;
            $row[] = number_format($nilai_total / $jumlah_mapel, 2, ',', '.');
            $row[] = $this->rapor->get_absensi($item->ID_SISWA, 'SAKIT');
            $row[] = $this->rapor->get_absensi($item->ID_SISWA, 'IZIN');
            $row[] = $this->rapor->get_absensi($item->ID_SISWA, 'ALPHA');

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->rapor->count_all($kelas),
            "recordsFiltered" => $this->rapor->count_filtered($jumlah_mapel, $kelas),
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

        $data = $this->kelas->get_kelas($this->input->post('ID_PEG'));

        $this->generate->output_JSON($data);
    }

    public function list_mapel() {
        $this->generate->set_header_JSON();

        $data = $this->rapor->get_mapel($this->input->post('ID_KELAS'));

        $this->generate->output_JSON($data);
    }

    public function cetak($ID_KELAS) {
        $data = array();
        $data['CAWU'] = $this->session->userdata('NAMA_CAWU_ACTIVE');
        $data['TA'] = $this->session->userdata('NAMA_TA_ACTIVE');

        $where = array(
            'KELAS_AS' => $ID_KELAS,
        );
        $data_siswa = $this->rapor->get_rows($where);

        foreach ($data_siswa as $detail_siswa) {
            $where_nilai = array(
                'TA_AGM' => $this->session->userdata('ID_TA_ACTIVE'),
                'KELAS_AGM' => $ID_KELAS,
                'SISWA_AS' => $detail_siswa->ID_SISWA,
            );
            $data_nilai = $this->nilai->get_rows($where_nilai);

            $data['DATA'][] = array(
                'SISWA' => $detail_siswa,
                'NILAI' => $data_nilai,
                'ABSEN' => array(
                    'SAKIT' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'SAKIT'),
                    'IZIN' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'IZIN'),
                    'ALPHA' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'ALPHA'),
                )
            );
        }

        $this->load->view('backend/akademik/rapor/cetak', $data);
    }

    private function isWaliKelas($ID_KELAS) {
        $ID_PEG = $this->session->userdata('ID_PEG');

        $result = $this->db_handler->is_available('akad_kelas', array(
            'ID_KELAS' => $ID_KELAS,
            'WALI_KELAS' => $ID_PEG
        ));

        if ($result) {
            echo '<h1>ANDA TIDAK TERDAFTAR DI KELAS INI</h1>';
            exit();
        }
    }

    public function downloadLegger($ID_KELAS) {
        if ($this->session->userdata('ID_HAKAKSES') != 2) {
            $this->isWaliKelas($ID_KELAS);
        }

        $data = array();
        $data['KELAS'] = $this->db_handler->get_row('akad_kelas', ['where' => ['ID_KELAS' => $ID_KELAS]], '*', [['md_pegawai', 'WALI_KELAS=ID_PEG']]);
        $data['CAWU'] = $this->session->userdata('NAMA_CAWU_ACTIVE');
        $data['TA'] = $this->session->userdata('NAMA_TA_ACTIVE');
        $data['MAPEL'] = $this->rapor->get_mapel_guru($ID_KELAS);

        $data_siswa = $this->rapor->get_rows(['KELAS_AS' => $ID_KELAS]);
        foreach ($data_siswa as $detail_siswa) {
            $where_nilai = array(
                'TA_AGM' => $this->session->userdata('ID_TA_ACTIVE'),
                'KELAS_AGM' => $ID_KELAS,
                'SISWA_AS' => $detail_siswa->ID_SISWA,
            );
            $data_nilai = $this->nilai->get_rows($where_nilai);

            $data['DATA'][] = array(
                'SISWA' => $detail_siswa,
                'NILAI' => $data_nilai,
                'ABSEN' => array(
                    'SAKIT' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'SAKIT'),
                    'IZIN' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'IZIN'),
                    'ALPHA' => $this->rapor->get_absensi($detail_siswa->ID_SISWA, 'ALPHA'),
                )
            );
        }

        $this->load->view('backend/akademik/rapor/legger', $data);
    }

    public function uploadLegger() {
        header('Content-Type: application/json');
        
        $ID_KELAS = $this->input->post('ID_KELAS');

        if ($this->session->userdata('ID_HAKAKSES') != 2) {
            $ID_PEG = $this->session->userdata('ID_PEG');

            $result = $this->db_handler->is_available('akad_kelas', array(
                'ID_KELAS' => $ID_KELAS,
                'WALI_KELAS' => $ID_PEG
            ));

            if ($result) {
                $this->generate->output_JSON(array(
                    'status' => false,
                    'msg' => 'Anda tidak menjadi wali kelas ini'
                ));
            }
        }

        $file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if (isset($_FILES['file-legger']['name']) && in_array($_FILES['file-legger']['type'], $file_mimes)) {

            $arr_legger = explode('.', $_FILES['file-legger']['name']);
            $extension = end($arr_legger);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $spreadsheet = $reader->load($_FILES['file-legger']['tmp_name']);

            $spreadsheet->setActiveSheetIndex(0);
            $dataNilai = $spreadsheet->getActiveSheet()->toArray();

            $spreadsheet->setActiveSheetIndex(1);
            $dataMapel = $spreadsheet->getActiveSheet()->toArray();

            $ID_KELAS_FILE = $dataNilai[5][count($dataNilai[5]) - 1];

            if ($ID_KELAS_FILE != $ID_KELAS) {
                $this->generate->output_JSON(array(
                    'status' => false,
                    'msg' => 'ID Kelas di file tidak cocok dengan browser'
                ));
            }

            $token = array();
            for ($row = 3; $row < count($dataMapel); $row++) {
                $token[] = $dataMapel[$row][1];
            }

            for ($row = 9; $row < count($dataNilai); $row++) {
                $detail = $dataNilai[$row];

                if ($detail[1] == "KELUAR")
                    continue;

                $startNilai = 3;
                for ($column = $startNilai; $column < (count($token) + $startNilai); $column++) {
                    if ($detail[$column] == null)
                        continue;

                    $nilaiSiswa = [
                        'TA_AN' => $this->session->userdata('ID_TA_ACTIVE'),
                        'CAWU_AN' => $this->session->userdata('ID_CAWU_ACTIVE'),
                        'USER_AN' => $this->session->userdata('ID_USER'),
                        'SISWA_AN' => $this->siswa->get_id_as_by_nis($detail[1]),
                        'GURU_MAPEL_AN' => $token[$column - $startNilai],
                        'NILAI_AN' => $detail[$column],
                    ];

                    $this->rapor->simpan_nilai($nilaiSiswa);
                }
            }

            $output = array(
                'status' => true,
                'msg' => 'Nilai berhasil diimport'
            );
        } else {
            $output = array(
                'status' => false,
                'msg' => 'Nilai gagal diimport'
            );
        }

        $this->generate->output_JSON($output);
    }

}
