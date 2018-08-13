<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Laporan_penanganan extends CI_Controller {

    var $table = 'bk_penanganan';
    var $joins = array(
        array('bk_pemanggilan', 'PEMANGGILAN_PENANGANAN=ID_PANGGIL'),
        array('md_siswa', 'SISWA_PENANGANAN=ID_SISWA'),
        array('akad_siswa', 'TA_PENANGANAN=TA_AS AND SISWA_AS=SISWA_PENANGANAN'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
        array('bk_kategori', 'KATEGORI_PENANGANAN=ID_BKKAT'),
        array('bk_kelompok', 'KELOMPOK_BKKAT=ID_BKKEL'),
    );
    var $params = array();
    var $primary_key = "ID_PENANGANAN";
    var $name_of_pk = "CREATED_PENANGANAN";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));

        $this->params = array(
            'where' => array(
                'TA_PENANGANAN' => $this->session->userdata('ID_TA_ACTIVE'),
                'STATUS_PENANGANAN' => 'SELESAI',
            )
        );
    }

    public function index() {
        $data = array(
            'title' => 'Laporan Penanganan Siswa',
            'subtitle' => 'Daftar siswa yang telah ditangani oleh BK',
            'columns' => array(
                'KATEGORI',
                'TANGGAL PANGGIL',
                'TANGGAL PENANGANAN',
                'NO ABSEN',
                'NIS',
                'NAMA',
                'KELAS',
                'WALI',
                'POIN PANGGIL',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Penanganan Siswa",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => array(
                'code_extra' => '$(".buttons-add").remove();',
                'full' => true,
                'searching' => array(
                    'multiple' => array(
                        array('id' => 'KATEGORI', 'target' => 0, 'options' => $this->db_handler->get_list('bk_kategori', NULL, 'NAMA_BKKAT', "NAMA_BKKAT")),
                    )
                ),
            )
        );

        $this->generate->datatables_view($data, 'bk/laporan_penanganan/index');
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array("NAMA_BKKAT", "DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y')", "DATE_FORMAT(TANGGAL_PENANGANAN,'%d-%m-%Y')", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN', 'NAMA_BKKEL', 'PENYEBAB_PENANGANAN', 'SOLUSI_PENANGANAN');
        $select = array("NAMA_BKKAT", "DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y') AS TANGGAL", "DATE_FORMAT(TANGGAL_PENANGANAN,'%d-%m-%Y') AS TANGGAL_TANGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN', 'NAMA_BKKEL', 'PENYEBAB_PENANGANAN', 'SOLUSI_PENANGANAN');
        $orders = array("NAMA_BKKAT", "TANGGAL", "TANGGAL_TANGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN', 'NAMA_BKKEL', 'PENYEBAB_PENANGANAN', 'SOLUSI_PENANGANAN');
        $order = array("ID_PENANGANAN" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select, $this->params);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_BKKAT;
            $row[] = $item->TANGGAL;
            $row[] = $item->TANGGAL_TANGAL;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->POIN_PANGGIL;

            $row[] = '<a href="' . site_url('bk/surat_pemanggilan/cetak_surat/0/' . $item->ID_PANGGIL) . '" target="_blank" class="btn btn-xs btn-primary" title="Lihat Surat Pemanggilan"><i class="fa fa-print"></i></a><button class="btn btn-xs btn-info" title="Lihat Detail Penanganan" onclick="detail_penanganan(this);" data-id="' . $item->ID_PENANGANAN . '"><i class="fa fa-eye"></i></button>';

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

    public function get_data() {
        $this->generate->set_header_JSON();

        $data = $this->db_handler->get_row($this->table, array(
            'where' => array(
                'ID_PENANGANAN' => $this->input->post('id')
            )
                ), '*');

        $this->generate->output_JSON($data);
    }

}
