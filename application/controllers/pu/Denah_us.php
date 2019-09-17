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
            'jenjang_sekolah_model' => 'jenjang_sekolah',
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

//    public function chackPengaturanUjianTingkat() {
//        $data = $this->db_handler->get_rows('md_tingkat');
//        foreach ($data as $detail) {
//            echo var_dump(json_decode($detail->GEDUNG_UJIAN_TINGK, true));
//            echo '<hr>';
//        }
//    }

    public function show_denah() {
        $this->buat_denah();
    }

    public function validasi_denah() {
        $this->generate->set_header_JSON();

        if ($this->status_validasi)
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));

        $result = 1; //$this->denah_handler->validasi_denah($this->mode);

        if ($this->aturan_denah->validasi_denah_us() && $result) {
            $status = 1;
            $msg = 'Berhasil memvalidasi denah. Anda akan diarahkan pada menu jadwal ujian sekolah';
            $link = site_url('pu/jadwal_us');
        } else {
            $status = 0;
            $msg = 'Denah gagal divalidasi';
            $link = '';
        }

        $this->generate->output_JSON(array('status' => $status, 'msg' => $msg, 'link' => $link));
    }

    public function cek_denah() {
        $this->generate->set_header_JSON();

        if ($this->status_validasi)
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'Denah telah divalidasi.'));

        $status = $this->aturan_denah->is_us_dibuat();

        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah telah dibuat. Anda tidak boleh membuat denah baru pada tahun ajaran aktif.'));
    }

    public function get_data_denah() {
        if ($this->aturan_denah->is_us_dibuat())
            $denah = json_decode($this->aturan_denah->get_denah_cawu(), true);
        else
            $denah = NULL;

        $data['JUMLAH_KURSI'] = null;
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
            'L' => (!isset($denah['L']) ? $this->ruang->get_ruang_ujian('L', $data['JUMLAH_KURSI']) : $denah['L']['RUANG']),
            'P' => (!isset($denah['P']) ? $this->ruang->get_ruang_ujian('P', $data['JUMLAH_KURSI']) : $denah['P']['RUANG']),
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
                if (isset($denah[$jk])) {
                    foreach ($detail['JUMLAH'] as $index => $jumlah) {
                        $data['JUMLAH_SISWA'][$jk][$index]['JUMLAH_SISWA'] = $jumlah;
                    }
                }
            }
        }

        return $data;
    }

    public function buat_denah() {
        $data = $this->get_data_denah();
        $data['VALIDASI'] = $this->status_validasi;
        $data['MODE'] = $this->mode;
        $data['TITLE'] = $this->title;

        $data_plan = $this->aturan_denah->get_denah_plan();
        if ($data_plan != NULL) {
            $aturan_denah = json_decode($data_plan, true);
            foreach ($aturan_denah as $jk => $detail) {
                $count_model = 0;
                $count_kursi = 1;
                $denah = array();
                foreach ($detail['denah'] as $key => $kursi) {
                    if (($key % 40) == 0) {
                        $count_model++;
                        $count_kursi = 1;
                    }
                    $denah[$count_model][$count_kursi] = $kursi;
                    $count_kursi++;
                }
                $aturan_denah[$jk]['denah'] = array_values($denah);
            }
        }

        $data['MODEL'] = isset($aturan_denah) ? $aturan_denah : array();

        $this->generate->backend_view('pu/denah_us_manual/form', $data);
    }

//    public function check_data() {
////        $this->generate->set_header_JSON();
//
//        $data = $this->aturan_denah->get_by_id(11);
//        $denah = json_decode($data->DATA_DENAH, TRUE);
//
////        echo json_encode($denah['L']['DATA'][7]);
////        echo '<hr>';
////        exit();
////
////        foreach ($denah['L']['DATA'] as $key => $value) {
////            echo json_encode($value);
////            echo '<hr>';
////        }
//
//        foreach ($denah as $key => $value) {
//            foreach ($value as $key1 => $value1) {
////                foreach ($value1 as $key2 => $value2) {
//                echo json_encode($key1);
//                echo '<br>';
//                echo json_encode($value1);
//                echo '<hr>';
////                }
//            }
//            break;
//        }
//
////        $this->generate->output_JSON(array('status' => $status, 'msg' => 'Denah telah dibuat. Anda tidak boleh membuat denah baru pada tahun ajaran aktif.'));
//    }

    public function simpan_denah() {
//        $this->generate->set_header_JSON();

        if ($this->status_validasi)
            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'ERROR 912: Denah telah divalidasi'));

        $data_form = $this->get_data_denah();

        $jk = $this->input->post('jk');
        $denah = explode(',', $this->input->post('denah'));
        $jumlah_ruang = explode(',', $this->input->post('jumlah_ruang'));
        $ruangan = explode(',', $this->input->post('ruangan'));
        $model = explode(',', $this->input->post('model'));
        $msg = 'Berhasil menyimpan denah';

