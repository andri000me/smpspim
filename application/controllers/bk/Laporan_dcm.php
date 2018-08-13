<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Laporan_dcm extends CI_Controller {

    var $table = 'bk_dcm';
    var $joins = array(
        array('bk_soal', 'SOAL_DCM=ID_BKSOAL'),
        array('bk_kategori', 'KATEGORI_BKSOAL=ID_BKKAT'),
        array('bk_kelompok', 'KELOMPOK_BKKAT=ID_BKKEL'),
        array('md_siswa', 'SISWA_DCM=ID_SISWA'),
        array('akad_siswa', 'TA_DCM=TA_AS AND SISWA_AS=SISWA_DCM'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
    );
    var $params = array();
    var $primary_key = "ID_DCM";
    var $name_of_pk = "URUTAN_BKSOAL";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));

        $this->params['where']['TA_DCM'] = $this->session->userdata('ID_TA_ACTIVE');
    }

    public function index() {
        $data = array(
            'title' => 'DCM Siswa',
            'subtitle' => 'Daftar semua rekap DCM Siswa',
            'columns' => array(
                'NO',
                'KELAS',
                'WALI',
                'ABS',
                'NIS',
                'NAMA',
                'KATEGORI',
                'SOAL',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Soal",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => array(
                'code_extra' => '$(".buttons-add").remove();'
            )
        );

        $this->generate->datatables_view($data);
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array('ID_DCM', 'NAMA_KELAS', 'NAMA_PEG', 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', "NO_ABSEN_AS", "NAMA_BKKAT", 'URUTAN_BKSOAL', 'ID_DCM');
        $select = $columns;
        $orders = $columns;
        $order = array("ID_DCM" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_BKKAT;
            $row[] = $item->URUTAN_BKSOAL;

            $row[] = '<button title="Hapus" onclick="delete_data_' . $this->id_datatables . '(\'' . $item->ID_DCM . '\')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>';

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

    public function action($action) {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form($action);

        $posts = $this->input->post();

        if ($action == 'delete')
            $affected_row = $this->db_handler->delete_datatables($this->table, $this->primary_key, $posts['ID']);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

}
