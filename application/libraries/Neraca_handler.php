<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Neraca_handler {
    
    public function __construct() {
        $this->CI = & get_instance();
        
        $this->CI->load->model(array(
            'neraca_model' => 'neraca',
        ));
    }
    
    public function proses() {
//        $this->kalkulasi_tagihan();
        $this->kalkulasi_tuk();
    }
    
    private function kalkulasi_tuk() {
        $data = $this->CI->neraca->get_jurnal();
        foreach ($data as $detail) {
            $nominal_sekarang = $this->CI->neraca->get_nominal_neraca($detail->KELOMPOK_TJ);
            $this->CI->neraca->ubah_status_neraca($detail->KELOMPOK_TJ, $nominal_sekarang + $detail->TOTAL);
            $this->CI->neraca->ubah_status_jurnal($detail->ID_TJ);
        }
    }
    
    private function kalkulasi_tagihan() {
        $data_pembayaran = $this->CI->neraca->get_pembayaran();
        $this->CI->neraca->ubah_status_pembayaran($data_pembayaran->ID_BAYAR);
        
        $data_pengembalian = $this->CI->neraca->get_pengembalian();
        $this->CI->neraca->ubah_status_pembayaran($data_pengembalian->ID_BAYAR);
        
        $nominal_sekarang = $this->CI->neraca->get_nominal_pembayaran();
        
        $this->CI->neraca->ubah_status_tagihan($nominal_sekarang + $data_pembayaran->TOTAL - $data_pengembalian->TOTAL);
    }
}