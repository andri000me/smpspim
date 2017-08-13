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
header("Content-Disposition: attachment; filename=" . $TA . "_jadwal_testing_kitab_" . date('Y-m-d_H-i-s') . ".xls");
?>
<table>
    <thead>
        <tr>
            <th colspan="12">JADWAL PENYEMAK, PENILAI DAN PEMBAGI WAKTU TESTING KITAB</th>
        </tr>
        <tr>
            <th colspan="12">KELAS 3 TSANAWIYAH DAN 2 DINIYAH WUSTHO</th>
        </tr>
        <tr>
            <th colspan="12">TAHUN AJARAN <?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?></th>
        </tr>
        <tr></tr>

        <tr>
            <th>No</th>
            <th>Peserta Didik</th>
            <th>Ruang</th>
            <th>Peserta</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Kitab</th>
            <th>Penyemak 1</th>
            <th>Penyemak 2</th>
            <th>Penilai 1</th>
            <th>Penilai 2</th>
            <th>Pembagi Waktu</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $nomor = 1;
        foreach ($RUANG as $JK_RUANG => $DATA_RUANG) {

            $SISA_PESERTA = $PEMBAGIAN[$JK_RUANG]['JUMLAH_SISA'];
            $i = 0;
            $ij = -1;
            foreach ($DATA_RUANG as $DETAIL_RUANG) {
                $i++;


                $j = 0;
                foreach ($WAKTU[$JK_RUANG] as $DETAIL_WAKTU) {
                    $j++;

                    $PESERTA = $PEMBAGIAN[$JK_RUANG]['JUMLAH_PERRUANG'] + ($SISA_PESERTA > 0 ? 1 : 0);

                    echo '<tr>';
                    echo '<td>' . $nomor++ . '</td>';
                    echo '<td>' . ($JK_RUANG == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN') . '</td>';
                    echo '<td>' . $DETAIL_RUANG->NAMA_RUANG . '</td>';
                    echo '<td>' . $PESERTA . '</td>';
                    echo '<td>' . $this->date_format->to_print_text($DETAIL_WAKTU->TANGGAL_TW) . '</td>';
                    echo '<td>' . $DETAIL_WAKTU->MULAI_TW . '-' . $DETAIL_WAKTU->AKHIR_TW . '</td>';
                    echo '<td>' . $MAPEL_JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]->NAMA_MAPEL . '</td>';
                    echo '<td>' . ($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 1']->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 1']->NON_PEGAWAI_TP : $this->cetak->nama_peg_print($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 1'])) . '</td>';
                    echo '<td>' . ($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 2']->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 2']->NON_PEGAWAI_TP : $this->cetak->nama_peg_print($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENYEMAK 2'])) . '</td>';
                    echo '<td>' . ($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 1']->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 1']->NON_PEGAWAI_TP : $this->cetak->nama_peg_print($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 1'])) . '</td>';
                    echo '<td>' . ($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 2']->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 2']->NON_PEGAWAI_TP : $this->cetak->nama_peg_print($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PENILAI 2'])) . '</td>';
                    echo '<td>' . ($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PEMBAGI WAKTU']->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PEMBAGI WAKTU']->NON_PEGAWAI_TP : $this->cetak->nama_peg_print($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]['PEMBAGI WAKTU'])) . '</td>';
                    echo '</tr>';

                    if ($SISA_PESERTA > 0)
                        $SISA_PESERTA--;
                }
            }
        }
        ?>
    </tbody>
</table>