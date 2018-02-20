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
class Denah_us extends CI_Controller {

    var $mode = 'US';
    var $title = 'Ujian Sekolah';
    var $status_validasi = FALSE;

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'aturan_denah_model' => 'aturan_denah',
            'denah_model' => 'denah',
            'denah_us_model' => 'denah_us',
            'peserta_us_model' => 'peserta_us',
            'ruang_model' => 'ruang',
            'tingkat_model' => 'tingkat',
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(6);
        $this->status_validasi = $this->aturan_denah->is_us_validasi();
    }

    public function index() {
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;

        $this->generate->backend_view('pu/denah_us_manual/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->denah_us->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;

            $aksi = '';
            if ($item->DATA_DENAH != NULL) {
                $aksi .= '<li><a href="javascript:void()" title="Lihat Denah" onclick="view_data_' . $id_datatables . '(\'' . $item->ID_PUD . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Denah</a></li>';
            }
            if (!$item->READY_DENAH) {
                $aksi .= '<li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PUD . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah Aturan Denah</a></li>';
            }
            if (!$item->VALIDASI_DENAH && ($item->ATURAN_RUANG_PUD != NULL)) {
                $aksi .= '<li><a href="javascript:void()" title="Hapus Aturan" onclick="hapus_denah(0)"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus Aturan</a></li>';
            }
            if (!$item->VALIDASI_DENAH && ($item->DATA_DENAH != NULL)) {
                $aksi .= '<li><a href="javascript:void()" title="Hapus Denah" onclick="hapus_denah(1)"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus Denah</a></li>';
            }

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">' . $aksi
                    . '</ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->denah_us->count_all(),
            "recordsFiltered" => $this->denah_us->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function cek_denah() {
        $this->generate->set_header_JSON();

        if ($this->status_validasi)
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));

        $status = $this->aturan_denah->is_us_dibuat();

        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah telah dibuat. Anda tidak boleh membuat denah baru pada tahun ajaran aktif.'));
    }

    public function get_data_denah($saving = false) {
        if ($this->aturan_denah->is_us_dibuat())
            $denah = json_decode($this->aturan_denah->get_denah_cawu(), true);
        else
            $denah = NULL;

        $data['JUMLAH_KURSI'] = 40;
        $data['TINGKAT'] = $this->tingkat->get_for_ujian();
        $data['DATA_JK'] = array(
            'L',
            'P'
        );
        $data['JUMLAH_SISWA'] = array(
            'L' => $this->peserta_us->get_peserta_ujian('L'),
            'P' => $this->peserta_us->get_peserta_ujian('P'),
        );
        $data['RUANG'] = array(
            'L' => ($denah == NULL ? $this->ruang->get_ruang_ujian('L', $data['JUMLAH_KURSI']) : $denah['L']['RUANG']),
            'P' => ($denah == NULL ? $this->ruang->get_ruang_ujian('P', $data['JUMLAH_KURSI']) : $denah['P']['RUANG']),
        );
        $data['MODEL'] = array(
            'L' => array(
                'data' => array(),
                'jumlah_ruang' => array()
            ),
            'P' => array(
                'data' => array(),
                'jumlah_ruang' => array()
            )
        );

        if ($denah != NULL) {
            foreach ($denah as $jk => $detail) {
                foreach ($detail['JUMLAH'] as $index => $jumlah) {
                    $data['JUMLAH_SISWA'][$jk][$index]['JUMLAH_SISWA'] = $jumlah;
                }
                if (!$saving) {
                    foreach ($detail['DATA'] as $index => $value) {
                        $temp_value = $value;
                        foreach ($temp_value as $key => $item) {
                            $temp_value[$key] ++;
                        }
                        if (in_array($temp_value, $data['MODEL'][$jk]['data'])) {
                            $data['MODEL'][$jk]['jumlah_ruang'][key($data['MODEL'][$jk]['data'])] ++;
                        } else {
                            $data['MODEL'][$jk]['data'][] = $temp_value;
                            $data['MODEL'][$jk]['jumlah_ruang'][] = 1;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function buat_denah() {
        if ($this->status_validasi)
            redirect('pu/denah_us/show_denah');

        $data = $this->get_data_denah();
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;

//        foreach ($data['TINGKAT'] as $key => $value) {
//            if (in_array('MI', $value))
//                unset($data['TINGKAT'][$key]);
//        }
//
//        foreach ($data['JUMLAH_SISWA'] as $key => $value) {
//            foreach ($value as $key1 => $value1) {
//                if (in_array('MI', $value1))
//                    unset($data['JUMLAH_SISWA'][$key][$key1]);
//            }
//        }
//
//        foreach ($data['RUANG'] as $key => $value) {
//            foreach ($value as $key1 => $value1) {
//                if (in_array('MI', $value1))
//                    unset($data['RUANG'][$key][$key1]);
//            }
//        }

        $this->generate->backend_view('pu/denah_us_manual/form', $data);
    }

    public function check_data() {
//        $this->generate->set_header_JSON();

        $data = $this->aturan_denah->get_by_id(11);
        $denah = json_decode($data->DATA_DENAH, TRUE);

//        echo json_encode($denah['L']['DATA'][7]);
//        echo '<hr>';
//        exit();
//
//        foreach ($denah['L']['DATA'] as $key => $value) {
//            echo json_encode($value);
//            echo '<hr>';
//        }

        foreach ($denah as $key => $value) {
            foreach ($value as $key1 => $value1) {
//                foreach ($value1 as $key2 => $value2) {
                echo json_encode($key1);
                echo '<br>';
                echo json_encode($value1);
                echo '<hr>';
//                }
            }
            break;
        }

//        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah telah dibuat. Anda tidak boleh membuat denah baru pada tahun ajaran aktif.'));
    }

    public function simpan_denah() {
//        $this->generate->set_header_JSON();
        $data_form = $this->get_data_denah(true);
//        $jk = 'L';
//        $denah = explode(',', "7,8,9,10,7,7,9,10,9,10,7,7,9,10,7,8,7,8,9,10,7,8,9,10,9,10,7,8,9,8,7,8,7,8,9,10,7,8,9,10,7,8,7,8,7,8,7,8,7,8,7,8,7,8,7,7,7,7,7,7,7,7,7,9,10,10,9,10,9,10,9,10,9,10,9,10,9,10,9,10,9,9,10,10,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,11,12,13,14,15,16,11,12,13,14,15,15,11,12,13,14,15,16,11,12,11,14,15,16,11,12,11,14,15,16,11,12,13,14,14,16,11,15,14,16,11,14,12,,14,12,,14,12,15,11,16,,14,12,12,15,14,12,,11,12,,16,12,13,14,,16,,11,12,16,14,12,11,14,12,,14,12,14,,12,14,,12,14,,13,,,,13,,,12,14,,12,14,,12,14,,,,,,,,,12,14,,12,14,,12,14,12,,,,12,,,,,,14,,,,14,,,,,,,,,,12,,,,12,,,,,,,,,,,14");
//        $jumlah_ruang = explode(',', "11,1,1,20,1,1,1");
//        $ruangan = explode(',', "A1-01,A1-02,A1-03,A1-04,A1-05,A1-06,A1-07,A2-06,A2-08,A2-09,A2-10,A2-11,A3-01,A3-02,A3-03,A3-04,A3-06,A3-07,A3-09,A3-10,B1-01,B1-02,B1-03,B1-04,B1-05,B1-06,B1-07,B1-08,B1-09,B1-10,B1-11,B1-12,C1-01,C1-02,C1-03,D2-01");
//        $model = explode(',', "1,1,1,1,1,1,1,1,2,1,1,1,3,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,5,6,7");

        $jk = $this->input->post('jk');
        $denah = explode(',', $this->input->post('denah'));
        $jumlah_ruang = explode(',', $this->input->post('jumlah_ruang'));
        $ruangan = explode(',', $this->input->post('ruangan'));
        $model = explode(',', $this->input->post('model'));
        $msg = 'Berhasil menyimpan denah';

        $temp_jumlah_ruang = 0;
        foreach ($jumlah_ruang as $jumlah_siswa) {
            $temp_jumlah_ruang += $jumlah_siswa;
        }

        if ($temp_jumlah_ruang != count($model))
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'ERROR 903: Jumlah ruang dengan jumlah model tidak sama'));

        $data_save = array(
            $jk => array(
                'denah' => $denah,
                'jumlah_ruang' => $jumlah_ruang,
                'ruangan' => $ruangan,
                'model' => $model,
            )
        );
        if ($this->aturan_denah->is_us_dibuat()) {
            $data_denah_lama = json_decode($this->aturan_denah->get_aturan_cawu(), true);
            $status = $this->aturan_denah->update_us_active(array('ATURAN_RUANG_PUD' => json_encode($data_denah_lama)));
        } else {
            $status = $this->aturan_denah->save_us_active(array('ATURAN_RUANG_PUD' => json_encode($data_save)));
        }

//        if(!$status)
//            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'ERROR 901: Gagal menyimpan data logging'));

        $data = array();
        $data['JUMLAH_PERBARIS'] = 8;
        $data['JUMLAH_PERUANG'] = $data_form['JUMLAH_KURSI'];
        foreach ($data_form['RUANG'][$jk] as $value) {
            $data['JUMLAH_KAPASITAS_PERUANG'][] = $value['KAPASITAS_UJIAN_RUANG'];
        }
        $temp_jumlah_siswa_form = 0;
        foreach ($data_form['TINGKAT'] as $index => $value) {
            $data['NAMA_JENJANG'][] = $value['DEPT_TINGK'];
            $data['NAMA_DEPT'][] = $value['DEPT_TINGK'];
            $data['TINGKAT'][] = $value['NAMA_TINGK'];
            $data['JUMLAH'][] = $data_form['JUMLAH_SISWA'][$jk][$index]['JUMLAH_SISWA'];
            $temp_jumlah_siswa_form += $data_form['JUMLAH_SISWA'][$jk][$index]['JUMLAH_SISWA'];
        }

        // MENATA MODEL DENAH
        $denah_model = array();
        $ruangan_ke = 0;
        $model_ke = 0;
        $start = true;
        foreach ($denah as $key => $value) {
            if ((($key % $data_form['JUMLAH_KURSI']) == 0) && !$start) {
                $ruangan_ke++;
                $model_ke++;
            }

            if ($value != '') {
                $denah_model[$ruangan_ke][$key - ($ruangan_ke * $data_form['JUMLAH_KURSI'])] = $value - 1;
            }

            $start = false;
        }

        $temp_ruang = $data_form['RUANG'][$jk];
        $data['RUANG'] = array();
        $data['DATA'] = array();
        foreach ($jumlah_ruang as $model_ke => $jumlah) {
            for ($i = 0; $i < $jumlah; $i++) {
                foreach ($model as $index => $id_model_ke) {
                    if ($id_model_ke == ($model_ke + 1)) {
                        $kode_ruang = $ruangan[$index];
                        foreach ($temp_ruang as $index_ruang => $item) {
                            if (in_array($kode_ruang, $item)) {
                                $data['RUANG'][$index] = $item;
                                $data['DATA'][$index] = $denah_model[$id_model_ke - 1];
                                $data['SISA'][$index] = array_fill(0, 16, 0);
                                $data['JUMLAH_SISA'][$index] = 0;
                                unset($temp_ruang[$index_ruang]);
                            }
                        }
                    }
                }
            }
        }

        ksort($data['RUANG']);
        ksort($data['DATA']);

        $data['RUANG'] = array_merge($data['RUANG'], $temp_ruang);
        $data['JUMLAH_SISA_SISWA_PERTINGKAT'] = array_fill(0, 16, 0);

        $temp_jumlah_siswa = 0;
        foreach ($data['DATA'] as $value) {
            $data['JUMLAH_PESERTA_PERRUANG'][] = count($value);
            $temp_jumlah_siswa += count($value);
        }

        foreach ($data['RUANG'] as $ruangan_ke => $detail) {
            $data['ATURAN_DENAH'][$ruangan_ke] = array_fill(0, 16, 0);
            if (isset($data['DATA'][$ruangan_ke])) {
                foreach ($data['DATA'][$ruangan_ke] as $index_tingkat) {
                    $data['ATURAN_DENAH'][$ruangan_ke][$index_tingkat] ++;
                }
            }
        }


//        echo '<hr>';
//        echo '$jumlah_ruang<br>';
//        echo json_encode($jumlah_ruang);
//        echo '<br>' . count($jumlah_ruang);
//        echo '<hr>';
//        echo '$ruangan<br>';
//        echo json_encode($ruangan);
//        echo '<br>' . count($ruangan);
//        echo '<hr>';
//        echo '$model<br>';
//        echo json_encode($model);
//        echo '<br>' . count($model);
//        echo '<hr>';
//        echo "RUANG<br>";
//        echo json_encode($data['RUANG']);
//        var_dump($data['RUANG']);
//        echo '<br>' . count($data['RUANG']);
//        echo '<hr>';
//        echo '$temp_ruang<br>';
//        echo json_encode($temp_ruang);
//        echo '<br>' . count($temp_ruang);
//        echo '<hr>';
//        echo '$denah_model<br>';
//        echo json_encode($denah_model);
//        echo '<br>' . count($denah_model);
//        echo '<hr>';
//        echo 'DATA<br>';
//        echo json_encode($data['DATA']);
//        echo '<br>' . count($data['DATA']);
//        echo '<hr>';
//        exit();
//        echo $temp_jumlah_siswa_form;
//        echo '<hr>';
//        echo $temp_jumlah_siswa;
//        echo '<hr>';
        if ($temp_jumlah_siswa != $temp_jumlah_siswa_form) {
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'ERROR 902: Data siswa form dengan database berbeda'));
        }

        $status = $this->aturan_denah->update_us_active(array('DATA_DENAH' => json_encode(array($jk => $data))));

//        foreach ($data as $key1 => $value1) {
//            var_dump($key1);
//            echo '<hr>';
//            var_dump($value1);
//            echo '<hr>';
//        }

        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah ' . ($status ? 'berhasil' : 'gagal') . ' disimpan'));
    }

}
