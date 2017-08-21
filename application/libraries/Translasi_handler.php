<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Translasi_handler {

    public function __construct() {
        $this->CI = & get_instance();

        $this->CI->load->model(array(
            'kamus_model' => 'kamus',
        ));
    }

    public function proses($NAMA_SISWA) {
        $arab = '';
        $text = trim($NAMA_SISWA);
        $text = strtoupper($text);
        $text = str_replace("`", "'", $text);
        $text = str_replace(".", " ", $text);

        $parsing = explode(" ", $text);

        foreach ($parsing as $latin) {
            if ($latin == '')
                continue;

            $result = $this->CI->kamus->get_text($latin);

            if ($result == NULL)
                $arab .= $this->pegon($latin) . ' ';
            else
                $arab .= $result;
        }

        return trim($arab);
    }

    private function pegon($latin, $sambung = FALSE) {
        $pegon = '';
        $konversi = array(
            '1' => '١',
            '2' => '٢',
            '3' => '٣',
            '4' => '٤',
            '5' => '٥',
            '6' => '٦',
            '7' => '٧',
            '8' => '٨',
            '9' => '٩',
            '0' => '٠',
            'A' => 'ا',
            'B' => 'ب',
            'C' => 'ج',
            'D' => 'د',
            'E' => 'اي',
            'F' => 'ف',
            'G' => 'ك',
            'H' => 'ه',
            'I' => 'ي',
            'J' => 'ج',
            'K' => 'ك',
            'L' => 'ل',
            'M' => 'م',
            'N' => 'ن',
            'O' => 'او',
            'P' => 'ف',
            'Q' => 'ق',
            'R' => 'ر',
            'S' => 'س',
            'T' => 'ت',
            'U' => 'و',
            'V' => 'ف',
            'W' => 'ى',
            'X' => 'ك',
            'Y' => 'ي',
            'Z' => 'ز',
            "'" => 'ع',
            "/" => '/',
            "." => '',
        );
        $konversi_depan = array(
            'I' => 'إ',
            'A' => 'أ',
            'U' => 'أ',
        );
        $konversi_double = array(
            'TS' => 'ث',
            'KH' => 'خ',
            'SY' => 'ش',
            'SH' => 'ص',
            'DH' => 'ض',
            'TH' => 'ط',
            'TZ' => 'ظ',
            'GH' => 'غ',
            "'A" => 'ع',
        );

        $latin_split = str_split($latin);
        $i = -1;
        $merge = FALSE;
        $tasydid = FALSE;
        $tasydid_double = FALSE;
        foreach ($latin_split as $char) {
            if (isset($konversi[$char]) || isset($konversi_depan[$char]) || isset($konversi_double[$char])) {
                if (is_numeric($char)) {
                    $pegon .= $konversi[$char];
                } else {
                    $i++;

                    if ($merge) {
                        $merge = FALSE;
                        $tasydid = FALSE;
                        continue;
                    }
                    if ($tasydid) {
                        $merge = FALSE;
                        $tasydid = FALSE;
                        continue;
                    }
                    if ($tasydid_double) {
                        $merge = FALSE;
                        $tasydid_double = FALSE;
                        $tasydid = TRUE;
                        continue;
                    }

                    if ($i == 0) {
                        if (isset($konversi_depan[$char]) && !$sambung) {
                            $pegon .= $konversi_depan[$char];
                        } else {
                            if (isset($latin_split[$i + 1]) && isset($konversi_double[$latin_split[$i] . $latin_split[$i + 1]])) {
                                $pegon .= $konversi_double[$latin_split[$i] . $latin_split[$i + 1]];
                                $merge = TRUE;
                            } else {
                                $pegon .= $konversi[$char];
                            }
                        }
                    } else {
                        if (isset($latin_split[$i + 1]) && isset($konversi_double[$latin_split[$i] . $latin_split[$i + 1]])) {
                            $pegon .= $konversi_double[$latin_split[$i] . $latin_split[$i + 1]];
                            $merge = TRUE;
                        } else {
                            $pegon .= $konversi[$char];
                        }
                    }

                    if ($merge) {
                        if (isset($latin_split[$i + 1]) && isset($latin_split[$i + 2]) && isset($latin_split[$i + 3]) && ($latin_split[$i] == $latin_split[$i + 2] && $latin_split[$i + 1] == $latin_split[$i + 3])) {
                            $tasydid_double = TRUE;
                        }
                    } else {
                        if (isset($latin_split[$i + 1]) && ($latin_split[$i] == $latin_split[$i + 1])) {
                            $tasydid = TRUE;
                        }
                    }
                }
            } else {
                $pegon .= ' ['.$char.'] ';
//                break;
            }
        }

        return $pegon;
    }

    public function terbilang($angka) {
        $bilangan = array(
            "1" => "وَاجِدة", 
            "2" => "إِثْنَانِ", 
            "3" => "ثَلَاثَة", 
            "4" => "رَابِعَة", 
            "5" => "خَمْسَة", 
            "6" => "سِتّة", 
            "7" => "سَبْعَة", 
            "8" => "ثَمَانِيَة", 
            "9" => "تِسْعَة",
            "0" => "-",
        );
        
        $angka = strval($angka);
        $result = '';
        for ($i = 0; $i < strlen($angka); $i++) {
            $result .= $bilangan[$angka[$i]].' ';
        }
        
        return $result;
    }

}
