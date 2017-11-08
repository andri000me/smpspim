<?php
$data_kode = array();

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rekap_akumulasi_pelanggaran_" . date('Y-m-d_H-i-s') . ".xls");
?>
<table>
    <h3>DATA REKAP AKUMULASI PELANGGARAN SEMUA KELAS</h3>
    <thead>
        <tr>
            <th rowspan="3">NO</th>
            <th rowspan="3">KELAS</th>
            <th colspan="<?php echo count($kode) * 2; ?>">KODE PELANGGARAN</th>
            <th colspan="2">TOTAL</th>
        </tr>
        <tr>
            <?php
            foreach ($kode as $detail) {
                $data_kode[] = $detail->KODE_KJP;
                ?>
                <th colspan="2"><?php echo $detail->KODE_KJP; ?></th>
            <?php }
            ?>
            <th rowspan="2">JUMLAH SISWA</th>
            <th rowspan="2">JUMLAH POIN</th>
        </tr>
        <tr>
            <?php
            for ($i = 0; $i < count($kode); $i++) {
                ?>
                <th>JUMLAH SISWA</th>
                <th>JUMLAH POIN</th>
            <?php }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $temp_kelas = NULL;
        $data_kode_flip = array_flip($data_kode);
        $temp_position = 0;
        $total_jumlah_siswa = 0;
        $total_jumlah_poin = 0;

        function cetak_pelanggaran($detail, $temp_position, $data_kode_flip) {
            for ($i = $temp_position; $i < $data_kode_flip[$detail->KODE_KJP]; $i++) {
                echo '<td></td>';
                echo '<td></td>';
            }
//                echo '<td>' . $data_kode_flip[$detail->KODE_KJP] . '</td>';
//                echo '<td>' . $detail->KODE_KJP . '</td>';

            echo '<td>' . $detail->JUMLAH_PELANGGAR . '</td>';
            echo '<td>' . $detail->JUMLAH_POIN . '</td>';

            return $data_kode_flip[$detail->KODE_KJP] + 1;
        }

        foreach ($kelas as $detail) {
            if ($temp_kelas == $detail->ID_KELAS) {
                $temp_position = cetak_pelanggaran($detail, $temp_position, $data_kode_flip);
            } else {
                if ($temp_kelas != NULL) {
                    for ($i = $temp_position; $i < count($data_kode_flip); $i++) {
                        echo '<td></td>';
                        echo '<td></td>';
                    }
                    echo '<td>' . $total_jumlah_siswa . '</td>';
                    echo '<td>' . $total_jumlah_poin . '</td>';

                    echo '</tr>';
                } else {
                    echo '<tr>';
                }

                echo '<td>' . $no++ . '</td>';
                echo '<td>' . $detail->NAMA_KELAS . '</td>';

                $temp_position = 0;
                $total_jumlah_siswa = 0;
                $total_jumlah_poin = 0;
                $temp_position = cetak_pelanggaran($detail, $temp_position, $data_kode_flip);
                $temp_kelas = $detail->ID_KELAS;
            }

            $total_jumlah_siswa += $detail->JUMLAH_PELANGGAR;
            $total_jumlah_poin += $detail->JUMLAH_POIN;
        }

        for ($i = $temp_position; $i < count($data_kode_flip); $i++) {
            echo '<td></td>';
            echo '<td></td>';
        }
        echo '<td>' . $total_jumlah_siswa . '</td>';
        echo '<td>' . $total_jumlah_poin . '</td>';

        echo '</tr>';
        ?>
        <tr>
            <td>##############################################################################################################</td>
        </tr>
        <tr>
            <td>KETERANGAN KODE PELANGGARAN:</td>
        </tr>
        <?php
        foreach ($kode as $detail) {
            $data_kode[] = $detail->KODE_KJP;
            ?>
        <tr>
            <td><?php echo $detail->KODE_KJP; ?></td>
            <td><?php echo $detail->NAMA_KJP; ?></td>
        </tr>
    <?php }
    ?>
    </tbody>
</table>