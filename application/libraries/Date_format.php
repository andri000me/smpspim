<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Date_format {

    public function to_store_db($data) {
//        $timestamp = strtotime($data);
//        if ($timestamp === FALSE) {
//            $tanggal_exp = explode("/", $data);
//            
//            return $tanggal_exp[2].'-'.$tanggal_exp[1].'-'.$tanggal_exp[0];
//        }

//        return date("Y-m-d", strtotime($data));
        
        return $data;
    }

    public function to_view($data) {
        return date("Y-m-d", strtotime($data));
    }

    public function to_print($data) {
        return date("d-m-Y", strtotime($data));
    }

    public function to_print_short($data) {
        return date("j-n-Y", strtotime($data));
    }

    public function to_print_text($data) {
        $bulan = array(
            '',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'Nopember',
            'Desember',
        );

        return date("j", strtotime($data)) . ' ' . $bulan[date("n", strtotime($data))] . ' ' . date("Y", strtotime($data));
    }

    public function get_day($data) {
        $hari = array(
            ' ',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            "Jum'at",
            "Sabtu",
            "Ahad",
        );

        return $hari[date('N', strtotime($data))];
    }

    public function toRomawi($n) {
        $hasil = "";
                
        $iromawi = array("","I","II","III","IV","V","VI","VII","VIII","IX","X",20=>"XX",30=>"XXX",40=>"XL",50=>"L",60 => "LX",70=>"LXX",80=>"LXXX",90=>"XC",100=>"C",200=>"CC",300=>"CCC",400=>"CD",500=>"D",600=>"DC",700=>"DCC",800 => "DCCC",900=>"CM",1000=>"M",2000=>"MM",3000=>"MMM");
        if (array_key_exists($n, $iromawi)) {
            $hasil = $iromawi[$n];
        } elseif ($n >= 11 && $n <= 99) {
            $i = $n % 10;
            $hasil = $iromawi[$n - $i] . $this->toRomawi($n % 10);
        } elseif ($n >= 101 && $n <= 999) {
            $i = $n % 100;
            $hasil = $iromawi[$n - $i] . $this->toRomawi($n % 100);
        } else {
            $i = $n % 1000;
            $hasil = $iromawi[$n - $i] . $this->toRomawi($n % 1000);
        }
        
        return $hasil;
    }

}
