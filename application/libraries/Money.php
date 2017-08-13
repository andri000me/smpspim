<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Money {

    public function format($nominal) {
        return "Rp " . number_format($nominal, 2, ",", ".");
    }

    public function terbilang($angka) {
        $bilangan = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($angka < 12)
            return " " . $bilangan[$angka];
        elseif ($angka < 20)
            return $this->terbilang($angka - 10) . "belas";
        elseif ($angka < 100)
            return $this->terbilang($angka / 10) . " puluh" . $this->terbilang($angka % 10);
        elseif ($angka < 200)
            return " seratus" . $this->terbilang($angka - 100);
        elseif ($angka < 1000)
            return $this->terbilang($angka / 100) . " ratus" . $this->terbilang($angka % 100);
        elseif ($angka < 2000)
            return " seribu" . $this->terbilang($angka - 1000);
        elseif ($angka < 1000000)
            return $this->terbilang($angka / 1000) . " ribu" . $this->terbilang($angka % 1000);
        elseif ($angka < 1000000000)
            return $this->terbilang($angka / 1000000) . " juta" . $this->terbilang($angka % 1000000);
    }

    public function terbilang_simpel($angka) {
        $bilangan = array(
            "." => "Koma", 
            "," => "Koma", 
            "1" => "Satu", 
            "2" => "Dua", 
            "3" => "Tiga", 
            "4" => "Empat", 
            "5" => "Lima", 
            "6" => "Enam", 
            "7" => "Tujuh", 
            "8" => "Delapan", 
            "9" => "Sembilan",
            "0" => "Nol",
        );
        
        $angka = strval($angka);
        $result = '';
        for ($i = 0; $i < strlen($angka); $i++) {
            $result .= $bilangan[$angka[$i]].' ';
        }
        
        return $result;
    }

}
