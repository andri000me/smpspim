<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_handler {

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function format_output_single($pie_donut, $data, $label_x, $label_y, $names) {
        if ($pie_donut) {
            $x_label = array();
            $data_json = array();
            $data_names = array();
            $data_colors = array();
            $i = 0;
            foreach ($data as $detail) {
                if ($detail->data == 0)
                    continue;

                $data_json['data' . $i] = $detail->data;
                $data_names['data' . $i] = $detail->x_label;
                $data_colors['data' . $i] = '#' . $this->CI->crypt->randomColorDark();
                $i++;
            }

            $data_single = array(
                'data' => $data_json,
                'colors' => $data_colors,
                'label_x' => $label_x,
                'label_y' => $label_y,
                'names' => $data_names,
            );
        } else {
            $x_label = array();
            $data1 = array();
            foreach ($data as $detail) {
                if ($detail->data == 0)
                    continue;

                $x_label[] = $detail->x_label;
                $data1[] = $detail->data;
            }

            $data_single = array(
                'data' => array(
                    'x_label' => $x_label,
                    'data1' => $data1
                ),
                'colors' => array(
                    'data1' => '#' . $this->CI->crypt->randomColorDark()
                ),
                'label_x' => $label_x,
                'label_y' => $label_y,
                'names' => array(
                    'data1' => $names,
                ),
            );
        }

        return $data_single;
    }

    public function format_output_multiple_date($data, $mulai_tanggal, $akhir_tanggal, $label_x, $label_y, $names) {
        $x_label = array();
        $data_tanggal = array();
        $data_pattern = array();
        $data_colors = array();
        
        $tanggal = $mulai_tanggal;
        $i = 0;
        while ($tanggal != $akhir_tanggal) {
            $data_pattern['x_label'][] = $tanggal;
            $data_tanggal[$tanggal] = $i;
            
            $tanggal = date('Y-m-d', strtotime($tanggal .' +1 day'));
            $i++;
        }
        $data_pattern['x_label'][] = $tanggal;
        $data_tanggal[$tanggal] = $i;
        
        for ($j = 0; $j < count($names); $j++) {
            $data_colors['data'.$j] = '#' . $this->CI->crypt->randomColorDark();
            $data_pattern['data'.$j] = array_fill(0, $i + 1, 0);
        }
        
        $k = 0;
        foreach ($data as $detail) {
            foreach ($detail as $value) {
                $data_pattern['data'.$k][$data_tanggal[$value->x_label]] = $value->data;
            }
            $k++;
        }

        $data_multiple = array(
            'data' => $data_pattern,
            'colors' => $data_colors,
            'label_x' => $label_x,
            'label_y' => $label_y,
            'names' => $names,
        );

        return $data_multiple;
    }

}
