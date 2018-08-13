<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Laporan_pemanggilan extends CI_Controller {

    var $table = 'bk_pemanggilan';
    var $joins = array(
        array('md_siswa', 'SISWA_PANGGIL=ID_SISWA'),
        array('akad_siswa', 'TA_PANGGIL=TA_AS AND SISWA_AS=SISWA_PANGGIL'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
    );
    var $params = array();
    var $primary_key = "ID_PANGGIL";
    var $name_of_pk = "POIN_PANGGIL";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));

        $this->params = array(
            'where' => array(
                'TA_PANGGIL' => $this->session->userdata('ID_TA_ACTIVE'),
            )
        );
    }

    public function index() {
        $data = array(
            'title' => 'Laporan surat pemanggilan',
            'subtitle' => 'Daftar laporan surat pemanggilan siswa',
            'columns' => array(
                'NO',
                'TANGGAL PANGGIL',
                'ABS',
                'NIS',
                'NAMA',
                'KELAS',
                'WALI',
                'POIN PANGGIL',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Surat_pemanggilan",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
        );

        $this->generate->datatables_view($data);
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array("ID_PANGGIL","DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y')", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'ID_PANGGIL');
        $select = array("ID_PANGGIL","DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y') AS TANGGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'ID_PANGGIL');
        $orders = array("ID_PANGGIL","TANGGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'ID_PANGGIL');
        $order = array("ID_PANGGIL" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select, $this->params);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->TANGGAL;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->POIN_PANGGIL;

            $row[] = '<a href="' . site_url('bk/surat_pemanggilan/cetak_surat/0/' . $item->ID_PANGGIL) . '" target="_blank" class="btn btn-xs btn-primary" title="Lihat Surat Pemanggilan"><i class="fa fa-print"></i></a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $datatables['count_all'],
            "recordsFiltered" => $datatables['count_filtered'],
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
}
