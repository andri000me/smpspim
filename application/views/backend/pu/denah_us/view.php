<?php
$status_sisa = FALSE;
?>
<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                DENAH <?php echo strtoupper($TITLE); ?>
            </h2>
            <small>Pembuatan denah <?php echo strtolower($TITLE); ?> pada tahun ajaran aktif</small>
        </div>
    </div>
</div>
<div class="content animate-panel">
    <?php if (!$STATUS_VALIDASI) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hgreen">
                    <div class="panel-heading hbuilt">
                        PENGATURAN
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-info" id="btn_request" disabled="true" onclick="proses_sisa();">SISA PESERTA SETIAP RUANG DIBUAT DENAH</button>
                                <button type="button" class="btn btn-warning" id="btn_validasi" onclick="validasi_denah();">VALIDASI DENAH</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                    </div>
                    DENAH <?php echo strtoupper($TITLE); ?> LAKI-LAKI
                </div>
                <div class="panel-body ">
                    <div class="panel-group" id="accordionL" role="tablist" aria-multiselectable="true">
                        <?php
                        $data_denah = $data;
                        $jk = 'L';
                        $ruang = $data_denah[$jk]['RUANG'];
                        $nomor = 1;
                        foreach ($data_denah[$jk]['DATA'] as $key => $value) {
                            if ($data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] == 0)
                                continue;
                            
                            if ($data_denah[$jk]['JUMLAH_SISA'][$key] > 0)
                                $status_sisa = TRUE;

                            echo '
                            <div class="panel panel-' . (($data_denah[$jk]['JUMLAH_SISA'][$key] > 0) ? 'danger' : 'info' ) . '">
                                <div class="panel-heading" role="tab" id="heading' . $jk . $key . '">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion' . $jk . '" href="#collapse' . $jk . $key . '" aria-expanded="true" aria-controls="collapse' . $jk . $key . '" data-jk="' . $jk . '" data-key="' . $key . '" onclick="request_denah(this);">
                                            ' . ($nomor++) . '.&nbsp;&nbsp;&nbsp;Ruang ' . $ruang[$key]['KODE_RUANG'] . ' - ' . $ruang[$key]['NAMA_RUANG'] . ' ( Kapasitas: ' . $ruang[$key]['KAPASITAS_UJIAN_RUANG'] . ' orang | Terisi: ' . $data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] . ' orang ' . (($data_denah[$jk]['JUMLAH_SISA'][$key] > 0) ? ' | Belum mendapat kursi: ' . $data_denah[$jk]['JUMLAH_SISA'][$key] . ' orang' : '') . ')
                                        </a>
                                        <!--' . ($STATUS_VALIDASI ? '' : '<input type="checkbox" class="gabung-kelas-' . $jk . ' pull-right" data-ruang="' . $key . '" data-kapasitas="' . $ruang[$key]['KAPASITAS_UJIAN_RUANG'] . '" data-terisi="' . $data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] . '" data-kode="' . $ruang[$key]['KODE_RUANG'] . '" data-jk="' . $jk . '" onclick="gabungKelas(\'' . $jk . '\')">') . '-->
                                    </h4>
                                </div>
                                <div id="collapse' . $jk . $key . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' . $jk . $key . '">
                                    <div class="panel-body container-denah" id="container' . $jk . $key . '">
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hviolet">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                    </div>
                    DENAH <?php echo strtoupper($TITLE); ?> PEREMPUAN
                </div>
                <div class="panel-body ">
                    <div class="panel-group" id="accordionP" role="tablist" aria-multiselectable="true">
                        <?php
                        $data_denah = $data;
                        $jk = 'P';
                        $ruang = $data_denah[$jk]['RUANG'];
                        $nomor = 1;
                        foreach ($data_denah[$jk]['DATA'] as $key => $value) {
                            if ($data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] == 0)
                                continue;
                            if ($data_denah[$jk]['JUMLAH_SISA'][$key] > 0)
                                $status_sisa = TRUE;

                            echo '
                            <div class="panel panel-' . (($data_denah[$jk]['JUMLAH_SISA'][$key] > 0) ? 'danger' : 'info' ) . '">
                                <div class="panel-heading" role="tab" id="heading' . $jk . $key . '">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion' . $jk . '" href="#collapse' . $jk . $key . '" aria-expanded="true" aria-controls="collapse' . $jk . $key . '" data-jk="' . $jk . '" data-key="' . $key . '" onclick="request_denah(this);">
                                            ' . ($nomor++) . '.&nbsp;&nbsp;&nbsp;Ruang ' . $ruang[$key]['KODE_RUANG'] . ' - ' . $ruang[$key]['NAMA_RUANG'] . ' ( Kapasitas: ' . $ruang[$key]['KAPASITAS_UJIAN_RUANG'] . ' orang | Terisi: ' . $data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] . ' orang ' . (($data_denah[$jk]['JUMLAH_SISA'][$key] > 0) ? ' | Belum mendapat kursi: ' . $data_denah[$jk]['JUMLAH_SISA'][$key] . ' orang' : '') . ')
                                        </a>
                                        <!--' . ($STATUS_VALIDASI ? '' : '<input type="checkbox" class="gabung-kelas-' . $jk . ' pull-right" data-ruang="' . $key . '" data-kapasitas="' . $ruang[$key]['KAPASITAS_UJIAN_RUANG'] . '" data-terisi="' . $data_denah[$jk]['JUMLAH_PESERTA_PERRUANG'][$key] . '" data-kode="' . $ruang[$key]['KODE_RUANG'] . '" data-jk="' . $jk . '" onclick="gabungKelas(\'' . $jk . '\')">') . '-->
                                    </h4>
                                </div>
                                <div id="collapse' . $jk . $key . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' . $jk . $key . '">
                                    <div class="panel-body container-denah" id="container' . $jk . $key . '">
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
<?php if (!$STATUS_VALIDASI) { ?>
        $(document).ready(function () {
    <?php if ($status_sisa) { ?>
                $("#btn_request").removeAttr('disabled');
                $("#btn_validasi").attr('disabled', true);
    <?php } else { ?>
                var success = function (data) {
                    create_homer_info("Denah siap untuk divalidasi. Pastikan denah sesuai dengan yang diharapkan.");
                };

                create_ajax('<?php echo site_url(($MODE == 'UM') ? 'pu/denah_um/denah_ready' : 'pu/denah_us/denah_ready'); ?>', 'TOKEN=<?php echo $TOKEN; ?>', success);
    <?php } ?>
        });
<?php } ?>
    function request_denah(t) {
        create_splash('Sedang mengambil data denah');
        $(".container-denah").html(" ");

        var jk = $(t).data('jk');
        var key = $(t).data('key');
        var id = 'container' + jk + key;

        var success = function (data) {
            if (data.status) {
                show_denah(jk, key, id, data.data, data.data.DATA, 'data');
<?php if (!$STATUS_VALIDASI) { ?>
                    if (data.data.SISA !== null) {
                        $("#" + id).append('<hr><h3>PESERTA YANG BELUM MENDAPAT KURSI</h3>');
                        show_denah(jk, key, id, data.data, data.data.SISA, 'sisa');
                    }
<?php } ?>
                setTimeout(function () {
                    remove_splash();
                }, 1000);
            }
        };

        create_ajax("<?php echo site_url(($MODE == 'UM') ? 'pu/denah_um/request_denah' : 'pu/denah_us/request_denah'); ?>", 'jk=' + jk + '&key=' + key, success);
    }

    function show_denah(jk, key, id, data, data_denah, mode) {
        var x = 1;
        var y = 0;
        var mark_col = 0;
        var mar_col_double = 0;

        $.each(data_denah, function (key1, value1) {
            if (mode === 'data') {
                while (parseInt(key1) != y) {
                    if (((parseInt(y) === 0) && !data.DOUBLE_COL) || (!data.DOUBLE_COL && ((parseInt(y) / data.JUMLAH_PERBARIS) == mark_col))) {
                        $("#" + id).append('<div class="row" id="mark_col_' + mode + mark_col + '"></div>');
                        mark_col++;
                    } else if (((parseInt(y) === 0) && data.DOUBLE_COL) || (data.DOUBLE_COL && (((parseInt(y) / data.JUMLAH_PERBARIS) == mar_col_double)))) {
                        $("#" + id).append('<div class="row" id="mar_col_double' + mode + mar_col_double + '"><div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div></div>');
                        mar_col_double++;
                        mark_col++;
                    } else if (data.DOUBLE_COL && ((((parseInt(y) * 2) / data.JUMLAH_PERBARIS) == mark_col))) {
                        $("#mar_col_double" + mode + (mar_col_double - 1)).append('<div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div>');
                        mark_col++;
                    }

                    $("#mark_col_" + mode + (mark_col - 1)).append(kursi_siswa(jk, key, data.PARSE_COL, null, y, '-', '-', null, mode, data.KURSI_KOSONG));

                    y++;
                }

                if (((parseInt(y) === 0) && !data.DOUBLE_COL) || (!data.DOUBLE_COL && ((parseInt(y) / data.JUMLAH_PERBARIS) == mark_col))) {
                    $("#" + id).append('<div class="row" id="mark_col_' + mode + mark_col + '"></div>');
                    mark_col++;
                } else if (((parseInt(y) === 0) && data.DOUBLE_COL) || (data.DOUBLE_COL && (((parseInt(y) / data.JUMLAH_PERBARIS) == mar_col_double)))) {
                    $("#" + id).append('<div class="row" id="mar_col_double' + mode + mar_col_double + '"><div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div></div>');
                    mar_col_double++;
                    mark_col++;
                } else if (data.DOUBLE_COL && ((((parseInt(y) * 2) / data.JUMLAH_PERBARIS) == mark_col))) {
                    $("#mar_col_double" + mode + (mar_col_double - 1)).append('<div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div>');
                    mark_col++;
                }

                $("#mark_col_" + mode + (mark_col - 1)).append(kursi_siswa(jk, key, data.PARSE_COL, value1, key1, data.NAMA_DEPT[value1], data.TINGKAT[value1], data.WARNA_JENJANG[value1], mode, data.KURSI_KOSONG));

                y++;
            } else {
                for (var i = 0; i < parseInt(value1); i++) {
                    if (((parseInt(y) === 0) && !data.DOUBLE_COL) || (!data.DOUBLE_COL && ((parseInt(y) / data.JUMLAH_PERBARIS) == mark_col))) {
                        $("#" + id).append('<div class="row" id="mark_col_' + mode + mark_col + '"></div>');
                        mark_col++;
                    } else if (((parseInt(y) === 0) && data.DOUBLE_COL) || (data.DOUBLE_COL && (((parseInt(y) / data.JUMLAH_PERBARIS) == mar_col_double)))) {
                        $("#" + id).append('<div class="row" id="mar_col_double' + mode + mar_col_double + '"><div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div></div>');
                        mar_col_double++;
                        mark_col++;
                    } else if (data.DOUBLE_COL && ((((parseInt(y) * 2) / data.JUMLAH_PERBARIS) == mark_col))) {
                        $("#mar_col_double" + mode + (mar_col_double - 1)).append('<div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div>');
                        mark_col++;
                    }

                    $("#mark_col_" + mode + (mark_col - 1)).append(kursi_siswa(jk, key, data.PARSE_COL, key1, y, data.NAMA_DEPT[key1], data.TINGKAT[key1], data.WARNA_JENJANG[key1], mode, data.KURSI_KOSONG));

                    y++;
                }
            }
        });

        if (mode === 'sisa')
            return;

        if (y < data.JUMLAH_KAPASITAS_PERUANG) {
            for (var z = y; z < data.JUMLAH_KAPASITAS_PERUANG; z++) {
                if (((parseInt(z) === 0) && !data.DOUBLE_COL) || (!data.DOUBLE_COL && ((parseInt(z) / data.JUMLAH_PERBARIS) == mark_col))) {
                    $("#" + id).append('<div class="row" id="mark_col_' + mode + mark_col + '"></div>');
                    mark_col++;
                } else if (((parseInt(z) === 0) && data.DOUBLE_COL) || (data.DOUBLE_COL && (((parseInt(z) / data.JUMLAH_PERBARIS) == mar_col_double)))) {
                    $("#" + id).append('<div class="row" id="mar_col_double' + mode + mar_col_double + '"><div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div></div>');
                    mar_col_double++;
                    mark_col++;
                } else if (data.DOUBLE_COL && ((((parseInt(z) * 2) / data.JUMLAH_PERBARIS) == mark_col))) {
                    $("#mar_col_double" + mode + (mar_col_double - 1)).append('<div class="col-md-6"><div class="row" id="mark_col_' + mode + mark_col + '"></div></div>');
                    mark_col++;
                }

                $("#mark_col_" + mode + (mark_col - 1)).append(kursi_siswa(jk, key, data.PARSE_COL, null, z, '-', '-', null, mode, data.KURSI_KOSONG));
            }
        }
    }

    function kursi_siswa(jk, key, parse_col_bootstrap, index, urutan, jenjang, tingkat, warna, mode, kursi_kosong) {
        var status = null;
        var html_option = '';

        if (warna === null)
            warna = 'hbgred';

        if (mode === 'sisa') {
            status = 'sisa';
<?php if (!$STATUS_VALIDASI) { ?>
                if (kursi_kosong.length > 0) {
                    html_option += '<div class="btn-group"><button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"><i class="fa fa-outdent"></i>&nbsp;&nbsp;<span class="caret"></span></button><ul class="dropdown-menu">';
                    $.each(kursi_kosong, function (key1, value) {
                        html_option += '<li><a href="#" onclick="atur_ulang_denah(\'' + jk + '\', \'' + key + '\', \'' + value + '\', \'' + index + '\', \'' + urutan + '\', \'' + jenjang + '\', \'' + key1 + '\');" class="pilihan_' + key1 + '">Pindah ke nomor ' + (parseInt(value) + 1) + '</a></li>';
                    });
                    html_option += '</ul></div>';
                }
<?php } ?>
        } else {
            status = 'penuh';
        }

        html = '<div class="col-md-' + parse_col_bootstrap + '" id="' + status + '_' + urutan + '">\n\
                <div class="hpanel ' + warna + '">\n\
                    <div class="panel-body text-center">\n\
                        <h6 class="font-light">No: ' + (parseInt(urutan) + 1) + '</h6>\n\
                        <h5 class="font-light text-bold"> ' + tingkat + ' ' + jenjang + '</h5>\n\
                        ' + html_option + '\n\
                    </div>\n\
                </div>\n\
            </div>';
        // html = '<div class="col-md-' + parse_col_bootstrap + '" style="cursor: pointer;" >\n\

        return html;
    }
