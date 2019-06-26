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
class Pembayaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'pembayaran_model' => 'pembayaran',
            'siswa_model' => 'siswa',
            'kode_nota_model' => 'kode_nota',
            'nota_model' => 'nota',
            'laporan_keuangan_model' => 'laporan',
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/pembayaran/form');
    }

    public function ajax_add_backup() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $status = TRUE;
        $msg = 'Pembayaran berhasil disimpan';
        $uri_cetak = 'keuangan/pembayaran/ajax_cetak';

        $pembayaran_nota = array();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $KETERANGAN = $this->input->post('KETERANGAN');
        $nominal = $this->input->post('NOMINAL');

//        $aturan_kode_nota = $this->kode_nota>get_by_key('PSB');
        // MENGAMBIL TAGIHAN SISWA DARI KEU_SETUP
        $data = $this->assign_tagihan->get_tagihan_siswa($ID_SISWA);

        foreach ($data as $detail) {
            if ($nominal == 0)
                break;
            $sisa_tagihan = $detail->NOMINAL_DT - $detail->BAYAR_SETUP;

            if ($sisa_tagihan <= $nominal) {
                $NOMINAL_BAYAR = $sisa_tagihan;
                $lunas = 1;
                $nominal -= $NOMINAL_BAYAR;
            } else {
                $NOMINAL_BAYAR = $nominal;
                $lunas = 0;
                $nominal = 0;
            }

            // SIMPAN PEMBAYARAN KE KEU_PEMBAYARAN

            $data_pembayaran = array(
                'JENIS_BAYAR' => 'PEMBAYARAN',
                'NOMINAL_BAYAR' => $NOMINAL_BAYAR,
                'SETUP_BAYAR' => $detail->ID_SETUP,
                'USER_BAYAR' => $this->session->userdata('ID_USER'),
                'KETERANGAN_BAYAR' => $KETERANGAN,
            );
            $status_pembayaran = $this->pembayaran->save($data_pembayaran);

            if ($status_pembayaran) {
                // UPDATE DATA SISA TAGIHAN SISWA DI KEU_DETAIL

                $data_setup = array(
                    'BAYAR_SETUP' => ($detail->BAYAR_SETUP + $NOMINAL_BAYAR),
                    'STATUS_SETUP' => $lunas
                );
                $where_setup = array(
                    'ID_SETUP' => $detail->ID_SETUP
                );
                $this->assign_tagihan->update($where_setup, $data_setup);

                $pembayaran_nota[] = $status_pembayaran;
            } else {
                $status = FALSE;
                $msg = 'Pembayaran gagal disimpan';
            }
        }

        if ($status) {
            $data_no_nota = $this->nota->get_no_terakhir($uri_cetak);
            $NOMOR_URUT = $data_no_nota->NOMOR_TERAKHIR + 1;
            $KODE_NOTA = $NOMOR_URUT . '/KEU/' . date('m/Y');
            $data_pemb = array(
                'ID_SISWA' => $ID_SISWA,
                'ID_PEMBAYARAN' => $pembayaran_nota,
                'KETERANGAN' => $KETERANGAN
            );

            $data_nota = array(
                'HAKAKSES_NOTA' => $this->session->userdata('ID_HAKAKSES'),
                'URUT_NOTA' => $NOMOR_URUT,
                'URI_NOTA' => $uri_cetak,
                'KODE_NOTA' => $KODE_NOTA,
                'DATA_NOTA' => json_encode($data_pemb),
            );
            $status_nota = $this->nota->save($data_nota);
        } else {
            $status_nota = 0;
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg, 'nota' => $status_nota));
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');

        $status = TRUE;
        $msg = 'Pembayaran berhasil disimpan';
        $uri_cetak = 'keuangan/pembayaran/ajax_cetak';

        $pembayaran_nota = array();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $KETERANGAN = $this->input->post('KETERANGAN');
        $PEMBAYARAN = $this->input->post('PEMBAYARAN');

        foreach ($PEMBAYARAN as $ID_SETUP) {
            $NOMINAL_BAYAR = $this->assign_tagihan->get_nominal_detail($ID_SETUP);
            $data_pembayaran = array(
                'NOMINAL_BAYAR' => $NOMINAL_BAYAR,
                'SETUP_BAYAR' => $ID_SETUP,
                'USER_BAYAR' => $this->session->userdata('ID_USER'),
                'KETERANGAN_BAYAR' => $KETERANGAN,
            );

            if (!$this->pembayaran->check_ketersediaan($ID_SETUP)) {
                if (isset($pembayaran_nota)) {
                    foreach ($pembayaran_nota as $id_bayar) {
                        $this->pembayaran->delete_by_id($id_bayar);
                    }
                }

                $status = FALSE;
                $msg = 'Pembayaran gagal disimpan';

                break;
            }

            $status_pembayaran = $this->pembayaran->save($data_pembayaran);

            if ($status_pembayaran) {
                // UPDATE DATA SISA TAGIHAN SISWA DI KEU_DETAIL

                $data_setup = array(
                    'BAYAR_SETUP' => $NOMINAL_BAYAR,
                    'STATUS_SETUP' => 1
                );
                $where_setup = array(
                    'ID_SETUP' => $ID_SETUP
                );
                $this->assign_tagihan->update($where_setup, $data_setup);

                $pembayaran_nota[] = $status_pembayaran;
            } else {
                if (isset($pembayaran_nota)) {
                    foreach ($pembayaran_nota as $id_bayar) {
                        $this->pembayaran->delete_by_id($id_bayar);
                    }
                }

                $status = FALSE;
                $msg = 'Pembayaran gagal disimpan';

                break;
            }
        }

        if ($status) {
            $data_no_nota = $this->nota->get_no_terakhir($uri_cetak);
            $NOMOR_URUT = $data_no_nota->NOMOR_TERAKHIR + 1;
            $KODE_NOTA = $NOMOR_URUT . '/KEU/' . date('m/Y');
            $data_pemb = array(
                'ID_SISWA' => $ID_SISWA,
                'ID_PEMBAYARAN' => $pembayaran_nota,
                'KETERANGAN' => $KETERANGAN
            );

            $data_nota = array(
                'HAKAKSES_NOTA' => $this->session->userdata('ID_HAKAKSES'),
                'URUT_NOTA' => $NOMOR_URUT,
                'URI_NOTA' => $uri_cetak,
                'KODE_NOTA' => $KODE_NOTA,
                'DATA_NOTA' => json_encode($data_pemb),
            );
            $status_nota = $this->nota->save($data_nota);

            foreach ($pembayaran_nota as $id_pembayaran) {
                $data_pemb = array(
                    'KODE_BAYAR' => $status_nota
                );
                $where_pemb = array(
                    'ID_BAYAR' => $id_pembayaran
                );

                $this->pembayaran->update($where_pemb, $data_pemb);
            }
        } else {
            $status_nota = 0;
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg, 'nota' => $status_nota));
    }

    public function ajax_cetak($id) {
        $data_nota = $this->nota->get_by_id($id);
        $status_psb = FALSE;

        $all_pembayaran = array();
        $data_pembayaran = json_decode($data_nota->DATA_NOTA);
        foreach ($data_pembayaran->ID_PEMBAYARAN as $ID_BAYAR) {
            $data_bayar = $this->pembayaran->get_by_id($ID_BAYAR);
            if ($data_bayar->PSB_TAG)
                $status_psb = TRUE;
            array_push($all_pembayaran, $data_bayar);
        }

        $data_siswa = $this->siswa->get_by_id($data_pembayaran->ID_SISWA);

        if ($status_psb)
            $status_lunas = $this->assign_tagihan->is_psb_lunas($data_pembayaran->ID_SISWA);
        else
            $status_lunas = '';

        $data = array(
            'NOTA' => $data_nota,
            'PEMBAYARAN' => $all_pembayaran,
            'SISWA' => $data_siswa,
            'STATUS_PSB' => $status_psb,
            'STATUS_LUNAS' => $status_lunas,
            'KETERANGAN' => $data_pembayaran->KETERANGAN
        );

        $this->load->view('backend/keuangan/pembayaran/cetak', $data);
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->pembayaran->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();

        $data = $this->pembayaran->get_all_ac($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function ac_siswa() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->get_ac_pembayaran($this->input->post('q'));

        $this->generate->output_JSON($data);
    }

    public function get_data_siswa() {
        $this->generate->set_header_JSON();

        $data = $this->siswa->get_by_id($this->input->post('ID_SISWA'));

        if (file_exists('files/siswa/' . $data->NIS_SISWA . '.jpg')) {
            $data->FOTO_SISWA = $data->NIS_SISWA . '.jpg';
        } elseif (file_exists('files/siswa/' . $data->ID_SISWA . '.png') || $data->FOTO_SISWA != NULL) {
            $data->FOTO_SISWA = $data->ID_SISWA . '.png';
        }

        $this->generate->output_JSON($data);
    }

    public function get_data_tagihan() {
        $this->generate->set_header_JSON();

        $data = $this->assign_tagihan->get_tagihan_siswa($this->input->post('ID_SISWA'));

        $this->generate->output_JSON($data);
    }

    public function get_data_nota() {
        $this->generate->set_header_JSON();

        $data = $this->db_handler->get_rows('keu_pembayaran',
                [
                    'where' => [
                        'SISWA_SETUP' => $this->input->post('ID_SISWA'),
                    ],
                    'group_by' => ['KODE_BAYAR'],
                    'order_by' => [
                        'CREATED_BAYAR' => 'DESC'
                    ]
                ],
                "KODE_BAYAR, DATE_FORMAT(CREATED_BAYAR,'%d-%m-%Y %H:%i:%s') AS TANGGAL",
                [
                    ['keu_setup', 'SETUP_BAYAR=ID_SETUP'],
                    ['keu_detail', 'DETAIL_SETUP=ID_DT'],
                    ['keu_tagihan', 'TAGIHAN_DT=ID_TAG'],
                ]
        );

        $this->generate->output_JSON($data);
    }

}
