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
 * MI2017-3832 L
 * MI2017-3834
 * MI2017-3835
 * 
 * MI2017-3836
 * MI2017-3837
 * MI2017-3838 mutasi syariah
 * 
 * @author rohmad
 */
class Calon_siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'calon_siswa_model' => 'calon_siswa',
            'siswa_model' => 'siswa',
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
            'alumni_model' => 'alumni',
        ));
        $this->load->library('denah_handler');
        $this->load->library('tagihan_handler');
        $this->auth->validation(3);
    }

    public function index() {
        $this->generate->backend_view('psb/calon_siswa/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $status_psb = $this->psb_validasi->is_psb_tutup();
        $list = $this->calon_siswa->get_datatables();
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
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Lihat" onclick="view_data_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat</a></li>
                        ' . ($status_psb ? '' : '<li><a href="javascript:void()" title="Mengundurkan diri" onclick="mengundurkan_diri(\'' . $item->ID_SISWA . '\')"><i class="fa fa-thumbs-down"></i>&nbsp;&nbsp;Mengundurkan diri</a></li>') . '
                        ' . ($status_psb ? '' : '<li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_SISWA . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>') . '
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->calon_siswa->count_all(),
            "recordsFiltered" => $this->calon_siswa->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form($ID_SISWA = NULL, $view = FALSE) {
        $data['STATUS_PSB'] = $this->psb_validasi->is_psb_tutup();

        if ($ID_SISWA !== NULL) {
            $data['data'] = $this->calon_siswa->get_by_id($ID_SISWA);

            if (file_exists('files/siswa/' . $data['data']->NIS_SISWA . '.jpg')) {
                $data['data']->FOTO_SISWA = $data['data']->NIS_SISWA . '.jpg';
            } elseif (file_exists('files/siswa/' . $data['data']->ID_SISWA . '.png') || $data['data']->FOTO_SISWA != NULL) {
                $data['data']->FOTO_SISWA = $data['data']->ID_SISWA . '.png';
            }

            $name_view = 'form';
        } else {
            $data['data'] = NULL;
            $name_view = 'form_add';
        }
        
        $data['mode_view'] = $view;

        $this->generate->backend_view('psb/calon_siswa/' . $name_view, $data);
    }

    private function selection_form($data) {
        if (!isset($data['IBU_TANGGAL_LAHIR_SISWA']))
            $data['IBU_TANGGAL_LAHIR_SISWA'] = date('Y-m-d');
        if (!isset($data['TANGGAL_IJASAH_SISWA']))
            $data['TANGGAL_IJASAH_SISWA'] = date('Y-m-d');

        if (isset($data['NIK_SISWA']))
            $data['NIK_SISWA'] = preg_replace('/[^0-9]/', '', $data['NIK_SISWA']);
        if (isset($data['KK_SISWA']))
            $data['KK_SISWA'] = preg_replace('/[^0-9]/', '', $data['KK_SISWA']);
        if (isset($data['NISN_SISWA']))
            $data['NISN_SISWA'] = preg_replace('/[^0-9]/', '', $data['NISN_SISWA']);
        if (isset($data['NOHP_SISWA']))
            $data['NOHP_SISWA'] = preg_replace('/[^0-9]/', '', $data['NOHP_SISWA']);

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
            $this->generate->output_JSON(array("status" => 0, 'msg' => 'Tingkat ' . $post['MASUK_TINGKAT_SISWA'] . ' tidak diperbolehkan pada jenjang tersebut.'));
        }

        if ($this->psb_validasi->is_psb_tutup())
            $post['NO_UM_SISWA'] = NULL;

        return $post;
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

//        if ($this->psb_validasi->is_psb_tutup()) {
//            $insert = 0;
//            $msg = 'PSB telah ditutup';
//        } else {
        $msg = '';
        $data = $this->selection_form($this->input->post());
        $data = $this->selection_form_photo($data);

        if (isset($data['TAKE_FOTO_SISWA'])) {
            $data_image = $data['TAKE_FOTO_SISWA'];
            unset($data['TAKE_FOTO_SISWA']);
        }

        $data = $this->cek_um($data);

        $data['ANGKATAN_SISWA'] = $this->pengaturan->getTahunPSBAwal();
        $insert = $this->siswa->save($data);

        // MENGECEK TAGIHAN PSB 
        // MEMASUKAN CALON SISWA KE TAGIHAN PSB
        if ($insert > 0) {
            $this->set_tagihan_siswa($insert, $data['MASUK_JENJANG_SISWA'], $data['MASUK_TINGKAT_SISWA'], $data['JK_SISWA']);

            if (isset($data_image)) {
                $this->save_photobooth($insert, $data_image);
            }
        }
//        }

        $this->generate->output_JSON(array("status" => $insert, "msg" => $msg));
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
        $data = $this->selection_form_photo($data);

        if (isset($data['MASUK_TINGKAT_SISWA']) && isset($data['ASAL_SEKOLAH_SISWA']))
            $data = $this->cek_um($data);

        if (isset($data['TAKE_FOTO_SISWA'])) {
            $affected_row_image = $this->save_photobooth($data['ID_SISWA'], $data['TAKE_FOTO_SISWA']);
            unset($data['TAKE_FOTO_SISWA']);
        }

        $where = array(
            'ID_SISWA' => $data['ID_SISWA']
        );
        unset($data['ID_SISWA']);
        unset($data['TEMP_MASUK_JENJANG_SISWA']);
        unset($data['TEMP_MASUK_TINGKAT_SISWA']);
        unset($data['TEMP_NO_UM_SISWA']);

//        if ($this->psb_validasi->is_psb_tutup()) {
        unset($data['MASUK_JENJANG_SISWA']);
        unset($data['MASUK_TINGKAT_SISWA']);
//        }

        $affected_row = $this->siswa->update($where, $data);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->siswa->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    private function save_photobooth($ID_SISWA, $data_image) {
        list($type, $data_image) = explode(';', $data_image);
        list(, $data_image) = explode(',', $data_image);
        $data_image = base64_decode($data_image);
        $name_file = 'files/siswa/' . $ID_SISWA . '.png';

        file_put_contents($name_file, $data_image);

        $data['FOTO_SISWA'] = $ID_SISWA . '.png';
        $where['ID_SISWA'] = $ID_SISWA;

        return $this->siswa->update($where, $data);
    }

    public function selection_form_photo($post) {
        unset($post['from_upload']);
        unset($post['UPLOAD_FOTO_SISWA']);

        return $post;
    }

    public function save_photo() {
        $ID_SISWA = $this->input->post('ID_SISWA');
        $file_element_name = 'UPLOAD_FOTO_SISWA';
        $config['upload_path'] = './files/siswa/';
        $config['allowed_types'] = 'png';
        $config['max_size'] = '2000';
        $config['max_width'] = '2400';
        $config['max_height'] = '2400';
        $config['overwrite'] = TRUE;
        $config['file_name'] = $ID_SISWA;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_element_name)) {
            $aa = $this->upload->data();

            $data['FOTO_SISWA'] = $ID_SISWA . '.png';
            $where['ID_SISWA'] = $ID_SISWA;
            $this->siswa->update($where, $data);

            $status = TRUE;
            $msg = "berhasil diupload";
            @unlink($_FILES[$file_element_name]);
        } else {
            $status = FALSE;
            $msg = 'gagal diupload (ERROR: ' . $this->upload->display_errors('', '') . ')';
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function check_data() {
        $this->generate->set_header_JSON();

        $data['name'] = $this->input->post('name');
        $data['value'] = $this->input->post('value');

        if ($this->calon_siswa->count_all($data) == 0)
            $this->generate->output_JSON(array("status" => TRUE));
        else
            $this->generate->output_JSON(array("status" => FALSE));
    }

    public function ac_suku() {
        $this->generate->set_header_JSON();

        $data = $this->suku->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_agama() {
        $this->generate->set_header_JSON();

        $data = $this->agama->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_kondisi() {
        $this->generate->set_header_JSON();

        $data = $this->kondisi->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_jk() {
        $this->generate->set_header_JSON();

        $data = $this->jk->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_warga() {
        $this->generate->set_header_JSON();

        $data = $this->warga->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_darah() {
        $this->generate->set_header_JSON();

        $data = $this->darah->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_kecamatan() {
        $this->generate->set_header_JSON();

        $data = $this->kecamatan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_tinggal() {
        $this->generate->set_header_JSON();

        $data = $this->tinggal->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_asal_sekolah() {
        $this->generate->set_header_JSON();

        $data = $this->asal_sekolah->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_ortu_hidup() {
        $this->generate->set_header_JSON();

        $data = $this->ortu_hidup->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_ortu_pendidikan() {
        $this->generate->set_header_JSON();

        $data = $this->pendidikan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_ortu_pekerjaan() {
        $this->generate->set_header_JSON();

        $data = $this->pekerjaan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_wali_hubungan() {
        $this->generate->set_header_JSON();

        $data = $this->hubungan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_ortu_penghasilan() {
        $this->generate->set_header_JSON();

        $data = $this->penghasilan->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_jenjang_sekolah() {
        $this->generate->set_header_JSON();

        $data = $this->jenjang_sekolah->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function change_status_um() {
//        $this->generate->set_header_JSON();
//
//        $data['PSB_TEST_SISWA'] = $this->input->post('PSB_TEST_SISWA');
//        $where['ID_SISWA'] = $this->input->post('ID_SISWA');
//
//        $affected_row = $this->siswa->update($where, $data);
//
//        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function list_tingkat_jenjang() {
        $this->generate->set_header_JSON();

        $data = $this->jenjang_sekolah->relasi_jenjang_departemen_tingkat($this->input->post('jenjang'));

        $this->generate->output_JSON($data);
    }

    public function mengundurkan_diri() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $FORCE_PROCCESS = $this->input->post('FORCE_PROCCESS');
        $data = array(
            'STATUS_PSB_SISWA' => 0
        );
        $where = array(
            'ID_SISWA' => $ID_SISWA
        );

        $status_tagihan = $this->tagihan_handler->pengunduran_diri($ID_SISWA, $FORCE_PROCCESS);
        if ($status_tagihan == "") {
            $status = $this->siswa->update($where, $data);
            $option = FALSE;

            if ($status)
                $msg = 'Berhasil memproses siswa';
            else
                $msg = 'Gagal memproses data siswa. Silahkan coba lagi.';
        } else {
            $option = FALSE;

            $status = FALSE;
            $msg = "Gagal memproses pengunduran diri siswa. Ada beberapa tagihan yang belum dikembalikan. Silahkan menghubungi pihak keuangan untuk detailnya.";
        }

        $this->generate->output_JSON(array('status' => $status, 'msg' => $msg, 'option' => $option));
    }

    public function alumni() {
        $this->generate->backend_view('psb/calon_siswa/form_alumni');
    }

    public function get_data_alumni() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $alumni = $this->alumni->get_by_id($ID_SISWA);

        if (file_exists('files/siswa/' . $alumni->NIS_SISWA . '.jpg')) {
            $alumni->FOTO_SISWA = $alumni->NIS_SISWA . '.jpg';
        } elseif (file_exists('files/siswa/' . $alumni->ID_SISWA . '.png') || $alumni->FOTO_SISWA != NULL) {
            $alumni->FOTO_SISWA = $alumni->ID_SISWA . '.png';
        }

        $this->generate->output_JSON(array('siswa' => $alumni));
    }

    public function from_alumni() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $data = (array) $this->alumni->get_by_id_simple($ID_SISWA);
        $data['MASUK_JENJANG_SISWA'] = $this->input->post('MASUK_JENJANG_SISWA');
        $data['MASUK_TINGKAT_SISWA'] = $this->input->post('MASUK_TINGKAT_SISWA');

        $data = $this->cek_um($data);

        $data['ANGKATAN_SISWA'] = $this->pengaturan->getTahunPSBAwal();

        // RESET PARAMETER
        $data['ALUMNI_SISWA'] = 0;
        $data['STATUS_MUTASI_SISWA'] = NULL;
        $data['TANGGAL_MUTASI_SISWA'] = NULL;
        $data['NO_SURAT_MUTASI_SISWA'] = NULL;
        $data['USER_MUTASI_SISWA'] = NULL;

        $where = array(
            'ID_SISWA' => $data['ID_SISWA']
        );
        $insert = $this->siswa->update($where, $data);

        // MENGECEK TAGIHAN PSB 
        // MEMASUKAN CALON SISWA KE TAGIHAN PSB
        if ($insert > 0) {
            $this->set_tagihan_siswa($insert, $data['MASUK_JENJANG_SISWA'], $data['MASUK_TINGKAT_SISWA']);
        }

        $this->generate->output_JSON(array('status' => $insert));
    }

}
