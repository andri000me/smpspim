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
class Pengunduran_diri extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_SISWA";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'pengunduran_diri_model' => 'pengunduran_diri',
            'siswa_model' => 'siswa',
            'calon_siswa_model' => 'calon_siswa',
            'suku_model' => 'suku',
            'agama_model' => 'agama',
            'kondisi_model' => 'kondisi',
            'jk_model' => 'jk',
            'Kewarganegaraan_model' => 'warga',
            'darah_model' => 'darah',
            'kecamatan_model' => 'kecamatan',
            'tinggal_model' => 'tinggal',
            'asal_sekolah_model' => 'asal_sekolah',
            'ortu_hidup_model' => 'ortu_hidup',
            'jenjang_pendidikan_model' => 'pendidikan',
            'pekerjaan_model' => 'pekerjaan',
            'hubungan_model' => 'hubungan',
            'penghasilan_model' => 'penghasilan',
            'tagihan_model' => 'tagihan',
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'psb_validasi_model' => 'psb_validasi',
            'tingkat_model' => 'tingkat',
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(3);
    }

    public function index() {
        $this->generate->backend_view('psb/pengunduran_diri/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $status_psb = $this->psb_validasi->is_psb_tutup();
        $list = $this->pengunduran_diri->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->TEMPAT_LAHIR_SISWA;
            $row[] = $item->TANGGAL_LAHIR_SISWA;
            $row[] = $item->ALAMAT_SISWA;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Kembalikan ke Calon Siswa" onclick="kembalikan_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;Kembalikan ke Calon Siswa</a></li>
                        <li><a href="javascript:void()" title="Lihat" onclick="view_data_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pengunduran_diri->count_all(),
            "recordsFiltered" => $this->pengunduran_diri->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form($ID_SISWA = NULL, $view = FALSE) {
        $data['STATUS_PSB'] = $this->psb_validasi->is_psb_tutup();

        if ($ID_SISWA !== NULL) {
            $data['data'] = $this->pengunduran_diri->get_by_id($ID_SISWA);
            $name_view = 'form';
        } else {
            $data['data'] = NULL;
            $name_view = 'form_add';
        }

        if($view) {
            $data['mode_view'] = $view;
            $data['title_view'] = 'Calon Siswa yang Mengundurkan Diri';
        
            $this->generate->backend_view('psb/calon_siswa/'.$name_view, $data);
        } else {
            $this->generate->backend_view('psb/pengunduran_diri/'.$name_view, $data);
        }
    }

    private function cek_um($post) {
        if (isset($post['TEMP_MASUK_JENJANG_SISWA']) && isset($post['TEMP_MASUK_TINGKAT_SISWA'])) {
            if (($post['TEMP_MASUK_TINGKAT_SISWA'] == $post['MASUK_TINGKAT_SISWA']) && ($post['TEMP_MASUK_JENJANG_SISWA'] == $post['MASUK_JENJANG_SISWA']))
                return $post;
        }
        
        $status_tingkat_pada_jenjang = $this->pengaturan->getStatusTingkat($post['MASUK_JENJANG_SISWA'], $post['MASUK_TINGKAT_SISWA']);
        if ($status_tingkat_pada_jenjang) {
            if ($this->pengaturan->getStatusUjianPSB($post['MASUK_JENJANG_SISWA'], $post['MASUK_TINGKAT_SISWA'])) {
                $post['NO_UM_SISWA'] = $this->calon_siswa->get_last_number($post['MASUK_JENJANG_SISWA'], $post['MASUK_TINGKAT_SISWA']) + 1;
            } else {
                $post['NO_UM_SISWA'] = NULL;
            }
        } else {
            $this->generate->output_JSON(array("status" => 0, 'msg' => 'Tingkat ' . $post['MASUK_TINGKAT_SISWA'].' tidak diperbolehkan pada jenjang tersebut.'));
        }
        
        if ($this->psb_validasi->is_psb_tutup()) $post['NO_UM_SISWA'] = NULL;
        
        return $post;
    }

    private function selection_form($data) {
        if(!isset($data['IBU_TANGGAL_LAHIR_SISWA'])) $data['IBU_TANGGAL_LAHIR_SISWA'] = date ('Y-m-d');
        if(!isset($data['TANGGAL_IJASAH_SISWA'])) $data['TANGGAL_IJASAH_SISWA'] = date ('Y-m-d');
        
        $data['TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['TANGGAL_LAHIR_SISWA']);
        $data['AYAH_TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['AYAH_TANGGAL_LAHIR_SISWA']);
        $data['IBU_TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['IBU_TANGGAL_LAHIR_SISWA']);
        $data['TANGGAL_IJASAH_SISWA'] = $this->date_format->to_store_db($data['TANGGAL_IJASAH_SISWA']);
        unset($data['validasi']);
        foreach ($data as $key => $value) {
            if ($value == '')
                unset($data[$key]);
        }

        return $data;
    }
    
    private function set_tagihan_siswa($ID_SISWA, $JENJANG, $TINGKAT, $JK) {
        $data_relasi = $this->jenjang_sekolah->relasi_jenjang_sekolah($JENJANG);
        $status_pengecualian_1 = $this->pengaturan->isPengecualianTagihan(1, $data_relasi->DEPT_MJD, $TINGKAT);
        $status_pengecualian_2 = $this->pengaturan->isPengecualianTagihan(2, $data_relasi->DEPT_MJD, $TINGKAT);
        $tagihan = $this->detail_tagihan->get_all_psb_active($data_relasi->DEPT_MJD, $status_pengecualian_1, $status_pengecualian_2, $JK);

        foreach ($tagihan as $tag) {
            $data_assign = array(
                'SISWA_SETUP' => $ID_SISWA,
                'DETAIL_SETUP' => $tag->ID_DT,
                'KETERANGAN_SETUP' => 'TAGIHAN PSB TAHUN ' . date('Y'),
            );
            
            $this->assign_tagihan->save($data_assign);
        }
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->selection_form($this->input->post());
        $data = $this->cek_um($data);

        $data['ANGKATAN_SISWA'] = $this->pengaturan->getTahunPSBAwal();
        $where['ID_SISWA'] = $data['ID_SISWA'];
        $insert = $this->siswa->update($where, $data);

        // MENGECEK TAGIHAN PSB 
        // MEMASUKAN CALON SISWA KE TAGIHAN PSB
        if ($insert > 0) {
            $this->set_tagihan_siswa($data['ID_SISWA'], $data['MASUK_JENJANG_SISWA'], $data['MASUK_TINGKAT_SISWA'], $data['JK_SISWA']);
        }
        
        $this->generate->output_JSON(array("status" => $insert));
    }
}
