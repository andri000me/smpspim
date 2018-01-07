<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Denah_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'ruang_model' => 'ruang',
            'siswa_model' => 'siswa',
            'peserta_um_model' => 'peserta_um',
            'peserta_us_model' => 'peserta_us',
            'psb_validasi_model' => 'psb_validasi',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'aturan_denah_model' => 'aturan_denah',
            'tingkat_model' => 'tingkat',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'akad_siswa_model' => 'akad_siswa'
        ));
    }

    // ================================================= URUTAN: 1 ======================================================================
    public function proses_aturan($status, $mode) {
        if ($status) {
            $jumlah_perruang = $this->CI->input->post('jumlah_perruang');

            if (($jumlah_perruang == 0) || ($jumlah_perruang == "")) {
                $data = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_aturan_um(), true) : json_decode($this->CI->aturan_denah->get_aturan_us(), true);

                $result = true;
                $msg = 'Aturan denah lama berhasil dibuka. Untuk membuat aturan baru, silahkan buat aturan baru.';
            } else {
                $this->CI->pengaturan->setJumlahSiswaPerruang($jumlah_perruang);

                $data = $this->generate_aturan($mode);

                $data_denah = array(
                    'ATURAN_RUANG_PUD' => json_encode($data)
                );
                $where_denah = array(
                    'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                    'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE')
                );
                $result = $this->CI->aturan_denah->update($where_denah, $data_denah);
                $msg = 'Aturan denah berhasil dibuat';
            }
        } else {
            $data = $this->generate_aturan($mode);

            $data_denah = array(
                'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE'),
                'ATURAN_RUANG_PUD' => json_encode($data)
            );
            $result = $this->CI->aturan_denah->save($data_denah);

            $msg = 'Aturan denah berhasil dibuat.';
        }

        if ($result) {
            $data_lk = $data['L'];
            $data_pr = $data['P'];

            $output = array(
                'status' => true,
                'msg' => $msg,
                'data' => array(
                    'lk' => array(
                        'data_aturan' => $data_lk['ATURAN_DENAH'],
                        'jenjang' => $data_lk['NAMA_JENJANG'],
                        'tingkat' => $data_lk['TINGKAT'],
                        'ruang' => $data_lk['RUANG'],
                        'jumlah' => $data_lk['JUMLAH'],
                        'jumlah_sisa' => $data_lk['JUMLAH_SISA_SISWA_PERRUANG'],
                        'jumlah_peserta_ruang' => isset($data_lk['JUMLAH_PESERTA_PERRUANG']) ? $data_lk['JUMLAH_PESERTA_PERRUANG'] : null
                    ),
                    'pr' => array(
                        'data_aturan' => $data_pr['ATURAN_DENAH'],
                        'jenjang' => $data_pr['NAMA_JENJANG'],
                        'tingkat' => $data_pr['TINGKAT'],
                        'ruang' => $data_pr['RUANG'],
                        'jumlah' => $data_pr['JUMLAH'],
                        'jumlah_sisa' => $data_pr['JUMLAH_SISA_SISWA_PERRUANG'],
                        'jumlah_peserta_ruang' => isset($data_pr['JUMLAH_PESERTA_PERRUANG']) ? $data_pr['JUMLAH_PESERTA_PERRUANG'] : null
                    )
                )
            );
        } else {
            $output = array(
                'status' => false,
                'msg' => 'Denah gagal disimpan. Silahkan muat ulang halaman ini.'
            );
        }

        return $output;
    }

    // ================================================= URUTAN: 2 ======================================================================
    private function generate_aturan($mode) {
        $data = array(
            'L' => $this->generate_denah($mode, 'L'),
            'P' => $this->generate_denah($mode, 'P'),
        );

        return $data;
    }

    // ================================================= URUTAN: 3 ======================================================================
    private function generate_denah($mode, $jk) {
        // MENGAMBIL DATA PESERTA
        if ($mode == 'UM') {
            $peserta = $this->CI->peserta_um->get_all_denah($jk);
        } elseif ($mode == 'US') {
            $peserta = $this->CI->peserta_us->get_all_denah($jk);
        }

        $data = $this->format_satu_baris($peserta, $jk);

        // MENGAMBIL DATA RUANG
        $data['RUANG'] = $this->CI->ruang->get_ruang_ujian($jk);
        $jumlah_ruang_tersedia = count($data['RUANG']);

        // MENGAMBIL DATA KODE RUANG
        $data = $this->data_kode_ruang($data);

        // MENGAMBIL DATA ATURAN DARI PENGATURAN
        $data['JUMLAH_PERBARIS'] = $this->CI->pengaturan->getJumlahSiswaPerbaris();
        $data['JUMLAH_PERRUANG'] = $this->CI->pengaturan->getJumlahSiswaPerruang();

        // PROSES ATURAN DENAH OTOMATIS
        // MENDAPATKAN JUMLAH PESERTA
        $data = $this->jumlah_peserta($data);

        // MENDAPATKAN JUMLAH RUANG YANG DIBUTUHKAN
        $data = $this->jumlah_ruang_dibutuhkan($data);

        // MEMBAGI PESERTA YANG TIDAK DIACAK
        $data = $this->distribusi_peserta_non_acak($mode, $data);

        // MENATARA GEDUNG UJIAN
        $data = $this->gedung_ujian_peserta($data);

        if ($data['JUMLAH_RUANG_DIGUNAKAN'] > $jumlah_ruang_tersedia) {
            $this->CI->generate->output_JSON(array(
                'status' => false,
                'msg' => 'Ruang yang tersedia tidak mencukupi terhadap ruang yang diminta. Jumlah ruang yang diminta sebanyak ' . $data['JUMLAH_RUANG_DIGUNAKAN'] . ' dengan kapasitas ' . $data['JUMLAH_PERRUANG'] . ' orang. Sedangkan ruang yang tersedia sebanyak ' . $jumlah_ruang_tersedia . ' buah. Silahkan memperbesar kapasitas ruang atau menambah ruang.'
            ));
        }

        // MEMBAGI JUMLAH PESERTA KESETIAP JENJANG
        $data = $this->distribusi_peserta_keruang($mode, $data);

        // MEMBUAT SIMULASI JUMLAH SISWA SETIAP RUANG
        $data = $this->jumlah_peserta_setiap_ruang($mode, $data);

        // JIKA SISA SISWA PERTINGKAT MELEBIHI KAPASITAS RUANG
        if ($mode == 'UM') {
            if ($data['JUMLAH_SISA_SISWA_PERRUANG'] > $data['JUMLAH_PERRUANG']) {
                $iterasi = 0;
                while ($data['JUMLAH_SISA_SISWA_PERRUANG'] > $data['JUMLAH_PERRUANG']) {
                    $data = $this->distribusi_sisa_keruangan($mode, $data);
                    if ($iterasi > 10) {
                        break;
                    }
                    $iterasi++;
                }
            }
        } elseif ($mode == 'US') {
            $data = $this->distribusi_sisa_keruangan($mode, $data);
        }

        return $data;
    }

    // MENGAMBIL DATA KODE RUANG
    private function data_kode_ruang($data) {
        $i = 0;
        $temp = null;
        foreach ($data['RUANG'] as $detail) {
            $gedung = $this->get_kode_gedung($detail['KODE_RUANG']);
            if ($temp != $gedung) {
                $data['GEDUNG'][] = $gedung;
                $data['RUANG_GEDUNG'][$gedung] = $i;

                $temp = $gedung;
            }
            $data['KODE_RUANG'][] = $detail['KODE_RUANG'];
            $data['RUANG_KELAS'][$detail['ID_KELAS']] = $detail['KODE_RUANG'];
            $data['DATA_RUANG'][$detail['KODE_RUANG']] = $detail;
            $i++;
        }

//        echo '<hr>DATA<br>' . json_encode($data['DATA']);
//        echo '<hr>KELAS_SISWA<br>' . json_encode($data['KELAS_SISWA']);
//        echo '<hr>KODE_RUANG<br>' . json_encode($data['KODE_RUANG']);
//        echo '<hr>RUANG_KELAS<br>' . json_encode($data['RUANG_KELAS']);
//        exit();

        return $data;
    }

    // MENGAMBIL GEDUNG
    private function get_kode_gedung($kode_ruang) {
        if (is_array($kode_ruang)) {
            $temp = null;
        } else {
            $temp = substr($kode_ruang, 0, 1);
        }

        return $temp;
    }

    // ================================================= URUTAN: 4 ======================================================================
    // MERUBAH FORMAT ARRAY DATABASE KE FORMAT ARRAY 1 LEVEL
    private function format_satu_baris($peserta, $jk) {
        foreach ($peserta as $type => $detail1) {
            foreach ($detail1 as $jenjang => $detail2) {
                foreach ($detail2 as $tingkat => $detail3) {
                    if ($type == 'DATA') {
                        $data_gedung = $this->CI->jenjang_sekolah->get_gedung_dept($jenjang, $tingkat, $jk);
                        $data['JENJANG'][] = $jenjang;
                        $data['NAMA_JENJANG'][] = $this->CI->jenjang_sekolah->get_nama_jenjang($jenjang);
                        $data['NAMA_DEPT'][] = $this->CI->jenjang_sekolah->get_nama_dept($jenjang);
                        $data['GEDUNG_DEPT'][] = $data_gedung['DATA'];
                        $data['STATUS_ACAK'][] = $data_gedung['STATUS'];
                        $data['TINGKAT'][] = $tingkat;
                        $data['DATA'][] = $detail3;
                    } else {
                        $data['JUMLAH'][] = $detail3;
                    }
                }
            }
        }

        foreach ($data['DATA'] as $index => $detail) {
            foreach ($detail as $key => $item) {
                $data['RUANG_SISWA'][$index][$key]['RUANG_KELAS'] = $item->RUANG_KELAS;
                unset($data['DATA'][$index][$key]->RUANG_KELAS);
            }
        }

        return $data;
    }

    // ================================================= URUTAN: 5 ======================================================================
    // MENDAPATKAN JUMLAH TOTAL SEMUA PESERTA
    private function jumlah_peserta($data) {
        $data['JUMLAH_PESERTA'] = 0;
        foreach ($data['JUMLAH'] as $value) {
            $data['JUMLAH_PESERTA'] += abs($value);
        }

        return $data;
    }

    // ================================================= URUTAN: 6 ======================================================================
    // MENDAPATKAN JUMLAH RUANG YANG DIBUTUHKAN, TERMASUK RUANG SISA
    private function jumlah_ruang_dibutuhkan($data) {
        $data['SISA_PESERTA'] = $data['JUMLAH_PESERTA'] % $data['JUMLAH_PERRUANG'];
        $data['JUMLAH_RUANG_DIGUNAKAN'] = ($data['JUMLAH_PESERTA'] - $data['SISA_PESERTA']) / $data['JUMLAH_PERRUANG'];
        $data['SISA_PESERTA'] = 1;
        if ($data['SISA_PESERTA'] > 0) {
            $data['JUMLAH_RUANG_DIGUNAKAN'] ++;
        }

        return $data;
    }

    // MENDAPATKAN JUMLAH PESERTA DALAM 1 RUANG
    private function jumlah_peserta_ruang($array) {
        $result = 0;

        foreach ($array as $value) {
            $result += abs($value);
        }

        return $result;
    }

    private function distribusi_peserta_non_acak($mode, $data) {
        if ($mode == 'US') {
            // MENGESET NILAI AWAL
            foreach ($data['KODE_RUANG'] as $value) {
                $data['ATURAN_DENAH'][] = array_fill(0, count($data['TINGKAT']), 0);
            }

            // MENGESET NILAI AWAL
            $kode_ruang_flip = array_flip($data['KODE_RUANG']);
            $data['STATUS_ACAK_RUANG'] = array_fill(0, count($data['KODE_RUANG']), true);
            foreach ($data['STATUS_ACAK'] as $index_tingkat => $status_acak) {
                if (!$status_acak) {
                    foreach ($data['RUANG_SISWA'][$index_tingkat] as $kode_ruang) {
                        $index_ruang = $kode_ruang_flip[$kode_ruang['RUANG_KELAS']];
                        $data['ATURAN_DENAH'][$index_ruang][$index_tingkat] ++;
                        $data['STATUS_ACAK_RUANG'][$index_ruang] = false;
                    }
                }
            }
//            echo '<hr>STATUS_ACAK<br>' . json_encode($data['STATUS_ACAK']);
//            echo '<hr>STATUS_ACAK_RUANG<br>' . json_encode($data['STATUS_ACAK_RUANG']);
//            echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
//            echo '<hr>RUANG_SISWA<br>' . json_encode($data['RUANG_SISWA']);
//            exit();
        }

        return $data;
    }

    // MEMBAGI SISWA KESETIAP RUANG
    private function distribusi_peserta_keruang($mode, $data) {
        if ($mode == 'UM') {
            $jumlah_ruang_digunakan_hasil_bagi = $data['SISA_PESERTA'] > 0 ? $data['JUMLAH_RUANG_DIGUNAKAN'] - 1 : $data['JUMLAH_RUANG_DIGUNAKAN'];
            // MEMBAGI SISWA PERTINGKAT KESETIAP RUANG
            $data['JUMLAH_SISA_SISWA_PERRUANG'] = 0;
            foreach ($data['JUMLAH'] as $value) {
                $sisa_siswa = $value % $jumlah_ruang_digunakan_hasil_bagi;
                $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'][] = ($value - $sisa_siswa) / $jumlah_ruang_digunakan_hasil_bagi;
                $data['JUMLAH_SISA_SISWA_PERRUANG'] += $sisa_siswa;
                $data['JUMLAH_SISA_SISWA_PERTINGKAT'][] = $sisa_siswa;
            }
            //    echo json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERRUANG']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']).'<hr>';
            // JIKA SISWA PERRUANG MELEBIHI KAPASITAS RUANG UJIAN MAKA SISWA AKAN DIMASUKAN KE RUANGAN SISA
            $i = 0;
            while ($this->jumlah_peserta_ruang($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']) > $data['JUMLAH_PERRUANG']) {
                $temp_jumlah_siswa = $this->urut_sisa_terbesar($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']);
                $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'][$temp_jumlah_siswa['INDEX'][$i]] --;
                $data['JUMLAH_SISA_SISWA_PERRUANG'] += $jumlah_ruang_digunakan_hasil_bagi;
                $data['JUMLAH_SISA_SISWA_PERTINGKAT'][$temp_jumlah_siswa['INDEX'][$i]] += $jumlah_ruang_digunakan_hasil_bagi;

//            echo json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERRUANG']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']).' => '. json_encode($temp_jumlah_siswa).'<hr>';
//            if ($i > count($temp_jumlah_siswa['DATA'])) $i = 0;
//            else $i++;
            }
        } elseif ($mode == 'US') {
            // MEMBAGI SISWA PERTINGKAT KESETIAP RUANG
            $data['JUMLAH_SISA_SISWA_PERRUANG'] = 0;
            foreach ($data['JUMLAH'] as $index => $value) {
                $jumlah_ruang_tersedia = $data['JUMLAH_RUANG_UJIAN_DEPT'][$index];
                $sisa_siswa = $data['STATUS_ACAK'][$index] ? $value % $jumlah_ruang_tersedia : 0;
                $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'][] = $data['STATUS_ACAK'][$index] ? (($value - $sisa_siswa) / $jumlah_ruang_tersedia) : 0;
                $data['JUMLAH_SISA_SISWA_PERRUANG'] += $data['STATUS_ACAK'][$index] ? $sisa_siswa : 0;
                $data['JUMLAH_SISA_SISWA_PERTINGKAT'][] = $data['STATUS_ACAK'][$index] ? $sisa_siswa : 0;
            }
//            echo '<hr>JUMLAH<br>' . json_encode($data['JUMLAH']);
//            echo '<hr>JUMLAH_RUANG_UJIAN_DEPT<br>' . json_encode($data['JUMLAH_RUANG_UJIAN_DEPT']);
//            echo '<hr>JUMLAH_SISWA_PERUANG_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']);
//            echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
//            echo '<hr>JUMLAH_RUANG_UJIAN_DEPT<br>' . json_encode($data['JUMLAH_RUANG_UJIAN_DEPT']);
//            echo '<hr>STATUS_ACAK<br>' . json_encode($data['STATUS_ACAK']);
//            exit();
        }

        return $data;
    }

    // MENDAPATKAN GEDUNG PESERTA UJIAN
    private function gedung_ujian_peserta($data) {
        foreach ($data['GEDUNG_DEPT'] as $key => $gedung) {
            foreach ($gedung as $kode_gedung) {
                $data['GEDUNG_UJIAN_DEPT'][$kode_gedung][] = $data['NAMA_DEPT'][$key] . '_' . $data['TINGKAT'][$key];

                $start = $data['RUANG_GEDUNG'][$kode_gedung];

                if (isset($data['RUANG_GEDUNG'][++$kode_gedung])) {
                    $maks = $data['RUANG_GEDUNG'][$kode_gedung];
                } else {
                    $maks = count($data['KODE_RUANG']);
                }

                for ($index_ruang = $start; $index_ruang < $maks; $index_ruang++) {
                    if ($data['STATUS_ACAK_RUANG'][$index_ruang])
                        $data['RUANG_UJIAN_DEPT'][$data['NAMA_DEPT'][$key] . '_' . $data['TINGKAT'][$key]][] = $index_ruang;
                }
            }

            if (!$data['STATUS_ACAK'][$key]) {
                $data['RUANG_UJIAN_DEPT'][$data['NAMA_DEPT'][$key] . '_' . $data['TINGKAT'][$key]] = array();
            }

            $data['JUMLAH_RUANG_UJIAN_DEPT'][$key] = count($data['RUANG_UJIAN_DEPT'][$data['NAMA_DEPT'][$key] . '_' . $data['TINGKAT'][$key]]);
        }

        ksort($data['GEDUNG_UJIAN_DEPT']);
        foreach ($data['GEDUNG_UJIAN_DEPT'] as $index => $jenjang) {
            $data['GEDUNG_UJIAN_DEPT'][$index] = array_values(array_unique($jenjang));
        }

//        echo '<hr>STATUS_ACAK<br>' . json_encode($data['STATUS_ACAK']);
//        echo '<hr>STATUS_ACAK_RUANG<br>' . json_encode($data['STATUS_ACAK_RUANG']);
//        echo '<hr>JUMLAH_RUANG_UJIAN_DEPT<br>' . json_encode($data['JUMLAH_RUANG_UJIAN_DEPT']);
//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
//        echo '<hr>GEDUNG_DEPT<br>' . json_encode($data['GEDUNG_DEPT']);
//        echo '<hr>GEDUNG_UJIAN_DEPT<br>' . json_encode($data['GEDUNG_UJIAN_DEPT']);
//        exit();

        return $data;
    }

    // MENGHITUNG JUMLAH SISWA SETIAP RUANG
    private function jumlah_peserta_setiap_ruang($mode, $data) {
        if ($mode == 'UM') {
            $jumlah_ruang_digunakan_hasil_bagi = ($data['SISA_PESERTA'] > 0) ? $data['JUMLAH_RUANG_DIGUNAKAN'] - 1 : $data['JUMLAH_RUANG_DIGUNAKAN'];
            // MENGHITUNG JUMLAH SISWA SETIAP RUANG
            for ($i = 0; $i < $jumlah_ruang_digunakan_hasil_bagi; $i++) {
                $data['ATURAN_DENAH'][$i] = $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'];
            }
            // MENGHITUNG JUMLAH SISWA DIRUANGAN SISA
            if ($data['SISA_PESERTA'] > 0) {
                $data['ATURAN_DENAH'][$jumlah_ruang_digunakan_hasil_bagi] = $data['JUMLAH_SISA_SISWA_PERTINGKAT'];
            }
        } elseif ($mode == 'US') {

            // MENGHITUNG JUMLAH SISWA SETIAP RUANG
            $data['RUANG_SISA'] = array_fill(0, count($data['TINGKAT']), 0);
            foreach ($data['JUMLAH_SISWA_PERUANG_PERTINGKAT'] as $index => $jumlah_siswa) {
                if ($jumlah_siswa > 0) {
                    for ($i = 0; $i < $data['JUMLAH_RUANG_UJIAN_DEPT'][$index]; $i++) {
                        $index_ruang_ujian_dept = $data['NAMA_DEPT'][$index] . '_' . $data['TINGKAT'][$index];
                        $index_ruang_ujian = $data['RUANG_UJIAN_DEPT'][$index_ruang_ujian_dept][$data['RUANG_SISA'][$index]];
                        if ($data['STATUS_ACAK_RUANG'][$index_ruang_ujian])
                            $data['ATURAN_DENAH'][$index_ruang_ujian][$index] = $jumlah_siswa;

                        // if ($index == 6) {
                        //     echo $data['RUANG_SISA'][$index].'-'.$data['ATURAN_DENAH'][$index_ruang_ujian][$index].'-'.$jumlah_siswa.'<br>';
                        // }

                        $data['RUANG_SISA'][$index] ++;
                    }
                    // if ($index == 6) {
                    //     break;
                    // }
                }
            }
        }

        $this->cek_jumlah_siswa_aturan($mode, $data);

//        echo '<hr>STATUS_ACAK<br>' . json_encode($data['STATUS_ACAK']);
//        echo '<hr>JUMLAH_SISWA_PERUANG_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']);
//        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
//        echo '<hr>STATUS_ACAK_RUANG<br>' . json_encode($data['STATUS_ACAK_RUANG']);
//        echo '<hr>KODE_RUANG<br>' . json_encode($data['KODE_RUANG']);
//        echo '<hr>$kode_ruang_flip<br>' . json_encode($kode_ruang_flip);
////            echo '<hr>RUANG<br>' . json_encode($data['RUANG']);
////            echo '<hr>DATA<br>' . json_encode($data['DATA']);
//        echo '<hr>RUANG_SISWA<br>' . json_encode($data['RUANG_SISWA']);
//        exit();

        return $data;
    }

    // MENGECEK JUMLAH SISWA HASIL GENERATE
    private function cek_jumlah_siswa_aturan($mode, $data, $debug = false) {
        $temp = array();
        foreach ($data['ATURAN_DENAH'] as $key => $detail) {
            foreach ($detail as $index => $item) {
                if (!isset($temp[$index])) {
                    $temp[$index] = 0;
                }
                $temp[$index] += abs($item);
            }
        }

        if ($mode == 'US') {
            if (isset($data['JUMLAH_SISA_SISWA_PERTINGKAT'])) {
                foreach ($temp as $index => $value) {
                    $temp[$index] = abs($value) + abs($data['JUMLAH_SISA_SISWA_PERTINGKAT'][$index]);
                }
            }
        }

        if ($debug) {
            echo '<hr>$temp<br>' . json_encode($temp);
            echo '<hr>JUMLAH<br>' . json_encode($data['JUMLAH']);
            echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
            echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        }

        foreach ($temp as $index => $detail) {
            if ($data['JUMLAH'][$index] != $detail) {
                $this->CI->generate->output_JSON(array(
                    'status' => false,
                    'msg' => 'Ada kesalahan dalam generate denah otomatis. Silahakan hubungi pembuat program atau modifikasi sistem di file Denah_handler.php pada function cek_jumlah_siswa_aturan. ERROR CODE: ' . $data['NAMA_DEPT'][$index] . '_' . $data['TINGKAT'][$index] . '_X_' . $detail . '_V_' . $data['JUMLAH'][$index]
                ));
            }
        }
//        exit();

        return true;
    }

    private function sisa_peserta_ada($data_sisa) {
        $jumlah_sisa = 0;
        foreach ($data_sisa as $detail) {
            $jumlah_sisa += abs($detail);
            if ($jumlah_sisa > 0) {
                return true;
            }
        }

        return false;
    }

    // MENGURUTKAN JUMLAH SISA SISWA PERTINGKAT DARI TERBESAR
    private function urut_sisa_terbesar($data) {
        $temp_index = array();
        $temp_data = $data;
        $temp_index_key = 0;

        for ($i = 0; $i < count($data); $i++) {
            $temp_index[] = $i;
        }

        foreach ($data as $index => $item) {
            foreach ($temp_data as $key => $value) {
                if ($key == $index) {
                    $nilai_terbesar = $value;
                } elseif ($key > $index) {
                    if ($value > $nilai_terbesar) {
                        $temp_index_key = $temp_index[$key];
                        $temp_index[$key] = $temp_index[$index];
                        $temp_index[$index] = $temp_index_key;

                        $temp_data[$key] = $nilai_terbesar;
                        $temp_data[$index] = $value;

                        $nilai_terbesar = $value;
                        $selisih_perubahan = $key - $index;
                    }
                }
            }
        }

        return array('DATA' => $temp_data, 'INDEX' => $temp_index);
    }

    // MEMBAGIKAN SISA SISWA KEDALAM RUANGAN JIKA RUANGAN PENUH DIMULAI DARI SISA TERBANYAK
    private function distribusi_sisa_keruangan($mode, $data) {
        $temp_i = 0;
        $temp_data = $this->urut_sisa_terbesar($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        $jumlah_ruang_digunakan_hasil_bagi = ($data['SISA_PESERTA'] > 0) ? $data['JUMLAH_RUANG_DIGUNAKAN'] - 1 : $data['JUMLAH_RUANG_DIGUNAKAN'];
        $jumlah_ruangan_tidak_sisa = $jumlah_ruang_digunakan_hasil_bagi - 1;
        $data['ATURAN_DENAH_BARU'] = $data['ATURAN_DENAH'];

        if ($mode == 'UM') {
            foreach ($temp_data['DATA'] as $index => $item) {
                $temp_data_index = $temp_data['INDEX'][$index];
                $temp_data_item = $item;

                // MEMASUKAN SISA SISWA KEDALAM RUANGAN SATU PERSATU SECARA MERATA PERTINGKAT
                for ($i = $temp_i; $i < $jumlah_ruangan_tidak_sisa; $i++) {
                    foreach ($data['ATURAN_DENAH'][$i] as $key => $value) {
                        if ($temp_data_index == $key) {
                            $data['ATURAN_DENAH_BARU'][$i][$key] = $data['ATURAN_DENAH'][$i][$key] + 1;
                            $temp_data_item--;
                        }
                    }

                    if ($temp_data_item == 0) {
                        break;
                    }
                }

                $temp_data['DATA'][$index] = $temp_data_item;
                $data['ATURAN_DENAH_BARU'][$jumlah_ruangan_tidak_sisa][$temp_data_index] = $temp_data_item;

                if ($i == $jumlah_ruangan_tidak_sisa) {
                    $temp_i = 0;
                    $jumlah_sisa_siswa = 0;

                    foreach ($temp_data['DATA'] as $key => $value) {
                        if ($key > $index) {
                            $jumlah_sisa_siswa += $value;
                        }
                    }

                    if ($jumlah_sisa_siswa < $jumlah_ruangan_tidak_sisa) {
                        break;
                    }
                } else {
                    $temp_i = $i + 1;
                }
            }

            $data['JUMLAH_SISA_SISWA_PERTINGKAT'] = $data['ATURAN_DENAH_BARU'][$jumlah_ruangan_tidak_sisa];
            $data['JUMLAH_SISA_SISWA_PERRUANG'] = $this->jumlah_peserta_ruang($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        } elseif ($mode == 'US') {
            foreach ($temp_data['DATA'] as $index => $item) {
                $temp_data_index = $temp_data['INDEX'][$index];
                $temp_data_item = $item;

                // MEMASUKAN SISA SISWA KEDALAM RUANGAN SATU PERSATU SECARA MERATA PERTINGKAT
                foreach ($data['RUANG_UJIAN_DEPT'][$data['NAMA_DEPT'][$temp_data_index] . '_' . $data['TINGKAT'][$temp_data_index]] as $index_ruang) {
                    // CEK JUMLAH PESERTA DIRUANG TSB
                    $jumlah_peserta_ruang = $this->jumlah_peserta_diruang($data['ATURAN_DENAH_BARU'][$index_ruang]);

                    if (($jumlah_peserta_ruang < $data['JUMLAH_PERRUANG']) && ($temp_data_item > 0)) {
                        $data['ATURAN_DENAH_BARU'][$index_ruang][$temp_data_index] ++;
                        $temp_data_item--;
                    }
                }

                $temp_data['DATA'][$index] = $temp_data_item;

                $data['JUMLAH_SISA_SISWA_PERTINGKAT'][$temp_data_index] = $temp_data_item;
            }

//            echo '<hr>ATURAN_DENAH_BARU<br>' . json_encode($data['ATURAN_DENAH_BARU']);
//            echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
//            exit();
            // MERATAKAN JUMLAH PESERTA AGAR SESUAI DENGAN JUMLAH MAKSIMAL PERUANG
            $looping_break = false;
            $i = 0;
            while (!$looping_break) {
                $temp_ruang_penuh = array_fill(0, count($data['ATURAN_DENAH_BARU']), false);
                $data['JUMLAH_PESERTA_PERRUANG'] = array_fill(0, count($data['ATURAN_DENAH_BARU']), 0);
                $j = 0;
                foreach ($data['ATURAN_DENAH_BARU'] as $index_ruang => $data_ruang) {
                    if (!$data['STATUS_ACAK_RUANG'][$index_ruang])
                        continue;

                    $temp_data = $this->urut_sisa_terbesar($data_ruang);

                    foreach ($temp_data['DATA'] as $index => $jumlah) {
                        $temp_data_index = $temp_data['INDEX'][$index];

                        $jumlah_peserta_ruang = $this->jumlah_peserta_diruang($data['ATURAN_DENAH_BARU'][$index_ruang]);
                        $index_ruang_ujian = $data['RUANG_UJIAN_DEPT'][$data['NAMA_DEPT'][$temp_data_index] . '_' . $data['TINGKAT'][$temp_data_index]];
                        $kapasitas = $data['RUANG'][$index_ruang]['KAPASITAS_UJIAN_RUANG'] < $data['JUMLAH_PERRUANG'] ? $data['RUANG'][$index_ruang]['KAPASITAS_UJIAN_RUANG'] : $data['JUMLAH_PERRUANG'];

                        $index_ruang_terakhir = end($index_ruang_ujian);
                        reset($index_ruang_ujian);
                        if (($jumlah_peserta_ruang >= $kapasitas) || ($jumlah_peserta_ruang == 0) || ($index_ruang_ujian == $index_ruang_terakhir)) {
                            $temp_ruang_penuh[$index_ruang] = true;

                            break;
                        } elseif (in_array($index_ruang, $index_ruang_ujian) && ($jumlah_peserta_ruang < $kapasitas)) {
                            if (isset($data['ATURAN_DENAH_BARU'][$index_ruang - 1])) {
                                $jumlah_peserta_ruang_selanjutnya = $this->jumlah_peserta_diruang($data['ATURAN_DENAH_BARU'][$index_ruang_terakhir]);
                                $jumlah_peserta_ruang_sebelumnya = $this->jumlah_peserta_diruang($data['ATURAN_DENAH_BARU'][$index_ruang - 1]);
                                if (($jumlah_peserta_ruang_selanjutnya == 0) && ($jumlah_peserta_ruang_sebelumnya >= $kapasitas)) {
                                    $temp_ruang_penuh[$index_ruang] = true;
                                }
                            }

                            while ($index_ruang_terakhir > $index_ruang) {
                                if ($data['ATURAN_DENAH_BARU'][$index_ruang_terakhir][$temp_data_index] > 0) {
                                    $data['ATURAN_DENAH_BARU'][$index_ruang][$temp_data_index] ++;
                                    $data['ATURAN_DENAH_BARU'][$index_ruang_terakhir][$temp_data_index] --;

                                    break;
                                }

                                $index_ruang_terakhir--;
                            }
                        }
                    }

                    foreach ($temp_ruang_penuh as $index => $status) {
                        $data['JUMLAH_PESERTA_PERRUANG'][$index] = $this->jumlah_peserta_diruang($data['ATURAN_DENAH_BARU'][$index]);
                    }

//                     echo '<hr>index<br>' . $j;
//                     echo '<hr>ATURAN_DENAH_BARU<br>' . json_encode($data['ATURAN_DENAH_BARU']);
//                     echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($data['JUMLAH_PESERTA_PERRUANG']);
                }

                $looping_break = true;
                foreach ($temp_ruang_penuh as $index => $status) {
                    $looping_break *= $status;
                }

                $i++;

                if ($i == 50) {
                    break;
                }
            }

            $data['JUMLAH_SISA_SISWA_PERRUANG'] = $this->jumlah_peserta_ruang($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        }

        $data['ATURAN_DENAH'] = $data['ATURAN_DENAH_BARU'];
        unset($data['ATURAN_DENAH_BARU']);

        $this->cek_jumlah_siswa_aturan($mode, $data);


//        echo '<hr>NAMA_DEPT<br>' . json_encode($data['NAMA_DEPT']);
//        echo '<hr>TINGKAT<br>' . json_encode($data['TINGKAT']);
//        echo '<hr>RUANG_GEDUNG<br>' . json_encode($data['RUANG_GEDUNG']);
//        // echo '<hr>GEDUNG<br>'.json_encode($data['GEDUNG']);
//        // echo '<hr>KODE_RUANG<br>'.json_encode($data['KODE_RUANG']);
//        echo '<hr>GEDUNG_UJIAN_DEPT<br>' . json_encode($data['GEDUNG_UJIAN_DEPT']);
//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
//        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
////        echo '<hr>ATURAN_DENAH_BARU<br>' . json_encode($data['ATURAN_DENAH_BARU']);
//        echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($data['JUMLAH_PESERTA_PERRUANG']);
//        echo '<hr>temp_ruang_penuh<br>' . json_encode($temp_ruang_penuh);
//        echo '<hr>JUMLAH<br>' . json_encode($data['JUMLAH']);
//        echo '<hr>CI_DEPT<br>' . json_encode($data['JUMLAH_RUANG_UJIAN_DEPT']);
//        echo '<hr>JUMLAH_SISWA_PERUANG_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']);
//        // echo '<hr>JUMLAH_SISA_SISWA_PERRUANG<br>'.json_encode($data['JUMLAH_SISA_SISWA_PERRUANG']);
//        echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
//        echo '<hr>temp_data<br>' . json_encode($temp_data);
//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
//        // echo '<hr>DATA_RUANG<br>'.json_encode($data['DATA_RUANG']);
//        echo '<hr>RUANG<br>' . json_encode($data['RUANG']);
//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
//        exit();

        return $data;
    }

    // MENDAPATKAN JUMLAH PESERTA UJIAN SETIAP RUANG
    private function jumlah_peserta_diruang($data) {
        $jumlah_peserta_ruang = 0;
        foreach ($data as $peserta_ruang) {
            $jumlah_peserta_ruang += $peserta_ruang;
        }

        return $jumlah_peserta_ruang;
    }

    // MENGECEK INPUTAN ATURAN DENAH MANUAL DENGAN MEMBANDINGKAN JUMLAH
    // DATA INPUTAN PERTINGKAT DENGAN DATA ATURAN DENAH BARU
    public function cek_jumlah_input($data) {
        $temp_jumlah = array_fill(0, count($data['JUMLAH']), 0);
        $status = true;

        // MENAMBAHKAN JUMLAH SETIAP TINGKAT PADA SETIAP RUANG
        foreach ($data['ATURAN_DENAH'] as $key => $value) {
            foreach ($value as $index => $item) {
                $temp_jumlah[$index] += $item;
            }
        }

        foreach ($temp_jumlah as $key => $value) {
            if ($data['JUMLAH'][$key] != $value) {
                $status = false;
            }
        }

//        echo '<hr>=====================================================================================================================================================================';
//        echo '<hr>$temp_jumlah<br>' . json_encode($temp_jumlah);
//        echo '<hr>JUMLAH<br>' . json_encode($data['JUMLAH']);
//        echo '<hr>ATURAN_DENAH_FINAL<br>' . json_encode($data['ATURAN_DENAH_FINAL']);
//        echo '<hr>$data<br>' . json_encode($data);
//
//        exit();

        return $status;
    }

    // MENGURUTKAN JENJANG BERDASARKAN JUMLAH SISWA PADA SETIAP JENJANG TERBANYAK
    private function urut_jenjang_terbesar($jenjang, $data) {
        $temp_jenjang = array();
        $temp_data = array();
        $temp_jumlah = array();
        $result = array(
            'DATA' => array(),
            'INDEX' => array()
        );
        $temp_jenjang_single = null;

        // MENGELOMPOKAN JENJANG
        foreach ($jenjang as $key => $value) {
            if ($temp_jenjang_single != $value) {
                $temp_jenjang[] = $value;
            }
            $temp_data[$value][] = $key;
            $temp_jenjang_single = $value;
        }

        // MEMASUKAN INDEX JENJANG KE JENJANG TERKELOMPOK
        foreach ($temp_data as $key => $value) {
            $total = 0;
            foreach ($value as $item) {
                $total += $data[$item];
            }
            $temp_jumlah[] = $total;
        }

        $jumlah_urut = $this->urut_sisa_terbesar($temp_jumlah);

        // MENATA KEMBALI DATA YANG TELAH URUT
        foreach ($jumlah_urut['DATA'] as $key => $value) {
            $result['INDEX'] = array_merge($result['INDEX'], $temp_data[$temp_jenjang[$jumlah_urut['INDEX'][$key]]]);
        }

        foreach ($result['INDEX'] as $key => $value) {
            $result['DATA'][] = $data[$result['INDEX'][$key]];
        }

        return $result;
    }

    // MENGECEK APAKAH LOKASI YANG AKAN DICEK DALAM SATU JENJANG APA TIDAK
    private function dalam_satu_jenjang($data_jenjang, $index_aturan, $index_cek) {
        $temp_index = array();
        $temp_data = null;
        $finish = false;

        // MENDAPATKAN KELOMPOK JENJANG INDEX LOKASI TERSEBUT
        foreach ($data_jenjang as $key => $value) {
            if ($temp_data != $value) {
                if ($finish) {
                    break;
                } else {
                    unset($temp_index);
                    $temp_index = array();
                }
            }

            if ($key == $index_aturan) {
                $finish = true;
            }

            $temp_index[] = $key;

            $temp_data = $value;
        }

        // MENGECEK LOKASI KURSI
        if (in_array($index_cek, $temp_index)) {
            return true;
        } else {
            return false;
        }
    }

    public function proses_buat_denah($mode, $req_sisa = false) {
        // MENGAMBIL DENAH DARI DATABASE
        $id_aturan_denah = ($mode == 'UM') ? $this->CI->aturan_denah->get_id_um() : $this->CI->aturan_denah->get_id_us();
        $data = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_aturan_um(), true) : json_decode($this->CI->aturan_denah->get_aturan_us(), true);
        $data_sisa = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_denah_psb(), true) : json_decode($this->CI->aturan_denah->get_denah_cawu(), true);

        // MEMISAH DATA LAKILAKI DAN PEREMPUAN
        $data_lk = $data['L'];
        $data_pr = $data['P'];

        if ($req_sisa) {
            // PROSES PENGELOMPOKAN SISWA SISA KE RUANGAN YANG LAIN
            if ($mode == 'UM') {
                $data_lk['ATURAN_DENAH_FINAL'] = $this->susun_data_sisa($mode, $data_sisa['L'], $data_lk);
                $data_pr['ATURAN_DENAH_FINAL'] = $this->susun_data_sisa($mode, $data_sisa['P'], $data_pr);
            } elseif ($mode == 'US') {
                $data_lk = array_merge($data_lk, $this->susun_data_sisa($mode, $data_sisa['L'], $data_lk, "L"));
                $data_pr = array_merge($data_pr, $this->susun_data_sisa($mode, $data_sisa['P'], $data_pr, "P"));
            }
        } else {
            // MENGAMBIL DATA DARI PERUBAHAN PENGGUNA
            $data_lk['ATURAN_DENAH_FINAL'] = $this->CI->input->post("aturan_lk");
            $data_pr['ATURAN_DENAH_FINAL'] = $this->CI->input->post("aturan_pr");
        }
//exit();
        // CEK APAKAH JUMLAH MASING-MASING TINGKAT TELAH COCOK
        if ((($mode == 'UM') && ($this->cek_jumlah_input($data_lk) && $this->cek_jumlah_input($data_pr))) || ($mode == "US")) {
            $data['L']['ATURAN_DENAH'] = $data_lk['ATURAN_DENAH_FINAL'];
            $data['P']['ATURAN_DENAH'] = $data_pr['ATURAN_DENAH_FINAL'];

            $data['L']['RUANG_WARIS'] = isset($data_lk['RUANG_WARIS']) ? $data_lk['RUANG_WARIS'] : array();
            $data['P']['RUANG_WARIS'] = isset($data_pr['RUANG_WARIS']) ? $data_pr['RUANG_WARIS'] : array();

            $data_denah = array(
                'ATURAN_RUANG_PUD' => json_encode($data)
            );
            $where_denah = array(
                'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE')
            );
            $this->CI->aturan_denah->update($where_denah, $data_denah);
        } else {
            // TAMPILKAN JIKA JUMLAH TIDAK SESUAI
            $this->CI->generate->output_JSON(array(
                'status' => false,
                'msg' => 'Jumlah pertingkat dalam aturan pembuatan denah tidak sesuai dengan didatabase. Silahkan muat ulang halaman ini.'
            ));
        }

        $result = array(
            'L' => $this->buat_denah($mode, $data_lk, $data_sisa['L']),
            'P' => $this->buat_denah($mode, $data_pr, $data_sisa['P'])
        );
        $data_save = array(
            'DATA_DENAH' => json_encode($result),
        );
        $where_save = array(
            'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE')
        );

        $this->CI->aturan_denah->update($where_save, $data_save);

        return $result;
    }

    private function susun_data_sisa($mode, $sisa, $data, $jk = "L") {
        $tingkat = $data['TINGKAT'];
        $jenjang = $data['JENJANG'];

        if ($mode == 'UM') {

            // MENDAPATKAN RUANG YANG BELUM DIGUNAKAN
            $ruang_terakhir = count($sisa['DATA']) - 1;
            $data['RUANG'] = $this->CI->ruang->get_ruang_ujian();
            $sisa_ruang_bebas = (count($data['RUANG']) - $ruang_terakhir + 1);

            if ($sisa_ruang_bebas <= 0) {
                $this->CI->generate->output_JSON(array(
                    'status' => false,
                    'msg' => 'Ruangan tidak ada yang kosong. Pengelompokan peserta sisa dibatalkan.'
                ));
            }

            // MENDAPATKAN MAKSIMAL JUMLAH SISA PERRUANG PERTINGKAT
            $jumlah_perbaris = $data['JUMLAH_PERBARIS'];
            $KAPASITAS_UJIAN_RUANG = array();
            $maksimal_perruang_pertingkat = array();
            foreach ($data['RUANG'] as $key => $value) {
                if ($key >= $ruang_terakhir) {
                    $KAPASITAS_UJIAN_RUANG[$key] = $value['KAPASITAS_UJIAN_RUANG'];
                    $jumlah_baris = round($value['KAPASITAS_UJIAN_RUANG'] / $jumlah_perbaris);
                    $jumlah_baris = (($jumlah_baris % 2) == 0) ? $jumlah_baris : ($jumlah_baris + 1);
                    $maksimal_perruang_pertingkat[$key] = round($jumlah_baris / 2) * round($jumlah_perbaris / 2);
                }
            }
            // MEMPERSIAPKAN VARIBALE
            $kumpulan_sisa = array_fill(0, count($tingkat), 0);

            // MENGAMBIL DATA RUANG TERAKHIR UNTUK DIGABUNG DENGAN SISWA
            foreach ($sisa['DATA'][$ruang_terakhir] as $tingkat_sisa) {
                $kumpulan_sisa[$tingkat_sisa] ++;
            }

            unset($sisa['DATA'][$ruang_terakhir]);
            unset($sisa['ATURAN_DENAH'][$ruang_terakhir]);

            foreach ($sisa['SISA'] as $ruang => $value) {
                foreach ($value as $index_tingkat => $jumlah_sisa) {
                    for ($i = 0; $i < $jumlah_sisa; $i++) {
                        $kumpulan_sisa[$index_tingkat] ++;
                    }
                }
            }

            // MEMPERSIAPKAN VARIBALE
            $result = array_fill($ruang_terakhir, count($data['RUANG']), 0);
            foreach ($result as $key => $value) {
                $result[$key] = array_fill(0, count($tingkat), 0);
            }

            $temp_jenjang = null;
            foreach ($kumpulan_sisa as $tingkat_sisa => $jumlah_peserta) {
                if ($temp_jenjang != $jenjang[$tingkat_sisa]) {
                    $ruang_sekarang = $ruang_terakhir;
                }

                for ($i = 0; $i < $jumlah_peserta; $i++) {
                    $jumlah_peserta_pertingkat = 0;
                    foreach ($result[$ruang_sekarang] as $tingkat_cek => $jumlah_cek) {
                        if ($jenjang[$tingkat_cek] == $jenjang[$tingkat_sisa]) {
                            $jumlah_peserta_pertingkat += $jumlah_cek;
                        }
                    }

                    if ($jumlah_peserta_pertingkat >= $maksimal_perruang_pertingkat[$ruang_sekarang]) {
                        $ruang_sekarang++;
                    }

                    $result[$ruang_sekarang][$tingkat_sisa] ++;

                    if ($ruang_sekarang >= count($data['RUANG'])) {
                        $this->CI->generate->output_JSON(array(
                            'status' => false,
                            'msg' => 'Jumlah ruangan tidak mencukupi. Silahkan menambah ruang terlebih dahulu.'
                        ));
                    }
                }

                $temp_jenjang = $jenjang[$tingkat_sisa];
            }

            // MEMASUKAN KE DATA ATURAN DENAH
            foreach ($result as $key => $aturan_denah) {
                $jumlah = 0;
                foreach ($aturan_denah as $index) {
                    $jumlah += $index;
                }

                if ($jumlah > 0) {
                    $sisa['ATURAN_DENAH'][$key] = $aturan_denah;
                }
            }
        } elseif ($mode == 'US') {

//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
//            echo '<hr>ATURAN_DENAH<br>' . json_encode((object) $data['ATURAN_DENAH']);
            // MENGGABUNG RUANGAN YG ISINYA SEDIKIT
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($sisa['JUMLAH_PESERTA_PERRUANG']);
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode((object) $sisa['JUMLAH_PESERTA_PERRUANG']);
            $temp_jumlah_peserta_peruang = $sisa['JUMLAH_PESERTA_PERRUANG'];
            krsort($temp_jumlah_peserta_peruang);
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($temp_jumlah_peserta_peruang);

            $temp_jumlah_peserta_gabung = 0;
            $temp_jumlah_ruang_gabung = 0;
            $temp_ruang_gabung = array();
            $temp_ruang_gabung_proses = array();
            $temp_ruang_waris = array_flip($data['RUANG_WARIS']);
            foreach ($temp_jumlah_peserta_peruang as $index_ruang => $jumlah_peserta) {
                if (!$data['STATUS_ACAK_RUANG'][$index_ruang])
                    continue;

                $jumlah_peserta = abs($jumlah_peserta);

                if ($jumlah_peserta > 0) {
                    $temp_jumlah_peserta_gabung += $jumlah_peserta;
                    $temp_ruang_gabung[] = $index_ruang;

                    if ((count($temp_ruang_gabung) < $temp_jumlah_ruang_gabung) && ($temp_jumlah_ruang_gabung > 2)) {
//                        echo '<hr>PROSES';

                        unset($temp_ruang_gabung_proses[count($temp_ruang_gabung_proses) - 1]);
                        asort($temp_ruang_gabung_proses);
//                        echo '<hr>$temp_ruang_gabung_proses<br>' . json_encode($temp_ruang_gabung_proses);
                        $index_ruang_target = NULL;

//                        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH'][39]);
//                        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH'][40]);
                        foreach ($temp_ruang_gabung_proses as $index_ruang_gabung) {
                            if ($index_ruang_target != NULL) {
                                foreach ($sisa['ATURAN_DENAH'][$index_ruang_gabung] as $index_tingkat => $jumlah) {
//                                    echo '<hr> >>>>>>>>>>> '.$jumlah.' >>>>> ' . json_encode($sisa['ATURAN_DENAH'][39]).' >>>>>> '. json_encode($sisa['ATURAN_DENAH'][40]);

                                    $sisa['ATURAN_DENAH'][$index_ruang_target][$index_tingkat] += $jumlah;
                                    $sisa['ATURAN_DENAH'][$index_ruang_gabung][$index_tingkat] -= $jumlah;

                                    $index_ruang_ujian_dept = $data['NAMA_DEPT'][$index_tingkat] . '_' . $data['TINGKAT'][$index_tingkat];
                                    if (isset($temp_ruang_waris[$index_ruang_ujian_dept . '_' . $index_ruang_gabung]))
                                        unset($data['RUANG_WARIS'][$temp_ruang_waris[$index_ruang_ujian_dept . '_' . $index_ruang_gabung]]);
                                }
                            }

                            if ($index_ruang_target == NULL)
                                $index_ruang_target = $index_ruang_gabung;

//                            echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH'][$index_ruang_target]) . ' | ' . json_encode($data['ATURAN_DENAH'][$index_ruang_gabung]);
                        }
//                        echo '<hr>ATURAN_DENAH<br>' . json_encode($sisa['ATURAN_DENAH'][39]);
//                        echo '<hr>ATURAN_DENAH<br>' . json_encode($sisa['ATURAN_DENAH'][40]);
                    }

                    $temp_jumlah_ruang_gabung = count($temp_ruang_gabung);
                    $temp_ruang_gabung_proses = $temp_ruang_gabung;

                    if ($temp_jumlah_peserta_gabung > $data['RUANG'][$index_ruang]['KAPASITAS_UJIAN_RUANG']) {
                        $temp_jumlah_peserta_gabung = 0;
                        $temp_ruang_gabung = array();
                    }
                }
            }

            // MEMBAGI SISA PESERTA KE RUANGAN YANG ADA
//            echo '<hr>##############################################################################################################################################################<br>';
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($sisa['JUMLAH_PESERTA_PERRUANG'][39]);
//            echo '<hr>NAMA_DEPT<br>' . json_encode($sisa['NAMA_DEPT']);
//            echo '<hr>TINGKAT<br>' . json_encode($sisa['TINGKAT']);
//            echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($sisa['JUMLAH_SISA_SISWA_PERTINGKAT']);
//            echo '<hr>RUANG_WARIS<br>' . json_encode($data['RUANG_WARIS']);
//            echo '<hr>SISA<br>' . json_encode($sisa['SISA']);
//            echo '<hr>ATURAN_DENAH<br>' . json_encode((object) $sisa['ATURAN_DENAH']);

            foreach ($sisa['JUMLAH_SISA_SISWA_PERTINGKAT'] as $index_tingkat => $jumlah_peserta_sisa) {
                if (!$data['STATUS_ACAK'][$index_tingkat])
                    continue;

//                if ($index_tingkat == 15) {
//                    echo '<hr>JUMLAH RUANGAN<br>' . json_encode(count($data['RUANG_UJIAN_DEPT'][$index_ruang_ujian_dept]));
//                    echo '<hr>SISA BEFORE<br>' . json_encode($jumlah_peserta_sisa);
//                    $cek_ruang = 0;
//                }

                if ($jumlah_peserta_sisa > 0) {
                    $index_ruang_ujian_dept = $data['NAMA_DEPT'][$index_tingkat] . '_' . $data['TINGKAT'][$index_tingkat];
                    foreach ($data['RUANG_UJIAN_DEPT'][$index_ruang_ujian_dept] as $key => $index_ruang) {
                        if (!isset($sisa['SISA'][$index_ruang][$index_tingkat]) || (isset($sisa['SISA'][$index_ruang][$index_tingkat]) && ($sisa['SISA'][$index_ruang][$index_tingkat] == 0)) && ((!isset($data['RUANG_WARIS'])) || (isset($data['RUANG_WARIS']) && !in_array($index_ruang_ujian_dept . '_' . $index_ruang, $data['RUANG_WARIS'])))) {
                            $data_ruang = $sisa['ATURAN_DENAH'][$index_ruang];
                            $jumlah_peserta_ruang = $this->jumlah_peserta_diruang($data_ruang);
                            $KAPASITAS_UJIAN_RUANG = $sisa['RUANG'][$index_ruang]['KAPASITAS_UJIAN_RUANG'];

//                            echo '<hr><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><br>';
//                            echo '<hr>SISA<br>' . json_encode($sisa['SISA'][$index_ruang]);
//                            echo '<hr>$jumlah_peserta_sisa<br>' . json_encode($jumlah_peserta_sisa);
//                            echo '<hr>NAMA_DEPT<br>' . json_encode($data['NAMA_DEPT'][$index_tingkat] . '_' . $data['TINGKAT'][$index_tingkat]);
//                            echo '<hr>$data_ruang<br>' . json_encode($data_ruang);
//                            echo '<hr>$index_ruang<br>' . json_encode($index_ruang);
//                            echo '<hr>KODE_RUANG<br>' . json_encode($sisa['RUANG'][$index_ruang]['KODE_RUANG']);
//                            echo '<hr>$jumlah_peserta_ruang<br>' . json_encode($jumlah_peserta_ruang);
//                            echo '<hr>$KAPASITAS_UJIAN_RUANG<br>' . json_encode($KAPASITAS_UJIAN_RUANG);
//                            echo '<hr>$data_ruang<br>' . json_encode($data_ruang);
//                            echo '<hr>ATURAN_DENAH<br>' . json_encode($sisa['ATURAN_DENAH'][$index_ruang]);

                            $temp_jumlah_peserta_sisa = $jumlah_peserta_sisa;
                            if ($jumlah_peserta_ruang < $KAPASITAS_UJIAN_RUANG) {
//                            echo '<hr>RUANG_WARIS<br>' . json_encode($data['RUANG_WARIS']);
                                $jumlah_peserta_kosong = $KAPASITAS_UJIAN_RUANG - $jumlah_peserta_ruang;

                                if ($jumlah_peserta_kosong >= $jumlah_peserta_sisa) {
                                    $sisa['ATURAN_DENAH'][$index_ruang][$index_tingkat] += $jumlah_peserta_sisa;
                                    $jumlah_peserta_sisa = 0;
                                } elseif ($jumlah_peserta_kosong < $jumlah_peserta_sisa) {
                                    $sisa['ATURAN_DENAH'][$index_ruang][$index_tingkat] += $jumlah_peserta_kosong;
                                    $jumlah_peserta_sisa -= $jumlah_peserta_kosong;
                                } elseif (!isset($sisa['RUANG_UJIAN_DEPT'][$index_ruang_ujian_dept][$key + 1])) {
                                    $this->CI->generate->output_JSON(array(
                                        'status' => false,
                                        'msg' => 'Jumlah ruangan tidak mencukupi. Silahkan menambah ruang terlebih dahulu.'
                                    ));
//                                    echo '<hr>Jumlah ruangan tidak mencukupi. Silahkan menambah ruang terlebih dahulu.<hr>';
                                }

//                                if ($index_tingkat == 15) {
//                                    echo '<hr>RUANG XXX KE <br>' . json_encode($cek_ruang);
//                                    echo '<hr>SISA XXX<br>' . json_encode($jumlah_peserta_sisa);
//                                }

                                $data['RUANG_WARIS'][] = $index_ruang_ujian_dept . '_' . $index_ruang;
                                $sisa['JUMLAH_SISA_SISWA_PERTINGKAT'][$index_tingkat] = $jumlah_peserta_sisa;

                                foreach ($sisa['SISA'] as $index_ruang_sisa => $data_ruang_sisa) {
                                    if ($data_ruang_sisa[$index_tingkat] > 0) {
                                        for ($i = 0; $i < $data_ruang_sisa[$index_tingkat]; $i++) {
                                            $temp_jumlah_peserta_sisa--;
                                            $sisa['SISA'][$index_ruang_sisa][$index_tingkat] --;

                                            if ($temp_jumlah_peserta_sisa == $jumlah_peserta_sisa)
                                                break 2;
                                        }
                                    }
                                }
                            }
//                            echo '<hr>ATURAN_DENAH<br>' . json_encode($sisa['ATURAN_DENAH'][$index_ruang]);
                        }

//                        if ($index_tingkat == 15) {
//                            echo '<hr>RUANG KE <br>' . json_encode($cek_ruang++);
//                            echo '<hr>SISA A<br>' . json_encode($jumlah_peserta_sisa);
//                        }

                        if ($jumlah_peserta_sisa == 0)
                            break;
                    }
                }

                if ($jumlah_peserta_sisa > 0) {
                    $this->CI->generate->output_JSON(array(
                        'status' => false,
                        'msg' => 'Jumlah ruangan untuk departemen '.$data['NAMA_DEPT'][$index_tingkat].' '.($jk == 'L' ? 'banin' : 'banat').' tidak mencukupi. Silahkan menambah ruang terlebih dahulu.'
                    ));
//                    $sisa['ATURAN_DENAH'][$index_ruang][$index_tingkat] += $jumlah_peserta_sisa;
//                    $jumlah_peserta_sisa = 0;
//                    $data['RUANG_WARIS'][] = $index_ruang_ujian_dept . '_' . $index_ruang;
//                    $sisa['JUMLAH_SISA_SISWA_PERTINGKAT'][$index_tingkat] = $jumlah_peserta_sisa;
                }


//                if ($index_tingkat == 15) {
//                    echo '<hr>RUANG KE <br>' . json_encode($cek_ruang++);
//                    echo '<hr>SISA A<br>' . json_encode($jumlah_peserta_sisa);
//                }
            }

            // MENDAPATKAN JUMLAH PESERTA SETIAP RUANG YANG BARU
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($sisa['JUMLAH_PESERTA_PERRUANG']);
//            echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($sisa['JUMLAH_SISA_SISWA_PERTINGKAT']);
            $sisa['JUMLAH_PESERTA_PERRUANG'] = array();
            foreach ($sisa['ATURAN_DENAH'] as $index_ruang => $data_ruang) {
                $sisa['JUMLAH_PESERTA_PERRUANG'][$index_ruang] = $this->jumlah_peserta_diruang($data_ruang);
            }
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($data['JUMLAH_PESERTA_PERRUANG']);
//            echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode((object) $sisa['JUMLAH_PESERTA_PERRUANG']);
//            echo '<hr>STATUS_ACAK_RUANG<br>' . json_encode($data['STATUS_ACAK_RUANG']);

            $sisa['ATURAN_DENAH_FINAL'] = $sisa['ATURAN_DENAH'];
            $sisa['RUANG_WARIS'] = $data['RUANG_WARIS'];

            $this->cek_jumlah_siswa_aturan($mode, $sisa);
        }


//        echo '<hr>##############################################################################################################################################################<br>';
//        echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($sisa['JUMLAH_SISA_SISWA_PERTINGKAT']);
////        echo '<hr>SISA<br>' . json_encode($sisa['SISA']);
//        echo '<hr>RUANG_WARIS<br>' . json_encode($data['RUANG_WARIS']);
////        echo '<hr>ATURAN_DENAH<br>' . json_encode((object) $sisa['ATURAN_DENAH']);
//        echo '<hr>##############################################################################################################################################################<br>';
//        echo '<hr>ATURAN_DENAH<br>' . json_encode($sisa['ATURAN_DENAH']);
////        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);
////        echo '<hr>DATA<br>' . json_encode($sisa['DATA']);
//        exit();

        return ($mode == 'UM') ? $sisa['ATURAN_DENAH'] : $sisa;
    }

    // MEMBUAT DENAH
    private function buat_denah($mode, $data, $data_denah_db) {
        $result = array();
        $jenjang = $data['JENJANG'];
        $nama_jenjang = $data['NAMA_JENJANG'];
        $nama_dept = $data['NAMA_DEPT'];
        $tingkat = $data['TINGKAT'];
        $jumlah_peserta_peruang = $data['JUMLAH_PESERTA_PERRUANG'];
        $result_denah = array();
        $result_denah_urut = array();
        $result_denah_sisa = array();
        $result_aturan_denah = array();
        $jumlah_sisa = array();
        $temp_ruang = 0;
        $maks_loop_ruang = 10;
//        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
//        echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
//        echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($data['JUMLAH_PESERTA_PERRUANG']);
        // LOOPING SETIAP RUANG
        $z = 0;
        foreach ($data['ATURAN_DENAH_FINAL'] as $key => $value) {
            $data_urut = $this->urut_jenjang_terbesar($jenjang, $value);

            if ($mode == 'US') {
                asort($value, SORT_DESC);
                $temp = array_reverse($value, TRUE);
                $data_urut['DATA'] = array_values($temp);
                $data_urut['INDEX'] = array_keys($temp);
            }

            $temp_data_urut = $data_urut;
            $total_peserta = $this->jumlah_peserta_ruang($data_urut['DATA']);
            $jumlah_perbaris = $data['JUMLAH_PERBARIS'];
            $result_aturan_denah[$key] = array_fill(0, count($tingkat), 0);

            // MENDAPATKAN JUMLAH PESERTA PADA RUANGAN INI
            $jumlah_peserta = 0;
            foreach ($data_urut['DATA'] as $value) {
                $jumlah_peserta += $value;
            }

            // MENGAMBIL NILAI JUMLAH PERUANG
            $jumlah_peruang = $jumlah_peserta > $data['RUANG'][$z]['KAPASITAS_UJIAN_RUANG'] ? $jumlah_peserta : $data['RUANG'][$z]['KAPASITAS_UJIAN_RUANG'];

            // LOOPING SETIAP TINGKAT
            $count_loop_ruang = 0;
            $data_looping = $data_urut;
            $temp_kode_ruang_cek = "";
            while (TRUE) {
                $cek_bangku_kosong = 0;
                foreach ($data_looping['DATA'] as $index => $item) {
                    $iterasi_over = false;
                    $iterasi = 0;
                    $index_aturan = $data_urut['INDEX'][$index];
//                echo ' ================================================================================<br> ';
                    // JIKA JUMLAH SISWA DITINGKAT TERSEBUT ADA
                    if ($item > 0) {
                        // LOOPING LOKASI SELURUH RUANGAN
                        for ($i = $cek_bangku_kosong; $i < $jumlah_peruang; $i++) {
                            $bangku_kosong = true;
                            $cek_bangku_kosong = $i;

                            // MENENTUKAN POSISI KURSI SISWA
//                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$index_aturan . ' | ' . $cek_bangku_kosong.' <><><> ';
                            while ($bangku_kosong) {
//                                echo $cek_bangku_kosong . ' | ' . $i . " | ";
                                // JIKA LOOPING MELEBIHI KAPASISTAS MAKA DIMULAI DARI AWAL
                                if ($cek_bangku_kosong > ($jumlah_peruang - 1)) {
                                    $cek_bangku_kosong = 0;
                                    $iterasi_over = true;
//                                echo ' V ';
                                }
                                // MENGAKHIRI LOOPING JIKA KEMBALI KE KURSI AWAL LOOPING
                                if (($i == $cek_bangku_kosong) && $iterasi_over) {
//                                echo ' X ';
                                    $iterasi_over = false;
                                    break;
                                }

//                                if (($data['KODE_RUANG'][$key] == 'A1-07') && $count_loop_ruang == 8) {
//                                    echo '#######################################################################################<br>';
//                                    echo 'INDEX = ' . $index_aturan . '<br>';
//                                    echo 'KURSI = ' . $cek_bangku_kosong . '<br>';
//                                    echo 'KURSI +1 = ' . var_dump(isset($result_denah[$key][$cek_bangku_kosong + 1])) . '<br>';
//                                    echo 'KURSI +1 = ' . (($cek_bangku_kosong + 1) % $jumlah_perbaris) . '<br>';
//                                    echo 'KURSI +1 = ' . (isset($result_denah[$key][$cek_bangku_kosong + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0)) . '<br>';
//                                    echo 'KURSI +2 = ' . var_dump(isset($result_denah[$key][$cek_bangku_kosong + 2])) . '<br>';
//                                    echo 'KURSI +2 = ' . (($cek_bangku_kosong + 2) % $jumlah_perbaris) . '<br>';
//                                    echo 'KURSI -1 = ' . var_dump(isset($result_denah[$key][$cek_bangku_kosong - 1])) . '<br>';
//                                    echo 'KURSI -1 = ' . ($cek_bangku_kosong % $jumlah_perbaris) . '<br>';
//                                    echo 'KURSI -2 = ' . var_dump(isset($result_denah[$key][$cek_bangku_kosong - 2])) . '<br>';
//                                    echo 'KURSI -2 = ' . (($cek_bangku_kosong - 1) % $jumlah_perbaris) . '<br>';
//                                }
                                // LEWATI JIKA KURSI TELAH ADA YANG PUNYA
                                if (isset($result_denah[$key][$cek_bangku_kosong])) {
                                    $cek_bangku_kosong++;
                                    // CEK KURSI KARENA KOSONG
                                } else {
                                    // CEK KURSI DENGAN KANAN KIRI ATAS BAWAH APAKAH DIPERBOLEHKAN
                                    if (
                                            (
                                            ($mode == 'UM') &&
                                            (
                                            (isset($result_denah[$key][$cek_bangku_kosong + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0)) ||
                                            (isset($result_denah[$key][$cek_bangku_kosong - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - 1]) && (($cek_bangku_kosong % $jumlah_perbaris) != 0)) ||
                                            (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris])) ||
//                                                (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong  + $jumlah_perbaris + 1])) ||
//                                                (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong  + $jumlah_perbaris - 1])) ||
                                            (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris]))
//                                                (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong  - $jumlah_perbaris + 1])) ||
//                                                (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong  - $jumlah_perbaris - 1]))
                                            )
                                            ) ||
                                            (
                                            ($mode == 'US') &&
                                            ($data['STATUS_ACAK_RUANG'][$key]) &&
                                            (
                                            // CEK KANAN
                                            (isset($result_denah[$key][$cek_bangku_kosong + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0)) ||
                                            // CEK KANAN JAUH
                                            (isset($result_denah[$key][$cek_bangku_kosong + 2]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + 2]) && ((($cek_bangku_kosong + 2) % $jumlah_perbaris) != 1)) ||
                                            // CEK KIRI
                                            (isset($result_denah[$key][$cek_bangku_kosong - 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - 1]) && (($cek_bangku_kosong % $jumlah_perbaris) != 0)) ||
                                            // CEK KIRI JAUH
                                            (isset($result_denah[$key][$cek_bangku_kosong - 2]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - 2]) && ((($cek_bangku_kosong - 1) % $jumlah_perbaris) != ($jumlah_perbaris - 1))) ||
                                            // CEK BAWAH
                                            (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris])) ||
                                            // CEK BAWAH KANAN
                                            (
                                            (($count_loop_ruang >= 5) && (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0))) ||
                                            (($count_loop_ruang < 5) && (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1])))
                                            ) ||
                                            // CEK BAWAH KIRI
                                            (
                                            (($count_loop_ruang >= 5) && (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1]) && (($cek_bangku_kosong % $jumlah_perbaris) != 0))) ||
                                            (($count_loop_ruang < 5) && (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1])))
                                            ) ||
                                            // CEK ATAS
                                            (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris])) ||
                                            // CEK ATAS KANAN
                                            (
                                            (($count_loop_ruang >= 5) && (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0))) ||
                                            (($count_loop_ruang < 5) && (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1])))
                                            ) ||
                                            // CEK ATAS KIRI
                                            (
                                            (($count_loop_ruang >= 5) && (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]) && (($cek_bangku_kosong % $jumlah_perbaris) != 0))) ||
                                            (($count_loop_ruang < 5) && (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]) && $this->dalam_satu_tingkat($index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1])))
                                            )
                                            )
                                            )
                                    ) {
                                        $cek_bangku_kosong++;
                                    } else {
                                        // SISWA TELAH MENDAPATKAN KURSI
                                        $result_denah[$key][$cek_bangku_kosong] = $index_aturan;
                                        $result_aturan_denah[$key][$index_aturan] ++;
                                        $temp_data_urut['DATA'][$index] --;
                                        $total_peserta--;
                                        $bangku_kosong = false;
                                    }
                                }
