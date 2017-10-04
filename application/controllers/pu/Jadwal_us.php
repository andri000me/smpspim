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
class Jadwal_us extends CI_Controller {

    var $edit_id = TRUE;
    var $primary_key = "ID_PUJ";
    var $tipe = 'US';

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'jadwal_pu_model' => 'jadwal',
            'aturan_denah_model' => 'aturan_denah',
            'denah_model' => 'denah',
            'jenjang_sekolah_model' => 'jenjang_sekolah',
            'pegawai_model' => 'pegawai',
            'pengawas_pu_model' => 'pengawas',
            'mapel_pu_model' => 'mapel',
            'tingkat_model' => 'tingkat',
            'departemen_model' => 'departemen',
            'siswa_model' => 'siswa',
            'peserta_us_model' => 'peserta',
        ));
        $this->load->library('denah_handler');
        $this->auth->validation(6);
    }

    public function index() {
        $data['validasi_denah'] = $this->aturan_denah->is_us_validasi();

        $this->generate->backend_view('pu/jadwal_us/index', $data);
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->jadwal->get_datatables($this->tipe);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->ID_PUJ;
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_CAWU;
            $row[] = $item->JK_PUJ;
            $row[] = $item->TANGGAL_PUJ;
            $row[] = $item->JAM_MULAI_PUJ;
            $row[] = $item->JAM_SELESAI_PUJ;

            $row[] = ($this->session->userdata('ID_TA_ACTIVE') == $item->ID_TA) ? '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Cetak Sampul" onclick="cetak_sampul_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Sampul</a></li>
                        <li><a href="javascript:void()" title="Cetak Soal" onclick="cetak_soal_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Soal</a></li>
                        <li><a href="javascript:void()" title="Cetak Absen Pengawas" onclick="cetak_absen_pengawas_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Absen Pengawas</a></li>
                        <hr class="line-divider">
                        <li><a href="javascript:void()" title="Cetak Absen Peserta" onclick="cetak_absen_peserta_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Absen Peserta</a></li>
                        <li><a href="javascript:void()" title="Cetak Jadwal Tanggal Ini" onclick="cetak_jadwal_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Jadwal</a></li>
                        <li><a href="javascript:void()" title="Cetak Denah Tanggal Ini" onclick="cetak_denah_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Denah</a></li>
                        <li><a href="javascript:void()" title="Cetak Kartu Peserta" onclick="cetak_kertu_siswa_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kartu Peserta</a></li>
                        <hr class="line-divider">
                        <li><a href="javascript:void()" title="Cetak Blanko Nilai" onclick="cetak_blanko_nilai()"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Blanko Nilai</a></li>
                        <hr class="line-divider">
                        <li><a href="javascript:void()" title="Ubah" onclick="update_data_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Ubah</a></li>
                        <li><a href="javascript:void()" title="Hapus" onclick="delete_data_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus</a></li>
                    </ul>
                </div>' : '-';

            $data[] = $row;
        }

        /*

          <li><a href="javascript:void()" title="Cetak Kartu Siswa" onclick="cetak_kertu_siswa_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kartu Siswa</a></li>
          <li><a href="javascript:void()" title="Cetak Kartu Meja" onclick="cetak_kertu_meja_' . $id_datatables . '(\'' . $item->ID_PUJ . '\')"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Kartu Meja</a></li>
          <hr class="line-divider">
         *          */
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jadwal->count_all($this->tipe),
            "recordsFiltered" => $this->jadwal->count_filtered($this->tipe),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function form($ID = NULL, $view = FALSE) {
        $data['validasi_denah'] = $this->aturan_denah->is_us_validasi();

        if ($ID !== NULL) {
            $data['jadwal'] = $this->jadwal->get_by_id($this->tipe, $ID);
            $data['mapel'] = $this->mapel->get_by_jadwal($ID);
            $data['pengawas_lk'] = $this->pengawas->get_by_jadwal_lk($ID);
            $data['pengawas_pr'] = $this->pengawas->get_by_jadwal_pr($ID);
        } else {
            $data['jadwal'] = NULL;
        }

        $data['denah'] = $this->aturan_denah->get_denah_cawu();

        $data['tingkat_us'] = json_decode($this->pengaturan->getUjianCawu(), TRUE);
        $dept = $this->jenjang_sekolah->relasi_jenjang_departemen();

        foreach ($dept as $value) {
            $data['dept'][$value['JENJANG_MJD']] = $value['DEPT_MJD'];
        }

        if ($view)
            $data['mode_view'] = TRUE;

        $this->generate->backend_view('pu/jadwal_us/form', $data);
    }

    private function generate_denah_siswa($tanggal_ujian) {
        if (!$this->denah->is_denah_exist($tanggal_ujian)) {
            $data = array(
                'ATURAN_DENAH' => $this->aturan_denah->get_id_us(),
                'JADWAL_DENAH' => $tanggal_ujian,
                'SISWA_DENAH' => json_encode($this->denah_handler->generate_denah_siswa($this->tipe)),
            );
            $this->denah->save($data);
        }
    }

    private function cek_pengawas($data, $jk) {
        foreach ($data as $key => $value) {
            if ($value == '')
                $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Ada ruangan yang tidak mempunyai pengawas. Pastikan semua ruangan telah memiliki pengawas'));

            $temp_data = $data;
            unset($temp_data[$key]);
            if (in_array($value, $temp_data))
                $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Guru atas nama ' . $this->pegawai->get_name($value) . ' tidak boleh mengawas lebih dari satu ruang'));
        }
    }

    private function save_data($insert, $data) {
        $this->mapel->delete_by_jadwal($insert);
        $this->pengawas->delete_by_jadwal($insert);

        foreach ($data['DEPT_TINGK'] as $key => $value) {
            if (isset($data['MAPEL_PUM'][$key]) && (($data['MAPEL_PUM'][$key] != NULL) || ($data['MAPEL_PUM'][$key] != ''))) {
                $data_save = array(
                    'JADWAL_PUM' => $insert,
                    'TINGKAT_PUM' => $this->tingkat->get_id($data['DEPT_TINGK'][$key], $data['NAMA_TINGK'][$key]),
                    'MAPEL_PUM' => $data['MAPEL_PUM'][$key],
                    'JENIS_PUM' => $data['JENIS_PUM'][$key],
                );

                $this->mapel->save($data_save);
            }
        }

        foreach ($data['RUANGAN_PENG_LK'] as $key => $value) {
            if (isset($data['PEGAWAI_PENG_LK'][$key]) && ($data['PEGAWAI_PENG_LK'][$key] != NULL)) {
                $data_save = array(
                    'JADWAL_PENG' => $insert,
                    'RUANGAN_PENG' => $value,
                    'JK_PENG' => 'L',
                    'PEGAWAI_PENG' => $data['PEGAWAI_PENG_LK'][$key]
                );

                $this->pengawas->save($data_save);
            }
        }

        foreach ($data['RUANGAN_PENG_PR'] as $key => $value) {
            if (isset($data['PEGAWAI_PENG_PR'][$key]) && ($data['PEGAWAI_PENG_PR'][$key] != NULL)) {
                $data_save = array(
                    'JADWAL_PENG' => $insert,
                    'RUANGAN_PENG' => $value,
                    'JK_PENG' => 'P',
                    'PEGAWAI_PENG' => $data['PEGAWAI_PENG_PR'][$key]
                );

                $this->pengawas->save($data_save);
            }
        }
    }

    private function cek_jam_bentrok($data, $update = FALSE) {
        $jam_database = $this->jadwal->get_by_tanggal($this->tipe, $data['TANGGAL_PUJ']);

        foreach ($jam_database as $jam) {
            if ($update && ($data['ID_PUJ'] == $jam['ID_PUJ']))
                continue;

            $input_mulai = new DateTime($data['JAM_MULAI_PUJ']);
            $input_selesai = new DateTime($data['JAM_SELESAI_PUJ']);
            $db_mulai = new DateTime($jam['JAM_MULAI_PUJ']);
            $db_selesai = new DateTime($jam['JAM_SELESAI_PUJ']);

            if (!(($input_mulai < $input_selesai) && ((($db_selesai <= $input_mulai) && ($db_selesai < $input_selesai)) || (($db_mulai >= $input_selesai) && ($db_selesai > $input_selesai))))) {
                $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Tanggal dan jam bentrok dengan yang ada didalam database.'));
            }
        }
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $data = $this->input->post();
//        $this->cek_pengawas($data['PEGAWAI_PENG_LK'], 'lk');
//        $this->cek_pengawas($data['PEGAWAI_PENG_PR'], 'pr');
//        $this->cek_jam_bentrok($data);
        if ($data['JAM_MULAI_PUJ'] == $data['JAM_SELESAI_PUJ'])
            $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Jam mulai dan jam selesai tidak boleh sama.'));

        $data_jadwal = array(
            'TA_PUJ' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUJ' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'TIPE_PUJ' => $this->tipe,
            'JK_PUJ' => $data['JK_PUJ'],
            'TANGGAL_PUJ' => $this->date_format->to_store_db($data['TANGGAL_PUJ']),
            'JAM_MULAI_PUJ' => $data['JAM_MULAI_PUJ'],
            'JAM_SELESAI_PUJ' => $data['JAM_SELESAI_PUJ'],
        );
        $insert = $this->jadwal->save($data_jadwal);
        $this->generate_denah_siswa($this->date_format->to_store_db($data['TANGGAL_PUJ']));

        if ($insert > 0)
            $this->save_data($insert, $data);
        else
            $this->generate->output_JSON(array("status" => FALSE, 'msg' => ''));

        $this->generate->output_JSON(array("status" => $insert));
    }

    public function ajax_update() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');

        $data = $this->input->post();
