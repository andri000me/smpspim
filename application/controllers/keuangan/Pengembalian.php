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
class Pengembalian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'detail_tagihan_model' => 'detail_tagihan',
            'assign_tagihan_model' => 'assign_tagihan',
            'pengembalian_model' => 'pengembalian',
            'siswa_model' => 'siswa',
            'kode_nota_model' => 'kode_nota',
            'nota_model' => 'nota',
        ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/pengembalian/form');
    }

    public function ajax_add() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');
        
        $status = TRUE;
        $msg = 'Pengembalian berhasil disimpan';
        $uri_cetak = 'keuangan/pengembalian/ajax_cetak';
        
        $pengembalian_nota = array();
        
        $ID_SISWA = $this->input->post('ID_SISWA');
        $KETERANGAN = $this->input->post('KETERANGAN');
        $PEMBAYARAN = $this->input->post('PEMBAYARAN');
        
        foreach ($PEMBAYARAN as $ID_SETUP) {
            $NOMINAL_BAYAR = $this->assign_tagihan->get_nominal_detail($ID_SETUP);
            $data_pengembalian = array(
                'JENIS_BAYAR' => 'PENGEMBALIAN',
                'NOMINAL_BAYAR' => $NOMINAL_BAYAR,
                'SETUP_BAYAR' => $ID_SETUP,
                'USER_BAYAR' => $this->session->userdata('ID_USER'),
                'KETERANGAN_BAYAR' => $KETERANGAN,
            );

            if (!$this->pengembalian->check_ketersediaan($ID_SETUP)) {
                if (isset($pengembalian_nota)) {
                    foreach ($pengembalian_nota as $id_bayar) {
                        $this->pengembalian->delete_by_id($id_bayar);
                    }
                }
                
                $status = FALSE;
                $msg = 'Pembayaran gagal disimpan';

                break;
            }

            $status_pengembalian = $this->pengembalian->save($data_pengembalian);
            
            if ($status_pengembalian) {
                // UPDATE DATA SISA TAGIHAN SISWA DI KEU_DETAIL
                
                $data_setup = array(
                    'BAYAR_SETUP' => 0,
                    'STATUS_SETUP' => 0
                );
                $where_setup = array(
                    'ID_SETUP' => $ID_SETUP
                );
                $this->assign_tagihan->update($where_setup, $data_setup);
                
                $pengembalian_nota[] = $status_pengembalian;
            } else {
                if (isset($pengembalian_nota)) {
                    foreach ($pengembalian_nota as $id_bayar) {
                        $this->pengembalian->delete_by_id($id_bayar);
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
            $KODE_NOTA = $NOMOR_URUT.'/KEU/'.date('m/Y');
            $data_pemb = array(
                'ID_SISWA' => $ID_SISWA,
                'ID_PEMBAYARAN' => $pengembalian_nota,
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
            
            foreach ($pengembalian_nota as $id_pengembalian) {
                $data_pemb = array(
                    'KODE_BAYAR' => $status_nota
                );
                $where_pemb = array(
                    'ID_BAYAR' => $id_pengembalian
                );
                
                $this->pengembalian->update($where_pemb, $data_pemb);
            }
        } else {
            $status_nota = 0;
        }

        $this->generate->output_JSON(array("status" => $status, 'msg' => $msg, 'nota' => $status_nota));
    }
    
    public function ajax_cetak($id) {
        $data_nota = $this->nota->get_by_id($id);
        
        $all_pengembalian = array();
        $data_pengembalian = json_decode($data_nota->DATA_NOTA);
        foreach ($data_pengembalian->ID_PEMBAYARAN as $ID_BAYAR) {
            $data_bayar = $this->pengembalian->get_by_id($ID_BAYAR);
            array_push($all_pengembalian, $data_bayar);
        }
        
        $data_siswa = $this->siswa->get_by_id($data_pengembalian->ID_SISWA);
        
        $data = array(
            'NOTA' => $data_nota,
            'PEMBAYARAN' => $all_pengembalian,
            'SISWA' => $data_siswa,
            'KETERANGAN' => $data_pengembalian->KETERANGAN
        );
        
        $this->load->view('backend/keuangan/pengembalian/cetak', $data);
    }

    public function ajax_delete() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_form('delete');

        $id = $this->input->post("ID");
        $affected_row = $this->pengembalian->delete_by_id($id);

        $this->generate->output_JSON(array("status" => $affected_row));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->pengembalian->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

    public function ac_siswa() {
        $this->generate->set_header_JSON();
        
        $data = $this->pengembalian->get_siswa($this->input->post('q'));
        
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
        
        $data = $this->pengembalian->get_tagihan_siswa($this->input->post('ID_SISWA'));
        
        $this->generate->output_JSON($data);
    }

}
