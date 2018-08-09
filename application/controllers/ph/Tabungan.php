<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi Sistem Informasi Akademik (SIAKAD)
 * MTS TBS KUDUS
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */

class Tabungan extends CI_Controller {

    var $table = 'ph_tabungan';
    var $joins = array(
        array('md_tahun_ajaran', 'ID_TA=TA_TABUNGAN'),
        array('md_siswa', 'ID_SISWA=SISWA_TABUNGAN'),
        array('akad_siswa', 'ID_SISWA=SISWA_AS AND TA_TABUNGAN=TA_AS'),
        array('akad_kelas', 'KELAS_AS=ID_KELAS'),
        array('ph_batasan', 'ID_BATASAN=BATASAN_TABUNGAN'),
        array('ph_kitab', 'ID_KITAB=KITAB_BATASAN'),
    );
    var $params = array();
    var $primary_key = "ID_TABUNGAN";
    var $name_of_pk = "NAMA_TABUNGAN";
    var $edit_id = FALSE;
    var $id_datatables = 'datatable1';

    public function __construct() {
        parent::__construct();
        $this->auth->validation(array(5));
    }

    public function index() {
        $data = array(
            'title' => 'Tabungan',
            'subtitle' => 'Daftar semua tabungan hafalan siswa',
            'columns' => array(
                'NO',
                'TA',
                'NIS',
                'NAMA',
                'KELAS',
                'KITAB',
                'BATASAN',
                'NILAI',
                'AKSI',
            ),
            'id_modal' => "modal-data",
            'title_form' => "Tambah Tabungan",
            'id_form' => "form-data",
            'id_datatables' => $this->id_datatables,
            'url' => 'ph/' . strtolower($this->router->fetch_class()),
            'url_action' => 'ph/' . strtolower($this->router->fetch_class()) . '/action',
            'datatables' => array(
                'full' => true
            )
        );

        $this->generate->datatables_view($data, 'ph/tabungan/index');
    }

    public function get_datatables() {
        $this->generate->set_header_JSON();

        $columns = array('ID_TABUNGAN', 'NAMA_TA', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_KITAB', "CONCAT(AWAL_BATASAN, ' - ', AKHIR_BATASAN)", 'NILAI_TABUNGAN', 'ID_TABUNGAN');
        $select = array('ID_TABUNGAN', 'NAMA_TA', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_KITAB', "CONCAT(AWAL_BATASAN, ' - ', AKHIR_BATASAN) AS BATASAN", 'NILAI_TABUNGAN', 'ID_TABUNGAN');
        $orders = array('ID_TABUNGAN', 'NAMA_TA', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_KITAB', "BATASAN", 'NILAI_TABUNGAN', 'ID_TABUNGAN');
        $order = array("ID_TABUNGAN" => 'ASC');
        $datatables = $this->db_handler->get_data_tables($this->table, $this->input->post(), $columns, $orders, $order, $this->joins, $select);

        $data = array();
        $no = $_POST['start'];
        foreach ($datatables['data'] as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->NAMA_TA;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_KITAB;
            $row[] = $item->BATASAN;
            $row[] = $item->NILAI_TABUNGAN;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="use_value(\'' . $item->ID_TABUNGAN . '\')"><i class="fa fa-rocket"></i>&nbsp;&nbsp;Gunakan</a></li>
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $this->id_datatables . '(\'' . $item->ID_TABUNGAN . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $this->id_datatables . '(\'' . $item->ID_TABUNGAN . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
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
                'label' => 'Siswa',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'SISWA_TABUNGAN',
                    'multiple' => FALSE,
                    'minimum' => 1,
                    'value' => $data == NULL ? "" : $data->SISWA_TABUNGAN,
                    'label' => $data == NULL ? "" : $data->NAMA_SISWA,
                    'data' => NULL,
                    'url' => base_url('akademik/siswa/ac_siswa_kelas')
                )
            ),
            array(
                'label' => 'Batasan Kitab',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'BATASAN_TABUNGAN',
                    'multiple' => FALSE,
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->BATASAN_TABUNGAN,
                    'label' => $data == NULL ? "" : 'KITAB: ' . $data->AWAL_BATASAN . ' | BATASAN: ' . $data->AWAL_BATASAN . ' - ' . $data->AKHIR_BATASAN,
                    'data' => NULL,
                    'url' => base_url('ph/batasan_kitab/auto_complete')
                )
            ),
            array(
                'label' => 'Nilai',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NILAI_TABUNGAN',
                    "placeholder" => " ",
                    "data-value" => $data == NULL ? "" : $data->NILAI_MAKS_BATASAN,
                    'value' => $data == NULL ? "" : $data->NILAI_TABUNGAN
                )
            ),
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function action($action) {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form($action);

        $posts = $this->input->post();
        $posts['TA_TABUNGAN'] = $this->session->userdata('ID_TA_ACTIVE');
        $posts['USER_TABUNGAN'] = $this->session->userdata('ID_USER');

        if ($action != 'delete') {
            $data_batasan = $this->db_handler->get_row('ph_batasan', array(
                'where' => array(
                    'ID_BATASAN' => $posts['BATASAN_TABUNGAN']
                )
            ));

            if ($posts['NILAI_TABUNGAN'] > $data_batasan->NILAI_MAKS_BATASAN)
                $this->generate->output_JSON(array("STATUS" => FALSE, "MESSAGE" => 'Nilai tidak boleh lebih dari ' . $data_batasan->NILAI_MAKS_BATASAN));
        }

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

    public function use_value() {
        $this->generate->set_header_JSON();

        $data_batasan = $this->db_handler->get_row('ph_tabungan', array(
            'where' => array(
                'ID_TABUNGAN' => $this->input->post('ID_TABUNGAN')
            )
        ));

        if ($data_batasan->TA_TABUNGAN == $this->session->userdata('ID_TA_ACTIVE'))
            $this->generate->output_JSON(array(
                'status' => false,
                'msg' => 'TA Tabungan tidak boleh digunakan pada tahun ajaran yang sama dengan TA Aktif'
            ));
        else
            $this->db_handler->call_procedure('gunakan_hafalan_tabungan', array(
                $this->session->userdata('ID_TA_ACTIVE'),
                $this->input->post('ID_TABUNGAN')
            ));


        $this->generate->output_JSON(array(
            'status' => true,
            'msg' => 'Tabungan berhasil digunakan'
        ));
    }

}
