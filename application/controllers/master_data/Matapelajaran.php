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
class Matapelajaran extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "KODE_MAPEL";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'matapelajaran_model' => 'matapelajaran',
            'tipe_mapel_model' => 'tipe_mapel',
            'departemen_model' => 'departemen',
        ));
        $this->auth->validation(array(11, 6));
    }

    public function index() {
        $this->generate->backend_view('master_data/matapelajaran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->matapelajaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->URUTAN_MAPEL . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" title="Klik untuk menaikan urutan" onclick="pindah_urutan(1, ' . $item->URUTAN_MAPEL . ')"><i class="fa fa-angle-double-up"></i></a>&nbsp;&nbsp;<a href="#" title="Klik untuk menurunkan urutan" onclick="pindah_urutan(0, ' . $item->URUTAN_MAPEL . ')"><i class="fa fa-angle-double-down"></i></a>';
            $row[] = $item->KODE_MAPEL;
            $row[] = $item->NAMA_DEPT;
            $row[] = $item->NAMA_MTM;
            $row[] = $item->NAMA_MAPEL;
            $row[] = ($item->PMB_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = ($item->UJIAN_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = ($item->RAPOR_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = ($item->TRANSKRIP_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = ($item->SYAHADAH_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = ($item->AKTIF_MAPEL == 1) ? 'YA' : 'TIDAK';
            $row[] = $item->NAMA_ARAB_MAPEL;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_MAPEL . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_MAPEL . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->matapelajaran->count_all(),
            "recordsFiltered" => $this->matapelajaran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->matapelajaran);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Kode',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 2,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KODE_MAPEL',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->KODE_MAPEL
                )
            ),
            array(
                'label' => 'Jenjang', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'autocomplete', // WAJIB
                    'name' => 'DEPT_MAPEL', // WAJIB
                    'multiple' => FALSE, // IF NEEDED
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->DEPT_MAPEL,
                    'label' => $data == NULL ? "" : $data->NAMA_DEPT,
                    'data' => NULL, // WAJIB
                    'url' => base_url('master_data/departemen/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Tipe', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 4,
                'data' => array(
                    'type' => 'autocomplete', // WAJIB
                    'name' => 'TIPE_MAPEL', // WAJIB
                    'multiple' => FALSE, // IF NEEDED
                    'minimum' => 0,
                    'value' => $data == NULL ? "" : $data->TIPE_MAPEL,
                    'label' => $data == NULL ? "" : $data->NAMA_MTM,
                    'data' => NULL, // WAJIB
                    'url' => base_url('master_data/tipe_mapel/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Nama',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_MAPEL',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_MAPEL
                )
            ),
            array(
                'label' => 'Nama Arab',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_ARAB_MAPEL',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : ($data->NAMA_ARAB_MAPEL == NULL ? '' : $data->NAMA_ARAB_MAPEL)
                )
            ),
            array(
                'label' => 'Aktif', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'AKTIF_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 1 : intval($data->AKTIF_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
            array(
                'label' => 'Diujikan PMB', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'PMB_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 0 : intval($data->PMB_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
            array(
                'label' => 'Diujikan tertulis', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'UJIAN_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 1 : intval($data->UJIAN_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
            array(
                'label' => 'Dicetak di rapor', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'RAPOR_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 1 : intval($data->RAPOR_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
            array(
                'label' => 'Dicetak di transkrip', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'TRANSKRIP_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 1 : intval($data->TRANSKRIP_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
            array(
                'label' => 'Dicetak di syahadah', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio', // WAJIB, ex checkbox, radio
                    'name' => 'SYAHADAH_MAPEL', // WAJIB
                    'inline' => true, // IF NEEDED
                    'value' => $data == NULL ? 1 : intval($data->SYAHADAH_MAPEL),
                    'data' => array(
                        array('value' => 1, 'label' => "YA"),
                        array('value' => 0, 'label' => "TIDAK"),
                    )
                )
            ),
        );

        if ($data != NULL) {
            $data_html[] = array(
                'hidden' => TRUE,
                'data' => array(
                    'name' => 'TEMP_KODE_MAPEL',
                    'value' => $data->KODE_MAPEL
                )
            );
            $data_html[] = array(
                'hidden' => TRUE,
                'data' => array(
                    'name' => 'TEMP_DEPT_MAPEL',
                    'value' => $data->DEPT_MAPEL
                )
            );
        }

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'ID_MAPEL' => $this->input->post('DEPT_MAPEL') . '-' . $this->input->post('KODE_MAPEL'),
            'DEPT_MAPEL' => $this->input->post('DEPT_MAPEL'),
            'KODE_MAPEL' => $this->input->post('KODE_MAPEL'),
            'NAMA_MAPEL' => $this->input->post('NAMA_MAPEL'),
            'NAMA_ARAB_MAPEL' => $this->input->post('NAMA_ARAB_MAPEL'),
            'TIPE_MAPEL' => $this->input->post('TIPE_MAPEL'),
            'PMB_MAPEL' => $this->input->post('PMB_MAPEL'),
            'UJIAN_MAPEL' => $this->input->post('UJIAN_MAPEL'),
            'RAPOR_MAPEL' => $this->input->post('RAPOR_MAPEL'),
            'TRANSKRIP_MAPEL' => $this->input->post('TRANSKRIP_MAPEL'),
            'SYAHADAH_MAPEL' => $this->input->post('SYAHADAH_MAPEL'),
            'AKTIF_MAPEL' => $this->input->post('AKTIF_MAPEL'),
        );
        $insert = $this->matapelajaran->save($data);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');

        $data = array(
            'ID_MAPEL' => $this->input->post('DEPT_MAPEL') . '-' . $this->input->post('KODE_MAPEL'),
            'DEPT_MAPEL' => $this->input->post('DEPT_MAPEL'),
            'KODE_MAPEL' => $this->input->post('KODE_MAPEL'),
            'NAMA_MAPEL' => $this->input->post('NAMA_MAPEL'),
            'NAMA_ARAB_MAPEL' => $this->input->post('NAMA_ARAB_MAPEL'),
            'TIPE_MAPEL' => $this->input->post('TIPE_MAPEL'),
            'PMB_MAPEL' => $this->input->post('PMB_MAPEL'),
            'UJIAN_MAPEL' => $this->input->post('UJIAN_MAPEL'),
            'RAPOR_MAPEL' => $this->input->post('RAPOR_MAPEL'),
            'TRANSKRIP_MAPEL' => $this->input->post('TRANSKRIP_MAPEL'),
            'SYAHADAH_MAPEL' => $this->input->post('SYAHADAH_MAPEL'),
            'AKTIF_MAPEL' => $this->input->post('AKTIF_MAPEL'),
        );
        $where = array(
            'DEPT_MAPEL' => $this->input->post('TEMP_DEPT_MAPEL'),
            'KODE_MAPEL' => $this->input->post('TEMP_KODE_MAPEL'),
        );

        $affected_row = $this->matapelajaran->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->matapelajaran->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->matapelajaran->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function pindah_urutan() {
        $this->generate->set_header_JSON();

        $URUTAN_MAPEL = $this->input->post('URUTAN_MAPEL');
        $NAIK = $this->input->post('NAIK');

        $mapel = $this->matapelajaran->get_urutan($URUTAN_MAPEL, $NAIK);

        if($mapel == NULL) 
            $this->generate->output_JSON(array('status' => FALSE));
        
        $where = array('URUTAN_MAPEL' => $URUTAN_MAPEL);
        $data = array('URUTAN_MAPEL' => 0);
        $affected_row = $this->matapelajaran->update($where, $data);

        if ($affected_row) {
            $where = array('URUTAN_MAPEL' => $mapel->URUTAN_MAPEL);
            $data = array('URUTAN_MAPEL' => $URUTAN_MAPEL);
            $affected_row = $this->matapelajaran->update($where, $data);
            
            if ($affected_row) {
                $where = array('URUTAN_MAPEL' => 0);
                $data = array('URUTAN_MAPEL' => $mapel->URUTAN_MAPEL);
                $affected_row = $this->matapelajaran->update($where, $data);
            }
        }

        $this->generate->output_JSON(array('status' => $affected_row));
    }

}