//        $jk = "P";
//        $denah = explode(',', "3,5,4,6,4,3,6,5,4,6,3,5,6,5,4,3,3,5,,6,,3,6,,6,,3,,6,,,,,,,,,,,,4,6,3,4,3,4,,6,3,,5,6,5,6,3,,,6,3,,3,,,6,3,,,,,,,,,,,,,,,,14,15,16,7,14,15,8,16,16,8,14,15,8,16,14,15,14,15,16,7,14,15,7,16,16,8,14,15,8,16,14,15,14,15,16,7,14,15,7,16,16,15,14,16,16,7,14,15,14,,16,15,14,15,16,,16,15,14,,16,,14,,14,,16,8,,,16,,16,,,,16,,,,16,15,14,16,16,15,14,15,14,,16,15,14,8,16,,16,,14,,16,,14,,14,,16,,,,16,,16,,,,16,,,,16,,16,,16,,16,,,16,,16,,16,,16,16,,16,,16,,16,,,16,,,,,,,,,,,,,,,12,11,13,9,12,13,9,11,13,10,12,11,10,11,12,10,12,11,13,10,12,13,9,11,13,9,12,11,10,11,12,10,12,11,10,9,11,13,9,11,13,11,10,12,13,11,10,12,10,12,13,11,10,12,13,11,13,11,10,12,13,11,10,9,10,,13,11,10,,13,11,13,11,10,,13,,10,,10,12,13,11,10,11,12,13,13,11,10,13,12,13,10,11,10,12,13,11,10,11,12,13,13,11,10,9,,13,10,11,10,,13,,10,,,13,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,,,,,,,,,,,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,,,,,,,,,,,,,,,,,,,,,,,,,,2,2,2,2,2,2,2,2,2,2,2,2,2,2,,,,,,,,,,,,,,,,,,,,,,,,,,");
//        $jumlah_ruang = explode(',', "4,1,31,1,1,1,24,1,1,1,1,1");
//        $ruangan = explode(',', "D2-01-Pagi,D2-01-Sore,D2-02-Pagi,D2-02-Sore,D2-03-Pagi,D2-03-Sore,D2-04-Pagi,D2-04-Sore,D2-05-Pagi,D2-05-Sore,D2-06-Pagi,D2-06-Sore,D2-07-Pagi,D2-07-Sore,D2-08-Pagi,D2-08-Sore,D2-09-Pagi,D2-09-Sore,D2-10-Pagi,D2-10-Sore,D2-11-Pagi,D2-11-Sore,D2-12-Pagi,D2-12-Sore,D2-13-Pagi,D2-13-Sore,D3-01-Pagi,D3-01-Sore,D3-02-Pagi,D3-02-Sore,D3-03-Pagi,D3-03-Sore,D3-04-Pagi,D3-04-Sore,D3-05-Pagi,D3-05-Sore,D3-06-Pagi,D3-06-Sore,D3-07-Pagi,D3-07-Sore,D3-08-Pagi,D3-08-Sore,D3-09-Pagi,D3-09-Sore,D3-10-Pagi,D3-10-Sore,D3-11-Pagi,D3-11-Sore,D3-12-Pagi,D3-12-Sore,D3-13-Pagi,D3-13-Sore,D3-14-Pagi,D3-15-Pagi,D3-16-Pagi,D4-01-Pagi,D4-02-Pagi,D4-03-Pagi,D4-04-Pagi,D4-05-Pagi,D4-06-Pagi,D4-07-Pagi,D4-08-Pagi,D4-09-Pagi,D4-10-Pagi,D4-11-Pagi,D4-12-Pagi,D4-13-Pagi");
//        $model = explode(',', "1,1,1,1,2,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,4,5,6,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,7,8,9,10,11,12");

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
            $data_denah_lama = json_decode($this->aturan_denah->get_denah_plan(), true);
            $data_denah_lama[$jk] = $data_save[$jk];
            $status = $this->aturan_denah->update_us_active(array('DENAH_PLAN_DENAH' => json_encode($data_denah_lama)));
        } else {
            $status = $this->aturan_denah->save_us_active(array('DENAH_PLAN_DENAH' => json_encode($data_save)));
        }

