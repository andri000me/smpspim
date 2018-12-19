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
class Pelanggaran extends CI_Controller {

    var $edit_id = TRUE;
    var $primary_key = "ID_KS";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'pelanggaran_model' => 'pelanggaran',
            'pelanggaran_catatan_model' => 'pelanggaran_catatan',
            'pelanggaran_header_model' => 'pelanggaran_header',
            'pelanggaran_perpelanggaran_model' => 'pelanggaran_perpelanggaran',
            'jenis_pelanggaran_model' => 'jenis_pelanggaran'
        ));
        $this->load->library('pelanggaran_handler');
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/pelanggaran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->pelanggaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->CREATED_KS;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->TANGGAL_KS;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->WALI_KELAS;
            $row[] = $item->DOMISILI_SISWA;
//            $row[] = $item->NO_KJP;
            $row[] = $item->NAMA_KJP;
            $row[] = $item->POIN_KJP;
//            $row[] = $item->KETERANGAN_KS;

            if ($item->KEHADIRAN_KS == NULL)
                $action = '<li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_KS . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>';
            else {
                $action = '<li><a href="javascript:void()" title="Hapus" onclick="delete_poin_kehadiran_' . $id_datatables . '(\'' . $item->ID_KS . '\',\'' . $item->KEHADIRAN_KS . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus Poin</a></li>';
                $action .= '<li><a href="javascript:void()" title="Hapus" onclick="delete_data_kehadiran_' . $id_datatables . '(\'' . $item->KEHADIRAN_KS . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus Absensi</a></li>';
            }

            $row[] = ($item->JENIS_AKH > 1 || $item->KEHADIRAN_KS == NULL) ? '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        '.$action.'
                    </ul>
                </div>' : '';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pelanggaran->count_all(),
            "recordsFiltered" => $this->pelanggaran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->pelanggaran);

        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Tanggal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_KS',
                    'value' => $data == NULL ? $this->date_format->to_view(date('Y-m-d')) : $data->TANGGAL_KS
                )
            ),
            array(
                'label' => 'Siswa',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'SISWA_KS',
                    'multiple' => FALSE,
                    'minimum' => 1,
                    'value' => $data == NULL ? "" : $data->SISWA_KS,
                    'label' => $data == NULL ? "" : ((($data->NIS_SISWA == NULL) ? 'BELUM ADA NIS' : $data->NIS_SISWA) . $data->NAMA_SISWA),
                    'data' => NULL,
                    'url' => base_url('akademik/siswa/ac_siswa_kelas')
                )
            ),
            array(
                'label' => 'Pelanggaran',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 10,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'PELANGGARAN_KS',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->PELANGGARAN_KS,
                    'label' => $data == NULL ? "" : $data->NAMA_KJP,
                    'data' => NULL,
                    'url' => base_url('komdis/jenis_pelanggaran/auto_complete_pelanggaran')
                )
            ),
            array(
                'label' => 'Sumber',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'SUMBER_KS',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->SUMBER_KS,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
                    'data' => NULL,
                    'url' => base_url('master_data/pegawai/auto_complete')
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 8,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_KS',
                    'value' => $data == NULL ? "" : $data->KETERANGAN_KS
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('add');

        $ID_TA = $this->session->userdata('ID_TA_ACTIVE');
        $ID_CAWU = $this->session->userdata('ID_CAWU_ACTIVE');
        $ID_PELANGGARAN = $this->input->post('PELANGGARAN_KS');
        $ID_SISWA = $this->input->post('SISWA_KS');
        $TANGGAL_KS = $this->input->post('TANGGAL_KS');
        $SUMBER_KS = $this->input->post('SUMBER_KS');
        $KETERANGAN_KS = $this->input->post('KETERANGAN_KS');

        $insert = $this->pelanggaran_handler->tambah($ID_TA, $ID_CAWU, $ID_SISWA, $ID_PELANGGARAN, $TANGGAL_KS, $SUMBER_KS, $KETERANGAN_KS);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");

        $data_delete = $this->pelanggaran->get_by_id_simple($id);
        $affected_row = $this->pelanggaran_handler->hapus($id);

        if ($affected_row) {
            unset($data_delete['ID_KS']);
            $affected_row = $this->pelanggaran_catatan->save($data_delete);
        }

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_move_data() {
        $this->generate->set_header_JSON();
//        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $data_delete = $this->pelanggaran->get_by_id_simple($id);

        unset($data_delete['ID_KS']);
        $affected_row = $this->pelanggaran_catatan->save($data_delete);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->pelanggaran->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function form() {
        $this->generate->backend_view('komdis/pelanggaran/form');
    }

    public function get_data_scanner() {
        $this->generate->set_header_JSON();

        $post = $this->input->post();
        $data = $this->pelanggaran->get_data_scanner($post['NIS_SISWA']);

        $insert = 0;
        if ($data != NULL) {
            $ID_TA = $this->session->userdata('ID_TA_ACTIVE');
            $ID_CAWU = $this->session->userdata('ID_CAWU_ACTIVE');
            $ID_PELANGGARAN = $post['PELANGGARAN_KS'];
            $ID_SISWA = $data->ID_SISWA;
            $TANGGAL_KS = $post['TANGGAL_KS'];
            $SUMBER_KS = $post['SUMBER_KS'];
            $KETERANGAN_KS = '';

            $insert = $this->pelanggaran_handler->tambah($ID_TA, $ID_CAWU, $ID_SISWA, $ID_PELANGGARAN, $TANGGAL_KS, $SUMBER_KS, $KETERANGAN_KS);
        }

        $this->generate->output_JSON(array('status' => $insert, 'data' => $data));
    }

    public function form_perpelanggaran() {
        $this->generate->backend_view('komdis/pelanggaran/form_perpelanggaran');
    }

    public function ajax_list_perpelanggaran($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->pelanggaran_perpelanggaran->get_datatables($TANGGAL_PELANGGARAN, $PELANGGARAN_KS);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->AYAH_NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;

            $row[] = ($item->ID_KS == NULL) ? '<button type="button" class="btn btn-info btn-sm" onclick="simpan_pelanggaran(this)" data-status="1" data-id="' . $item->ID_SISWA . '"><i class="fa fa-check-circle"></i></button>' : '<button type="button" class="btn btn-danger btn-sm" onclick="simpan_pelanggaran(this)" data-status="0" data-id="' . $item->ID_KS . '"><i class="fa fa-trash"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pelanggaran_perpelanggaran->count_all($TANGGAL_PELANGGARAN, $PELANGGARAN_KS),
            "recordsFiltered" => $this->pelanggaran_perpelanggaran->count_filtered($TANGGAL_PELANGGARAN, $PELANGGARAN_KS),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