//                                echo ' >>> ';
                            }

                            $i = ($cek_bangku_kosong >= ($jumlah_peruang - 1)) ? 0 : $cek_bangku_kosong;
                            $iterasi++;

//                            if($data['KODE_RUANG'][$key] == 'A1-07') {
//                            echo '<br>';
//                            echo $index_aturan . ' | ' . $cek_bangku_kosong . ' # ' . $cek_bangku_kosong . ' | ' . $i . ' # ' . $iterasi . ' | ' . $item;
//                            echo '<br>';
//                            }

                            if ($iterasi == $item) {
//                            echo ' Z ';
                                break;
                            }
                        }
                    }
                }

                if (isset($result_denah[$key])) {
                    // URUTKAN KURSI
                    $result_denah_urut[$key] = $this->urutkan_index($result_denah[$key]);
                    $temp_data_urut_index = array_combine($temp_data_urut['INDEX'], $temp_data_urut['DATA']);
                    $result_denah_sisa[$key] = $this->urutkan_index($temp_data_urut_index);
                    $jumlah_peserta_sisa = $this->jumlah_peserta_ruang($result_denah_sisa[$key]);
                    $jumlah_sisa[$key] = $total_peserta;

//            if ($data['KODE_RUANG'][$key] == 'A1-07') {
//                    echo '<br><br><br><br>';
//                    echo '<hr><hr><hr>LOOP ' . $count_loop_ruang;
//                    echo '<hr>RUANG = ' . $data['KODE_RUANG'][$key];
//                    for ($i = 0; $i < $jumlah_peruang; $i++) {
//                        if (($i % $jumlah_perbaris) == 0)
//                            echo '<br>';
//                        echo '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;' . (isset($result_denah_urut[$key][$i]) ? $result_denah_urut[$key][$i] : 'X');
//                    }
//
//                    echo '<hr>ATURAN_DENAH_FINAL<br>' . json_encode($data['ATURAN_DENAH_FINAL'][$key]);
//                    echo '<hr>$data_urut<br>' . json_encode($data_urut);
//                    echo '<hr>$temp_data_urut<br>' . json_encode($temp_data_urut);
//                    echo '<hr>$result_denah_urut<br>' . json_encode($result_denah_urut[$key]);
//                    echo '<hr>$result_denah_sisa<br>' . json_encode($result_denah_sisa[$key]);
//                    echo '<hr>$jumlah_peserta_sisa<br>' . json_encode($jumlah_peserta_sisa);
//                    echo '<br><br><br><br>';
//            }
                }

                if ($jumlah_peserta_sisa == 0) {
                    break;
                } elseif ($count_loop_ruang > $maks_loop_ruang) {
                    break;
                } else {
                    $data_looping = $temp_data_urut;
                }

                $count_loop_ruang++;
            }

            $jumlah_kapasitas_peruang[$z] = $jumlah_peruang;

            $z++;
        }
