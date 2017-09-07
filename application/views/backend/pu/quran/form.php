<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                ATURAN JADWAL TESTING QURAN
            </h2>
            <small>Pembuatan aturan jadwal untuk testing baca quran</small>
        </div>
    </div>
</div>
<?php echo $this->generate->form_open('form-denah', 'denah'); ?>
<div class="content animate-panel">
    <?php if(count($WAKTU['L']) == 0 || count($WAKTU['P']) == 0 || count($RUANG['L']) == 0 || count($RUANG['P']) == 0 || count($MAPEL) == 0) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Form Aturan Ujian
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Sesi Ujian Laki-laki</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="WAKTU_LK" />
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Sesi Ujian Perempuan</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="WAKTU_PR" />
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Matapelajaran</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="MAPEL" />
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Ruang Ujian Laki-laki</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="RUANG_LK" />
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Ruang Ujian Perempuan</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="RUANG_PR" />
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Peserta Laki-laki</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="PESERTA" value="<?php echo $JUMLAH['L']; ?> siswa" readonly=""/>
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Jumlah Peserta Perempuan</label>
                        <div class="col-sm-2">
                            <input class="form-control required" type="text" name="PESERTA" value="<?php echo $JUMLAH['P']; ?> siswa" readonly=""/>
                            <span class="help-block m-b-none text-left">Wajib diisi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary" ><i class="fa fa-save"></i>&nbsp;&nbsp;Proses Aturan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <?php 
    $i = 0;
    foreach ($WAKTU as $JK_WAKTU => $DATA_WAKTU) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Form Waktu Pelaksanaan <?php echo ($JK_WAKTU == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'); ?>
                </div>
                <div class="panel-body">
                    <?php 
                    $j = 0;
                    foreach ($DATA_WAKTU as $DETAIL_WAKTU) { 
                        $i++;
                        $j++;
                        ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Sesi <?php echo $j; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->generate->input_hidden('ID_TW[]', $DETAIL_WAKTU->ID_TW); ?>
                            <?php $this->generate->input_date('Tanggal Ujian<br>', array('name' => 'TANGGAL_TW[]', 'id' => 'TANGGAL_TW_'.$i, 'value' => $this->date_format->to_view($DETAIL_WAKTU->TANGGAL_TW == NULL ? date('Y-m-d') : $DETAIL_WAKTU->TANGGAL_TW)), TRUE, 2); ?>
                            <?php $this->generate->input_time('Jam Mulai', array('name' => 'MULAI_TW[]', 'id' => 'MULAI_TW_'.$i, 'value' => $DETAIL_WAKTU->MULAI_TW == NULL ? '' : $DETAIL_WAKTU->MULAI_TW), TRUE, 2); ?>
                            <?php $this->generate->input_time('Jam Selesai', array('name' => 'AKHIR_TW[]', 'id' => 'AKHIR_TW_'.$i, 'value' => $DETAIL_WAKTU->AKHIR_TW == NULL ? '' : $DETAIL_WAKTU->AKHIR_TW), TRUE, 2); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Form quran yang Diujikan 
                </div>
                <div class="panel-body">
                    <?php 
                    $i = 0;
                    foreach ($MAPEL as $DETAIL_MAPEL) { 
                        $i++;
                        ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->generate->input_hidden('ID_TM[]', $DETAIL_MAPEL->ID_TM); ?>
                            <?php $this->generate->input_select2('quran '.$i, array('name' => 'MAPEL_TM[]','id' => 'MAPEL_TM_'.$i, 'url' => site_url('master_data/matapelajaran/auto_complete')), TRUE, 5, FALSE, $DETAIL_MAPEL->MAPEL_TM == NULL ? NULL : array('id' => $DETAIL_MAPEL->MAPEL_TM, 'text' => $DETAIL_MAPEL->NAMA_MAPEL)); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
    $i = 0;
    foreach ($RUANG as $JK_WAKTU => $DATA_RUANG) { 
        ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Form Ruang Ujian <?php echo ($JK_WAKTU == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'); ?>
                </div>
                <div class="panel-body">
                    <?php 
                    $j = 0;
                    foreach ($DATA_RUANG as $DETAIL_RUANG) { 
                        $i++;
                        $j++;
                        ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->generate->input_hidden('ID_TR[]', $DETAIL_RUANG->ID_TR); ?>
                            <?php $this->generate->input_select2('Ruang '.$j, array('name' => 'RUANG_TR[]', 'id' => 'RUANG_TR_'.$i, 'url' => site_url('master_data/ruang/auto_complete')), TRUE, 5, FALSE, $DETAIL_RUANG->RUANG_TR == NULL ? NULL : array('id' => $DETAIL_RUANG->RUANG_TR, 'text' => $DETAIL_RUANG->NAMA_RUANG)); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
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
    <?php } ?>
</div>
</form>
<script type="text/javascript">

    $(document).ready(function(){        
        <?php
        if(count($WAKTU['L']) == 0 || count($WAKTU['P']) == 0 || count($RUANG['L']) == 0 || count($RUANG['P']) == 0 || count($MAPEL) == 0) { ?>
            create_homer_info("Belum ada jadwal pada tahun ajaran ini. Silahkan buat aturan terlebih dahulu.");
        <?php } ?>
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
                <?php if(count($WAKTU['L']) == 0 || count($WAKTU['P']) == 0 || count($RUANG['L']) == 0 || count($RUANG['P']) == 0 || count($MAPEL) == 0) { ?>
                    create_form_ajax('<?php echo site_url('pu/quran/tambah_aturan') ?>', id, success, message);
                <?php } else { ?>
                    create_form_ajax('<?php echo site_url('pu/quran/simpan_aturan') ?>', id, success, message);
                <?php } ?>
            }
        };
        
        
        create_swal_option('Apakah Anda yakin akan menyimpan?', '', action);

        return false;
    }
</script>