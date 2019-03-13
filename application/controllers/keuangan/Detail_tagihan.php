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
class Detail_tagihan extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_DT";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'detail_tagihan_model' => 'detail_tagihan',
            'departemen_model' => 'departemen',
            'assign_tagihan_model' => 'assign_tagihan'
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/detail_tagihan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->detail_tagihan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_TAG;
            $row[] = $item->NAMA_DT . ($item->PENGECUALIAN_1_DT || $item->PENGECUALIAN_2_DT ? "<br>(PENGECUALIAN " . ($item->PENGECUALIAN_1_DT ? " MI 1, 2 & 3" : "") . ($item->PENGECUALIAN_1_DT && $item->PENGECUALIAN_2_DT ? " DAN " : "") . ($item->PENGECUALIAN_2_DT ? " MI 1" : "") . ")" : "");
            $row[] = $item->NAMA_DEPT;
            $row[] = $this->money->format($item->NOMINAL_DT);
            $row[] = $item->L_DT ? '<strong>YA</strong>' : 'TIDAK';
            $row[] = $item->P_DT ? '<strong>YA</strong>' : 'TIDAK';

            if ($this->assign_tagihan->is_assigned($item->ID_DT))
                $row[] = '-';
            else
                $row[] = '
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_DT . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                            <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_DT . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                        </ul>
                    </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->detail_tagihan->count_all(),
            "recordsFiltered" => $this->detail_tagihan->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form_add() {
        $data = $this->generate->set_header_form_JSON($this->detail_tagihan);

        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Tagihan', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'autocomplete', // WAJIB
                    'name' => 'TAGIHAN_DT', // WAJIB
                    'multiple' => FALSE, // IF NEEDED
                    'value' => $data == NULL ? "" : $data->TAGIHAN_DT,
                    'label' => $data == NULL ? "" : $data->NAMA_TA . ' - ' . $data->NAMA_TAG,
                    'data' => NULL, // WAJIB
                    'url' => base_url('keuangan/tagihan/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Detail Tagihan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_DT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_DT
                )
            ),
            array(
                'label' => 'Pengecualian MI kelas 1, 2, dan 3',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio',
                    'name' => 'PENGECUALIAN_1_DT',
                    'inline' => true,
                    'value' => $data == NULL ? "0" : $data->PENGECUALIAN_1_DT,
                    'data' => array(
                        array('label' => 'Tidak', 'value' => '0'),
                        array('label' => 'Ya', 'value' => '1'),
                    )
                )
            ),
            array(
                'label' => 'Pengecualian MI kelas 1',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'radio',
                    'name' => 'PENGECUALIAN_2_DT',
                    'inline' => true,
                    'value' => $data == NULL ? "0" : $data->PENGECUALIAN_2_DT,
                    'data' => array(
                        array('label' => 'Tidak', 'value' => '0'),
                        array('label' => 'Ya', 'value' => '1'),
                    )
                )
            ),
        );

        $data_dept = $this->departemen->get_all(false);
        foreach ($data_dept as $jenjang) {
            $JENJANG = $jenjang->ID_DEPT;

            $data_html[] = array(
                'label' => 'Nominal utk ' . $JENJANG,
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'finance',
                    "onblur" => "to_currency_nominal('" . 'NOMINAL_DT_' . $JENJANG . "', this);",
                    "onfocus" => "to_number_nominal('" . 'NOMINAL_DT_' . $JENJANG . "', this);",
                    'value' => $data == NULL ? "Rp. 0,00" : "Rp. " . number_format($data->NOMINAL_DT, 2, ",", "."),
                )
            );
            $data_html[] = array(
                'label' => 'JK Nominal ' . $JENJANG,
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'checkbox',
                    'name' => 'JK_NOMINAL_' . $JENJANG,
                    'inline' => true,
                    'value' => '',
                    'data' => array(
                        array('value' => 'L', 'label' => "Laki-laki"),
                        array('value' => 'P', 'label' => "Perempuan"),
                    )
                )
            );
            $data_html[] = array(
                'hidden' => TRUE, // WAJIB
                'data' => array(
                    'name' => 'NOMINAL_DT_' . $JENJANG, // WAJIB
                    'id' => 'NOMINAL_DT_' . $JENJANG, // WAJIB
                    'value' => $data == NULL ? 0 : $data->NOMINAL_DT
                )
            );
        }

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function request_form_update() {
        $data = $this->generate->set_header_form_JSON($this->detail_tagihan);

        $JENJANG = $data->DEPT_DT;
        $input_id = FALSE;
        $show_id = FALSE;

        $data_html = array(
            array(
                'label' => 'Tagihan', // WAJIB
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 5,
                'data' => array(
                    'type' => 'autocomplete', // WAJIB
                    'name' => 'TAGIHAN_DT', // WAJIB
                    'multiple' => FALSE, // IF NEEDED
                    'value' => $data == NULL ? "" : $data->TAGIHAN_DT,
                    'label' => $data == NULL ? "" : $data->NAMA_TA . ' - ' . $data->NAMA_TAG,
                    'data' => NULL, // WAJIB
                    'url' => base_url('keuangan/tagihan/auto_complete')                      // WAJIB
                )
            ),
            array(
                'label' => 'Detail Tagihan',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'text',
                    'name' => 'NAMA_DT',
                    "placeholder" => " ",
                    'value' => $data == NULL ? "" : $data->NAMA_DT
                )
            ),
            array(
                'label' => 'Nominal utk ' . $JENJANG,
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'finance',
                    "onblur" => "to_currency_nominal('" . 'NOMINAL_DT_' . $JENJANG . "', this);",
                    "onfocus" => "to_number_nominal('" . 'NOMINAL_DT_' . $JENJANG . "', this);",
                    'value' => $data == NULL ? "Rp. 0,00" : "Rp. " . number_format($data->NOMINAL_DT, 2, ",", "."),
                )
            ), array(
                'label' => 'JK Nominal ' . $JENJANG,
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'checkbox',
                    'name' => 'JK_NOMINAL',
                    'inline' => true,
                    'value' => '',
                    'data' => array(
                        array('value' => 'L', 'label' => "Laki-laki"),
                        array('value' => 'P', 'label' => "Perempuan"),
                    )
                )
            ),
            array(
                'hidden' => TRUE, // WAJIB
                'data' => array(
                    'name' => 'NOMINAL_DT', // WAJIB
                    'id' => 'NOMINAL_DT_' . $JENJANG, // WAJIB
                    'value' => $data == NULL ? 0 : $data->NOMINAL_DT
                )
            )
        );

        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = array(
            'TAGIHAN_DT' => $this->input->post('TAGIHAN_DT'),
            'NAMA_DT' => $this->input->post('NAMA_DT'),
            'NOMINAL_DT' => $this->input->post('NOMINAL_DT'),
        );

        $data_dept = $this->departemen->get_all(false);
        foreach ($data_dept as $jenjang) {
            $JENJANG = $jenjang->ID_DEPT;

            if ($this->pengaturan->isPengecualianTagihan(1, $JENJANG))
                $data['PENGECUALIAN_1_DT'] = $this->input->post('PENGECUALIAN_1_DT');
            if ($this->pengaturan->isPengecualianTagihan(2, $JENJANG))
                $data['PENGECUALIAN_2_DT'] = $this->input->post('PENGECUALIAN_2_DT');

            $data['DEPT_DT'] = $JENJANG;
            $data['L_DT'] = $this->input->post('JK_NOMINAL_' . $JENJANG . 'L') == null ? 0 : 1;
            $data['P_DT'] = $this->input->post('JK_NOMINAL_' . $JENJANG . 'P') == null ? 0 : 1;
            $data['NOMINAL_DT'] = $this->input->post('NOMINAL_DT_' . $JENJANG);

            $insert = $this->detail_tagihan->save($data);

            if (isset($data['PENGECUALIAN_1_DT']))
                unset($data['PENGECUALIAN_1_DT']);
            if (isset($data['PENGECUALIAN_2_DT']))
                unset($data['PENGECUALIAN_2_DT']);
        }

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $this->input->post());

        $where = $cek['where'];

        if (isset($cek['data']))
            $data = $cek['data'];
        else
            $data = array();

        $data['TAGIHAN_DT'] = $this->input->post('TAGIHAN_DT');
        $data['NAMA_DT'] = $this->input->post('NAMA_DT');
        $data['NOMINAL_DT'] = $this->input->post('NOMINAL_DT');
        $data['L_DT'] = $this->input->post('JK_NOMINALL') == null ? 0 : 1;
        $data['P_DT'] = $this->input->post('JK_NOMINALP') == null ? 0 : 1;

        if ($this->assign_tagihan->is_assigned($this->primary_key))
            $this->generate->output_JSON(array("status" => 0));

        $affected_row = $this->detail_tagihan->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->detail_tagihan->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->detail_tagihan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

}