//            exit();

        $jumlah_sisa_siswa_pertingkat = array_fill(0, count($data['NAMA_DEPT']), 0);
        foreach ($result_denah_sisa as $value) {
            foreach ($value as $index_tingkat => $item) {
                $jumlah_sisa_siswa_pertingkat[$index_tingkat] += $item;
            }
        }
//        
//        foreach ($data['JUMLAH_SISA_SISWA_PERTINGKAT'] as $index_tingkat => $item) {
//            $jumlah_sisa_siswa_pertingkat[$index_tingkat] += $item;
//        }

        $return = array(
            'JUMLAH_PERBARIS' => $jumlah_perbaris,
            'JUMLAH_PERUANG' => $jumlah_peruang,
            'JUMLAH_KAPASITAS_PERUANG' => $jumlah_kapasitas_peruang,
            'JUMLAH_PESERTA_PERRUANG' => $jumlah_peserta_peruang,
            'NAMA_JENJANG' => $nama_jenjang,
            'NAMA_DEPT' => $nama_dept,
            'TINGKAT' => $tingkat,
            'RUANG' => $data['RUANG'],
            'JUMLAH' => $data['JUMLAH'],
            'JUMLAH_SISA_SISWA_PERTINGKAT' => $jumlah_sisa_siswa_pertingkat,
            'ATURAN_DENAH' => $result_aturan_denah,
            'DATA' => $result_denah_urut,
            'SISA' => $result_denah_sisa,
            'JUMLAH_SISA' => $jumlah_sisa,
        );

