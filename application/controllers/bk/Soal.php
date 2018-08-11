<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Soal extends CI_Controller {

    var $table = 'bk_soal';
    var $joins = array(
        array('bk_kategori', 'KATEGORI_BKSOAL=ID_BKKAT'),
        array('bk_kelompok', 'KELOMPOK_BKKAT=ID_BKKEL'),
    );
    var $params = array();
    var $primary_key = "ID_BKSOAL";
    var $name_of_pk = "URUTAN_BKSOAL";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(14));
    }

    public function index() {
        $data = array(
            'title' => 'Soal',
            'subtitle' => 'Daftar semua soal DCM',
            'columns' => array(
                'NO',
                'KELOMPOK',
                'KATEGORI',
                'SOAL',
                'URUTAN',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Soal",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'bk/' . strtolower($this->router->fetch_class()),
            'url_action' => 'bk/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => array(
                'full' => true
            )
        );

        $this->generate->datatables_view($data);
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array('ID_BKSOAL', 'NAMA_BKKEL', 'NAMA_BKKAT', 'KONTEN_BKSOAL', 'URUTAN_BKSOAL', 'ID_BKSOAL');
        $select = $columns;
        $orders = $columns;
        $order = array("ID_BKSOAL" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->NAMA_BKKEL;
            $row[] = $item->NAMA_BKKAT;
            $row[] = $item->KONTEN_BKSOAL;
            $row[] = $item->URUTAN_BKSOAL;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $this->id_datatables . '(\'' . $item->ID_BKSOAL . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $this->id_datatables . '(\'' . $item->ID_BKSOAL . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
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
                'label' => 'Kategori',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'KATEGORI_BKSOAL',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->KATEGORI_BKSOAL,
                    'label' => $data == NULL ? "" : $data->NAMA_BKKAT . ' - ' . $data->NAMA_BKKEL,
                    'data' => NULL,
                    'url' => base_url('bk/kategori/auto_complete')
                )
            ),
            array(
                'label' => 'Soal',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KONTEN_BKSOAL',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->KONTEN_BKSOAL
                )
            ),
            array(
                'label' => 'Urutan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'URUTAN_BKSOAL',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->URUTAN_BKSOAL
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function action($action) {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form($action);

        $posts = $this->input->post();
        $posts['USER_BKSOAL'] = $this->session->userdata('ID_USER');

        if ($action == 'add')
            $affected_row = $this->db_handler->insert_datatables($this->table, $posts);
        elseif ($action == 'edit')
            $affected_row = $this->db_handler->update_datatables($this->table, $this->primary_key, $posts, $this->edit_id);
        elseif ($action == 'delete')
            $affected_row = $this->db_handler->delete_datatables($this->table, $this->primary_key, $posts['ID']);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->db_handler->get_auto_complete($this->table, $this->input->post('q'), $this->primary_key, $this->name_of_pk, $this->joins);

        $this->generate->output_JSON($data);
    }

}
