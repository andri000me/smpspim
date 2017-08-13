<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                JADWAL TESTING KITAB
            </h2>
            <small>Jadwal untuk testing baca kitab</small>
        </div>
    </div>
</div>
<?php echo $this->generate->form_open('form-denah', 'denah'); ?>
<div class="modal fade" id="modal-cetak" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <h4 class="modal-title">Form Export Jadwal</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->generate->input_text('Direktur', array('id' => 'DIREKTUR'), TRUE, 9); ?>
                        <?php $this->generate->input_text('PD Pend. & Kur.', array('id' => 'PD'), TRUE, 9); ?>
                        <?php $this->generate->input_text('Ketua', array('id' => 'KETUA'), TRUE, 9); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="cetak_jadwal();" class="btn btn-primary">Export</button>
            </div>
        </div>
    </div>
</div>
</form>
<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hblue">
                <div class="panel-body text-center">
                    <a href="<?php echo site_url('pu/kitab/form_aturan/1'); ?>" class="btn btn-primary">Ubah Aturan Jadwal</a>
                    <a href="<?php echo site_url('pu/kitab/form_aturan/0'); ?>" class="btn btn-info">Ubah Jadwal</a>
                    <!--<a href="" class="btn btn-success" data-toggle="modal" data-target="#modal-cetak">Export Jadwal</a>-->
                    <a href="<?php echo site_url('pu/kitab/cetak'); ?>" class="btn btn-success">Export Jadwal</a>
                    <a href="<?php echo site_url('pu/kitab/reset_jadwal'); ?>" onclick="return confirm('Apakah Anda yakin menghapus jadwal?');" class="btn btn-danger">Hapus Jadwal</a>
                    <a href="<?php echo site_url('pu/kitab/input_nilai'); ?>" class="btn btn-primary2">Nilai Testing</a>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($RUANG as $JK_RUANG => $DATA_RUANG) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Data jadwal <?php echo ($JK_RUANG == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'); ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                    <?php 
                    $SISA_PESERTA = $PEMBAGIAN[$JK_RUANG]['JUMLAH_SISA'];
                    $i = 0;
                    $ij = -1;
                    foreach ($DATA_RUANG as $DETAIL_RUANG) { 
                        $i++;
                        ?>
                        <div class="col-md-6 <?php if(($i%2) != 0) echo 'border-right'; ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Ruang <?php echo $DETAIL_RUANG->NAMA_RUANG; ?></h3>
                                </div>
                            </div>
                                <?php 
                                $j = 0;
                                foreach ($WAKTU[$JK_RUANG] as $DETAIL_WAKTU) { 
                                    $j++;
                                    $PESERTA = $PEMBAGIAN[$JK_RUANG]['JUMLAH_PERRUANG'] + ($SISA_PESERTA > 0 ? 1 : 0);
                                    ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Tanggal: <?php echo $this->date_format->to_print_text($DETAIL_WAKTU->TANGGAL_TW); ?></h4>
                                    <h4>Jam: <?php echo $DETAIL_WAKTU->MULAI_TW; ?> - <?php echo $DETAIL_WAKTU->AKHIR_TW; ?></h4>
                                    <h4>Jumlah Siswa: <?php echo $PESERTA; ?></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php 
                                    for ($k = 0; $k < 5; $k++) {
                                        $ij++;
                                        
                                        if($k == 0) echo '<h4>Kitab: '.$MAPEL_JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]->NAMA_MAPEL.'</h4>';
//                                        if($k == 0) echo '<h4>RUANG: '.$DETAIL_RUANG->ID_TR.'</h4>';
//                                        if($k == 0) echo '<h4>WAKTU: '.$DETAIL_WAKTU->ID_TW.'</h4>';
                                        echo '<h4>'.$TUGAS[$k].': '.(
                                                $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->PEGAWAI_TP == NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->NON_PEGAWAI_TP : $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->NAMA_PEG
                                                ).'</h4>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <hr>
                <?php 
                    if($SISA_PESERTA > 0) $SISA_PESERTA--;
                    } ?>
                        </div>
                <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<script type="text/javascript">

    $(document).ready(function(){      
        
    });
    
    function cetak_jadwal() {
        var DIREKTUR = $("#DIREKTUR").val();
        var PD = $("#PD").val();
        var KETUA = $("#KETUA").val();
        
        if(DIREKTUR === "" || PD === "" || KETUA === "")
            create_homer_error("Form belum lengkap");
        else
            window.open('<?php echo site_url('pu/kitab/cetak_denah'); ?>/' + DIREKTUR + "/" + PD + "/" + KETUA);
    }
    
</script>