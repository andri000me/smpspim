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
            <small>Pembuatan jadwal untuk testing baca kitab</small>
        </div>
    </div>
</div>
<?php echo $this->generate->form_open('form-denah', 'denah'); ?>
<div class="content animate-panel">
    <?php 
    $ij = -1;
    foreach ($RUANG as $JK_RUANG => $DATA_RUANG) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Form Jadwal Pelaksanaan <?php echo ($JK_RUANG == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'); ?>
                </div>
                <div class="panel-body">
                    <?php 
                    $SISA_PESERTA = $PEMBAGIAN[$JK_RUANG]['JUMLAH_SISA'];
                    $i = 0;
                    foreach ($DATA_RUANG as $DETAIL_RUANG) { 
                        $i++;
                        ?>
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
                            <h4>Tanggal: <?php echo $this->date_format->to_print_text($DETAIL_WAKTU->TANGGAL_TW); ?> | Jam: <?php echo $DETAIL_WAKTU->MULAI_TW; ?> - <?php echo $DETAIL_WAKTU->AKHIR_TW; ?> | Jumlah Siswa: <?php echo $PESERTA; ?></h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->generate->input_hidden('RUANG_TP[]', $DETAIL_RUANG->ID_TR); ?>
                            <?php $this->generate->input_hidden('WAKTU_TP[]', $DETAIL_WAKTU->ID_TW); ?>
                            <?php $this->generate->input_hidden('PESERTA_TP[]', $PESERTA); ?>
                            <?php 
                            $LIST_MAPEL = array();
                            foreach ($MAPEL as $detail_mapel_html) {
                                $LIST_MAPEL[] = array(
                                    'id' => $detail_mapel_html->ID_TM, 
                                    'text' => $detail_mapel_html->NAMA_MAPEL, 
                                    'selected' => ($MAPEL_JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW] == NULL ? TRUE : ($MAPEL_JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW]->MAPEL_TP == $detail_mapel_html->ID_TM ? TRUE : FALSE))
                                );
                            }
                            
                            $this->generate->input_dropdown('Kitab', 'MAPEL_TP[]', $LIST_MAPEL, TRUE, 4); 
                            ?> 
                            <?php 
                            for ($k = 0; $k < 5; $k++) {
                                $ij++;
                                
                                $this->generate->input_select2($TUGAS[$k], array('name' => 'PEGAWAI_TP[]','id' => 'PEGAWAI_TP_'.$ij, 'url' => site_url('master_data/pegawai/auto_complete')), FALSE, 5, FALSE, isset($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]) && $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->PEGAWAI_TP != NULL ? array('id' => $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->PEGAWAI_TP, 'text' => $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->NAMA_PEG ) : NULL, '<div class="col-sm-4"><input type="text" class="form-control non_peg_'.$ij.'" name="NON_PEGAWAI_TP[]" value="'.(isset($JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]) && $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->NON_PEGAWAI_TP != NULL ? $JADWAL[$DETAIL_RUANG->ID_TR][$DETAIL_WAKTU->ID_TW][$TUGAS[$k]]->NON_PEGAWAI_TP : '').'" placeholder="Non guru" /></div>'); 
                                ?>
                            <script type="text/javascript">
                                $(document).ready(function(){       
                                    $(".js-source-states-PEGAWAI_TP_<?php echo $ij; ?>").on("change", function (e) {
                                        $(".non_peg_<?php echo $ij; ?>").val('');
                                    });
                                    $(".non_peg_<?php echo $ij; ?>").change(function(){
                                        $(".js-source-states-PEGAWAI_TP_<?php echo $ij; ?>").select2('val', null);
                                    });
                                });
                            </script>
                            <?php }
                            ?>
                        </div>
                    </div>
                        <?php 
                            if($SISA_PESERTA > 0) $SISA_PESERTA--;
                            } ?>
                    <hr>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if($JK_RUANG == 'L') $this->generate->input_hidden('JUMLAH_LK', $ij); ?>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hbgblue">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-2 col-md-offset-10">
                            <button type="submit" class="btn btn-primary" ><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">

    $(document).ready(function(){       
        
    });
    
    function action_save_denah(id) {
        var message = "Sistem sedang menyimpan data";
        var success = function(data) {
            remove_splash();
            
            if(data.status) {
                create_homer_success("Berhasil menyimpan data. Halaman ini akan dimuat ulang secara otomatis.");
                
                setTimeout(function () {
                    window.location = data.url;
                }, 1500);
            } else {
                create_homer_error("Gagal menyimpan data. " + data.msg);
            }
            
        }
        var action = function(isConfirm) {
            if(isConfirm) {
                create_form_ajax('<?php echo site_url('pu/kitab/simpan_denah') ?>', id, success, message);
            }
        };
        
        
        create_swal_option('Apakah Anda yakin akan menyimpan?', '', action);

        return false;
    }
</script>