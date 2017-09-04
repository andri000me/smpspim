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
class Laporan_poin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model(array(
            'pelanggaran_header_model' => 'laporan_poin',
            'pelanggaran_model' => 'pelanggaran',
            'jenis_tindakan_model' => 'jenis_tindakan',
            'laporan_tindakan_model' => 'tindakan',
            'departemen_model' => 'dept',
            'kelas_model' => 'kelas',
            'pondok_siswa_model' => 'pondok_siswa',
            'laporan_surat_segera_model' => 'surat_segera',
        ));
        $this->load->library('pelanggaran_handler');
        $this->auth->validation(7);
    }

    public function index() {
        $this->generate->backend_view('komdis/laporan_poin/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $tindakan = $this->jenis_tindakan->get_all(FALSE);
        $list = $this->laporan_poin->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $aksi = '';
            $row = array();
//            $row[] = $item->NAMA_CAWU;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->POIN_KSH;
            $row[] = $item->LARI_KSH;
            $row[] = $item->SURAT;

//            foreach ($tindakan as $detail) {
//                if($this->tindakan->sudah_ditindak($item->ID_KSH, $detail->ID_KJT) AND $item->POIN_KSH $item $detail->POIN_KJT) { 
            $row[] = '<button type="button" class="btn btn-primary btn-sm" onclick="cetak(' . $item->ID_KSH . ');"><i class="fa fa-print"></i></button>&nbsp;';
//                }
//            }
//
//            $row[] = $aksi;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_poin->count_all(),
            "recordsFiltered" => $this->laporan_poin->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function request_form() {
        $data = $this->generate->set_header_JSON();

        $data_html = array(
            array(
                'label' => 'Penanggungjawab',
                'required' => TRUE,
                'keterangan' => 'Wajib diisi',
                'length' => 7,
                'data' => array(
                    'type' => 'autocomplete',
                    'name' => 'PENANGGUNGJAWAB_KT',
                    'multiple' => FALSE,
                    'minimum' => 1,
                    'value' => $data == NULL ? "" : $data->PENANGGUNGJAWAB_KT,
                    'label' => $data == NULL ? "" : $data->NAMA_PEG,
                    'data' => NULL,
                    'url' => base_url('master_data/pegawai/auto_complete')
                )
            )
        );

        $this->generate->output_JSON(array('STATUS' => TRUE, 'DATA' => $data_html));
    }

    public function ajax_add() {
//        $this->generate->set_header_JSON();

        $data = $this->input->get();

        $nomor_surat = $this->pengaturan->getNomorSuratKomdis($data['URL_KJT']);
        $nomor_paket_sp = $this->pengaturan->getNomorPaketSP();

        $input_kolektif = $data['KOLEKTIF_KJT'];
        $where_surat_segera = array(
            'ID_KJT' => $data['TINDAKAN_KT']
        );
        $data_kolektif = $this->surat_segera->get_rows($where_surat_segera);

        $data['USER_KT'] = $this->session->userdata('ID_USER');
        $data['TANGGAL_KT'] = date("Y-m-d");
        $data['PAKET_SP_KT'] = NULL;

        $jenis_surat = $data['URL_KJT'];

        unset($data['URL_KJT']);
        unset($data['KOLEKTIF_KJT']);
        var_dump($nomor_surat);
        $start = TRUE;
        if ($input_kolektif) {
            foreach ($data_kolektif as $detail) {
                if ($start || $data['TINDAKAN_KT'] == 1)
                    $data['NOMOR_SURAT_KT'] = $nomor_surat;
                if ($data['TINDAKAN_KT'] == 1)
                    $data['PAKET_SP_KT'] = $nomor_paket_sp;

                $data['PELANGGARAN_HEADER_KT'] = $detail->ID_KSH;

                $id = $this->tindakan->save($data);

                if ($id) {
                    if ($data['TINDAKAN_KT'] == 5)
                        $data_update = array('PROSES_MUTASI_KSH' => 1);
                    elseif ($data['TINDAKAN_KT'] == 4)
                        $data_update = array('PROSES_TAKLIQ_KSH' => 1);
                    $data_where = array('ID_KSH' => $detail->ID_KSH);

                    if (isset($data_update))
                        $this->laporan_poin->update($data_where, $data_update);
                }

                $start = FALSE;
                $nomor_surat++;
            }
        } else {
            $data['NOMOR_SURAT_KT'] = $nomor_surat;
            $id = $this->tindakan->save($data);
            $nomor_surat++;
        }

        if ($data['TINDAKAN_KT'] == 1) {
            $id = $nomor_paket_sp;
            $this->pengaturan->setNomorPaketSP($nomor_paket_sp + 1);
        }

        $this->pengaturan->setNomorSuratKomdis($jenis_surat, $nomor_surat);

        redirect(site_url('komdis/laporan_poin/cetak_surat/' . $id . '/' . $data['TINDAKAN_KT']));
    }

    public function cetak($ID_KSH) {
        $where = array('ID_KSH' => $ID_KSH);
        $siswa = $this->laporan_poin->get_full_by_id($where);
        $data = array();

        if (count($siswa) == 1) {
            foreach ($siswa as $detail) {
                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                $data['data'][] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        }

        $this->load->view('backend/komdis/laporan_poin/cetak', $data);
    }

    public function cetak_ringan_perkelas() {
        $input_kelas = $this->input->get('KELAS');
        $data = array();

        if ($input_kelas != "") {
            $kelas_exp = explode(',', $input_kelas);
            foreach ($kelas_exp as $ID_KELAS) {
                $where = array('ID_KELAS' => $ID_KELAS);
                $siswa = $this->laporan_poin->get_full_by_id($where);
                $tindakan = $this->jenis_tindakan->get_by_id(1);
                
                foreach ($siswa as $detail) {
                    if ($detail->JUMLAH_POIN_KSH > $tindakan->POIN_MAKS_KJT)
                        continue;

                    $where = array(
                        'TA_KS' => $detail->TA_KSH,
                        'SISWA_KS' => $detail->SISWA_KSH,
                    );
                    $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                    $data['data'][$ID_KELAS][] = array(
                        'siswa' => $detail,
                        'pelanggaran' => $pelanggaran
                    );
                }
            }
        }

//        $where = array('ID_KELAS' => $ID_KELAS);
//        $siswa = $this->laporan_poin->get_full_by_id($where);
//        $tindakan = $this->jenis_tindakan->get_by_id(1);
//        $data = array();
//
//        foreach ($siswa as $detail) {
//            if ($detail->JUMLAH_POIN_KSH > $tindakan->POIN_MAKS_KJT)
//                continue;
//
//            $where = array(
//                'TA_KS' => $detail->TA_KSH,
//                'SISWA_KS' => $detail->SISWA_KSH,
//            );
//            $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);
//
//            $data['data'][] = array(
//                'siswa' => $detail,
//                'pelanggaran' => $pelanggaran
//            );
//        }

        $this->load->view('backend/komdis/laporan_poin/cetak', $data);
    }

    public function cetak_pertindakan($ID_KJT) {
        $data = array();

        if ($ID_KJT != 0) {
            $where = array('kjt.ID_KJT' => $ID_KJT);
            $siswa = $this->laporan_poin->get_full_by_id($where);
            foreach ($siswa as $detail) {
                $where = array(
                    'TA_KS' => $detail->TA_KSH,
                    'SISWA_KS' => $detail->SISWA_KSH,
                );
                $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran($where);

                $data['data'][] = array(
                    'siswa' => $detail,
                    'pelanggaran' => $pelanggaran
                );
            }
        }

        $this->load->view('backend/komdis/laporan_poin/cetak', $data);
    }

    public function hapus_surat($ID_KT) {
        $this->generate->set_header_JSON();

        $result = $this->tindakan->hapus_surat($ID_KT);

        $this->generate->output_JSON(array('status' => $result));
    }

    public function cetak_surat($ID_KT, $TINDAKAN_KT) {
        $data = array(
            'nama_panitia' => 'KOMISI DISIPLIN SISWA'
        );

        if ($TINDAKAN_KT == 1) {
            $data_tindakan = $this->tindakan->get_data_tindakan_sp($ID_KT);
            foreach ($data_tindakan as $detail) {
                if ($detail['DATA_KT'] == NULL) {
                    $data_array = array(
                        'nomor_surat' => $this->pengaturan->getFormatSurat($detail['NOMOR_SURAT_KT'], $detail['URL_KJT'], $detail['TANGGAL_KT'], 'KOMDIS'),
                        'NAMA_SISWA' => $detail['NAMA_SISWA'],
                        'NAMA_KELAS' => $detail['NAMA_KELAS'],
                        'ALAMAT_SISWA' => $detail['ALAMAT_SISWA'],
                        'NAMA_KEC' => $detail['NAMA_KEC'],
                        'NAMA_KAB' => $detail['NAMA_KAB'],
                        'NAMA_PROV' => $detail['NAMA_PROV'],
                        'PONDOK_SISWA' => $detail['PONDOK_SISWA'],
                        'NAMA_PONDOK_MPS' => $detail['NAMA_PONDOK_MPS'],
                        'ALAMAT_MPS' => $detail['ALAMAT_MPS'],
                        'POIN_KSH' => $detail['POIN_KSH'],
                        'WALI_KELAS' => $detail['WALI_KELAS'],
                        'NAMA_TANGGUNGJAWAB' => $detail['NAMA_TANGGUNGJAWAB'],
                        'GELAR_AWAL_TANGGUNGJAWAB' => $detail['GELAR_AWAL_TANGGUNGJAWAB'],
                        'GELAR_AKHIR_TANGGUNGJAWAB' => $detail['GELAR_AKHIR_TANGGUNGJAWAB'],
                        'tanggal' => $this->date_format->to_view($detail['TANGGAL_KT'])
                    );

                    $where_update = array('NOMOR_SURAT_KT' => $detail['NOMOR_SURAT_KT']);
                    $data_update = array('DATA_KT' => json_encode($data_array));

                    $this->tindakan->update($where_update, $data_update);

                    $data['data_tindakan'][] = $data_array;
                } else {
                    $data['data_tindakan'][] = json_decode($detail['DATA_KT'], TRUE);
                }
            }

            $data_tindakan = array('URL_KJT' => 'sp');
        } else {
            $data_tindakan = $this->tindakan->get_by_id($ID_KT);
            $data_tindakan = (array) $data_tindakan;
            if ($data_tindakan['DATA_KT'] == NULL) {
                if ($data_tindakan['KOLEKTIF_KJT']) {
                    $data['NAMA_TANGGUNGJAWAB'] = $data_tindakan['NAMA_TANGGUNGJAWAB'];
                    $data['GELAR_AWAL_TANGGUNGJAWAB'] = $data_tindakan['GELAR_AWAL_TANGGUNGJAWAB'];
                    $data['GELAR_AKHIR_TANGGUNGJAWAB'] = $data_tindakan['GELAR_AKHIR_TANGGUNGJAWAB'];

                    $data['JENJANG'] = array();
                    $data['data'] = array();

                    $data_siswa = $this->tindakan->get_detail_kolektif($data_tindakan['ID_KJT'], $data_tindakan['NOMOR_SURAT_KT']);
                    if ($data_tindakan['POIN_KSH'] >= 100) {
                        foreach ($data_siswa as $data_kolektif) {
                            $data_syariah = $this->pelanggaran_handler->cek_pelanggaran_syariah($data_kolektif['ID_SISWA']);

                            if ($data_syariah == NULL) {
                                $data['JENJANG']['NON-SYARIAH'][$data_kolektif['ID_DEPT']] = $data_kolektif['NAMA_DEPT'];
                                $data['data']['NON-SYARIAH'][$data_kolektif['ID_DEPT']][] = $data_kolektif;
                            } else {
                                $data['JENJANG']['SYARIAH'][$data_kolektif['ID_DEPT']] = $data_kolektif['NAMA_DEPT'];
                                $data['JENIS_PELANGGARAN'] = $data_syariah;

                                $data['data']['SYARIAH'][$data_kolektif['ID_DEPT']][] = $data_kolektif;
                            }
                        }
                    } else {
                        foreach ($data_siswa as $data_kolektif) {
                            $data['JENJANG'][$data_kolektif['ID_DEPT']] = $data_kolektif['NAMA_DEPT'];
                            $data['data'][$data_kolektif['ID_DEPT']][] = $data_kolektif;
                        }
                    }

                    foreach ($data_siswa as $detail) {
                        $where = array(
                            'TA_KS' => $detail['TA_KSH'],
                            'SISWA_KS' => $detail['SISWA_KSH'],
                        );
                        $pelanggaran = $this->pelanggaran->get_cetak_pelanggaran_array($where);

                        $data['DETAIL_PELANGGARAN'][] = array(
                            'siswa' => $detail,
                            'pelanggaran' => $pelanggaran
                        );
                    }
                } else {
                    $data['data'] = $data_tindakan;
                }

                if ($data['data'] == NULL) {
                    echo '<h1>DATA SISWA TIDAK LENGKAP. PERIKSA TERLEBIH DAHULU KELAS SISWA DAN ALAMAT SISWA</h1>';
                    exit();
                }

                $data['tanggal'] = $this->date_format->to_view($data_tindakan['TANGGAL_KT']);
                $data['nomor_surat'] = $this->pengaturan->getFormatSurat($data_tindakan['NOMOR_SURAT_KT'], $data_tindakan['URL_KJT'], $data_tindakan['TANGGAL_KT'], 'KOMDIS');

                $where_update = array('NOMOR_SURAT_KT' => $data_tindakan['NOMOR_SURAT_KT'], 'TINDAKAN_KT' => $TINDAKAN_KT);
                $data_update = array('DATA_KT' => json_encode($data));

                $this->tindakan->update($where_update, $data_update);
            } else {
                $data = json_decode($data_tindakan['DATA_KT'], TRUE);
            }
        }

        $this->load->view('backend/komdis/laporan_poin/cetak_' . $data_tindakan['URL_KJT'], $data);
    }

    public function cetak_perkelas($ID_KELAS) {
        $input_kelas = $this->input->get('KELAS');
        $data = array();

        if ($input_kelas != "") {
            $kelas_exp = explode(',', $input_kelas);
            foreach ($kelas_exp as $ID_KELAS) {
                $data['data'][] = array(
                    'KELAS' => $this->kelas->get_by_id($ID_KELAS),
                    'DATA' => $this->laporan_poin->get_data_perkelas($ID_KELAS),
                );
            }
            $data['TANGGAL'] = $this->laporan_poin->get_terakhir_input();
        }

//        if ($ID_KELAS != 0) {
//            $data = array(
//                'KELAS' => $this->kelas->get_by_id($ID_KELAS),
//                'DATA' => $this->laporan_poin->get_data_perkelas($ID_KELAS),
//                'TANGGAL' => $this->laporan_poin->get_terakhir_input()
//            );
//        }

        $this->load->view('backend/komdis/laporan_poin/cetak_perkelas', $data);
    }

    public function cetak_perpondok($ID_PONDOK) {
        if ($ID_PONDOK != 0) {
            $data = array(
                'PONDOK' => $this->pondok_siswa->get_by_id($ID_PONDOK),
                'DATA' => $this->laporan_poin->get_data_perkelas($ID_PONDOK, FALSE),
                'TANGGAL' => $this->laporan_poin->get_terakhir_input()
            );
        }

        $this->load->view('backend/komdis/laporan_poin/cetak_perpondok', $data);
    }

}
