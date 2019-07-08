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
class Assign_tagihan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'assign_tagihan_model' => 'assign_tagihan'
        ));
        $this->auth->validation(array(4, 2));
    }

    public function index() {
        $this->generate->backend_view('keuangan/assign_tagihan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_kecamatan_margoyoso = 1172;

        $id_datatables = 'datatable1';
        $list = $this->assign_tagihan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $lunas = FALSE;
            $tag_siswa = $this->assign_tagihan->get_tagihan_siswa_simple($item->ID_SISWA);
            $tag_id = array();
            foreach ($tag_siswa as $detail) {
                if ($detail->STATUS_SETUP)
                    $lunas = TRUE;
                $tag_id[] = $detail->ID_SETUP;
            }

            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS;

            $row[] = $item->NAMA_PEG;

            $alamat_siswa = strtoupper($item->ALAMAT_SISWA);

            if ((strpos($alamat_siswa, 'KAJEN') !== FALSE) && ($item->KECAMATAN_SISWA == $id_kecamatan_margoyoso))
                $row[] = '<strong>KAJEN</strong>';
            else
                $row[] = '<button type="button" class="btn btn-sm btn-' . (count($tag_siswa) > 0 ? 'danger' : 'info') . ' btn-' . (count($tag_siswa) > 0 ? 'delete' : 'assign') . '" onclick="proses_tagihan(this)" data-siswa="' . $item->ID_SISWA . '" data-hapus="' . (count($tag_siswa) > 0 ? '1' : '0') . '" data-nama="' . $item->NIS_SISWA . ' - ' . $item->NAMA_SISWA . '" data-dept="' . $item->DEPT_TINGK . '" data-tagihan="' . urlencode(json_encode($tag_id)) . '" ' . ($lunas ? 'disabled' : '') . ' title="' . ($lunas ? 'Tagihan ada yang telah dibayar. Kembalikan terlebih dahulu sebelum menghapus tagihan ini.' : '') . '"><i class="fa fa-' . (count($tag_siswa) > 0 ? 'remove' : 'plus') . '"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->assign_tagihan->count_all(),
            "recordsFiltered" => $this->assign_tagihan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function cetak_kartu($ID_KELAS = NULL, $ID_SISWA = NULL) {
        $siswa = $this->assign_tagihan->get_data_kartu($ID_KELAS, $ID_SISWA);

        $data = array();
        foreach ($siswa as $detail) {
            $data['siswa'][] = array(
                'siswa' => $detail,
                'tagihan' => $this->assign_tagihan->get_tagihan_siswa_kartu($detail->ID_SISWA),
            ); 
        }  
        var_dump(json_decode(json_encode($data), true)['siswa'][0]['tagihan']);exit();  

        $this->load->view('backend/keuangan/assign_tagihan/cetak_kartu', $data);
    }

    public function proses_unsign() {
        $this->generate->set_header_JSON();
        $this->load->library('tagihan_handler');

        $ID_SETUP_JSON = $this->input->post('ID_SETUP');
        $ID_SETUP = json_decode($ID_SETUP_JSON, TRUE);

        $this->tagihan_handler->unsign_tagihan($ID_SETUP);

        $this->generate->output_JSON(array('status' => 1));
    }

    public function proses_sign() {
        $this->generate->set_header_JSON();
        $this->load->library('tagihan_handler');

        $ID_SISWA = $this->input->post('ID_SISWA');
        $DEPT_TINGK = $this->input->post('DEPT_TINGK');

        $this->tagihan_handler->assign_tagihan($DEPT_TINGK, $ID_SISWA);

        $this->generate->output_JSON(array('status' => 1));
    }

}
