<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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

    public function proses_aturan($status, $mode) {
        if ($status) {
            $jumlah_perruang = $this->CI->input->post('jumlah_perruang');

            if (($jumlah_perruang == 0) || ($jumlah_perruang == "")) {
                $data = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_aturan_um(), TRUE) : json_decode($this->CI->aturan_denah->get_aturan_us(), TRUE);

                $result = TRUE;
                $msg = 'Aturan denah lama berhasil dibuka. Untuk membuat aturan baru, silahkan buat aturan baru.';
            } else {
                $this->CI->pengaturan->setJumlahSiswaPerruang($jumlah_perruang);

                $data = $this->generate_aturan($mode);

                $data_denah = array(
                    'ATURAN_RUANG_PUD' => json_encode($data)
                );
                $where_denah = array(
                    'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                    'CAWU_PUD' => ($mode == 'UM') ? NULL : $this->CI->session->userdata('ID_CAWU_ACTIVE')
                );
                $result = $this->CI->aturan_denah->update($where_denah, $data_denah);
                $msg = 'Aturan denah berhasil dibuat';
            }
        } else {
            $data = $this->generate_aturan($mode);

            $data_denah = array(
                'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                'CAWU_PUD' => ($mode == 'UM') ? NULL : $this->CI->session->userdata('ID_CAWU_ACTIVE'),
                'ATURAN_RUANG_PUD' => json_encode($data)
            );
            $result = $this->CI->aturan_denah->save($data_denah);

            $msg = 'Aturan denah berhasil dibuat.';
        }

        if ($result) {
            $data_lk = $data['L'];
            $data_pr = $data['P'];

            $output = array(
                'status' => TRUE,
                'msg' => $msg,
                'data' => array(
                    'lk' => array(
                        'data_aturan' => $data_lk['ATURAN_DENAH'],
                        'jenjang' => $data_lk['NAMA_JENJANG'],
                        'tingkat' => $data_lk['TINGKAT'],
                        'ruang' => $data_lk['RUANG'],
                        'jumlah' => $data_lk['JUMLAH'],
                        'jumlah_sisa' => $data_lk['JUMLAH_SISA_SISWA_PERRUANG'],
                    ),
                    'pr' => array(
                        'data_aturan' => $data_pr['ATURAN_DENAH'],
                        'jenjang' => $data_pr['NAMA_JENJANG'],
                        'tingkat' => $data_pr['TINGKAT'],
                        'ruang' => $data_pr['RUANG'],
                        'jumlah' => $data_pr['JUMLAH'],
                        'jumlah_sisa' => $data_pr['JUMLAH_SISA_SISWA_PERRUANG'],
                    )
                )
            );
        } else {
            $output = array(
                'status' => FALSE,
                'msg' => 'Denah gagal disimpan. Silahkan muat ulang halaman ini.'
            );
        }

        return $output;
    }

    private function generate_aturan($mode) {
        $data = array(
            'L' => $this->generate_denah($mode, 'L'),
            'P' => $this->generate_denah($mode, 'P'),
        );

        return $data;
    }

    private function generate_denah($mode, $jk) {
        // MENGAMBIL DATA PESERTA
        if ($mode == 'UM')
            $peserta = $this->CI->peserta_um->get_all_denah($jk);
        elseif ($mode == 'US')
            $peserta = $this->CI->peserta_us->get_all_denah($jk);

        $data = $this->format_satu_baris($peserta);

        // MENGAMBIL DATA RUANG
        $data['RUANG'] = $this->CI->ruang->get_ruang_ujian();
        $jumlah_ruang_tersedia = count($data['RUANG']);

        // MENGAMBIL DATA ATURAN DARI PENGATURAN
        $data['JUMLAH_PERBARIS'] = $this->CI->pengaturan->getJumlahSiswaPerbaris();
        $data['JUMLAH_PERRUANG'] = $this->CI->pengaturan->getJumlahSiswaPerruang();

        // PROSES ATURAN DENAH OTOMATIS
        // MENDAPATKAN JUMLAH PESERTA
        $data = $this->jumlah_peserta($data);

        // MENDAPATKAN JUMLAH RUANG YANG DIBUTUHKAN
        $data = $this->jumlah_ruang_dibutuhkan($data);

        if ($data['JUMLAH_RUANG_DIGUNAKAN'] > $jumlah_ruang_tersedia) {
            $this->CI->generate->output_JSON(array(
                'status' => FALSE,
                'msg' => 'Ruang yang tersedia tidak mencukupi terhadap ruang yang diminta. Jumlah ruang yang diminta sebanyak ' . $data['JUMLAH_RUANG_DIGUNAKAN'] . ' dengan kapasitas ' . $data['JUMLAH_PERRUANG'] . ' orang. Sedangkan ruang yang tersedia sebanyak ' . $jumlah_ruang_tersedia . ' buah. Silahkan memperbesar kapasitas ruang atau menambah ruang.'
            ));
        }

        // MEMBAGI JUMLAH PESERTA KESETIAP JENJANG
        $data = $this->distribusi_peserta_keruang($data);

        // MEMBUAT SIMULASI JUMLAH SISWA SETIAP RUANG
        $data = $this->jumlah_peserta_setiap_ruang($data);

        // JIKA SISA SISWA PERTINGKAT MELEBIHI KAPASITAS RUANG
        if ($data['JUMLAH_SISA_SISWA_PERRUANG'] > $data['JUMLAH_PERRUANG']) {
            $iterasi = 0;
            while ($data['JUMLAH_SISA_SISWA_PERRUANG'] > $data['JUMLAH_PERRUANG']) {
                $data = $this->distribusi_sisa_keruangan($data, $data['JUMLAH_PERRUANG']);
                if ($iterasi > 10)
                    break;
                $iterasi++;
            }
        }

        return $data;
    }

    // MERUBAH FORMAT ARRAY DATABASE KE FORMAT ARRAY 1 LEVEL
    private function format_satu_baris($peserta) {
        foreach ($peserta as $type => $detail1) {
            foreach ($detail1 as $jenjang => $detail2) {
                foreach ($detail2 as $tingkat => $detail3) {
                    if ($type == 'DATA') {
                        $data['JENJANG'][] = $jenjang;
                        $data['NAMA_JENJANG'][] = $this->CI->jenjang_sekolah->get_nama_jenjang($jenjang);
                        $data['NAMA_DEPT'][] = $this->CI->jenjang_sekolah->get_nama_dept($jenjang);
                        $data['TINGKAT'][] = $tingkat;
                        $data['DATA'][] = $detail3;
                    } else {
                        $data['JUMLAH'][] = $detail3;
                    }
                }
            }
        }

        return $data;
    }

    // MENDAPATKAN JUMLAH TOTAL SEMUA PESERTA
    private function jumlah_peserta($data) {
        $data['JUMLAH_PESERTA'] = 0;
        foreach ($data['JUMLAH'] as $value) {
            $data['JUMLAH_PESERTA'] += $value;
        }

        return $data;
    }

    // MENDAPATKAN JUMLAH RUANG YANG DIBUTUHKAN, TERMASUK RUANG SISA
    private function jumlah_ruang_dibutuhkan($data) {
        $data['SISA_PESERTA'] = $data['JUMLAH_PESERTA'] % $data['JUMLAH_PERRUANG'];
        $data['JUMLAH_RUANG_DIGUNAKAN'] = ($data['JUMLAH_PESERTA'] - $data['SISA_PESERTA']) / $data['JUMLAH_PERRUANG'];
        $data['SISA_PESERTA'] = 1;
        if ($data['SISA_PESERTA'] > 0)
            $data['JUMLAH_RUANG_DIGUNAKAN'] ++;

        return $data;
    }

    // MENDAPATKAN JUMLAH PESERTA DALAM 1 RUANG
    private function jumlah_peserta_ruang($array) {
        $result = 0;

        foreach ($array as $value) {
            $result += $value;
        }

        return $result;
    }

    // MEMBAGI SISWA KESETIAP RUANG
    private function distribusi_peserta_keruang($data) {
        $jumlah_ruang_digunakan_hasil_bagi = $data['SISA_PESERTA'] > 0 ? $data['JUMLAH_RUANG_DIGUNAKAN'] - 1 : $data['JUMLAH_RUANG_DIGUNAKAN'];
        // MEMBAGI SISWA PERTINGKAT KESETIAP RUANG
        $data['JUMLAH_SISA_SISWA_PERRUANG'] = 0;
        foreach ($data['JUMLAH'] as $value) {
            $sisa_siswa = $value % $jumlah_ruang_digunakan_hasil_bagi;
            $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'][] = ($value - $sisa_siswa) / $jumlah_ruang_digunakan_hasil_bagi;
            $data['JUMLAH_SISA_SISWA_PERRUANG'] += $sisa_siswa;
            $data['JUMLAH_SISA_SISWA_PERTINGKAT'][] = $sisa_siswa;
        }
//        echo json_encode($data['JUMLAH_SISWA_PERUANG_PERTINGKAT']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERRUANG']).' => '.json_encode($data['JUMLAH_SISA_SISWA_PERTINGKAT']).'<hr>';
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

//        exit();
        return $data;
    }

    // MENGHITUNG JUMLAH SISWA SETIAP RUANG
    private function jumlah_peserta_setiap_ruang($data) {
        $jumlah_ruang_digunakan_hasil_bagi = ($data['SISA_PESERTA'] > 0) ? $data['JUMLAH_RUANG_DIGUNAKAN'] - 1 : $data['JUMLAH_RUANG_DIGUNAKAN'];
        // MENGHITUNG JUMLAH SISWA SETIAP RUANG
        for ($i = 0; $i < $jumlah_ruang_digunakan_hasil_bagi; $i++) {
            $data['ATURAN_DENAH'][$i] = $data['JUMLAH_SISWA_PERUANG_PERTINGKAT'];
        }
        // MENGHITUNG JUMLAH SISWA DIRUANGAN SISA
        if ($data['SISA_PESERTA'] > 0)
            $data['ATURAN_DENAH'][$jumlah_ruang_digunakan_hasil_bagi] = $data['JUMLAH_SISA_SISWA_PERTINGKAT'];

        return $data;
    }

    private function sisa_peserta_ada($data_sisa) {
        $jumlah_sisa = 0;
        foreach ($data_sisa as $detail) {
            $jumlah_sisa += $detail;
            if ($jumlah_sisa > 0)
                return TRUE;
        }

        return FALSE;
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
    private function distribusi_sisa_keruangan($data) {
        $temp_i = 0;
        $temp_data = $this->urut_sisa_terbesar($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        $jumlah_ruangan_tidak_sisa = count($data['ATURAN_DENAH']) - 1;
        $data['ATURAN_DENAH_BARU'] = $data['ATURAN_DENAH'];
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

                if ($temp_data_item == 0)
                    break;
            }

            $temp_data['DATA'][$index] = $temp_data_item;
            $data['ATURAN_DENAH_BARU'][$jumlah_ruangan_tidak_sisa][$temp_data_index] = $temp_data_item;

            if ($i == $jumlah_ruangan_tidak_sisa) {
                $temp_i = 0;
                $jumlah_sisa_siswa = 0;

                foreach ($temp_data['DATA'] as $key => $value) {
                    if ($key > $index)
                        $jumlah_sisa_siswa += $value;
                }

                if ($jumlah_sisa_siswa < $jumlah_ruangan_tidak_sisa)
                    break;
            } else {
                $temp_i = $i + 1;
            }
        }

        $data['JUMLAH_SISA_SISWA_PERTINGKAT'] = $data['ATURAN_DENAH_BARU'][$jumlah_ruangan_tidak_sisa];
        $data['JUMLAH_SISA_SISWA_PERRUANG'] = $this->jumlah_peserta_ruang($data['JUMLAH_SISA_SISWA_PERTINGKAT']);
        $data['ATURAN_DENAH'] = $data['ATURAN_DENAH_BARU'];
        unset($data['ATURAN_DENAH_BARU']);

        return $data;
    }

    // MENGECEK INPUTAN ATURAN DENAH MANUAL DENGAN MEMBANDINGKAN JUMLAH
    // DATA INPUTAN PERTINGKAT DENGAN DATA ATURAN DENAH BARU
    public function cek_jumlah_input($data) {
        $temp_jumlah = array();
        $status = TRUE;

        for ($i = 0; $i < count($data['JUMLAH']); $i++) {
            $temp_jumlah[] = 0;
        }

        // MENAMBAHKAN JUMLAH SETIAP TINGKAT PADA SETIAP RUANG
        foreach ($data['ATURAN_DENAH_FINAL'] as $key => $value) {
            foreach ($value as $index => $item) {
                $temp_jumlah[$index] += $item;
            }
        }

        foreach ($temp_jumlah as $key => $value) {
            if ($data['JUMLAH'][$key] != $value)
                $status = FALSE;
        }

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
        $temp_jenjang_single = NULL;

        // MENGELOMPOKAN JENJANG
        foreach ($jenjang as $key => $value) {
            if ($temp_jenjang_single != $value)
                $temp_jenjang[] = $value;
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
        $temp_data = NULL;
        $finish = FALSE;

        // MENDAPATKAN KELOMPOK JENJANG INDEX LOKASI TERSEBUT
        foreach ($data_jenjang as $key => $value) {
            if ($temp_data != $value) {
                if ($finish)
                    break;
                else {
                    unset($temp_index);
                    $temp_index = array();
                }
            }

            if ($key == $index_aturan)
                $finish = TRUE;

            $temp_index[] = $key;

            $temp_data = $value;
        }

        // MENGECEK LOKASI KURSI 
        if (in_array($index_cek, $temp_index)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function proses_buat_denah($mode, $req_sisa = FALSE) {
        // MENGAMBIL DENAH DARI DATABASE
        $id_aturan_denah = ($mode == 'UM') ? $this->CI->aturan_denah->get_id_um() : $this->CI->aturan_denah->get_id_us();
        $data = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_aturan_um(), TRUE) : json_decode($this->CI->aturan_denah->get_aturan_us(), TRUE);
        $data_sisa = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_denah_psb(), TRUE) : json_decode($this->CI->aturan_denah->get_denah_cawu(), TRUE);

        // MEMISAH DATA LAKILAKI DAN PEREMPUAN
        $data_lk = $data['L'];
        $data_pr = $data['P'];

        if ($req_sisa) {
            // PROSES PENGELOMPOKAN SISWA SISA KE RUANGAN YANG LAIN

            $data_lk['ATURAN_DENAH_FINAL'] = $this->susun_data_sisa($data_sisa['L'], $data_lk);
            $data_pr['ATURAN_DENAH_FINAL'] = $this->susun_data_sisa($data_sisa['P'], $data_pr);
        } else {
            // MENGAMBIL DATA DARI PERUBAHAN PENGGUNA
            $data_lk['ATURAN_DENAH_FINAL'] = $this->CI->input->post("aturan_lk");
            $data_pr['ATURAN_DENAH_FINAL'] = $this->CI->input->post("aturan_pr");
        }

        // CEK APAKAH JUMLAH MASING-MASING TINGKAT TELAH COCOK
        if ($this->cek_jumlah_input($data_lk) && $this->cek_jumlah_input($data_pr)) {
            $data['L']['ATURAN_DENAH'] = $data_lk['ATURAN_DENAH_FINAL'];
            $data['P']['ATURAN_DENAH'] = $data_pr['ATURAN_DENAH_FINAL'];

            $data_denah = array(
                'ATURAN_RUANG_PUD' => json_encode($data)
            );
            $where_denah = array(
                'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
                'CAWU_PUD' => ($mode == 'UM') ? NULL : $this->CI->session->userdata('ID_CAWU_ACTIVE')
            );
            $this->CI->aturan_denah->update($where_denah, $data_denah);
        } else {
            // TAMPILKAN JIKA JUMLAH TIDAK SESUAI
            $this->CI->generate->output_JSON(array(
                'status' => FALSE,
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
            'CAWU_PUD' => ($mode == 'UM') ? NULL : $this->CI->session->userdata('ID_CAWU_ACTIVE')
        );

        $this->CI->aturan_denah->update($where_save, $data_save);

        return $result;
    }

    private function susun_data_sisa($sisa, $data) {
        $tingkat = $data['TINGKAT'];
        $jenjang = $data['JENJANG'];

        // MENDAPATKAN RUANG YANG BELUM DIGUNAKAN
        $ruang_terakhir = count($sisa['DATA']) - 1;
        $data['RUANG'] = $this->CI->ruang->get_ruang_ujian();
        $sisa_ruang_bebas = (count($data['RUANG']) - $ruang_terakhir + 1);

        if ($sisa_ruang_bebas <= 0) {
            $this->CI->generate->output_JSON(array(
                'status' => FALSE,
                'msg' => 'Ruangan tidak ada yang kosong. Pengelompokan peserta sisa dibatalkan.'
            ));
        }

        // MENDAPATKAN MAKSIMAL JUMLAH SISA PERRUANG PERTINGKAT 
        $jumlah_perbaris = $data['JUMLAH_PERBARIS'];
        $kapasitas_ruang = array();
        $maksimal_perruang_pertingkat = array();
        foreach ($data['RUANG'] as $key => $value) {
            if ($key >= $ruang_terakhir) {
                $kapasitas_ruang[$key] = $value['KAPASITAS_RUANG'];
                $jumlah_baris = round($value['KAPASITAS_RUANG'] / $jumlah_perbaris);
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
            if ($temp_jenjang != $jenjang[$tingkat_sisa])
                $ruang_sekarang = $ruang_terakhir;

            for ($i = 0; $i < $jumlah_peserta; $i++) {
                $jumlah_peserta_pertingkat = 0;
                foreach ($result[$ruang_sekarang] as $tingkat_cek => $jumlah_cek) {
                    if ($jenjang[$tingkat_cek] == $jenjang[$tingkat_sisa])
                        $jumlah_peserta_pertingkat += $jumlah_cek;
                }

                if ($jumlah_peserta_pertingkat >= $maksimal_perruang_pertingkat[$ruang_sekarang])
                    $ruang_sekarang++;

                $result[$ruang_sekarang][$tingkat_sisa] ++;

                if ($ruang_sekarang >= count($data['RUANG']))
                    $this->CI->generate->output_JSON(array(
                        'status' => FALSE,
                        'msg' => 'Jumlah ruangan tidak mencukupi. Silahkan menambah ruang terlebih dahulu.'
                    ));
            }

            $temp_jenjang = $jenjang[$tingkat_sisa];
        }

        // MEMASUKAN KE DATA ATURAN DENAH
        foreach ($result as $key => $aturan_denah) {
            $jumlah = 0;
            foreach ($aturan_denah as $index) {
                $jumlah += $index;
            }

            if ($jumlah > 0)
                $sisa['ATURAN_DENAH'][$key] = $aturan_denah;
        }

        return $sisa['ATURAN_DENAH'];
    }

    // MEMBUAT DENAH
    private function buat_denah($mode, $data, $data_denah_db) {
        $result = array();
        $jenjang = $data['JENJANG'];
        $nama_jenjang = $data['NAMA_JENJANG'];
        $nama_dept = $data['NAMA_DEPT'];
        $tingkat = $data['TINGKAT'];
        $result_denah = array();
        $result_denah_urut = array();
        $result_denah_sisa = array();
        $result_aturan_denah = array();
        $jumlah_sisa = array();
        $temp_ruang = 0;

        // LOOPING SETIAP RUANG
        $z = 0;
        foreach ($data['ATURAN_DENAH_FINAL'] as $key => $value) {
            $data_urut = $this->urut_jenjang_terbesar($jenjang, $value);
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
            $jumlah_peruang = $jumlah_peserta > $data['RUANG'][$z]['KAPASITAS_RUANG'] ? $jumlah_peserta : $data['RUANG'][$z]['KAPASITAS_RUANG'];
            
            // LOOPING SETIAP TINGKAT
            foreach ($data_urut['DATA'] as $index => $item) {
                $cek_bangku_kosong = 0;
                $iterasi_over = FALSE;
                $iterasi = 0;
                $index_aturan = $data_urut['INDEX'][$index];

                // JIKA JUMLAH SISWA DITINGKAT TERSEBUT ADA
                if ($item > 0) {
                    // LOOPING LOKASI SELURUH RUANGAN
                    for ($i = 0; $i < $jumlah_peruang; $i++) {
                        $bangku_kosong = TRUE;
                        $cek_bangku_kosong = $i;

                        // MENENTUKAN POSISI KURSI SISWA
                        while ($bangku_kosong) {
                            // JIKA LOOPING MELEBIHI KAPASISTAS MAKA DIMULAI DARI AWAL
                            if ($cek_bangku_kosong > ($jumlah_peruang - 1)) {
                                $cek_bangku_kosong = 0;
                                $iterasi_over = TRUE;
                            }
                            // MENGAKHIRI LOOPING JIKA KEMBALI KE KURSI AWAL LOOPING
                            if (($i == $cek_bangku_kosong) && $iterasi_over) {
                                break;
                            }

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
                                        (
                                        (isset($result_denah[$key][$cek_bangku_kosong + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + 1]) && ((($cek_bangku_kosong + 1) % $jumlah_perbaris) != 0)) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - 1]) && (($cek_bangku_kosong % $jumlah_perbaris) != 0)) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris])) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris + 1])) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong + $jumlah_perbaris - 1])) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris])) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris + 1])) ||
                                        (isset($result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]) && $this->dalam_satu_jenjang($jenjang, $index_aturan, $result_denah[$key][$cek_bangku_kosong - $jumlah_perbaris - 1]))
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
                                    $bangku_kosong = FALSE;
                                }
                            }
                        }

                        $i = $cek_bangku_kosong;
                        $iterasi++;

                        if ($iterasi == $item)
                            break;
                    }
                }
            }

            if (isset($result_denah[$key])) {
                // URUTKAN KURSI
                $result_denah_urut[$key] = $this->urutkan_index($result_denah[$key]);
                $temp_data_urut_index = array_combine($temp_data_urut['INDEX'], $temp_data_urut['DATA']);
                $result_denah_sisa[$key] = $this->urutkan_index($temp_data_urut_index);
                $jumlah_sisa[$key] = $total_peserta;
            }
            
            $jumlah_kapasitas_peruang[$z] = $jumlah_peruang;
                    
            $z++;
        }

        return array(
            'JUMLAH_PERBARIS' => $jumlah_perbaris,
            'JUMLAH_PERUANG' => $jumlah_peruang,
            'JUMLAH_KAPASITAS_PERUANG' => $jumlah_kapasitas_peruang,
            'NAMA_JENJANG' => $nama_jenjang,
            'NAMA_DEPT' => $nama_dept,
            'TINGKAT' => $tingkat,
            'RUANG' => $data['RUANG'],
            'ATURAN_DENAH' => $result_aturan_denah,
            'DATA' => $result_denah_urut,
            'SISA' => $result_denah_sisa,
            'JUMLAH_SISA' => $jumlah_sisa
        );
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
        $data_denah = ($mode == 'UM') ? json_decode($this->CI->aturan_denah->get_denah_psb(), TRUE) : json_decode($this->CI->aturan_denah->get_denah_cawu(), TRUE);

        // MENENTUKAN COLOM DI BOOTSTRAP
        $jumlah_perbaris = $data_denah[$jk]['JUMLAH_PERBARIS'];
        $max_col_bootstrap = '12';
        $double_col_bootstrap = ($max_col_bootstrap % $jumlah_perbaris == 0) ? FALSE : TRUE;
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
            'SISA' => isset($data_denah[$jk]['SISA'][$index]) ? $data_denah[$jk]['SISA'][$index] : NULL,
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

        $denah = json_decode($this->CI->aturan_denah->get_denah_cawu(), TRUE);

        if (isset($denah[$JK][$RUANG][$KURSI]))
            $this->CI->generate->output_JSON(array('status' => FALSE, 'msg' => 'Gagal memproses data. Kursi telah digunakan. Halaman akan dimuat ulang otomatis.'));

        $denah[$JK]['DATA'][$RUANG][$KURSI] = $TINGKAT;
        $denah[$JK]['SISA'][$RUANG][$TINGKAT] --;
        $denah[$JK]['JUMLAH_SISA'][$RUANG] --;

        $data_save = array(
            'DATA_DENAH' => json_encode($denah),
        );
        $where_save = array(
            'TA_PUD' => ($mode == 'UM') ? $this->CI->session->userdata('ID_PSB_ACTIVE') : $this->CI->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => ($mode == 'UM') ? NULL : $this->CI->session->userdata('ID_CAWU_ACTIVE')
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

        $denah = json_decode(($mode == 'UM') ? $this->CI->aturan_denah->get_denah_psb() : $this->CI->aturan_denah->get_denah_cawu(), TRUE);
        $aturan_denah = json_decode(($mode == 'UM') ? $this->CI->aturan_denah->get_aturan_um() : $this->CI->aturan_denah->get_aturan_us(), TRUE);

        $result['L'] = $this->proses_generate_siswa($denah['L'], $aturan_denah['L']);
        $result['P'] = $this->proses_generate_siswa($denah['P'], $aturan_denah['P']);

        return $result;
    }

    private function proses_generate_siswa($denah, $aturan_denah) {
        $result = array();

        $result['JENJANG'] = $aturan_denah['JENJANG'];
        $result['TINGKAT'] = $denah['TINGKAT'];
        $result['JUMLAH_PERUANG'] = $denah['JUMLAH_PERUANG'];
        $result['JUMLAH_PERBARIS'] = $denah['JUMLAH_PERBARIS'];
        $result['ATURAN_DENAH'] = $denah['ATURAN_DENAH'];
        $result['JUMLAH_SISWA'] = $aturan_denah['JUMLAH'];
        $result['RUANG'] = $denah['RUANG'];

        // MENDAPATKAN ID TINGKAT
        foreach ($result['TINGKAT'] as $key => $value) {
            $result['ID_TINGKAT'][] = intval($this->CI->tingkat->get_id_jenjang($result['JENJANG'][$key], $value));
        }

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

        $result['DENAH'] = $denah['DATA'];

        return $result;
    }

}
