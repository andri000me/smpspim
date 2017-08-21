<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pencarian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'pencarian_model' => 'pencarian',
            'siswa_model' => 'siswa',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
        ));
        $this->auth->validation();
    }

    public function index() {
        $this->load->view('layout/main/header');
        $this->load->view('backend/pencarian/index');
    }

    public function cari() {
        $this->generate->set_header_JSON();

        $kata_kunci = $this->input->post('kata_kunci');
        $filter = $this->input->post('filter');

        $data = $this->pencarian->get_rows($kata_kunci, $filter);
        $jumlah = $this->pencarian->count_filtered($kata_kunci, $filter);
        $total = $this->pencarian->count_all();

        foreach ($data as $detail) {
            if (file_exists('files/siswa/' . $detail->NIS_SISWA . '.jpg')) {
                $detail->FOTO_SISWA = $detail->NIS_SISWA . '.jpg';
            } elseif (file_exists('files/siswa/' . $detail->ID_SISWA . '.png') || $detail->FOTO_SISWA != NULL) {
                $detail->FOTO_SISWA = $detail->ID_SISWA . '.png';
            }
        }

        $this->generate->output_JSON(array(
            'DATA' => $data,
            'JUMLAH' => $jumlah,
            'TOTAL' => $total
        ));
    }

    public function detail($ID_SISWA) {
        $data['SISWA'] = $this->pencarian->get_by_id($ID_SISWA);
        $data['NILAI_PSB'] = $this->pencarian->get_nilai_um($ID_SISWA);
        $data['AKADEMIK'] = array();
        $jenis_kehadiran = $this->pencarian->get_jenis_kehadiran();

        // MENGAMBIL DATA AKADEMIK
        $data_ta = $this->ta->get_all(FALSE);
        $data_cawu = $this->cawu->get_all(FALSE);
        foreach ($data_ta as $detail_ta) {
            $data_akad_siswa = $this->pencarian->get_akad_siswa($ID_SISWA, $detail_ta->ID_TA);
            $data_keuangan = $this->pencarian->get_keuangan($ID_SISWA, $detail_ta->ID_TA);
            $NIS = $this->pencarian->get_nis($ID_SISWA, $detail_ta->ID_TA);
            if ($NIS == NULL)
                $NIS = $data['SISWA']->NIS_SISWA;

            if ($data_akad_siswa != NULL && $NIS != NULL) {
                $data_nilai = array();
                $data_kehadiran = array();
                $data_poin = array();

                if ($data_akad_siswa->KELAS_AS != NULL) {
                    foreach ($data_cawu as $detail_cawu) {
                        $data_nilai[$detail_cawu->ID_CAWU] = $this->pencarian->get_nilai($data_akad_siswa->ID_AS, $data_akad_siswa->KELAS_AS, $detail_cawu->ID_CAWU, $detail_ta->ID_TA);
                        foreach ($jenis_kehadiran as $kehadiran) {
                            $ID = $kehadiran->ID_MJK;
                            $data_kehadiran[$detail_cawu->ID_CAWU][] = array(
                                'DATA' => $kehadiran,
                                'SAKIT' => $this->pencarian->get_absensi($ID_SISWA, 'SAKIT', $detail_cawu->ID_CAWU, $detail_ta->ID_TA, $ID),
                                'IZIN' => $this->pencarian->get_absensi($ID_SISWA, 'IZIN', $detail_cawu->ID_CAWU, $detail_ta->ID_TA, $ID),
                                'ALPHA' => $this->pencarian->get_absensi($ID_SISWA, 'ALPHA', $detail_cawu->ID_CAWU, $detail_ta->ID_TA, $ID),
                            );
                        }
                        $data_poin[$detail_cawu->ID_CAWU] = $this->pencarian->get_poin($ID_SISWA, $detail_cawu->ID_CAWU, $detail_ta->ID_TA);
                    }
                }

                $data['AKADEMIK'][] = array(
                    'TA' => $detail_ta,
                    'CAWU' => $data_cawu,
                    'NIS' => $NIS,
                    'KEUANGAN' => $data_keuangan,
                    'AKADEMIK' => $data_akad_siswa,
                    'NILAI' => $data_nilai,
                    'KEHADIRAN' => $data_kehadiran,
                    'POIN' => $data_poin
                );
            }
        }

        $this->load->view('layout/main/header');
        $this->load->view('backend/pencarian/detail', $data);
    }

}
