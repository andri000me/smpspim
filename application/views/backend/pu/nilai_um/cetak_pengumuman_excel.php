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
header("Content-Disposition: attachment; filename=hasil_kelulusan_psb_" . date('Y-m-d_H-i-s') . ".xls");
?>
<table>
    <thead>
        <tr>
            <th colspan="5">PANITIA UJIAN</th>
        </tr>
        <tr>
            <th colspan="5">HASIL KELULUSAN PSB TAHUN <?php echo $this->pengaturan->getTahunPSBAwal(); ?></th>
        </tr>
        <tr></tr>

        <tr>
            <th>No</th>
            <th>No UM</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Jenjang Pilihan</th>
            <th>Tingkat Pilihan</th>
            <th>Jenjang Lulus</th>
            <th>Tingkat Lulus</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($siswa as $item) {
            echo '<tr>';
            echo '<td>'.$no++.'</td>';
            echo '<td>'.$this->pengaturan->getKodeUM($item).'</td>';
            echo '<td>'.$item->NAMA_SISWA.'</td>';
            echo '<td>'.$item->JK_SISWA.'</td>';
            echo '<td>'.$item->DEPT_TINGK_PSB.'</td>';
            echo '<td>'.$item->NAMA_TINGK_PSB.'</td>';
            echo '<td>'.$item->DEPT_TINGK_NOW.'</td>';
            echo '<td>'.$item->NAMA_TINGK_NOW.'</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>