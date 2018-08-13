<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Dcm_siswa extends CI_Controller {

    var $table = 'md_siswa';
    var $joins = array(
        array('akad_siswa', 'SISWA_AS=ID_SISWA'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
    );
    var $params = array();
    var $primary_key = "ID_BKKEL";
    var $name_of_pk = "URUTAN_BKKEL";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));

        $this->params['where']['TA_AS'] = $this->session->userdata('ID_TA_ACTIVE');
    }

    public function index() {
        $data = array(
            'title' => 'DCM Siswa',
            'subtitle' => 'Input DCM Siswa',
            'columns' => array(
                'KELAS',
                'WALI',
                'ABS',
                'NIS',
                'NAMA',
                'TAMBAH',
                'SOAL',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah DCM Siswa",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => array(
                'full' => true
            )
        );

        $this->generate->datatables_view($data, 'bk/dcm_siswa/index');
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $kategori = $this->db_handler->get_list('bk_kategori', NULL, 'ID_BKKAT', "NAMA_BKKAT");
        $html_kategori = "<select class='form-control input-sm input-kategori' style='width: 120px;'>";
        foreach ($kategori as $detail) {
            $html_kategori .= "<option value='" . $detail['id'] . "'>" . $detail['text'] . "</option>";
        }
        $html_kategori .= "</select>&nbsp;&nbsp;";

        $columns = array('NAMA_KELAS', 'NAMA_PEG', 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', "NO_ABSEN_AS", "(SELECT GROUP_CONCAT(SOAL_DCM SEPARATOR ', ')  FROM bk_dcm WHERE SISWA_DCM=SISWA_AS AND TA_DCM=TA_AS)", 'ID_SISWA');
        $select = array('NAMA_KELAS', 'NAMA_PEG', 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', "NO_ABSEN_AS", "(SELECT GROUP_CONCAT(SOAL_DCM SEPARATOR ', ')  FROM bk_dcm WHERE SISWA_DCM=SISWA_AS AND TA_DCM=TA_AS) AS SOAL", 'ID_SISWA');
        $orders = array('NAMA_KELAS', 'NAMA_PEG', 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', "NO_ABSEN_AS", "SOAL", 'ID_SISWA');
        $order = array("ID_SISWA" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select);

        $data = array();
        $no = $_POST['start'];
        $temp_html = "";
        foreach ($datatables['data'] as $item) {

            $temp_html = $html_kategori;
            $temp_html .= "<input type='number' class='form-control input-sm input-dcm' data-siswa='" . $item->ID_SISWA . "' style='width: 50px;'/>&nbsp;&nbsp;";
            $temp_html .= "<button type='button' class='btn btn-xs btn-primary' onclick='simpan_dcm(this);'><i class='fa fa-save'></i></button>";

            $no++;
            $row = array();
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $temp_html;
            $row[] = '<img src="' . base_url('assets/images/loading-bars.svg') . '" class="loading-soal" onclick="get_dcm(this)" data-siswa="' . $item->ID_SISWA . '"/>';

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

    public function simpan() {
        $this->generate->set_header_JSON();

        $ID_BKKAT = $this->input->post('kategori');
        $ID_SISWA = $this->input->post('siswa');
        $ID_BKSOAL = $this->input->post('nilai');

        $data_soal = $this->db_handler->get_row('bk_soal', array(
            'where' => array(
                'KATEGORI_BKSOAL' => $ID_BKKAT,
                'URUTAN_BKSOAL' => $ID_BKSOAL,
            )
        ));

        if ($data_soal == NULL) {
            $output = array(
                'status' => false,
                'msg' => 'Soal urutan ' . $ID_BKSOAL . ' tidak ditemukan'
            );
        } else {
            $result = $this->db_handler->insert('bk_dcm', array(
                'TA_DCM' => $this->session->userdata('ID_TA_ACTIVE'),
                'SISWA_DCM' => $ID_SISWA,
                'TAHUN_DCM' => date('Y'),
                'SOAL_DCM' => $data_soal->ID_BKSOAL,
                'USER_DCM' => $this->session->userdata('ID_USER'),
            ));

            $output = array(
                'status' => $result,
                'msg' => 'Nilai ' . ($result ? 'berhasil' : 'gagal') . ' disimpan'
            );
        }

        $this->generate->output_JSON($output);
    }

    public function get_dcm() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('siswa');
        $data = $this->db_handler->get_rows('bk_dcm', array(
            'where' => array(
                'SISWA_DCM' => $ID_SISWA,
                'TA_DCM' => $this->session->userdata('ID_TA_ACTIVE')
            ),
            'order_by' => array(
                'URUTAN_BKKEL' => 'ASC',
                'URUTAN_BKKAT' => 'ASC',
                'URUTAN_BKSOAL' => 'ASC',
            )
                ), '*', array(
            array('bk_soal', 'SOAL_DCM=ID_BKSOAL'),
            array('bk_kategori', 'KATEGORI_BKSOAL=ID_BKKAT'),
            array('bk_kelompok', 'KELOMPOK_BKKAT=ID_BKKEL'),
        ));

        $output = array();
        $kategori = null;
        foreach ($data as $detail) {
            if ($kategori != $detail->ID_BKKAT) {
                $output[$detail->ID_BKKAT] = '<strong>' . $detail->NAMA_BKKAT . '</strong>:&nbsp;&nbsp;';
                $kategori = $detail->ID_BKKAT;
            }
            $output[$detail->ID_BKKAT] .= $detail->URUTAN_BKSOAL . ', ';
        }

        $this->generate->output_JSON($output);
    }

}