//        echo '<hr>ATURAN_DENAH<br>' . json_encode($data['ATURAN_DENAH']);
//        echo '<hr>ATURAN_DENAH<br>' . json_encode((object) $return['ATURAN_DENAH']);
////        echo '<hr>RUANG<br>' . json_encode((object)$return['RUANG']);
//        echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
//        echo '<hr>JUMLAH_SISA_SISWA_PERTINGKAT<br>' . json_encode($return['JUMLAH_SISA_SISWA_PERTINGKAT']);
//        echo '<hr>JUMLAH_PESERTA_PERRUANG<br>' . json_encode($return['JUMLAH_PESERTA_PERRUANG']);
//        echo '<hr>SISA<br>' . json_encode($return['SISA']);
////        echo '<hr>DATA<br>' . json_encode($data);
//        echo '<hr>RUANG_UJIAN_DEPT<br>' . json_encode($data['RUANG_UJIAN_DEPT']);

        $this->cek_jumlah_siswa_aturan($mode, $return);

//        exit();

        return $return;
    }

    // MENGECEK APAKAH LOKASI YANG AKAN DICEK DALAM SATU TINGKAT APA TIDAK
    private function dalam_satu_tingkat($index_tingkat, $index_cek) {
        // MENGECEK LOKASI KURSI
        if ($index_tingkat == $index_cek) {
            return true;
        } else {
            return false;
        }
    }

    // MENGURUTKAN INDEX
    private function urutkan_index($data) {
        $result = array();
        foreach ($data as $key => $value) {
            $result[] = $key;
        }

        $urut = $this->urut_terkecil($result);

        $return = array();
        foreach ($urut['DATA'] as $value) {
            $return[$value] = $data[$value];
        }

        return $return;
    }

    // MENGURUTKAN DARI TERKECIL
    private function urut_terkecil($data) {
        $temp_index = array();
        $temp_data = $data;
        $temp_index_key = 0;

        for ($i = 0; $i < count($data); $i++) {
            $temp_index[] = $i;
        }

        foreach ($data as $index => $item) {
            foreach ($temp_data as $key => $value) {
                if ($key == $index) {
                    $nilai_terbesar = $value;
                } elseif ($key > $index) {
                    if ($value < $nilai_terbesar) {
                        $temp_index_key = $temp_index[$key];
                        $temp_index[$key] = $temp_index[$index];
                        $temp_index[$index] = $temp_index_key;

                        $temp_data[$key] = $nilai_terbesar;
                        $temp_data[$index] = $value;

                        $nilai_terbesar = $value;
                        $selisih_perubahan = $key - $index;
                    }
                }
            }
        }

        return array('DATA' => $temp_data, 'INDEX' => $temp_index);
    }

    // MENAMPILKAN DENAH
    public function show_denah($mode) {
        $jk = $this->CI->input->post('jk');
        $index = $this->CI->input->post('key');
        $data_denah = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_denah_psb(), true) : json_decode($this->CI->aturan_denah->get_denah_cawu(), true);

        // MENENTUKAN COLOM DI BOOTSTRAP
        $jumlah_perbaris = $data_denah[$jk]['JUMLAH_PERBARIS'];
        $max_col_bootstrap = '12';
        $double_col_bootstrap = ($max_col_bootstrap % $jumlah_perbaris == 0) ? false : true;
        $parse_col_bootstrap = ($max_col_bootstrap % $jumlah_perbaris == 0) ? ($max_col_bootstrap / $jumlah_perbaris) : ($max_col_bootstrap / ($jumlah_perbaris / 2));

        $result = array(
            'JUMLAH_PERBARIS' => $jumlah_perbaris,
            'JUMLAH_PERUANG' => $data_denah[$jk]['JUMLAH_PERUANG'],
            'JUMLAH_KAPASITAS_PERUANG' => $data_denah[$jk]['JUMLAH_KAPASITAS_PERUANG'][$index],
            'NAMA_JENJANG' => $data_denah[$jk]['NAMA_JENJANG'],
            'NAMA_DEPT' => $data_denah[$jk]['NAMA_DEPT'],
            'WARNA_JENJANG' => $this->warna_denah($data_denah[$jk]['NAMA_JENJANG']),
            'TINGKAT' => $data_denah[$jk]['TINGKAT'],
            'DATA' => $data_denah[$jk]['DATA'][$index],
            'SISA' => isset($data_denah[$jk]['SISA'][$index]) ? $data_denah[$jk]['SISA'][$index] : null,
            'DOUBLE_COL' => $double_col_bootstrap,
            'PARSE_COL' => $parse_col_bootstrap,
            'KURSI_KOSONG' => $this->get_kursi_kosong($data_denah[$jk], $index)
        );

        return $result;
    }

    private function get_kursi_kosong($data_denah, $ruang) {
        $data_sisa = array_fill(0, $data_denah['JUMLAH_PERUANG'], 0);
        $kursi_kosong = array_diff_key($data_sisa, $data_denah['DATA'][$ruang]);

        return array_keys($kursi_kosong);
    }

    public function atur_ulang_denah($mode) {
        $JK = $this->CI->input->post('JK');
        $RUANG = $this->CI->input->post('RUANG');
        $KURSI = $this->CI->input->post('KURSI');
        $TINGKAT = $this->CI->input->post('TINGKAT');

        $denah = json_decode($this->CI->aturan_denah->get_denah_cawu(), true);

        if (isset($denah[$JK][$RUANG][$KURSI])) {
            $this->CI->generate->output_JSON(array('status' => false, 'msg' => 'Gagal memproses data. Kursi telah digunakan. Halaman akan dimuat ulang otomatis.'));
        }

        $denah[$JK]['DATA'][$RUANG][$KURSI] = $TINGKAT;
        $denah[$JK]['SISA'][$RUANG][$TINGKAT] --;
        $denah[$JK]['JUMLAH_SISA'][$RUANG] --;

        $data_save = array(
            'DATA_DENAH' => json_encode($denah),
        );
        $where_save = array(
            'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE')
        );

        $status = $this->CI->aturan_denah->update($where_save, $data_save);

        return $status;
    }

    // MENGAMBIL WARNA UNTUK DENAH PERJENJANG
    private function warna_denah($data) {
        $result = array();

        foreach ($data as $key => $value) {
            $result[] = $this->CI->jenjang_sekolah->get_warna_jenjang($value);
        }

        return $result;
    }

    public function generate_denah_siswa($mode) {
        $result = array();

        $denah = json_decode(($mode == 'UM') ? $this->CI->aturan_denah->get_denah_psb() : $this->CI->aturan_denah->get_denah_cawu(), true);
        $aturan_denah = json_decode(($mode == 'UM') ? $this->CI->aturan_denah->get_aturan_um() : $this->CI->aturan_denah->get_aturan_us(), true);

        $result['L'] = $this->proses_generate_siswa($mode, $denah['L'], $aturan_denah['L']);
        $result['P'] = $this->proses_generate_siswa($mode, $denah['P'], $aturan_denah['P']);

//        echo json_encode($result).'<hr>';
//        exit();
        return $result;
    }

    private function proses_generate_siswa($mode, $denah, $aturan_denah) {
        $result = array();

        $result['JENJANG'] = $aturan_denah['JENJANG'];
        $result['TINGKAT'] = $denah['TINGKAT'];
        $result['JUMLAH_PERUANG'] = $denah['JUMLAH_PERUANG'];
        $result['JUMLAH_PERBARIS'] = $denah['JUMLAH_PERBARIS'];
        $result['ATURAN_DENAH'] = $denah['ATURAN_DENAH'];
        $result['JUMLAH_SISWA'] = $aturan_denah['JUMLAH'];
        $result['RUANG'] = $denah['RUANG'];

//        echo 'JENJANG'.json_encode($result['JENJANG']).'<hr>';
//        echo 'TINGKAT'.json_encode($result['TINGKAT']).'<hr>';
//        echo 'ATURAN_DENAH'.json_encode($result['ATURAN_DENAH']).'<hr>';
//        echo 'JUMLAH_SISWA'.json_encode($result['JUMLAH_SISWA']).'<hr>';
        // MENDAPATKAN ID TINGKAT
        foreach ($result['TINGKAT'] as $key => $value) {
            $result['ID_TINGKAT'][] = intval($this->CI->tingkat->get_id_jenjang($result['JENJANG'][$key], $value));
        }

        if ($mode == 'UM') {
            // MENYUSUN ULANG DATA SISWA
            foreach ($aturan_denah['DATA'] as $index_jenjang => $value) {
                foreach ($value as $id_siswa) {
                    $result['DATA_SISWA'][$result['JENJANG'][$index_jenjang]][] = intval($id_siswa['ID_SISWA']);
                }
            }

            // MERANDOM DATA SISWA
            foreach ($result['DATA_SISWA'] as $index_jenjang => $data_siswa) {
                shuffle($data_siswa);
                $result['DATA_SISWA_RANDOM'][$index_jenjang] = $data_siswa;
            }
        } elseif ($mode == 'US') {
            // MERANDOM DATA SISWA
            $result['DATA_SISWA'] = $aturan_denah['DATA'];
            foreach ($result['DATA_SISWA'] as $index_tingkat => $data_siswa) {
                shuffle($data_siswa);
                $result['DATA_SISWA_RANDOM'][$index_tingkat] = $data_siswa;
            }
        }

        $result['DENAH'] = $denah['DATA'];
//        echo '=========================================================================================================================<br>';
//        $data_ruang = array();
//        foreach ($result['ATURAN_DENAH'] as $index_ruang => $data_jumlah_siswa) {
//            $data_ruang[$index_ruang] = 0;
//            foreach ($data_jumlah_siswa as $value) {
//                $data_ruang[$index_ruang] += $value;
//            }
//        }
//        echo 'JUMLAH ATURAN_DENAH '.json_encode($data_ruang[0]).'<hr>';
//        echo 'ATURAN_DENAH'.json_encode($result['ATURAN_DENAH'][0]).'<hr>';
//        echo 'JUMLAH DATA '.json_encode(count($result['DENAH'][0])).'<hr>';
//        echo 'DATA'.json_encode($result['DENAH'][0]).'<hr>';
//        echo 'JUMLAH DATA_SISWA_RANDOM '.count($result['DATA_SISWA_RANDOM'][12]).'<hr>';
//        echo 'DATA_SISWA_RANDOM'.json_encode($result['DATA_SISWA_RANDOM'][12]).'<hr>';
//        echo '=========================================================================================================================<br>';
//        echo 'DATA'.json_encode($result['DENAH']).'<hr>';
//        echo 'DATA_SISWA_RANDOM'.json_encode($result['DATA_SISWA_RANDOM']).'<hr>';
//        exit();
        return $result;
    }

    public function validasi_denah($mode) {
        // MENGAMBIL DENAH DARI DATABASE
        $id_aturan_denah = ($mode == 'UM') ? $this->CI->aturan_denah->get_id_um() : $this->CI->aturan_denah->get_id_us();
        $data = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_aturan_um(), true) : json_decode($this->CI->aturan_denah->get_aturan_us(), true);
        $data_sisa = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_denah_psb(), true) : json_decode($this->CI->aturan_denah->get_denah_cawu(), true);

        // MEMISAH DATA LAKILAKI DAN PEREMPUAN