//        if (!$status)
//            $this->generate->output_JSON(array('status' => FALSE, 'msg' => 'ERROR 901: Gagal menyimpan data logging'));

        $denah_db_cawu = $this->aturan_denah->get_denah_cawu();

        if ($denah_db_cawu == NULL)
            $denah_db = array();
        else
            $denah_db = json_decode($this->aturan_denah->get_denah_cawu(), true);

        $data = array();
        $data['JUMLAH_PERBARIS'] = 8;
        $data['JUMLAH_PERUANG'] = $data_form['JUMLAH_KURSI'] == NULL ? 40 : $data_form['JUMLAH_KURSI'];
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
//        echo json_encode($data);
//        exit();
        // MENATA MODEL DENAH
        $denah_model = array();
        $ruangan_ke = 0;
        $model_ke = 0;
        $start = true;
        $temp_tingkat = array_fill(0, count($data['TINGKAT']), 0);
        $change_id_tingkat = array(
            1 => 0,
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 4,
            6 => 5,
            11 => 6,
            12 => 7,
            13 => 8,
            14 => 9,
            15 => 10,
            16 => 11,
            7 => 12,
            8 => 13,
            9 => 14,
            10 => 15,
        );
        foreach ($denah as $key => $value) {
            if ((($key % ($data_form['JUMLAH_KURSI'] == NULL ? 40 : $data_form['JUMLAH_KURSI'])) == 0) && !$start) {
//                echo '<hr>' . json_encode($denah_model[$ruangan_ke]);
                $ruangan_ke++;
                $model_ke++;
            }

            if ($value != '') {
//                echo '<br>' . $ruangan_ke . ' >>> ' . $key . ' >>> ' . $ruangan_ke . ' >>> ' . ($data_form['JUMLAH_KURSI'] == NULL ? 40 : $data_form['JUMLAH_KURSI']) . ' >>> ' . ($key - ($ruangan_ke * ($data_form['JUMLAH_KURSI'] == NULL ? 40 : $data_form['JUMLAH_KURSI'])));
                $denah_model[$ruangan_ke][$key - ($ruangan_ke * ($data_form['JUMLAH_KURSI'] == NULL ? 40 : $data_form['JUMLAH_KURSI']))] = $change_id_tingkat[intval($value)];
            }

            $start = false;
        }


        $temp_ruang = $data_form['RUANG'][$jk];
//        echo '<hr>' . json_encode($temp_ruang);
        $data['RUANG'] = array();
        $data['DATA'] = array();
        foreach ($jumlah_ruang as $model_ke => $jumlah) {
            for ($i = 0; $i < $jumlah; $i++) {
                foreach ($model as $index => $id_model_ke) {
                    if ($id_model_ke == ($model_ke + 1)) {
                        $kode_ruang = $ruangan[$index];
                        foreach ($temp_ruang as $index_ruang => $item) {
                            if (in_array($kode_ruang, $item) && isset($denah_model[$id_model_ke - 1])) {
                                $data['RUANG'][$index] = $item;
                                $data['DATA'][$index] = $denah_model[$id_model_ke - 1];
                                $data['SISA'][$index] = array_fill(0, 16, 0);
                                $data['JUMLAH_SISA'][$index] = 0;
                                unset($temp_ruang[$index_ruang]);

//                                foreach ($data['DATA'][$index] as $kursi => $tingkat) {
//                                    $temp_tingkat[$tingkat]++;
//                                }
                            }
                        }
                    }
                }
            }
        }

//        echo '<hr>' . json_encode($data['RUANG']);
//        echo '<hr>' . json_encode($temp_tingkat);
//        echo '<hr>' . json_encode($denah_model);
//        echo '<hr>' . count($denah_model);
//        exit();
        ksort($data['RUANG']);
        ksort($data['DATA']);
//        echo '<hr>' . json_encode($data['RUANG']);

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

        $denah_db[$jk] = $data;

//echo '<hr>$denah_db<br>' . json_encode($denah_db);
//exit();
        $status = $this->aturan_denah->update_us_active(array('DATA_DENAH' => json_encode($denah_db)));

        $this->update_aturan_denah($jk);

//        foreach ($data as $key1 => $value1) {
//            var_dump($key1);
//            echo '<hr>';
//            var_dump($value1);
//            echo '<hr>';
//        }

        $this->generate->output_JSON(array('status' => true, 'msg' => 'Denah berhasil disimpan'));
    }

    public function update_aturan_denah($jk) {
        $data = array();

        $jenjang_sekolah = $this->jenjang_sekolah->relasi_jenjang_departemen_tingkat();
        $data_peserta_obj = $this->peserta_us->get_all_denah($jk);
        $data_peserta_json = json_encode($data_peserta_obj);
        $data_peserta = json_decode($data_peserta_json, TRUE);

        foreach ($jenjang_sekolah as $detail) {
            $data['JENJANG'][] = $detail->ID_JS;
            $data['NAMA_JENJANG'][] = $detail->ID_JS;
            $data['DATA'][] = $data_peserta['DATA'][$detail->ID_JS][$detail->NAMA_TINGK];
            $data['JUMLAH'][] = $data_peserta['COUNT'][$detail->ID_JS][$detail->NAMA_TINGK];
        }

        $data_plan = $this->aturan_denah->get_aturan_us();
        if ($data_plan != NULL)
            $data_db = json_decode($data_plan, true);

        $data_db[$jk] = $data;

        $status = $this->aturan_denah->update_us_active(array('ATURAN_RUANG_PUD' => json_encode($data_db)));
    }

    public function keep_up_session() {
        $this->generate->set_header_JSON();

        $this->generate->output_JSON(array('status' => true, 'msg' => $this->input->post('check')));
    }

}
