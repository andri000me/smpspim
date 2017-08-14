<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

if (!isset($DATA)) {
    echo "<h1>TIDAK ADA SISWA DIKELAS INI</h1>";
    exit();
}

if (count($DATA) != 1) {
    echo "<h1>TIDAK ADA GENERATE EXCEL KARENA KELAS LEBIH DARI SATU</h1>";
    exit();
}

$DETAIL = $DATA[0];

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=dattar_excel_" . date('Y-m-d_H-i-s') . ".xls");
?>
<table>
    <thead>
        <tr>
            <th colspan="21">DAFTAR NILAI TAHUN AJARAN <?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?></th>
        </tr>
        <tr>
            <th colspan="3">Kelas</th>
            <th><?php echo $DETAIL['KELAS']->NAMA_KELAS; ?></th>
        </tr>
        <tr>
            <th colspan="3">Wali Kelas</th>
            <th><?php echo $this->cetak->nama_peg_print($DETAIL['KELAS']); ?></th>
        </tr>
        <tr>
            <th colspan="3">Matapelajaran</th>
        </tr>
        <tr></tr>

        <tr>
            <th>No</th>
            <th>No. Induk</th>
            <th>Nama</th>
            <th colspan="3">Tugas</th>
            <th>Sub Sumatif</th>
            <th>U. Cawu I</th>
            <th>Rapor</th>
            <th colspan="3">Tugas</th>
            <th>Sub Sumatif</th>
            <th>U. Cawu II</th>
            <th>Rapor</th>
            <th colspan="3">Tugas</th>
            <th>Sub Sumatif</th>
            <th>U. Cawu III</th>
            <th>Rapor</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($DETAIL['DATA'] as $SISWA) {
            ?>
            <tr>
                <th><?php echo $SISWA->NO_ABSEN_AS; ?></th>
                <th><?php echo $SISWA->NIS_SISWA; ?></th>
                <th><?php echo $SISWA->NAMA_SISWA; ?></th>
            </tr>
        <?php }
        ?>
    </tbody>
</table>