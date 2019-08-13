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
class Kehadiran_guru extends CI_Controller {

    var $edit_id = TRUE;
    var $primary_key = "ID_AKG";

    public function __construct() {
        parent::__construct();
        $this->load->model('kehadiran_guru_model', 'kehadiran_guru');
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/kehadiran_guru/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->kehadiran_guru->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NIP_PEG;

            $row[] = $item->NAMA_PEG;

            $row[] = $item->TANGGAL_AKG;
            $row[] = $item->ALASAN_AKG;
            $row[] = $item->KETERANGAN_AKG;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_AKG . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kehadiran_guru->count_all(),
            "recordsFiltered" => $this->kehadiran_guru->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->kehadiran_guru);

        $input_id = FALSE;
        $show_id = TRUE;

        $data_html = array(
            array(
                'label' => 'Guru',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'PEGAWAI_AKG',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->PEGAWAI_AKG,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
                    'data' => NULL,
                    'url' => base_url('master_data/pegawai/auto_complete')
                )
            ),
            array(
                'label' => 'Tanggal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_AKG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->TANGGAL_AKG
                )
            ),
            array(
                'label' => 'Alasan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'dropdown',
                    'name' => 'ALASAN_AKG',
                    'value' => $data == NULL ? "" : $data->ALASAN_AKG,
                    'value_blank' => '-- Pilih Alasan --',
                    'data' => array(
                        array('id' => 'SAKIT', 'text' => "SAKIT"),
                        array('id' => 'IZIN', 'text' => "IZIN"),
                        array('id' => 'ALPHA', 'text' => "TANPA KETERANGAN"),
                    )
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => FALSE,
                'keterangan' => '',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_AKG',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->KETERANGAN_AKG
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TA_AKG' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_AKG' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'USER_AKG' => $this->session->userdata('ID_USER'),
            'PEGAWAI_AKG' => $this->input->post('PEGAWAI_AKG'),
            'TANGGAL_AKG' => $this->input->post('TANGGAL_AKG'),
            'ALASAN_AKG' => $this->input->post('ALASAN_AKG'),
            'KETERANGAN_AKG' => $this->input->post('KETERANGAN_AKG'),
        );
        $insert = $this->kehadiran_guru->save($data);

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());

        $where = $cek['where'];

        if (isset($cek['data']))
            $data = $cek['data'];
        else
            $data = array();

        $data['PEGAWAI_AKG'] = $this->input->post('PEGAWAI_AKG');
        $data['TANGGAL_AKG'] = $this->input->post('TANGGAL_AKG');
        $data['ALASAN_AKG'] = $this->input->post('ALASAN_AKG');
        $data['KETERANGAN_AKG'] = $this->input->post('KETERANGAN_AKG');

        $affected_row = $this->kehadiran_guru->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->kehadiran_guru->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->kehadiran_guru->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function laporan_kehadiran() {
        $data = [
            'data' => $this->db_handler->get_rows('akad_kehadiran_guru', [
                'where' => [
                    'TA_AKG' => $this->session->userdata('ID_TA_ACTIVE')
                ],
                'group_by' => [
                    "ID_PEG",
                    "DATE_FORMAT(TANGGAL_AKG, '%m-%Y')"
                ]
                    ], "*, "
                    . "DATE_FORMAT(TANGGAL_AKG, '%m-%Y') AS BULAN, "
                    . "SUM(CASE WHEN ALASAN_AKG='HADIR' THEN 1 ELSE 0 END) AS JUMLAH_HADIR, "
                    . "SUM(CASE WHEN ALASAN_AKG='SAKIT' THEN 1 ELSE 0 END) AS JUMLAH_SAKIT, "
                    . "SUM(CASE WHEN ALASAN_AKG='IZIN' THEN 1 ELSE 0 END) AS JUMLAH_IZIN, "
                    . "SUM(CASE WHEN ALASAN_AKG='ALPHA' THEN 1 ELSE 0 END) AS JUMLAH_ALPHA"
                    , [
                ['md_pegawai', 'PEGAWAI_AKG=ID_PEG'],
                ['md_tahun_ajaran', 'TA_AKG=ID_TA'],
                ['md_catur_wulan', 'CAWU_AKG=ID_CAWU']
            ])
        ];

        $this->load->view('backend/akademik/kehadiran_guru/laporan_kehadiran', $data);
    }

}
