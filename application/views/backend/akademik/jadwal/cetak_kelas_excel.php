<script src="<?php echo base_url('assets/scripts/excel_multiple_sheets.js'); ?>"></script>

<?php
$kelas_terjadwal = array();
foreach ($kelas as $detail_kelas) {
    if(isset($jadwal[$detail_kelas->ID_KELAS])) 
        echo '<table id="tbl'.$detail_kelas->ID_KELAS.'">';
    else 
        continue;
    
    $kelas_terjadwal[$detail_kelas->ID_KELAS] = $detail_kelas->NAMA_KELAS;
    
    echo '<tr>';
    foreach ($hari as $detail_hari) {
        echo '<th>'.$detail_hari->NAMA_HARI.'</th>';
        echo '<th>Jam</th>';
        echo '<th>Matapelajaran</th>';
        echo '<th>Guru Pengampu</th>';
    }
    echo '</tr>';
    
    foreach ($jam as $detail_jam) {
        $jadwal_ada = FALSE;
        foreach ($hari as $detail_hari) {
            if(isset($jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jam->ID_MJP])) {
                $jadwal_ada = TRUE;
                break;
            }
        }
        
        if($jadwal_ada) {
            echo '<tr>';
            foreach ($hari as $detail_hari) {
                if(isset($jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jam->ID_MJP])) {
                    $jadwal_pelajaran = $jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jam->ID_MJP];
                    $start_detail_jadwal = 0;
                    foreach ($jadwal_pelajaran as $detail_jadwal) {
                        $start_detail_jadwal++;
                        
                        if($start_detail_jadwal == 1) {
                            echo '<td>#</td>';
                            echo '<td>'.$detail_jadwal->MULAI_MJP.' - '.$detail_jadwal->AKHIR_MJP.' WIS</td>';
                            echo '<td>'.$detail_jadwal->NAMA_MAPEL.'</td>';
                            echo '<td>';
                        }
                        if($start_detail_jadwal > 1)
                            echo ', ';
                        
                        echo ($detail_jadwal->GELAR_AWAL_PEG == NULL ? '' : $detail_jadwal->GELAR_AWAL_PEG.' ').$this->cetak->nama_peg_print($detail_jadwal).($detail_jadwal->GELAR_AKHIR_PEG == NULL ? '' : ' '.$detail_jadwal->GELAR_AKHIR_PEG);
                        
                        if($start_detail_jadwal == count($jadwal_pelajaran))
                            echo '</td>';
                    }
                } else {
                    echo '<td>#</td>';
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                }
            }
            echo '</tr>';
        }
    }
    
    echo '</table>';
    echo '<hr>';
}

?>
<script type="text/javascript">
    tablesToExcel([<?php 
    $i=0;
    foreach ($kelas_terjadwal as $id => $nama) {
        $i++;
        echo "'tbl".$id."'";
        if($i != count($kelas_terjadwal)) echo ',';
    }
    ?>], [<?php 
    $i=0;
    foreach ($kelas_terjadwal as $id => $nama) {
        $i++;
        echo "'".$nama."'";
        if($i != count($kelas_terjadwal)) echo ',';
    }
    ?>], 'jadwal_kelas_<?php echo date('Y_m_d_H_i_s'); ?>.xls', 'Excel');
    window.close();
</script>