<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Penanganan_siswa extends CI_Controller {

    var $table = 'bk_penanganan';
    var $joins = array(
        array('bk_pemanggilan', 'PEMANGGILAN_PENANGANAN=ID_PANGGIL'),
        array('md_siswa', 'SISWA_PENANGANAN=ID_SISWA'),
        array('akad_siswa', 'TA_PENANGANAN=TA_AS AND SISWA_AS=SISWA_PENANGANAN'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('md_pegawai', 'WALI_KELAS=ID_PEG'),
        array('bk_kategori', 'KATEGORI_PENANGANAN=ID_BKKAT', 'LEFT'),
        array('bk_kelompok', 'KELOMPOK_BKKAT=ID_BKKEL', 'LEFT'),
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
                'STATUS_PENANGANAN <> ' => 'SELESAI',
            )
        );
    }

    public function index() {
        $data = array(
            'title' => 'Penanganan Siswa',
            'subtitle' => 'Daftar siswa yang belum ditangani setelah diberikan surat pemanggilan',
            'columns' => array(
                'TANGGAL PANGGIL',
                'NO ABSEN',
                'NIS',
                'NAMA',
                'KELAS',
                'WALI',
                'POIN PANGGIL',
                'STATUS',
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
                        array('id' => 'STATUS', 'target' => 7, 'options' => array(
                                array('id' => '', 'text' => "-- Pilih Status --"),
                                array('id' => 'PROSES', 'text' => "PROSES"),
                                array('id' => 'BELUM', 'text' => "BELUM"),
                            )),
                    )
                ),
            )
        );

        $this->generate->datatables_view($data);
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array("DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y')", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN');
        $select = array("DATE_FORMAT(TANGGAL_PANGGIL,'%d-%m-%Y') AS TANGGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN');
        $orders = array("TANGGAL", 'NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'POIN_PANGGIL', 'STATUS_PENANGANAN', 'ID_PANGGIL', 'ID_PENANGANAN');
        $order = array("ID_PENANGANAN" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select, $this->params);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $item->TANGGAL;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->POIN_PANGGIL;
            $row[] = $item->STATUS_PENANGANAN;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Input Penanganan" onclick="update_data_' . $this->id_datatables . '(\'' . $item->ID_PENANGANAN . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Input Penanganan</a></li>
                        <li><a href="' . site_url('bk/surat_pemanggilan/cetak_surat/0/' . $item->ID_PANGGIL) . '" target="_blank" title="Lihat Surat Pemanggilan"><i class="fa fa-print"></i>&nbsp;&nbsp;Lihat Surat Pemanggilan</a></li>
                    </ul>
                </div>';

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

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->table, $this->primary_key, $this->joins);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Tanggal Penanganan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_PENANGANAN',
                    'value' => $data == NULL ? "" : $data->TANGGAL_PENANGANAN
                )
            ),
            array(
                'label' => 'Kategori',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'KATEGORI_PENANGANAN',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->KATEGORI_PENANGANAN,
                    'label' => $data == NULL ? "" : $data->NAMA_BKKAT . ' - ' . $data->NAMA_BKKEL,
                    'data' => NULL,
                    'url' => base_url('bk/kategori/auto_complete')
                )
            ),
            array(
                'label' => 'Penyebab',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 10,
                'data' => array(
                    'type' => 'text',
                    'name' => 'PENYEBAB_PENANGANAN',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->PENYEBAB_PENANGANAN
                )
            ),
            array(
                'label' => 'Solusi',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 10,
                'data' => array(
                    'type' => 'text',
                    'name' => 'SOLUSI_PENANGANAN',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->SOLUSI_PENANGANAN
                )
            ),
            array(
                'label' => 'Status Penanganan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'dropdown',
                    'name' => 'STATUS_PENANGANAN',
                    'value' => $data == NULL ? "" : $data->STATUS_PENANGANAN,
                    'value_blank' => '-- Pilih Status --',
                    'data' => array(
                        array('id' => 'SELESAI', 'text' => "SELESAI"),
                        array('id' => 'PROSES', 'text' => "PROSES"),
                        array('id' => 'BELUM', 'text' => "BELUM"),
                    )
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function action($action) {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form($action);

        $posts = $this->input->post();
        $posts['USER_PENANGANAN'] = $this->session->userdata('ID_USER');

        if ($action == 'add')
            $affected_row = $this->db_handler->insert_datatables($this->table, $posts);
        elseif ($action == 'edit')
            $affected_row = $this->db_handler->update_datatables($this->table, $this->primary_key, $posts, $this->edit_id);
        elseif ($action == 'delete')
            $affected_row = $this->db_handler->delete_datatables($this->table, $this->primary_key, $posts['ID']);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

}
