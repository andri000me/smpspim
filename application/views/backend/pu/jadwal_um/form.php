<?php
if ($jadwal === NULL)
    $mode_edit = FALSE;
else
    $mode_edit = TRUE;

if ($jadwal !== NULL and isset($mode_view))
    $mode_view = TRUE;
else
    $mode_view = FALSE;

$mode_edit ? $title = 'Perbaharui Jadwal Ujian Masuk' : $title = 'Tambah Jadwal Ujian Masuk';
$mode_view ? $title = 'Lihat Jadwal Ujian Masuk' : $title = $title;
$subtitle = "Form untuk jadwal ujian";

$id_form = 'form-jadwal';
$name_function = 'jadwal';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel">
    <?php
    echo $this->generate->form_open($id_form, $name_function);
    if ($mode_edit)
        $this->generate->input_hidden('ID_PUJ', $jadwal->ID_PUJ);
    ?>
    <?php if (!$validasi_denah) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hbggreen">
                    <div class="panel-body">
                        <h1 class="text-center">DENAH PADA TAHUN AJARAN AKTIF BELUM DIVALIDASI. ANDA TIDAK DIPERBOLEHKAN MENAMBAH DATA BARU.</h1>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL UJIAN
                    </div>
                    <div class="panel-body">
                        <?php $this->generate->input_date('Tanggal', array('name' => 'TANGGAL_PUJ', 'maxlength' => 100, 'value' => $mode_edit ? $this->date_format->to_view($jadwal->TANGGAL_PUJ) : ''), TRUE, 3); ?>
                        <?php $this->generate->input_time('Jam Mulai', array('name' => 'JAM_MULAI_PUJ', 'maxlength' => 100, 'value' => $mode_edit ? $jadwal->JAM_MULAI_PUJ : ''), TRUE, 2); ?>
                        <?php $this->generate->input_time('Jam Selesai', array('name' => 'JAM_SELESAI_PUJ', 'maxlength' => 100, 'value' => $mode_edit ? $jadwal->JAM_SELESAI_PUJ : ''), TRUE, 2); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL MATAPELAJARAN
                    </div>
                    <div class="panel-body">
                        <?php //var_dump($mapel);
                        $id_mapel = array();
                        $x = 0;
                            foreach ($tingkat_um as $jenjang => $value) { ?>
                        <h3>JENJANG: <?php echo $dept[$jenjang]; ?></h3>
                                <?php foreach ($value as $tingkat) {
                            ?>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">TINGKAT</label>
                            <div class="col-sm-1">
                                <input class="form-control" readonly="TRUE" type="hidden" name="DEPT_TINGK[]" value="<?php echo $dept[$jenjang]; ?>" />
                                <input class="form-control" readonly="TRUE" type="text" name="NAMA_TINGK[]" value="<?php echo $tingkat; ?>" />
                            </div>
                            <label class="col-sm-2 control-label">MATAPELAJARAN</label>
                            <div class="col-sm-5">
                                <input class="form-control js-source-multi js-source-states-mapel required mapel" name="MAPEL_PUM[]" style="width: 100%" multiple="multiple" id="mapel-<?php echo strtolower($dept[$jenjang]).'-'.$tingkat; ?>">
                                <span class="help-block m-b-none text-left">Wajib diisi</span>
                            </div>
                            <label class="col-sm-1 control-label">JENIS</label>
                            <div class="col-sm-2">
                                <select name="JENIS_PUM[]" class="form-control" >
                                    <option value="TULIS">TULIS</option>
                                    <option value="LISAN" <?php if($mode_edit && $mapel[$x]['JENIS_PUM'] == 'LISAN') echo 'selected'; ?>>LISAN</option>
                                </select>
                            </div>
                        </div>
                        <?php 
                                    $id_mapel[] = 'mapel-'.strtolower($dept[$jenjang]).'-'.$tingkat;
                                    $x++;
                                }
                            ?><hr><?php
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL PENGAWAS LAKI-LAKI (OPSIONAL)
                    </div>
                    <div class="panel-body">
                        <?php 
                            $denah = json_decode($denah, TRUE);
                            $jadwal_lk = $denah['L'];
                            $id_pengawas_lk = array();
                            for ($i = 0; $i < count($jadwal_lk['DATA']); $i++) {
                            ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo ($i + 1); ?>.&nbsp;&nbsp;&nbsp;RUANG *</label>
                            <div class="col-sm-2">
                                <input class="form-control" readonly="TRUE" type="text" name="RUANGAN_PENG_LK[]" value="<?php echo $jadwal_lk['RUANG'][$i]['KODE_RUANG']; ?>" />
                            </div>
                            <label class="col-sm-1 control-label">PENGAWAS</label>
                            <div class="col-sm-6">
                                <input class="form-control js-source-multi js-source-states-pengawas  pegawas-lk" name="PEGAWAI_PENG_LK[]" style="width: 100%" multiple="multiple" id="pegawas-lk-<?php echo $i; ?>">
                                <span class="help-block m-b-none text-left">Wajib diisi</span>
                            </div>
                        </div>
                        <?php                            
                            $id_pengawas_lk[] = 'pegawas-lk-'.$i;
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL PENGAWAS PEREMPUAN (OPSIONAL)
                    </div>
                    <div class="panel-body">
                        <?php 
                            $jadwal_pr = $denah['P'];
                            $id_pengawas_pr = array();
                            for ($i = 0; $i < count($jadwal_pr['DATA']); $i++) {
                            ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo ($i + 1); ?>.&nbsp;&nbsp;&nbsp;RUANG *</label>
                            <div class="col-sm-2">
                                <input class="form-control" readonly="TRUE" type="text" name="RUANGAN_PENG_PR[]" value="<?php echo $jadwal_pr['RUANG'][$i]['KODE_RUANG']; ?>" />
                            </div>
                            <label class="col-sm-1 control-label">PENGAWAS</label>
                            <div class="col-sm-6">
                                <input class="form-control js-source-multi js-source-states-pengawas  pegawas-pr" name="PEGAWAI_PENG_PR[]" style="width: 100%" multiple="multiple" id="pegawas-pr-<?php echo $i; ?>">
                                <span class="help-block m-b-none text-left">Wajib diisi</span>
                            </div>
                        </div>
                        <?php          
                            $id_pengawas_pr[] = 'pegawas-pr-'.$i;
                            }                   
                            ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!$mode_view) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hbgblue">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3 col-md-offset-9">
                                    <button type="reset" class="btn w-xs btn-danger" id="reset">Reset</button>
                                    <button type="submit" class="btn w-xs btn-primary" id="simpan">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <script type="text/javascript">
    <?php
    if ($mode_view) {
        ?>
            $(function () {
                $(".form-control").prop('disabled', true);
                $(".js-source-multi").select2("enable", false);
            });
        <?php
    } else {
        ?>
            
    function action_save_<?php echo $name_function; ?>(id) {
        var action = function(isConfirm) {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
                if (data.status) {
                    remove_splash();
                    create_swal_success('', 'Data berhasil disimpan. Halaman ini akan dimuat ulang.');
                    reaload_page();
                } else {
                    remove_splash();
                    create_homer_error('Gagal menyimpan data. ' + data.msg);
                }
            };
    <?php if ($mode_edit) { ?>create_form_ajax('<?php echo site_url('pu/jadwal_um/ajax_update') ?>', id, success, message);
    <?php } else { ?>create_form_ajax('<?php echo site_url('pu/jadwal_um/ajax_add') ?>', id, success, message);
    <?php } ?>
        };
        
        create_swal_option('Apakah Anda yakin akan menyimpan?', '', action);
        
        return false;
    }

    function reaload_page() {
        setTimeout(function () {
        window.location.reload();
<?php // if (!$mode_edit) {           ?>window.location.reload();<?php //}           ?>
        }, 1500);
    }
    
    $(".js-source-states-pengawas").select2({escapeMarkup: function (markup) { return markup; },
        ajax: {
            url: "<?php echo site_url('master_data/pegawai/auto_complete'); ?>",
            dataType: "json",
            type: "POST",
            delay: 100,
            cache: true,
            data: function (term, page) {
                return {
                    q: term
                }
            },
            results: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        },
        formatResult: function(element){
            return element.id + " - " + element.text;
        },
        formatSelection: function(element){
            return element.id + " - " + element.text;
        },
    });
    $(".js-source-states-mapel").select2({escapeMarkup: function (markup) { return markup; },
        ajax: {
            url: "<?php echo site_url('master_data/matapelajaran/auto_complete'); ?>",
            dataType: "json",
            type: "POST",
            delay: 100,
            cache: true,
            data: function (term, page) {
                return {
                    q: term
                }
            },
            results: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            id: item.id
                        }
                    })
                };
            }
        },
        formatResult: function(element){
            return element.id + " - " + element.text;
        },
        formatSelection: function(element){
            return element.id + " - " + element.text;
        },
    });
    <?php if ($mode_edit) { ?>

        <?php foreach ($id_mapel as $key => $value) {
            if(isset($mapel[$key]['MAPEL_PUM']) && isset($mapel[$key]['NAMA_MAPEL'])) {
        ?>
        $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $mapel[$key]['MAPEL_PUM']; ?>', text: "<?php echo $mapel[$key]['NAMA_MAPEL']; ?>"});
    <?php } ?>
    <?php } ?>

        <?php foreach ($id_pengawas_lk as $key => $value) {
            if(isset($pengawas_lk[$key]['PEGAWAI_PENG']) && isset($pengawas_lk[$key]['NAMA_PEG'])) {
        ?>
        $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $pengawas_lk[$key]['PEGAWAI_PENG']; ?>', text: "<?php echo $pengawas_lk[$key]['NAMA_PEG']; ?>"});
    <?php } ?>
    <?php } ?>

        <?php foreach ($id_pengawas_pr as $key => $value) {
            if(isset($pengawas_pr[$key]['PEGAWAI_PENG']) && isset($pengawas_pr[$key]['NAMA_PEG'])) {
        ?>
        $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $pengawas_pr[$key]['PEGAWAI_PENG']; ?>', text: "<?php echo $pengawas_pr[$key]['NAMA_PEG']; ?>"});
    <?php } ?>
    <?php } ?>
        
    <?php } ?>
    <?php } ?>
        </script>
    <?php } ?>
</form>
</div>