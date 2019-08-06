<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi Sistem Informasi Manajemen Pesantren (SIMAPES)
 * PIM
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Tanggal_cawu extends CI_Controller {

    var $table = 'md_tanggal_cawu';
    var $joins = array(
        ['md_tahun_ajaran', 'ID_TA=TA_TC'],
        ['md_catur_wulan', 'ID_CAWU=CAWU_TC'],
    );
    var $params = array();
    var $primary_key = "ID_TC";
    var $name_of_pk = "NAMA_TC";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(11));
    }

    public function index() {
        $data = array(
            'title' => 'Tanggal Cau',
            'subtitle' => 'Daftar tanggal cawu',
            'columns' => array(
                'NO',
                'TA',
                'CAWU',
                'AWAL',
                'AKHIR',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Tanggal Cawu",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'master_data/' . strtolower($this->router->fetch_class()),
            'url_action' => 'master_data/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => [
                'code_extra' => '$(".buttons-add").remove();'
            ]
        );

        $this->generate->datatables_view($data);
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array('ID_TC', 'NAMA_TA', 'NAMA_CAWU', 'AWAL_TC', 'AKHIR_TC', 'ID_TC');
        $select = '*';
        $orders = $columns;
        $order = array("ID_TC" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->AWAL_TC;
            $row[] = $item->AKHIR_TC;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $this->id_datatables . '(\'' . $item->ID_TC . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <!--<li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $this->id_datatables . '(\'' . $item->ID_TC . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>-->
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
                'label' => 'Awal CAWU',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'AWAL_TC',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->AWAL_TC
                )
            ),
            array(
                'label' => 'Akhir CAWU',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'AKHIR_TC',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->AKHIR_TC
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function action($action) {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form($action);

        $posts = $this->input->post();

//        if ($action == 'add')
//            $affected_row = $this->db_handler->insert_datatables($this->table, $posts);
        if ($action == 'edit')
            $affected_row = $this->db_handler->update_datatables($this->table, $this->primary_key, $posts, $this->edit_id);
//        elseif ($action == 'delete')
//            $affected_row = $this->db_handler->delete_datatables($this->table, $this->primary_key, $posts['ID']);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->db_handler->get_auto_complete($this->table, $this->input->post('q'), $this->primary_key, $this->name_of_pk, $this->joins);

        $this->generate->output_JSON($data);
    }

}