//        $this->cek_pengawas($data['PEGAWAI_PENG_LK'], 'lk');
//        $this->cek_pengawas($data['PEGAWAI_PENG_PR'], 'pr');
//        $this->cek_jam_bentrok($data, TRUE);
        if ($data['JAM_MULAI_PUJ'] == $data['JAM_SELESAI_PUJ'])
            $this->generate->output_JSON(array("status" => FALSE, 'msg' => 'Jam mulai dan jam selesai tidak boleh sama.'));

        $data_jadwal = array(
            'TANGGAL_PUJ' => $this->date_format->to_store_db($data['TANGGAL_PUJ']),
            'JAM_MULAI_PUJ' => $data['JAM_MULAI_PUJ'],
            'JAM_SELESAI_PUJ' => $data['JAM_SELESAI_PUJ'],
            'JK_PUJ' => $data['JK_PUJ'],
        );
        $where_jadwal = array(
            'ID_PUJ' => $data['ID_PUJ']
        );
        $status = $this->jadwal->update($where_jadwal, $data_jadwal);
        $this->save_data($data['ID_PUJ'], $data);
        $this->generate_denah_siswa($this->date_format->to_store_db($data['TANGGAL_PUJ']));

        $this->generate->output_JSON(array("status" => 1));
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->jadwal->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function cetak_jadwal($id) {
        $data['data'] = $this->mapel->get_all_by_jadwal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();

        $this->load->view('backend/pu/jadwal_us/cetak_jadwal', $data);
    }
    
    private function cek_denah_siswa() {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        
        foreach ($data_jadwal as $index => $detail) {
            $this->generate_denah_siswa($detail['TANGGAL_PUJ']);
        }
    }

    public function cetak_denah($id) {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();

        $this->cek_denah_siswa();
        
        foreach ($data_jadwal as $index => $detail) {
            $data['data'][$index]['TANGGAL'] = $detail['TANGGAL_PUJ'];
            $data['data'][$index]['DENAH'] = $this->denah->get_denah_by_tanggal($detail['TANGGAL_PUJ']);
            
            break;
        }

        $this->load->view('backend/pu/jadwal_us/cetak_denah', $data);
    }

    public function cetak_absen_pengawas($id) {
        $jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['jadwal'] = $jadwal;
        if ($jadwal->JK_PUJ == 'L')
            $data['data']['L'] = $this->pengawas->get_by_jadwal_lk($id);
        else
            $data['data']['P'] = $this->pengawas->get_by_jadwal_pr($id);
        $data['denah'] = $this->denah->get_denah_by_tanggal($jadwal->TANGGAL_PUJ);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();

        $this->load->view('backend/pu/jadwal_us/cetak_absen_pengawas', $data);
    }

    public function cetak_absen_peserta($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['data'][0]['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['data'][0]['JK_PUJ'] = $data_jadwal->JK_PUJ;
        $data['data'][0]['DATA'] = $this->jadwal->get_by_tanggal($this->tipe, $data_jadwal->TANGGAL_PUJ, $data_jadwal->JK_PUJ);
        $data['data'][0]['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);

        $this->load->view('backend/pu/jadwal_us/cetak_absen_peserta', $data);
    }

    public function cetak_kertu_meja($id) {
        $data_jadwal = $this->jadwal->get_all_group_tanggal($this->tipe);
        $data['ketua'] = $this->pengaturan->getDataKetuaPU();

        foreach ($data_jadwal as $index => $detail) {
            $data['data'][$index]['TANGGAL'] = $detail['TANGGAL_PUJ'];
            $data['data'][$index]['DENAH'] = $this->denah->get_denah_by_tanggal($detail['TANGGAL_PUJ']);
        }

        $this->load->view('backend/pu/jadwal_us/cetak_kertu_meja', $data);
    }

    public function cetak_kertu_siswa($id) {
        $this->cek_denah_siswa();
        
        $where = array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        );
        $data_denah = $this->denah->get_rows_array($where);

        if (count($data_denah) != 6) {
            echo '<h1>UJIAN HARUS 6 HARI. SILAHKAN PERIKSA KEMBALI DATA JADWAL UJIAN SEKOLAH.</h1>';

            exit();
        }
        // MENDAPATKAN DATA SISWA SESUAI DENGAN DENAH
        $data_siswa = array();
        foreach ($data_denah as $detail_denah) { // LOOPING PERTANGGAL DENAH
            $denah = json_decode($detail_denah['SISWA_DENAH'], TRUE);
            $jadwal_denah = $detail_denah['JADWAL_DENAH'];
            foreach ($denah as $data_denah) { // LOOPING L DAN P
//                $jumlah = 0;
//                for ($id = 0; $id < count($data_denah['DATA_SISWA']); $id++) {
//                    $jumlah += count($data_denah['DATA_SISWA'][$id]);
////                    echo '<hr>$data_denah<br>' . json_encode(count($data_denah['DATA_SISWA'][$id]));
//                }
//                echo '<hr>$jumlah<br>' . json_encode($jumlah);
//                echo '<hr>$data_denah<br>' . json_encode(count($data_denah['DATA_SISWA']));
//                echo '<hr>$data_denah<br>' . json_encode($data_denah['DATA_SISWA']);
                $jumlah_perbaris = $data_denah['JUMLAH_PERBARIS'];
                // MEMBUAT PARAMENTER UNTUK JENJANG
                $temp_last_id = array_fill(0, count($data_denah['TINGKAT']), 0);
                // MENGAMBIL KODE DEPARTEMEN
                foreach ($data_denah['JENJANG'] as $dept) {
                    $data_denah['KODE_JENJANG'][] = $this->departemen->get_id_by_jenjang($dept);
                }
                // LOOPING RUANGAN
                foreach ($data_denah['DENAH'] as $ruang => $value) {
                    $id_ruang = $data_denah["RUANG"][$ruang]['KODE_RUANG'];
                    $nama_ruang = $data_denah["RUANG"][$ruang]['NAMA_RUANG'];
                    $jumlah = 0;
                    $temp_data_siswa = array();
                    // LOOPING NOMOR URUT DALAM RUANGAN
                    $jumlah_peruang = $data_denah["RUANG"][$ruang]['KAPASITAS_UJIAN_RUANG'];
                    for ($i = 0; $i < $jumlah_peruang; $i++) {
                        $no_urut = $i;
                        // MEMBUAT ATURAN DALAM DISTRIBUSI SISWA DIRUANGAN
                        if ((($i + 1) % $jumlah_perbaris) == 0) {
                            for ($x = ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                                if (isset($data_denah['DENAH'][$ruang][$x])) {
                                    // MENDAPATKAN DATA SISWA PADA SETIAP RUANGAN
                                    $id_tingkat = $data_denah['DENAH'][$ruang][$x];
                                    $id_dept = $data_denah['KODE_JENJANG'][$id_tingkat];
                                    $id_jenjang = $data_denah['JENJANG'][$id_tingkat];
                                    $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_tingkat][$temp_last_id[$id_tingkat]]['ID_SISWA'];

                                    $data_siswa[$id_siswa][] = array(
                                        'TANGGAL' => $jadwal_denah,
                                        'DEPT' => $id_dept,
                                        'JENJANG' => $id_jenjang,
                                        'TINGKAT' => $id_tingkat,
                                        'RUANG' => array(
                                            'ID' => $id_ruang,
                                            'NAMA' => $nama_ruang,
                                            'NOMOR' => $no_urut,
                                        )
                                    );
                                    $temp_data_siswa[$jumlah] = $id_siswa;

                                    $temp_last_id[$id_tingkat] ++;
                                    $jumlah++;
                                }
                            }
                        }
                    }

//                    $count_denah = count($data_denah['DENAH'][$ruang]);
//                    
//                    if ($count_denah == $jumlah) {
//                        echo '<br>'.$ruang.' | ' . $count_denah . ' | ' . $jumlah;
//                    } else {
//                        echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>>>>>>>>>&nbsp;&nbsp;&nbsp;'.$ruang.' | ' . $count_denah . ' | ' . $jumlah;
//                        echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<<<<<<<<<<<&nbsp;&nbsp;&nbsp;'. json_encode((object)$data_denah['DENAH'][$ruang]);
//                        echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<<<<<<<<<<<&nbsp;&nbsp;&nbsp;'. json_encode((object)$temp_data_siswa);
//                    }
                }
//                echo '<hr>$data_siswa<br>' . json_encode(count($data_siswa));
//
//                $jumlah = 0;
//                for ($id = 0; $id < count($data_denah['DENAH']); $id++) {
//                    $jumlah += count($data_denah['DENAH'][$id]);
////                    echo '<hr>$data_denah<br>' . json_encode(count($data_denah['DATA_SISWA'][$id]));
//                }
//                echo '<hr>$jumlah<br>' . json_encode($jumlah);
//                echo '<hr>$data_siswa<br>' . json_encode($data_siswa);
//                echo '<hr>DENAH<br>' . json_encode($data_denah['DENAH']);
//                exit();
            }
        }

        $data_peserta_us = $this->peserta->get_siswa_kartu();

//        echo '<hr>$data_siswa<br>' . json_encode(count($data_siswa));
//        echo '<hr>$data_peserta_us<br>' . json_encode(count($data_peserta_us));
//        exit();
//
//        if (count($data_peserta_us) != count($data_siswa)) {
//            echo '<h1>DATA SISWA BERBEDA ANTARA DENAH DENGAN DATABASE. SILAHKAN ULANGI KEMBALI PEMBUATAN DENAHNYA.</h1>';
//
//            exit();
//        }
        // MENYUSUN ULANG DATA SISWA AGAR SESUAI PERKELAS
        $data_siswa_final = array();
        foreach ($data_peserta_us as $detail_siswa) {
            $data_siswa_final[] = array(
                'ID' => $detail_siswa['ID_SISWA'],
                'AKAD_SISWA' => $detail_siswa,
                'DENAH' => $data_siswa[$detail_siswa['ID_SISWA']]
            );
        }

        $data['siswa'] = $data_siswa_final;

        $this->load->view('backend/pu/jadwal_us/cetak_kertu_siswa', $data);
    }

    public function cetak_sampul($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['data'][0]['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['data'][0]['JK_PUJ'] = $data_jadwal->JK_PUJ;
        $data['data'][0]['JAM_MULAI'] = $data_jadwal->JAM_MULAI_PUJ;
        $data['data'][0]['JAM_SELESAI'] = $data_jadwal->JAM_SELESAI_PUJ;
        $data['data'][0]['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);

        $this->load->view('backend/pu/jadwal_us/cetak_sampul', $data);
    }

    public function cetak_soal($id) {
        $data_jadwal = $this->jadwal->get_by_id($this->tipe, $id);
        $data['ID'] = $id;
        $data['TANGGAL'] = $data_jadwal->TANGGAL_PUJ;
        $data['JK_PUJ'] = $data_jadwal->JK_PUJ;
        $data['JAM_MULAI'] = $data_jadwal->JAM_MULAI_PUJ;
        $data['JAM_SELESAI'] = $data_jadwal->JAM_SELESAI_PUJ;
        $data['DENAH'] = $this->denah->get_denah_by_tanggal($data_jadwal->TANGGAL_PUJ);

        $this->generate->backend_view('pu/jadwal_us/cetak_soal', $data);
    }

    public function cetak_blanko_nilai() {
        $data['data'] = $this->peserta->get_data_blanko_nilai(FALSE);

        $this->load->view('backend/pu/jadwal_us/cetak_blanko_nilai', $data);
    }

    public function get_file_bat() {
        $input = $this->input->get();

        header("Content-Type: text/plain;");
        header('Content-Disposition: attachment; filename=' . $input['title'] . '.bat');

        $file_exp = explode(',', $input['file']);
        foreach ($file_exp as $pdf) {
            printf('"' . $input['exe'] . '" /n /s /h /t "' . $input['folder'] . $pdf . '"\r\n');
        }
    }

    public function get_mapel() {
        $this->generate->set_header_JSON();

        $data = $this->mapel->get_mapel_us($this->input->post('q'), $this->input->post('dept'), $this->input->post('tingk'), $this->input->post('jk'));

        $this->generate->output_JSON($data);
    }

    public function get_pengawas() {
        $this->generate->set_header_JSON();

        $data = $this->pengawas->get_pengawas($this->input->post('q'), $this->input->post('pengawas'));

        $this->generate->output_JSON($data);
    }

}
