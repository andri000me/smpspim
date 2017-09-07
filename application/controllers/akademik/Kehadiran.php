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
class Kehadiran extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_AKH";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'kehadiran_model' => 'kehadiran',
            'pelanggaran_model' => 'pelanggaran',
            'pelanggaran_header_model' => 'pelanggaran_header',
            'kelas_model' => 'kelas',
            'akad_siswa_model' => 'kelas_siswa',
            'absen_siswa_model' => 'absen_siswa',
            'jenis_absensi_model' => 'jenis_absensi',
        ));
        $this->load->library('pelanggaran_handler');
        $this->auth->validation(array(2, 7));
    }

    public function index() {
        $this->generate->backend_view('akademik/kehadiran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kehadiran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
//            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;

            $row[] = $item->NAMA_PEG;

            $row[] = $item->TANGGAL_AKH;
            $row[] = $item->NAMA_MJK;
            $row[] = $item->ALASAN_AKH;
            $row[] = $item->KETERANGAN_AKH;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_AKH . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kehadiran->count_all(),
            "recordsFiltered" => $this->kehadiran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kehadiran);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Tanggal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_AKH',
                    'value' => $data == NULL ? $this->date_format->to_view(date('Y-m-d')) : $data->TANGGAL_AKH
                )
            ),
            array(
                'label' => 'Siswa',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'SISWA_AKH',
                    'multiple' => FALSE,
                    'value' => $data == NULL ? "" : $data->SISWA_AKH,
                    'label' => $data == NULL ? "" : $data->NAMA_SISWA,
                    'data' => NULL,
                    'url' => base_url('akademik/kehadiran/ac_siswa')
                )
            ),
            array(
                'label' => 'Ketidakhadiran',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'radio',
                    'name' => 'ALASAN_AKH',
                    'inline' => true,
                    'value' => $data == NULL ? 'ALPHA' : $data->ALASAN_AKH,
                    'data' => array(
                        array('value' => 'SAKIT', 'label' => "SAKIT"),
                        array('value' => 'IZIN', 'label' => "IZIN"),
                        array('value' => 'ALPHA', 'label' => "ALPHA"),
                    )
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_AKH',
                    'value' => $data == NULL ? "" : $data->KETERANGAN_AKH
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = $this->input->post();
        $data = $this->generate->clear_token($data);
        $data['TANGGAL_AKH'] = $this->date_format->to_store_db($data['TANGGAL_AKH']);
        $data['TA_AKH'] = $this->session->userdata('ID_TA_ACTIVE');
        $data['CAWU_AKH'] = $this->session->userdata('ID_CAWU_ACTIVE');
        $data['USER_AKH'] = $this->session->userdata('ID_USER');
        $data['CREATED_AKH'] = date("Y-m-d H:i:s");

        $insert = $this->kehadiran->save($data);

        if ($insert && $data['ALASAN_AKH'] == 'ALPHA')
            $insert = $this->pelanggaran_handler->tambah($data['TA_AKH'], $data['CAWU_AKH'], $data['SISWA_AKH'], $this->pengaturan->getPelanggaranAbsensi(), $data['TANGGAL_AKH'], $data['USER_AKH'], $data['KETERANGAN_AKH'], $insert);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $data = $this->kehadiran->get_by_id($id);

        $status = TRUE;
        if ($data->ALASAN_AKH == 'ALPHA')
            $status = $this->pelanggaran_handler->hapus($id, TRUE);

        if ($status)
            $affected_row = $this->kehadiran->delete_by_id($id);
        else
            $affected_row = 0;

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->kehadiran->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_siswa() {
        $this->generate->set_header_JSON();

        $data = $this->kehadiran->get_siswa($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function cetak_absen($ID_KELAS, $JENIS_CETAK) {
        $data = array();
        $data['TA'] = $this->session->userdata('NAMA_TA_ACTIVE');

        $where_kelas = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE')
        );
        if ($ID_KELAS != 0) {
            $where_kelas['ID_KELAS'] = $ID_KELAS;
        }
        $kelas = $this->kelas->get_rows($where_kelas);

        foreach ($kelas as $detail_kelas) {
            $where_siswa = array(
                'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
                'KELAS_AS' => $detail_kelas->ID_KELAS,
                'KONVERSI_AS' => 0
            );
            $kelas_siswa = $this->kelas_siswa->get_absensi($where_siswa);

            if (count($kelas_siswa) > 0) {
                $data['DATA'][] = array(
                    'KELAS' => $detail_kelas,
                    'DATA' => $kelas_siswa
                );
            }
        }

        if ($JENIS_CETAK == 0)
            $view = 'cetak';
        elseif ($JENIS_CETAK == 1)
            $view = 'cetak_dauroh_arab';
        elseif ($JENIS_CETAK == 2)
            $view = 'cetak_daftar_nilai';
        elseif ($JENIS_CETAK == 3)
            $view = 'cetak_sorogan';
        elseif ($JENIS_CETAK == 4)
            $view = 'cetak_dauroh_inggris';
        elseif ($JENIS_CETAK == 5)
            $view = 'cetak_ilmu_alat';
        elseif ($JENIS_CETAK == 6)
            $view = 'cetak_pramuka';
        elseif ($JENIS_CETAK == 7)
            $view = 'excel_daftar_nilai';

        $this->load->view('backend/akademik/kehadiran/' . $view, $data);
    }

    public function cetak_rekap_semua() {
        $data = array();

        $this->load->view('backend/akademik/kehadiran/cetak_rekap_semua', $data);
    }

    public function cetak_rekap_perkelas_perbulan() {
        $data = array();

        $this->load->view('backend/akademik/kehadiran/cetak_rekap_perkelas_perbulan', $data);
    }

    public function cetak_rekap_perkelas_percawu() {
        $data = array();

        $this->load->view('backend/akademik/kehadiran/cetak_rekap_perkelas_percawu', $data);
    }

    public function cetak_rekap_persiswa_perbulan() {
        $data = array();

        $this->load->view('backend/akademik/kehadiran/cetak_rekap_persiswa_perbulan', $data);
    }

    public function cetak_rekap_persiswa_percawu() {
        $data = array();

        $this->load->view('backend/akademik/kehadiran/cetak_rekap_persiswa_percawu', $data);
    }

    public function tambah_absen() {
        $this->generate->backend_view('akademik/kehadiran/form');
    }

    public function ajax_form($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH) {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->absen_siswa->get_datatables($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();

            if ($JENIS_AKH > 1 && $item->ALASAN_AKH == 'ALPHA' && $item->JENIS_AKH == 1) {
                $absen_lock = TRUE;
            } else {
                $absen_lock = FALSE;
            }

            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA_SHOW;
            $row[] = $item->NAMA_SISWA;
            $row[] = $absen_lock ? '' : '<select class="form-control input-sm" style="width: 100px" ' . ($item->ALASAN_AKH == NULL ? '' : 'disabled="true"') . ' ' . (($item->NIS_SISWA_SHOW == 'KELUAR') ? 'disabled' : '') . '><option value="-">-</option><option value="SAKIT" ' . ($item->ALASAN_AKH == 'SAKIT' ? 'selected' : '') . '>SAKIT</option><option value="IZIN" ' . ($item->ALASAN_AKH == 'IZIN' ? 'selected' : '') . '>IZIN</option><option value="ALPHA" ' . ($item->ALASAN_AKH == 'ALPHA' ? 'selected' : '') . '>ALPHA</option><option value="TERLAMBAT" ' . ($item->ALASAN_AKH == 'TERLAMBAT' ? 'selected' : '') . '>TERLAMBAT</option></select>';
            $row[] = $absen_lock ? '' : '<input type="text" class="form-control input-sm" style="width: 200px" value="' . $item->KETERANGAN_AKH . '" ' . ($item->ALASAN_AKH == NULL ? '' : 'disabled="true"') . ' ' . (($item->NIS_SISWA_SHOW == 'KELUAR') ? 'disabled' : '') . '/>';

            $row[] = $absen_lock ? '' : '<button type="button" class="btn btn-success btn-sm" onclick="simpan_absen(this)" data-siswa="' . $item->ID_SISWA . '" ' . ($item->ALASAN_AKH == NULL ? '' : 'style="display: none"') . ' ' . (($item->NIS_SISWA_SHOW == 'KELUAR') ? 'disabled' : '') . '><i class="fa fa-check-circle"></i></button><button type="button" class="btn btn-danger btn-sm" onclick="hapus_absen(this)" data-siswa="' . $item->ID_AKH . '" ' . ($item->ALASAN_AKH == NULL ? 'style="display: none"' : '') . ' ' . (($item->NIS_SISWA_SHOW == 'KELUAR') ? 'disabled' : '') . '><i class="fa fa-trash"></i></button>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->absen_siswa->count_all($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH),
            "recordsFiltered" => $this->absen_siswa->count_filtered($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function ajax_form_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = $this->input->post();
        $data['TANGGAL_AKH'] = $this->date_format->to_store_db($data['TANGGAL_AKH']);
        $data['TA_AKH'] = $this->session->userdata('ID_TA_ACTIVE');
        $data['CAWU_AKH'] = $this->session->userdata('ID_CAWU_ACTIVE');
        $data['USER_AKH'] = $this->session->userdata('ID_USER');
        $data['CREATED_AKH'] = date("Y-m-d H:i:s");

        $insert = $this->kehadiran->save($data);

        if ($insert && ($data['ALASAN_AKH'] == 'ALPHA' || $data['ALASAN_AKH'] == 'TERLAMBAT'))
            $insert = $this->pelanggaran_handler->tambah($data['TA_AKH'], $data['CAWU_AKH'], $data['SISWA_AKH'], $this->jenis_absensi->get_id_pelanggaran($data['JENIS_AKH'], $data['ALASAN_AKH']), $data['TANGGAL_AKH'], $this->session->userdata('ID_PEG'), $data['KETERANGAN_AKH'], $insert, $data['ALASAN_AKH'], $data['JENIS_AKH']);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_form_delete() {
        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_simple('delete');

        $id = $this->input->post("ID");
        $HAPUS_POIN = $this->input->post("HAPUS_POIN");
        $data = $this->kehadiran->get_by_id($id);
        
        $status = TRUE;
        if ($data->ALASAN_AKH == 'ALPHA' || $data->ALASAN_AKH == 'TERLAMBAT')
            $status = $this->pelanggaran_handler->hapus($id, TRUE, $data->ALASAN_AKH, $data->JENIS_AKH);
        
        if ($status && ($HAPUS_POIN == NULL || !$HAPUS_POIN))
            $affected_row = $this->kehadiran->delete_by_id($id);
        else
            $affected_row = 0;
        
        if($HAPUS_POIN) $affected_row = $status;

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function cek_status_validasi() {
        $this->generate->set_header_JSON();

        $post = $this->input->post();
        $status = $this->kehadiran->absen_divalidasi($post['TANGGAL_AKH'], $post['KELAS_FILTER']);

        $this->generate->output_JSON(array('status' => $status));
    }

    public function validasi_kelas() {
        $this->generate->set_header_JSON();

        $post = $this->input->post();
        $status = $this->kehadiran->absen_divalidasi($post['TANGGAL_AKH'], $post['KELAS_FILTER']);

        if ($status)
            $this->kehadiran->hapus_validasi_kelas($post['TANGGAL_AKH'], $post['KELAS_FILTER']);
        else
            $this->kehadiran->tambah_validasi_kelas($post['TANGGAL_AKH'], $post['KELAS_FILTER']);

        $this->generate->output_JSON(array('status' => $status));
    }

    public function validasi_semua_kelas() {
        $this->generate->set_header_JSON();

        $post = $this->input->post();
        $kelas = $this->kelas->get_all(FALSE);
        $this->kehadiran->tambah_validasi_kelas($post['TANGGAL_AKH'], $kelas);

        $this->generate->output_JSON(array('status' => 1));
    }

}
