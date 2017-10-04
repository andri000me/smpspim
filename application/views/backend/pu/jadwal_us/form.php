<?php
if ($jadwal === NULL)
    $mode_edit = FALSE;
else
    $mode_edit = TRUE;

if ($jadwal !== NULL and isset($mode_view))
    $mode_view = TRUE;
else
    $mode_view = FALSE;

$mode_edit ? $title = 'Perbaharui Jadwal Ujian Sekolah' : $title = 'Tambah Jadwal Ujian Sekolah';
$mode_view ? $title = 'Lihat Jadwal Ujian Sekolah' : $title = $title;
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
                        <?php
                        $this->generate->input_dropdown('Jenis Kelamin', 'JK_PUJ', array(
                            array('id' => 'L', 'text' => "BANIN", 'selected' => $mode_edit ? ($jadwal->JK_PUJ == 'L' ? TRUE : FALSE) : TRUE),
                            array('id' => 'P', 'text' => "BANAT", 'selected' => $mode_edit ? ($jadwal->JK_PUJ == 'P' ? TRUE : FALSE) : FALSE),
                                ), TRUE, 3);
                        ?>
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
                        <?php
//                        var_dump($mapel);
                        $id_mapel = array();
                        $x = 0;
                        foreach ($tingkat_us as $jenjang => $value) {
                            ?>
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
                                        <input class="form-control js-source-multi js-source-states-mapel mapel" name="MAPEL_PUM[]" style="width: 100%" multiple="multiple" id="mapel-<?php echo strtolower($dept[$jenjang]) . '-' . $tingkat; ?>" data-dept="<?php echo $dept[$jenjang]; ?>" data-tingk="<?php echo $tingkat; ?>">
                                    </div>
                                    <label class="col-sm-1 control-label">JENIS</label>
                                    <div class="col-sm-2">
                                        <select name="JENIS_PUM[]" class="form-control" >
                                            <option value="TULIS">TULIS</option>
                                            <option value="LISAN" <?php if ($mode_edit && $mapel[$x]['JENIS_PUM'] == 'LISAN') echo 'selected'; ?>>LISAN</option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                $id_mapel[] = 'mapel-' . strtolower($dept[$jenjang]) . '-' . $tingkat;
                                $x++;
                            }
                            ?><hr><?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pengawas-lk">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL PENGAWAS LAKI-LAKI
                    </div>
                    <div class="panel-body">
                        <?php
                        $denah = json_decode($denah, TRUE);
                        $jadwal_lk = $denah['L'];
                        $number = 1;
                        foreach ($jadwal_lk['DATA'] as $index_ruang => $data_ruang) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ($number++); ?>.&nbsp;&nbsp;&nbsp;RUANG</label>
                                <div class="col-sm-2">
                                    <input class="form-control" readonly="TRUE" type="text" name="RUANGAN_PENG_LK[]" value="<?php echo $jadwal_lk['RUANG'][$index_ruang]['KODE_RUANG']; ?>" />
                                </div>
                                <label class="col-sm-1 control-label">PENGAWAS</label>
                                <div class="col-sm-6">
                                    <input class="form-control js-source-multi js-source-states-pengawas pegawas-lk" name="PEGAWAI_PENG_LK[]" style="width: 100%" multiple="multiple" id="pegawas-lk-<?php echo $jadwal_lk['RUANG'][$index_ruang]['KODE_RUANG']; ?>" data-jk="L" data-index="<?php echo $jadwal_lk['RUANG'][$index_ruang]['KODE_RUANG']; ?>">
                                </div>
                            </div>
                            <?php
                            $id_pengawas_lk[] = 'pegawas-lk-' . $jadwal_lk['RUANG'][$index_ruang]['KODE_RUANG'];
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pengawas-pr">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        JADWAL PENGAWAS PEREMPUAN
                    </div>
                    <div class="panel-body">
                        <?php
                        $jadwal_pr = $denah['P'];
                        $id_pengawas_pr = array();
                        $number = 1;
                        foreach ($jadwal_pr['DATA'] as $index_ruang => $data_ruang) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo ($number++); ?>.&nbsp;&nbsp;&nbsp;RUANG</label>
                                <div class="col-sm-2">
                                    <input class="form-control" readonly="TRUE" type="text" name="RUANGAN_PENG_PR[]" value="<?php echo $jadwal_pr['RUANG'][$index_ruang]['KODE_RUANG']; ?>" />
                                </div>
                                <label class="col-sm-1 control-label">PENGAWAS</label>
                                <div class="col-sm-6">
                                    <input class="form-control js-source-multi js-source-states-pengawas pegawas-pr" name="PEGAWAI_PENG_PR[]" style="width: 100%" multiple="multiple" id="pegawas-pr-<?php echo $jadwal_pr['RUANG'][$index_ruang]['KODE_RUANG']; ?>" data-jk="P" data-index="<?php echo $jadwal_pr['RUANG'][$index_ruang]['KODE_RUANG']; ?>">
                                </div>
                            </div>
                            <?php
                            $id_pengawas_pr[] = 'pegawas-pr-' . $jadwal_pr['RUANG'][$index_ruang]['KODE_RUANG'];
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
            var form_jk = 'L';
            var pengawas = {
                'L': {<?php
    foreach ($jadwal_lk['DATA'] as $index_ruang => $data_ruang) {
        echo $index_ruang . ':null,';
    }
    ?>},
                'P': {<?php
    foreach ($jadwal_pr['DATA'] as $index_ruang => $data_ruang) {
        echo $index_ruang . ':null,';
    }
    ?>},
            };
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
                $(function () {
                    $(".pengawas-pr").hide();

                    $("#JK_PUJ").change(function () {
                        form_jk = $(this).val();

                        if (form_jk === 'L') {
                            $(".pengawas-pr").slideUp();
                            $(".pengawas-lk").slideDown();
                        } else {
                            $(".pengawas-lk").slideUp();
                            $(".pengawas-pr").slideDown();
                        }
                    });
                });

                function action_save_<?php echo $name_function; ?>(id) {
                    var action = function (isConfirm) {
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
        <?php if ($mode_edit) { ?>create_form_ajax('<?php echo site_url('pu/jadwal_us/ajax_update') ?>', id, success, message);
        <?php } else { ?>create_form_ajax('<?php echo site_url('pu/jadwal_us/ajax_add') ?>', id, success, message);
        <?php } ?>
                    };

                    create_swal_option('Apakah Anda yakin akan menyimpan?', '', action);

                    return false;
                }

                function reaload_page() {
                    setTimeout(function () {
                        window.location.reload();
        <?php // if (!$mode_edit) {                        ?>window.location.reload();<?php //}                        ?>
                    }, 1500);
                }

                $(".js-source-states-pengawas").each(function () {
                    var jk = $(this).data("jk");
                    var index = $(this).data("index");

                    $(this).select2({
                        escapeMarkup: function (markup) {
                            return markup;
                        },
                        ajax: {
                            url: "<?php echo site_url('pu/jadwal_us/get_pengawas'); ?>",
                            dataType: "json",
                            type: "POST",
                            delay: 100,
                            cache: true,
                            data: function (term, page) {
                                return {
                                    q: term,
                                    pengawas: pengawas[jk]
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
                        formatResult: function (element) {
                            return element.id + " - " + element.text;
                        },
                        formatSelection: function (element) {
                            pengawas[jk][index] = element.id;
                            console.log('RESULT', pengawas);

                            return element.id + " - " + element.text;
                        },
                    });
                });
                $(".js-source-states-mapel").each(function () {
                    var dept = $(this).data('dept');
                    var tingk = $(this).data('tingk');

                    $(this).select2({escapeMarkup: function (markup) {
                            return markup;
                        },
                        ajax: {
                            url: "<?php echo site_url('pu/jadwal_us/get_mapel'); ?>",
                            dataType: "json",
                            type: "POST",
                            delay: 100,
                            cache: true,
                            data: function (term, page) {
                                return {
                                    q: term,
                                    dept: dept,
                                    tingk: tingk,
                                    jk: form_jk,
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
                        formatResult: function (element) {
                            return element.id + " - " + element.text;
                        },
                        formatSelection: function (element) {
                            return element.id + " - " + element.text;
                        },
                    });
                });
        <?php if ($mode_edit) { ?>

            <?php
            foreach ($id_mapel as $value) {
                foreach ($mapel as $key => $detail) {
                    if ($value == 'mapel-' . strtolower($detail['DEPT_TINGK']) . '-' . $detail['NAMA_TINGK']) {
                        ?>
                                $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $detail['MAPEL_PUM']; ?>', text: "<?php echo $detail['NAMA_MAPEL']; ?>"});
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php
            foreach ($id_pengawas_lk as $value) {
                foreach ($pengawas_lk as $key => $detail) {
                    if ($value == 'pegawas-lk-' . $detail['KODE_RUANG']) {
                        ?>
                                $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $detail['PEGAWAI_PENG']; ?>', text: "<?php echo $detail['NAMA_PEG']; ?>"});
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <?php
            foreach ($id_pengawas_pr as $key => $value) {
                foreach ($pengawas_pr as $key => $detail) {
                    if ($value == 'pegawas-pr-' . $detail['KODE_RUANG']) {
                        ?>
                                $('#<?php echo $value; ?>').select2('data', {id: '<?php echo $detail['PEGAWAI_PENG']; ?>', text: "<?php echo $detail['NAMA_PEG']; ?>"});
                    <?php } ?>
                <?php } ?>
            <?php } ?>

        <?php } ?>
    <?php } ?>
        </script>
    <?php } ?>
</form>
</div>