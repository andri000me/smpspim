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
class Absen_jamaah extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_KAH";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'absen_jamaah_model' => 'absen_jamaah',
            'akad_siswa_model' => 'akad_siswa',
            'absen_jamaah_siswa_model' => 'absen_jamaah_siswa',
            'jenis_absensi_model' => 'jenis_absensi',
        ));
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/absen_jamaah/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->absen_jamaah->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->TANGGAL_KAH;
            $row[] = $item->VALIDASI_KAH ? '<i class="fa fa-check"></i>' : '<i class="fa fa-close"></i>';
            $row[] = $item->VALIDASI_KAH ? '-' : '<button type="button" class="btn btn-primary btn-xs" onclick="buka_absen('.$item->ID_KAH.')" title="Klik untuk memulai absen jamaah"><i class="fa fa-pencil-square-o"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->absen_jamaah->count_all(),
            "recordsFiltered" => $this->absen_jamaah->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->absen_jamaah);
        
        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Tanggal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_KAH',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->TANGGAL_KAH
                )
            )
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TA_KAH' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_KAH' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'TANGGAL_KAH' => $this->input->post('TANGGAL_KAH'),
            'USER_KAH' => $this->session->userdata('ID_USER'),
        );
        $insert = $this->absen_jamaah->save($data);

        if ($insert) $this->absen_jamaah->proses_buat_absen($insert);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->absen_jamaah->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function show_absen($ID_KAH) {
        $data_header = $this->absen_jamaah->get_by_id($ID_KAH);

        if($data_header->VALIDASI_KAH)
            redirect(site_url('komdis/absen_jamaah'));

        $data = array('header' => $data_header);

        $this->generate->backend_view('komdis/absen_jamaah/form', $data);
    }

    public function ajax_list_siswa($ID_KAH) {
        $this->generate->set_header_JSON();

        if($this->absen_jamaah->status_validasi($ID_KAH)) redirect(site_url('komdis/absen_jamaah'));

        $id_datatables = 'datatable1';
        $list = $this->absen_jamaah_siswa->get_datatables($ID_KAH);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->TANGGAL_KAH;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->AYAH_NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;

            $row[] = '<select class="form-control input-sm" style="width: 100px" data-kah="'.$item->KAH_KA.'" data-siswa="'.$item->SISWA_KA.'" data-field="ALASAN_KA" onchange="simpan_absen(this)"><option value="-">-</option><option value="HADIR" '.($item->ALASAN_KA_SHOW == 'HADIR' ? 'selected' : '').'>HADIR</option><option value="SAKIT" '.($item->ALASAN_KA_SHOW == 'SAKIT' ? 'selected' : '').'>SAKIT</option><option value="IZIN" '.($item->ALASAN_KA_SHOW == 'IZIN' ? 'selected' : '').'>IZIN</option><option value="ALPHA" '.($item->ALASAN_KA_SHOW == 'ALPHA' ? 'selected' : '').'>ALPHA</option></select>';
            $row[] = '<input type="text" class="form-control input-sm" style="width: 200px" value="'.$item->KETERANGAN_KA_SHOW.'" data-kah="'.$item->KAH_KA.'" data-siswa="'.$item->SISWA_KA.'" data-field="KETERANGAN_KA" onchange="simpan_absen(this)"/>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->absen_jamaah_siswa->count_all($ID_KAH),
            "recordsFiltered" => $this->absen_jamaah_siswa->count_filtered($ID_KAH),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function simpan_absen() {
        $this->generate->set_header_JSON();

        $post = $this->input->post();

        if($this->absen_jamaah->status_validasi($post['KAH_KA'])) redirect(site_url('komdis/absen_jamaah'));

        $data = array($post['FIELD'] => $post['VALUE']);
        $where = array(
            'SISWA_KA' => $post['SISWA_KA'],
            'KAH_KA' => $post['KAH_KA'],
        );
        $result = $this->absen_jamaah_siswa->update($where, $data);

        $this->generate->output_JSON(array('status' => $result));
    }

    public function get_data_scanner() {
        $this->generate->set_header_JSON();

        $NIS_SISWA = $this->input->post('NIS_SISWA');
        $ID_KAH = $this->input->post('ID_KAH');

        if($this->absen_jamaah->status_validasi($ID_KAH)) redirect(site_url('komdis/absen_jamaah'));

        $result = $this->absen_jamaah_siswa->get_data_scanner($ID_KAH, $NIS_SISWA);

        if($result != NULL) {
            $data = array('ALASAN_KA' => 'HADIR');
            $where = array(
                'SISWA_KA' => $result->ID_AS,
                'KAH_KA' => $ID_KAH,
            );
            $this->absen_jamaah_siswa->update($where, $data);
        }

        $this->generate->output_JSON(array('status' => $result == NULL ? FALSE : TRUE, 'data' => $result));
    }

    public function cek_absen() {
        $this->generate->set_header_JSON();

        $ID_KAH = $this->input->post('ID_KAH');

        if($this->absen_jamaah->status_validasi($ID_KAH)) redirect(site_url('komdis/absen_jamaah'));

        $result = $this->absen_jamaah_siswa->get_absen_kosong($ID_KAH);

        $this->generate->output_JSON(array('status' => $result));
    }

    public function proses_validasi() {
        $this->load->library('pelanggaran_handler');
        $this->generate->set_header_JSON();

        $ID_KAH = $this->input->post('ID_KAH');

        if($this->absen_jamaah->status_validasi($ID_KAH)) redirect(site_url('komdis/absen_jamaah'));

        $cek_absen = $this->absen_jamaah_siswa->get_absen_kosong($ID_KAH);

        $status = TRUE;
        $msg = '';
        if($cek_absen) {
            $this->absen_jamaah_siswa->delete_absen($ID_KAH, 'HADIR');
            $this->absen_jamaah_siswa->pindah_data_kekehadiran($ID_KAH);
            $this->absen_jamaah_siswa->delete_absen($ID_KAH, 'SAKIT');
            $this->absen_jamaah_siswa->delete_absen($ID_KAH, 'IZIN');
            
            $data_siswa = $this->absen_jamaah_siswa->get_data_alpha($ID_KAH);
            foreach ($data_siswa as $detail) {
                if ($detail->ALASAN_KA == 'ALPHA') 
                    $this->pelanggaran_handler->tambah($detail->TA_KAH, $detail->CAWU_KAH, $detail->SISWA_AS, $this->jenis_absensi->get_id_pelanggaran(5), $detail->TANGGAL_KAH, $this->session->userdata('ID_USER'), $detail->KETERANGAN_KA == NULL ? "-" : $detail->KETERANGAN_KA, $detail->ID_AKH);
            }

            $this->absen_jamaah_siswa->delete_absen($ID_KAH, 'ALPHA');

            $data = array('VALIDASI_KAH' => 1);
            $where = array('ID_KAH' => $ID_KAH);
            $this->absen_jamaah->update($where, $data);
        } else {
            $status = FALSE;
            $msg = 'Masih ada siswa yang belum memiliki alasan kehadiran. Silahkan cek kembali data absen siswa.';
        }

        $this->generate->output_JSON(array('status' => $status, 'msg' => $msg));
    }
}
