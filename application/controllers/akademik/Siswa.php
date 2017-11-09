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
class Siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'siswa_model' => 'siswa',
            'departemen_model' => 'dept',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
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
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(array(2, 7));
    }

    public function index() {
        $this->generate->backend_view('akademik/siswa/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->siswa->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->TEMPAT_LAHIR_SISWA;
            $row[] = $item->TANGGAL_LAHIR_SISWA;
            $row[] = $item->ALAMAT_SISWA_SHOW;
            $row[] = $item->NAMA_PONDOK_MPS_SHOW;
            $row[] = $item->NAMA_KELAS_SHOW;

            if ($item->KELAS_AS == NULL)
                $surat_keterangan_aktif = '';
            else
                $surat_keterangan_aktif = '<li><a href="javascript:void()" title="Surat Keterangan Aktif" onclick="surat_keterangan_aktif(\'' . $item->ID_SISWA . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Surat Keterangan Aktif</a></li>';

            if ($this->session->userdata('ID_HAKAKSES') == 7) {
                $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Lihat Data" onclick="view_data(\'' . $item->ID_SISWA . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Data</a></li>
                        <li><a href="javascript:void()" title="Foto Siswa" onclick="view_photo(\'' . $item->ID_SISWA . '\')"><i class="fa fa-file-photo-o "></i>&nbsp;&nbsp;Foto Siswa</a></li>
                        ' . $surat_keterangan_aktif . '
                    </ul>
                </div>';
            } else {
                $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Ubah Data" onclick="update_data(\'' . $item->ID_SISWA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Data</a></li>
                        <li><a href="javascript:void()" title="Ubah Data Popup" onclick="update_data_popup(\'' . $item->ID_SISWA . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Data Popup</a></li>
                        <li><a href="javascript:void()" title="Lihat Data" onclick="view_data(\'' . $item->ID_SISWA . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Data</a></li>
                        <li><a href="javascript:void()" title="Foto Siswa" onclick="view_photo(\'' . $item->ID_SISWA . '\')"><i class="fa fa-file-photo-o "></i>&nbsp;&nbsp;Foto Siswa</a></li>
                        <li><a href="javascript:void()" title="Kartu Siswa" onclick="kartu_pelajar(\'' . $item->ID_SISWA . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Kartu Siswa</a></li>
                        <li><a href="' . site_url('keuangan/assign_tagihan/cetak_kartu/1/' . $item->ID_SISWA) . '" title="Khoirot Siswa" target="_blank"><i class="fa fa-print"></i>&nbsp;&nbsp;Khoirot Siswa</a></li>
                        ' . $surat_keterangan_aktif . '
                    </ul>
                </div>';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->siswa->count_all(),
            "recordsFiltered" => $this->siswa->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function view_data() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->get_by_id($this->input->post('ID_SISWA'));

        $this->generate->output_JSON($data);
    }

    public function view_photo() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->get_by_id($this->input->post('ID_SISWA'));

        $status = FALSE;
        $name_file_photo = '';
        if (file_exists('files/siswa/' . $data->NIS_SISWA . '.jpg')) {
            $status = TRUE;
            $name_file_photo = $data->NIS_SISWA . '.jpg';
        } elseif (file_exists('files/siswa/' . $data->ID_SISWA . '.png') || $data->FOTO_SISWA != NULL) {
            $status = TRUE;
            $name_file_photo = $data->ID_SISWA . '.png';
        }

        $this->generate->output_JSON(array(
            'status' => $status,
            'data' => array(
                'FOTO_SISWA' => $name_file_photo,
                'NAMA_SISWA' => $data->NAMA_SISWA,
            )
        ));
    }

    public function kartu($ID_SISWA) {
        $data = $this->siswa->get_by_id($ID_SISWA);

        $this->generate->backend_view('akademik/siswa/kartu', $data);
    }

    public function surat_keterangan_aktif($ID_SISWA) {
        $data['siswa'] = $this->siswa->get_by_id($ID_SISWA);

        $this->load->view('backend/akademik/siswa/cetak_keterangan_aktif', $data);
    }

    public function cetak_kartu($ID_SISWA = NULL) {
        $data['siswa'] = $this->siswa->get_data_kartu($ID_SISWA);

        $this->load->view('backend/akademik/siswa/cetak_kartu', $data);
    }

    public function cetak_kartu_kelas($ID_KELAS) {
        if ($ID_KELAS == 0)
            $ID_KELAS = NULL;
        $data['siswa'] = $this->siswa->get_data_kartu(NULL, $ID_KELAS);

        $this->load->view('backend/akademik/siswa/cetak_kartu', $data);
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function form($ID_SISWA, $POP_UP = 0) {
        $data['data'] = $this->siswa->get_by_id($ID_SISWA);
        $data['pop_up'] = $POP_UP;

        $this->generate->backend_view('akademik/siswa/form', $data);
    }

    private function selection_form($data) {
        if (isset($data['TANGGAL_LAHIR_SISWA']))
            $data['TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['TANGGAL_LAHIR_SISWA']);
        if (isset($data['AYAH_TANGGAL_LAHIR_SISWA']))
            $data['AYAH_TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['AYAH_TANGGAL_LAHIR_SISWA']);
        if (isset($data['IBU_TANGGAL_LAHIR_SISWA']))
            $data['IBU_TANGGAL_LAHIR_SISWA'] = $this->date_format->to_store_db($data['IBU_TANGGAL_LAHIR_SISWA']);
        if (isset($data['TANGGAL_IJASAH_SISWA']))
            $data['TANGGAL_IJASAH_SISWA'] = $this->date_format->to_store_db($data['TANGGAL_IJASAH_SISWA']);
        unset($data['validasi']);
        foreach ($data as $key => $value) {
            if ($value == '')
                unset($data[$key]);
        }

        return $data;
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->selection_form($this->input->post());

        $where = array(
            'ID_SISWA' => $data['ID_SISWA']
        );
        unset($data['ID_SISWA']);

        $affected_row = $this->siswa->update($where, $data);

        $this->generate->output_JSON(array("status" => 1));
    }

    public function save_take_photo() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $data_image = $this->input->post('TAKE_FOTO_SISWA');

        $where = array(
            'ID_SISWA' => $ID_SISWA
        );
        $data = array(
            'FOTO_SISWA' => NULL
        );
        $this->siswa->update($where, $data);
        $status = $this->save_photobooth($ID_SISWA, $data_image);

        $this->generate->output_JSON(array("status" => $status, 'msg' => ''));
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

        if ($this->siswa->count_all($data) == 0)
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

    public function ac_siswa_kelas() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->ac_siswa_kelas($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function import_export_data() {
        $data = array(
            'JENJANG' => $this->dept->get_all(FALSE),
            'TINGKAT' => $this->tingkat->get_all(FALSE),
            'KELAS' => $this->kelas->get_all(FALSE),
        );

        $this->generate->backend_view('akademik/siswa/import_export_data', $data);
    }

    public function export_data() {
        $jenjang = $this->input->get('jenjang');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');

        $data = $this->siswa->get_all_data_simple($jenjang, $tingkat, $kelas);

        if (isset($data[0]))
            $field_column = array_keys($data[0]);
        else
            $field_column = array();

        $this->load->library('PHPExcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Rohmad Eko Wahyudi")->setTitle("SIMAPES - AKADEMIK - DATA SISWA");
        $objPHPExcel->getActiveSheet()->fromArray($field_column, null, 'A1');
        $objPHPExcel->getActiveSheet()->fromArray($data, null, 'A2');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export_data_siswa_' . date('Y_m_d_H_i_s') . '.xls"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function import_data() {
        $file_element_name = 'FILE_EXCEL';
        $config['upload_path'] = './files/general/';
        $config['allowed_types'] = 'xls';
        $config['overwrite'] = TRUE;
        $config['file_name'] = 'import_data_siswa';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload($file_element_name)) {
            $aa = $this->upload->data();

            $status = TRUE;
            $msg = "berhasil diupload";

            $this->reading_data_import('./files/general/import_data_siswa.xls');

            @unlink($_FILES[$file_element_name]);
        } else {
            $status = FALSE;
            $msg = 'gagal diupload (ERROR: ' . $this->upload->display_errors('', '') . ')';
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg));
    }

    public function reading_data_import($filename) {
        ini_set("precision", "20");

        $this->load->library('PHPExcel/PHPExcel');

        try {
            $inputFileType = PHPExcel_IOFactory::identify($filename);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($filename);
        } catch (Exception $e) {
            $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Error loading file "' . pathinfo($filename, PATHINFO_BASENAME) . '": ' . $e->getMessage()));
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $rowData = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE);
        $data_field_column = $rowData[0];

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $data_value_column = $rowData[0];
            $data = array_combine($data_field_column, $data_value_column);
            $where = array(
                'NIS_SISWA' => $data['ID_SISWA']
            );
            unset($data['ID_SISWA']);
            $result = $this->siswa->update($where, $data);

//            if ($result != NULL) {
//                echo '<h2>Gagal mengimport pada baris ' . $row . '. </h2><br>Silahkan cek data para baris tersebut. <hr>QUERY: ' . $result;
//                exit();
//            }
        }
    }

}