//        $data['L'] = $this->proses_validasi($data['L']);
//        $data_sisa['L'] = $this->proses_validasi($data_sisa['L']);

        $ruang = $this->proses_validasi($data_sisa['L']['JUMLAH_PESERTA_PERRUANG'], $data_sisa['L']['RUANG']);
        $data['L']['RUANG'] = $ruang;
        $data['L']['RUANG_OLD'] = $data_sisa['L']['RUANG'];
        $data_sisa['L']['RUANG'] = $ruang;
        $data_sisa['L']['RUANG_OLD'] = $data_sisa['L']['RUANG'];

        $ruang = $this->proses_validasi($data_sisa['P']['JUMLAH_PESERTA_PERRUANG'], $data_sisa['P']['RUANG']);
        $data['P']['RUANG'] = $ruang;
        $data['P']['RUANG_OLD'] = $data_sisa['P']['RUANG'];
        $data_sisa['P']['RUANG'] = $ruang;
        $data_sisa['P']['RUANG_OLD'] = $data_sisa['P']['RUANG'];

        $data_denah = array(
            'DATA_DENAH' => json_encode($data_sisa),
            'ATURAN_RUANG_PUD' => json_encode($data)
        );
        $where_denah = array(
            'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => ($mode == 'UM') ? null : $this->CI->session->userdata('ID_CAWU_ACTIVE')
        );
        $result = $this->CI->aturan_denah->update($where_denah, $data_denah);

        return $result;
    }

    private function proses_validasi($jumlah_peserta_peruang, $ruang) {
//        echo '<hr>RUANG<br>' . count($data['RUANG']).'<hr>';
        foreach ($jumlah_peserta_peruang as $index_ruang => $jumlah_peserta) {
            if ($jumlah_peserta == 0) {
//                echo $data['RUANG'][$index_ruang]['KODE_RUANG'].' | ';
                unset($ruang[$index_ruang]);
            }
        }

        $ruang = array_values($ruang);
//        echo '<hr>RUANG<br>' . count($ruang);

        return $ruang;
    }

}
