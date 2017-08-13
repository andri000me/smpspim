<?php

/**
 * Description of Crypt
 *
 * @author rohmad
 */
class Crypt {

    //put your code here
    // MD5 hash for +-{[API$AN-NAMIROH*TRAVEL]}= is : 13aec2244fb406dda69513b43b9aca48
    private $iv = '17caa1acaa2b50ae'; #Same as in JAVA
    private $key = '13aec2244fb406dda69513b43b9aca48'; #Same as in JAVA

    function __construct() {
        
    }

    /**
     * @param string $str
     * @param bool $isBinary whether to encrypt as binary or not. Default is: false
     * @return string Encrypted data
     */
    function encrypt($str, $isBinary = false) {
        $iv = $this->iv;
        $str = json_encode($str);
        $str = $isBinary ? $str : utf8_decode($str);

        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $isBinary ? $encrypted : bin2hex($encrypted);
    }

    /**
     * @param string $code
     * @param bool $isBinary whether to decrypt as binary or not. Default is: false
     * @return string Decrypted data
     */
    function decrypt($code, $isBinary = false) {
        $code = $isBinary ? $code : $this->hex2bin($code);
        $iv = $this->iv;

        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);

        mcrypt_generic_init($td, $this->key, $iv);
        $decrypted = mdecrypt_generic($td, $code);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        $return = $isBinary ? trim($decrypted) : utf8_encode(trim($decrypted));

        return json_decode($return, true);
    }

    protected function hex2bin($hexdata) {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

    public function encryptPassword($string) {
        return md5($string);
    }

    public function encryptDefaultPassword() {
        return md5('12345');
    }

    public function randomString($length = 25, $char_kapital = FALSE) {
        if ($char_kapital)
            $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
        else
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function randomColor() {
        $color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

        return strtoupper($color);
    }

    public function randomColorDark() {
        do {
            $hex = $this->randomColor();
            $luminance = $this->colorLuminanceLight($hex);
        } while (!$luminance);
        
        return $hex;
    }

    public function randomColorLight() {
        do {
            $hex = $this->randomColor();
            $luminance = $this->colorLuminanceLight($hex);
        } while ($luminance);
        
        return $hex;
    }

    private function colorLuminanceLight($hex) {
        $luminance = 0.3 * hexdec(substr($hex, 0, 2)) + 0.59 * hexdec(substr($hex, 2, 2)) + 0.11 * hexdec(substr($hex, 4, 2));
        
        if ($luminance < 128) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function colourBrightness($hex, $percent) {
        $rgb = array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));

        for ($i = 0; $i < 3; $i++) {
            if ($percent > 0) { // Lighter
                $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
            } else { // Darker
                $positivePercent = $percent - ($percent * 2);
                $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1 - $positivePercent));
            }
            if ($rgb[$i] > 255) {
                $rgb[$i] = 255;
            }
        }

        for ($i = 0; $i < 3; $i++) {
            $hexDigit = dechex($rgb[$i]);
            if (strlen($hexDigit) == 1) {
                $hexDigit = "0" . $hexDigit;
            }
            $hex .= $hexDigit;
        }

        return $hex;
    }

}
