<script src="<?php echo base_url('assets/scripts/excel_multiple_sheets.js'); ?>"></script>

<?php

$guru_terjadwal = array();
foreach ($guru as $detail_guru) {
    if(isset($jadwal[$detail_guru->ID_PEG])) 
        $guru_terjadwal[$detail_guru->ID_PEG] = $detail_guru->NAMA_PEG;
    else 
        continue;
    
    echo '<table id="tbl'.$detail_guru->ID_PEG.'">';
    
    echo '<tr>';
    echo '<th>NO</th>';
    echo '<th>HARI</th>';
    echo '<th>JAM</th>';
    echo '<th>KELAS</th>';
    echo '<th>MATAPELAJARAN</th>';
    echo '</tr>';
    
    $no = 1;
    foreach ($jadwal[$detail_guru->ID_PEG] as $detail_jadwal) {
        echo '<tr>';
        echo '<td>'.$no++.'</td>';
        echo '<td>'.$detail_jadwal->NAMA_HARI.'</td>';
        echo '<td>'.$detail_jadwal->MULAI_MJP.' - '.$detail_jadwal->AKHIR_MJP.' WIS</td>';
        echo '<td>'.$detail_jadwal->NAMA_KELAS.'</td>';
        echo '<td>'.$detail_jadwal->NAMA_MAPEL.'</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '<hr>';
}

?>
<script type="text/javascript">
    tablesToExcel([<?php 
    $i=0;
    foreach ($guru_terjadwal as $id => $nama) {
        $i++;
        echo "'tbl".$id."'";
        if($i != count($guru_terjadwal)) echo ',';
    }
    ?>], [<?php 
    $i=0;
    foreach ($guru_terjadwal as $id => $nama) {
        $i++;
        echo "'".$nama."'";
        if($i != count($guru_terjadwal)) echo ',';
    }
    ?>], 'jadwal_guru_<?php echo date('Y_m_d_H_i_s'); ?>.xls', 'Excel');
    window.close();
</script>