<?php if (!$STATUS_VALIDASI) { ?>
        function gabungKelas(jk) {
            var id_ruang = [];
            var msg = '';
            $('.gabung-kelas-' + jk).each(function () {
                if ($(this).is(":checked")) {
                    id_ruang.push($(this).data('ruang'));
                    msg += $(this).data('kode') + ", ";
                }

                if (id_ruang.length > 1) {
                    proses_gabung_ruang(id_ruang, '.gabung-kelas-' + jk, msg);
                }
            });
        }

        function proses_gabung_ruang(id_ruang, class_ruang, msg) {
            var success = function (data) {
                if (data.status) {
                    window.location.reload();
                } else {
                    create_homer_error(data.msg);
                }
            };
            var action = function (isConfirm) {
                if (isConfirm) {
                    create_splash('Sistem sedang menggabungkan ruangan');
                    create_ajax('<?php echo site_url('pu/denah_us/gabung_ruang'); ?>', 'id_ruang=' + id_ruang, success);
                }
            };

            create_swal_success('Apakah Anda yakin melanjutkan?', "Ruangan yang tercheck (" + msg + ") akan digabung menjadi satu. Jika kapasitas tidak mencukupi maka penggabungan akan dibatalkan. Jangan klik tombol SISA PESERTA SETIAP RUANG DIBUAT DENAH karena ruangan akan direset kembali oleh sistem.", action);

            $(class_ruang).removeAttr('checked');
        }

        function atur_ulang_denah(JK, RUANG, KURSI, TINGKAT, URUTAN, JENJANG, PILIHAN) {
            var success = function (data) {
                if (data.status) {
                    create_homer_success("Denah berhasil disimpan.");

                    $("#sisa_" + URUTAN).remove();
                    $(".pilihan_" + PILIHAN).remove();
                    $("#penuh_" + KURSI).find("h5").html(JENJANG);
                } else {
                    create_homer_error(data.msg);
                    reload_window();
                }

                remove_splash();
            };
            var action = function (isConfirm) {
                if (isConfirm) {
                    create_splash('Sistem sedang menyimpan perubahan denah.');
                    create_ajax("<?php echo site_url(($MODE == 'UM') ? 'pu/denah_um/atur_ulang_denah' : 'pu/denah_us/atur_ulang_denah'); ?>", 'JK=' + JK + '&RUANG=' + RUANG + '&KURSI=' + KURSI + '&TINGKAT=' + TINGKAT, success);
                }
            };

            create_swal_option('Apakah Anda yakin?', 'Perubahan akan disimpan', action);
        }

        function proses_sisa() {
            create_splash('Sistem sedang membuat denah untuk sisa peserta');

            var success = function (data) {
                if (data.status) {
                    window.location.reload();
                } else {
                    create_homer_error(data.msg);
                }

                remove_splash();
            };

            create_ajax("<?php echo site_url(($MODE == 'UM') ? 'pu/denah_um/proses_sisa' : 'pu/denah_us/proses_sisa'); ?>", '', success);
        }
    <?php if (!$status_sisa) { ?>
            function validasi_denah() {
                var action = function (isConfirm) {
                    if (isConfirm) {
                        proses_validasi();
                    }
                };

                create_swal_option('Apakah Anda yakin?', 'Proses validasi akan menutup pembuatan denah pada tahun ajaran ini. Pastikan denah yang telah dibuat benar.', action);
            }

            function proses_validasi() {
                create_splash("Sistem sedang memvalidasi denah");

                var success = function (data) {
                    if (data.status) {
                        create_homer_success(data.msg);

                        setTimeout(function () {
                            window.location = data.link;
                        }, 1500);
                    } else {
                        create_homer_error(data.msg);
                    }

                    remove_splash();
                };

                create_ajax('<?php echo site_url(($MODE == 'UM') ? 'pu/denah_um/validasi_denah' : 'pu/denah_us/validasi_denah'); ?>', 'TOKEN=<?php echo $TOKEN; ?>', success)
            }
    <?php } ?>
<?php } ?>
</script>
