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
class Tahun_ajaran extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_TA";

    public function __construct() {
        parent::__construct();
        $this->load->model('tahun_ajaran_model', 'tahun_ajaran');
        $this->auth->validation(array(11, 2, 10, 5));
    }

    public function index() {
        $this->generate->backend_view('master_data/tahun_ajaran/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->tahun_ajaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_TA;
            $row[] = $item->NAMA_TA;
            $row[] = $item->TANGGAL_MULAI_TA;
            $row[] = $item->TANGGAL_AKHIR_TA;
            
            if ($item->AKTIF_TA) $row[] = '<i class="fa fa-check"></i>';
            else $row[] = '<i class="fa fa-close" style="cursor: pointer;" onclick="return change_active(1, \''.$item->ID_TA.'\');"></i>';
            
            if ($item->PSB_TA) $row[] = '<i class="fa fa-check"></i>';
            else $row[] = '<i class="fa fa-close" style="cursor: pointer;" onclick="return change_active(0, \''.$item->ID_TA.'\');"></i>';

            $row[] = $item->KETERANGAN_TA;
            
            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_TA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_TA . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tahun_ajaran->count_all(),
            "recordsFiltered" => $this->tahun_ajaran->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_form_JSON($this->tahun_ajaran);
        
        $input_id = FALSE;
        $show_id = TRUE;
        
        $data_ta = array();
        for ($tahun = (date('Y') - 2); $tahun < (date('Y') + 4); $tahun++) {
            $data_tahun = array();
            $data_tahun['id'] = $tahun.'/'.($tahun+1);
            $data_tahun['text'] = $tahun.'/'.($tahun+1);
            array_push($data_ta, $data_tahun);
        }

        $data_html = array(
            array(
                'label' => 'Tahun Ajaran',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
//                'data' => array(
//                    'type' => 'text',
//                    'name' => 'NAMA_TA',
//                    "placeholder" => "Contoh: 2016/2017",
//                    'value' => $data == NULL ? "" : $data->NAMA_TA
//                )
                'data' => array(
                    'type'  => 'dropdown',                                      // WAJIB
                    'name'  => 'NAMA_TA',                                    // WAJIB
                    'value' => $data == NULL ? "" : $data->NAMA_TA,
//                    'value_blank'  => '-- Pilih Kelompok --',
                    'data'  => $data_ta
                )
            ),
            array(
                'label' => 'Tanggal Mulai',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_MULAI_TA',
                    "placeholder" => "MM/DD/YYYY",
                    'value' => $data == NULL ? "" : $this->date_format->to_view($data->TANGGAL_MULAI_TA)
                )
            ),
            array(
                'label' => 'Tanggal Selesai',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 3,
                'data' => array(
                    'type' => 'datepicker',
                    'name' => 'TANGGAL_AKHIR_TA',
                    "placeholder" => "MM/DD/YYYY",
                    'value' => $data == NULL ? "" : $this->date_format->to_view($data->TANGGAL_AKHIR_TA)
                )
            ),
            array(
                'label' => 'Keterangan',
                'required' => FALSE,
                'keterangan' => 'Wajib diisi',
                'length' => 9,
                'data' => array(
                    'type' => 'text',
                    'name' => 'KETERANGAN_TA',
                    "placeholder" => "",
                    'value' => $data == NULL ? "" : $data->KETERANGAN_TA
                )
            ),
        );
        
        $this->generate->output_form_JSON($data, $this->primary_key, $data_html, $input_id, $show_id, $this->edit_id);
    }
    
    private function selection_form($data) {
        $data['TANGGAL_AKHIR_TA'] = date("Y-m-d", strtotime($data['TANGGAL_AKHIR_TA']));
        $data['TANGGAL_MULAI_TA'] = date("Y-m-d", strtotime($data['TANGGAL_MULAI_TA']));
        
        return $data;
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('add');

        $data = $this->selection_form($this->input->post());
        unset($data['TOKEN']);
        $insert = $this->tahun_ajaran->save($data);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('edit');
        $data = array();
        $post = $this->selection_form($this->input->post());
        $data = $this->generate->filter_data_post($this->edit_id, $this->primary_key, $post);
        $cek = $this->generate->cek_update_id($this->edit_id, $this->primary_key, $post);
        
        $where = $cek['where'];
        if (isset($cek['data'])) $data[$this->primary_key] = $cek['data'][$this->primary_key];
        
        $affected_row = $this->tahun_ajaran->update($where, $data);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->tahun_ajaran->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->tahun_ajaran->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
    
    function change_active() {
        $this->generate->set_header_JSON();
        
        $req_active_ta = $this->input->post('TA');
        $where['ID_TA'] = $this->input->post('ID');
        
        if ($req_active_ta) {
            $this->tahun_ajaran->set_ta_deactive();

            if ($this->tahun_ajaran->set_ta_active($where)) {
                $data['message'] = 'Tahun ajaran berhasil diaktifkan. Silahkan login kembali untuk melihat perubahan.';
            } else {
                $data['message'] = 'Tahun ajaran gagal diaktifkan';
            }
        } else {
            $this->tahun_ajaran->set_psb_deactive();

            if ($this->tahun_ajaran->set_psb_active($where)) {
                $data['message'] = 'PSB pada tahun ajaran tersebut berhasil diaktifkan. Silahkan login kembali untuk melihat perubahan.';
            } else {
                $data['message'] = 'PSB pada tahun ajaran tersebut gagal diaktifkan';
            }
        }
        
        $this->generate->output_JSON($data);
    }

}
