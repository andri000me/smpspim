<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=nilai_um_" . date('Y-m-d_H-i-s') . ".xls");
?>
<table>
    <thead>
        <tr>
            <th colspan="12">NILAI PSB TAHUN <?php echo $tahun; ?></th>
        </tr>
        <tr></tr>

        <tr>
            <th>No</th>
            <th>No UM</th>
            <th>Nama</th>
            <?php  
            foreach ($mapel as $detail) {
                echo '<th>'.$detail['NAMA_MAPEL'].' ['.$detail['JENIS_PUM'].']</th>';
            }
            ?>
            <th>Rata-rata Tulis</th>
            <th>Rata-rata Lisan</th>
            <th>Jenjang Lulus</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $nilai_lulus_psb = json_decode($this->pengaturan->getNilaiLulusPSB(), TRUE);
        
        $no = 1;
        foreach ($siswa as $item) {
            echo '<tr>';
            echo '<td>'.$no++.'</td>';
            echo '<td>'.$this->pengaturan->getKodeUM($item).'</td>';
            echo '<td>'.$item->NAMA_SISWA.'</td>';
            
            $jumlah_mapel_lisan = 0;
            $total_nilai_lisan = 0;
            $jumlah_mapel_tulis = 0;
            $total_nilai_tulis = 0;
            foreach ($mapel as $value) {
                $nilai = $this->nilai_um->get_data($value['ID_PUJ'], $item->ID_SISWA, $value['MAPEL_PUM']);

                if($value['JENIS_PUM'] == 'LISAN') {
                    $jumlah_mapel_lisan++;
                    $total_nilai_lisan += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
                } else {
                    $jumlah_mapel_tulis++;
                    $total_nilai_tulis += ($nilai == NULL ? 0 : $nilai->NILAI_PNU);
                }
                
                echo '<td>'.($nilai == NULL ? 0 : $nilai->NILAI_PNU).'</td>';
            }
            
            $rata_tulis = number_format($jumlah_mapel_tulis > 0 ? $total_nilai_tulis/$jumlah_mapel_tulis : 0, 1, '.', ',');
            $rata_lisan = number_format($jumlah_mapel_lisan > 0 ? $total_nilai_lisan/$jumlah_mapel_lisan : 0, 1, '.', ',');
            
            echo '<td>'.$rata_tulis.'</td>';
            echo '<td>'.$rata_lisan.'</td>';
            
            $temp_id = $ID_TINGK;
            $temp_lulus = $nilai_lulus_psb[$temp_id];
            $temp_lisan = $temp_lulus['LISAN'];
            unset($temp_lulus['LISAN']);
            $i = 0;
            $log = '';
            foreach ($temp_lulus as $temp_detail) {
                if(!isset($temp_detail['MAX'])) {
                    $temp_id = $temp_detail['TINGK'];
                    break;
                }

                if (($rata_tulis > $temp_detail['MAX']) || (($rata_tulis <= $temp_detail['MAX']) && ($rata_tulis > $temp_detail['MIN']) && ($rata_lisan >= $temp_lisan))) {
                    $temp_id = $temp_detail['TINGK'];
                    break;
                } elseif(($rata_tulis <= $temp_detail['MAX']) && ($rata_tulis > $temp_detail['MIN']) && ($rata_lisan < $temp_lisan)) {
                    $temp_id = isset($temp_lulus[$i + 1]['TINGK']) ? $temp_lulus[$i + 1]['TINGK'] : $temp_lulus[$i]['TINGK'];
                    break;
                }

                $i++;
            }
            
            $jejang = '';
            foreach ($tingkat as $detail) {
                  if ($temp_id == $detail->ID_TINGK) $jejang = $detail->DEPT_TINGK.' - '.$detail->NAMA_TINGK;
            }
        
            echo '<td>'.$jejang.'</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>