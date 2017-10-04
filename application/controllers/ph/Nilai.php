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
class Nilai extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_PNH";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kelas_model' => 'kelas',
            'nilai_hafalan_model' => 'nilai',
            'nilai_hafalan_kelas_model' => 'nilai_hafalan',
            'batasan_kitab_model' => 'batasan_kitab',
            'pelanggaran_header_model' => 'pelanggaran_header',
        ));
        $this->auth->validation(5);
    }

    public function index() {
        $this->generate->backend_view('ph/nilai/index');
    }

    public function get_kitab() {
        $this->generate->set_header_JSON();

        $where = array(
            'ID_KELAS' => $this->input->post('ID_KELAS')
        );
        $data = $this->batasan_kitab->get_rows_kelas($where);

        $this->generate->output_JSON($data);
    }

    public function ajax_list($ID_KELAS) {
        $this->generate->set_header_JSON();

        $where = array(
            'ID_KELAS' => $ID_KELAS
        );
        $data_kitab = $this->batasan_kitab->get_rows_kelas($where);
        $maksimal_lari = $this->pengaturan->getMaksimalLariHafalan();

        $id_datatables = 'datatable1';
        $list = $this->nilai_hafalan->get_datatables($ID_KELAS);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $jumlah_lari = $this->pelanggaran_header->get_total_lari_siswa($this->session->userdata('ID_TA_ACTIVE'), $item->ID_SISWA);

            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $jumlah_lari;

            $disabled = '';
            $class = '';

            if ($jumlah_lari >= $maksimal_lari) {
                $disabled = 'disabled';
                $class = 'siswa-lari';
            }

            if (!$item->AKTIF_AS) {
                $disabled = 'disabled';
                $class = 'siswa-keluar';
            }

            $id_batasan = array();
            $id_kitab = array();
            $nilai_maks = array();
            foreach ($data_kitab as $detail_kitab) {
                $data_nilai = $this->nilai_hafalan->get_nilai_siswa($item->TA_AS, $item->ID_SISWA, $detail_kitab->ID_BATASAN);
                $id_batasan[] = intval($detail_kitab->ID_BATASAN);
                $id_kitab[] = intval($detail_kitab->ID_KITAB);
                $nilai_maks[] = intval($detail_kitab->NILAI_MAKS_BATASAN);

//                $row[] = '<select class="form-control input-sm option-pegawai" id="penyemak_' . $item->ID_SISWA . '_' . $detail_kitab->ID_BATASAN . '" data-pegawai="' . ($data_nilai == NULL ? '' : $data_nilai->PENYEMAK_PHN) . '" style="width: 150px;" '.$disabled.'></select>';
                $row[] = '<input type="number" class="form-control input-sm" id="nilai_' . $item->ID_SISWA . '_' . $detail_kitab->ID_BATASAN . '" value="' . ($data_nilai == NULL ? '' : $data_nilai->NILAI_PHN) . '" onchange="check_nilai(this)" data-nilai="' . $detail_kitab->NILAI_MAKS_BATASAN . '" style="width: 60px;" ' . $disabled . '/>';
            }

            $row[] = '<p id="nilai_total_' . $item->ID_SISWA . '">' . $item->NILAI_PNH . '</p>';
            $row[] = '<p id="status_' . $item->ID_SISWA . '">' . $item->STATUS_PNH . '</p>';
            $row[] = '<button type="button" class="btn btn-sm btn-primary ' . $class . '" data-batasan="' . json_encode($id_batasan) . '" data-nilai="' . json_encode($nilai_maks) . '" data-siswa="' . $item->ID_SISWA . '" data-kitab="' . json_encode($id_kitab) . '" onclick="simpan_nilai(this)" ' . $disabled . '><i class="fa fa-save"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai_hafalan->count_all($ID_KELAS),
            "recordsFiltered" => $this->nilai_hafalan->count_filtered($ID_KELAS),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function cetak_kelas($ID_KELAS) {
        $where = array(
            'ID_KELAS' => $ID_KELAS
        );
        $data_kitab = $this->batasan_kitab->get_rows_kelas($where);
        $list = $this->nilai_hafalan->get_data_cetak($ID_KELAS);
        $kelas = $this->kelas->get_by_id($ID_KELAS);
        $data = array();
        foreach ($list as $item) {
            $row = array();
            $jumlah_lari = $this->pelanggaran_header->get_total_lari_siswa($this->session->userdata('ID_TA_ACTIVE'), $item->ID_SISWA);

            $row['NO_ABSEN_AS'] = $item->NO_ABSEN_AS;
            $row['NIS_SISWA'] = $item->NIS_SISWA;
            $row['NAMA_SISWA'] = $item->NAMA_SISWA;
            $row['LARI'] = $jumlah_lari;

            foreach ($data_kitab as $detail_kitab) {
                $data_nilai = $this->nilai_hafalan->get_nilai_siswa($item->TA_AS, $item->ID_SISWA, $detail_kitab->ID_BATASAN);

                $row[$detail_kitab->ID_KITAB] = $data_nilai == NULL ? '' : $data_nilai->NILAI_PHN;
            }

            $row['NILAI_PNH'] = $item->NILAI_PNH;
            $row['STATUS_PNH'] = $item->STATUS_PNH;

            $data[] = $row;
        }

        $output = array(
            'data' => array(
                'data' => array(
                    'siswa' => $data,
                    'kitab' => $data_kitab,
                    'kelas' => $kelas
                )
            )
        );

        $this->load->view('backend/ph/nilai/cetak_nilai', $output);
    }

    public function form($ID = NULL) {
        $data = array();

        if ($ID == NULL) {
            $data['ADD'] = TRUE;
        } else {
            $data = array(
                'ADD' => FALSE,
                'DATA' => $this->nilai->get_detail_nilai($ID)
            );
        }

        $this->generate->backend_view('ph/nilai/form', $data);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $this->input->post('ID_SISWA'),
            'USER_PHN' => $this->session->userdata('ID_USER'),
        );

        $ID_KITAB = $this->input->post('ID_KITAB');
        $NILAI_PHN = $this->input->post('NILAI_PHN');
        $BATASAN_PHN = $this->input->post('BATASAN_PHN');
        $NILAI_MAKS_PNH = $this->input->post('NILAI_MAKS_BATASAN');
//        $PENYEMAK_PHN = $this->input->post('PENYEMAK');

        $NILAI_PNH = array();
        for ($i = 0; $i < count($NILAI_PHN); $i++) {
            $data['BATASAN_PHN'] = $BATASAN_PHN[$i];
            $data['NILAI_PHN'] = $NILAI_PHN[$i];
            $data['PENYEMAK_PHN'] = 1; //$PENYEMAK_PHN[$i];
            $NILAI_PNH[$ID_KITAB[$i]][] = $NILAI_PHN[$i];

            $insert = $this->nilai->simpan_nilai($data);
        }

        $this->hitung_nilai($data['SISWA_PHN'], $NILAI_MAKS_PNH, $NILAI_PHN);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function simpan_nilai() {
        $this->generate->set_header_JSON();

        $ID_KITAB = $this->input->post('kitab');
        $ID_BATASAN = $this->input->post('batasan');
        $NILAI_MAKS_BATASAN = $this->input->post('nilai_maks');
        $ID_SISWA = $this->input->post('siswa');
        $ID_BATASAN_EXP = explode(',', $ID_BATASAN);
        $ID_KITAB_EXP = explode(',', $ID_KITAB);
        $NILAI_MAKS_BATASAN_EXP = explode(',', $NILAI_MAKS_BATASAN);

        $data = array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $ID_SISWA,
            'USER_PHN' => $this->session->userdata('ID_USER'),
        );

        $i = 0;
        $NILAI_PHN = array();
        foreach ($ID_BATASAN_EXP as $BATASAN) {
            $NILAI_PHN[] = $this->input->post('nilai_' . $BATASAN);

            $data['BATASAN_PHN'] = $BATASAN;
            $data['NILAI_PHN'] = $this->input->post('nilai_' . $BATASAN);
            $data['PENYEMAK_PHN'] = $this->input->post('penyemak_' . $BATASAN);

            $insert = $this->nilai->simpan_nilai($data);
            $i++;
        }

        $result = $this->hitung_nilai($ID_SISWA, $NILAI_MAKS_BATASAN_EXP, $NILAI_PHN, $ID_KITAB_EXP);

        $this->generate->output_JSON($result);
    }

    private function hitung_nilai($ID_SISWA, $NILAI_MAKS_PNH, $NILAI_PHN, $ID_KITAB) {
        $NILAI_AKHIR = array();

        $NILAI_PERKITAB = array();
        foreach ($ID_KITAB as $INDEX => $DETAIL_KITAB) {
            $NILAI_PERKITAB[$DETAIL_KITAB][] = array(
                'NILAI' => $NILAI_PHN[$INDEX],
                'NILAI_MAKS' => $NILAI_MAKS_PNH[$INDEX],
            );
        }

        $NILAI = array();
        foreach ($NILAI_PERKITAB as $ID_KITAB => $DETAIL_NILAI) {
            foreach ($DETAIL_NILAI as $DETAIL) {
                if (isset($NILAI[$ID_KITAB]))
                    $NILAI[$ID_KITAB] += $DETAIL['NILAI'];
                else
                    $NILAI[$ID_KITAB] = $DETAIL['NILAI'];
            }
        }

        $NILAI_TOTAL = 0;
        foreach ($NILAI as $DETAIL) {
            $NILAI_TOTAL += $DETAIL;
        }

        $NILAI_RATA = round($NILAI_TOTAL / count($NILAI));
        $NILAI_MINIMAL = $this->pengaturan->getNilaiMinimalHafal();

        if ($NILAI_RATA >= $NILAI_MINIMAL)
            $STATUS_PNH = 'HAFAL';
        else
            $STATUS_PNH = 'TIDAK HAFAL';

        $result = $this->nilai->reset_nilai_header($ID_SISWA, $NILAI_RATA, $STATUS_PNH);

        return array(
            'NILAI' => $NILAI_RATA,
            'STATUS' => $STATUS_PNH,
            'RESULT' => $result
        );
    }

    public function simpan_status() {
        $this->generate->set_header_JSON();

        $keluar = $this->input->post('keluar');
        $keluar_exp = explode(',', $keluar);
        $lari = $this->input->post('lari');
        $lari_exp = explode(',', $lari);

        foreach ($keluar_exp as $ID_SISWA) {
            if ($ID_SISWA != '')
                $this->nilai->update_status($ID_SISWA, 'KELUAR');
        }

        foreach ($lari_exp as $ID_SISWA) {
            if ($ID_SISWA != '')
                $this->nilai->update_status($ID_SISWA, 'GUGUR');
        }

        $this->generate->output_JSON(array('status' => 1));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = array(
            'USER_PHN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $this->input->post('ID_SISWA'),
        );

        $ID_KITAB = $this->input->post('ID_KITAB');
        $NILAI_PHN = $this->input->post('NILAI_PHN');
        $NILAI_MAKS_PNH = $this->input->post('NILAI_MAKS_BATASAN');
        $BATASAN_PHN = $this->input->post('BATASAN_PHN');
        $PENYEMAK_PHN = $this->input->post('PENYEMAK');

        $NILAI_PNH = array();
        for ($i = 0; $i < count($NILAI_PHN); $i++) {
            $where['BATASAN_PHN'] = $BATASAN_PHN[$i];
            $data['NILAI_PHN'] = $NILAI_PHN[$i];
            $data['PENYEMAK_PHN'] = $PENYEMAK_PHN[$i];
            $NILAI_PNH[$ID_KITAB[$i]][] = $NILAI_PHN[$i];

            $insert = $this->nilai->ubah_nilai($where, $data);
        }

        $this->hitung_nilai($where['SISWA_PHN'], $NILAI_MAKS_PNH, $NILAI_PHN);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->nilai->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->nilai->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function get_batasan() {
        $this->generate->set_header_JSON();

        $data = $this->nilai->get_batasan($this->input->post('ID_SISWA'));

        $this->generate->output_JSON(array('data' => $data));
    }

    public function get_penyemak() {
        $this->generate->set_header_JSON();

        $data = $this->nilai->get_penyemak();

        $this->generate->output_JSON(array('data' => $data));
    }

    private function hitung_nilai_backup($ID_SISWA, $DATA) {
        $NILAI_RATA = 0;
        $NILAI_AKHIR = array();

        foreach ($DATA as $KITAB => $NILAI) {
            $temp = 0;
            foreach ($NILAI as $DETAIL_NILAI) {
                $temp += $DETAIL_NILAI;
            }
            $NILAI_AKHIR[$KITAB] = $temp;
        }

        $temp = 0;
        foreach ($NILAI_AKHIR as $DETAIL_NILAI) {
            $temp += $DETAIL_NILAI;
        }

        $NILAI_RATA = $temp / count($DATA);

        var_dump($DATA);
        echo '<br>';
        echo json_encode($DATA);
        exit();
        $this->nilai->reset_nilai_header($ID_SISWA, $NILAI_RATA);
    }

}
