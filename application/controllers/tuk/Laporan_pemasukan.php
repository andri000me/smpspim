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
class Laporan_pemasukan extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_TJ";
    var $title = 'PEMASUKAN';

    public function __construct() {
        parent::__construct();
        $this->load->model('jurnal_model', 'jurnal');
        $this->auth->validation(13);
    }

    public function index() {
        $data = array(
            'title' => $this->title,
            'url' => 'laporan_pemasukan'
        );
        $this->generate->backend_view('tuk/laporan/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jurnal->get_datatables($this->title);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TJK;
            $row[] = 'Rp ' . number_format($item->NOMINAL_TJ, 2, ',', '.');
            $row[] = $item->KETERANGAN_TJ;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->CREATED_TJ;

            $row[] = ($item->DIHITUNG_TJ) ? '-' : '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TJ . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="' . site_url('tuk/cetak_kwitansi/cetak_individu/' . $item->ID_TJ) . '" title="Cetak" target="_blank"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jurnal->count_all($this->title),
            "recordsFiltered" => $this->jurnal->count_filtered($this->title),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->jurnal);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Nominal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NOMINAL_TJ',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NOMINAL_TJ,
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_TJ',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->KETERANGAN_TJ,
                )
            )
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
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

        $data['NOMINAL_TJ'] = $this->input->post('NOMINAL_TJ');
        $data['KETERANGAN_TJ'] = $this->input->post('KETERANGAN_TJ');
        $data['USER_TJ'] = $this->session->userdata('ID_USER');

        $affected_row = $this->jurnal->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

}
