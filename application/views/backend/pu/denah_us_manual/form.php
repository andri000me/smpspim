<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                PROSES PEMBUATAN DENAH <?php echo strtoupper($TITLE); ?>
            </h2>
            <small>Pembuatan denah <?php echo strtolower($TITLE); ?> pada tahun ajaran dan catur wulan aktif</small>
        </div>
    </div>
</div>
<div class="content animate-panel">
    <?php foreach ($DATA_JK as $jk) { ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="text-big"><?php echo $jk == 'L' ? 'BANIN' : 'BANAT'; ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hpanel" id="panel-jumlah-<?php echo $jk; ?>">
                            <div class="panel-heading hbuilt text-center">
                                Jumlah Siswa
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Jenjang</th>
                                                    <th>Jumlah</th>
                                                    <th>Sisa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($JUMLAH_SISWA[$jk] as $detail) { ?>
                                                    <tr>
                                                        <td id="jenjang-<?php echo $jk . '-' . $detail['ID_TINGK']; ?>"><?php echo $detail['NAMA_TINGK'] . ' ' . $detail['DEPT_TINGK']; ?></td>
                                                        <td id="jumlah-<?php echo $jk . '-' . $detail['ID_TINGK']; ?>"><?php echo $detail['JUMLAH_SISWA']; ?></td>
                                                        <td class="sisa-<?php echo $jk ?>" id="sisa-<?php echo $jk . '-' . $detail['ID_TINGK']; ?>"><?php echo $detail['JUMLAH_SISWA']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary" data-jk="<?php echo $jk; ?>" onclick="simpan_denah(this)">SIMPAN DENAH</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="hpanel" id="panel-ruangan-<?php echo $jk; ?>">
                            <div class="panel-heading hbuilt text-center">
                                Ruangan
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>KODE</th>
                                            <th>MODEL</th>
                                            <th>KODE</th>
                                            <th>MODEL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $right = false;
                                        foreach ($RUANG[$jk] as $detail) {
                                            if (!$right)
                                                echo '<tr>';
                                            echo '<td>' . $detail['KODE_RUANG'] . '</td><td><select class="form-control input-sm model-ruang-' . $jk . '" id="model-ruang-' . $jk . '-' . $detail['KODE_RUANG'] . '" data-ruang="' . $detail['KODE_RUANG'] . '"><option value="">-</option></select></td>';
                                            // <input type="checkbox" class="ruang-' . $jk . '" id="ruang-' . $jk . '-' . $detail['KODE_RUANG'] . '" value="' . $detail['KODE_RUANG'] . '"/>&nbsp;&nbsp;

                                            if ($right)
                                                echo '</tr>';

                                            $right = !$right;
                                        }
                                        if (!$right)
                                            echo '</tr>';
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9" id="panel-<?php echo $jk; ?>">
                <div class="hpanel">
                    <div class="panel-heading hbuilt text-center">
                        <div class="panel-tools">
                            <a href="#" onclick="return create_tab('<?php echo $jk; ?>');"><i class="fa fa-plus"></i></a>
                        </div>
                        Denah Siswa
                    </div>
                    <div class="panel-body">
                        <ul class="nav nav-tabs" id="nav-tab-<?php echo $jk; ?>"></ul>
                        <div class="tab-content" id="content-tab-<?php echo $jk; ?>"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">
    var id = {
        'L': 1,
        'P': 1,
    };
    var jumlah_kusi = <?php echo $JUMLAH_KURSI; ?>;
    var jenjang = <?php echo json_encode($TINGKAT); ?>;
    var color = {<?php
    foreach ($TINGKAT as $detail) {
        echo $detail['ID_TINGK'] . ':"' . $detail['WARNA_JS'] . '",';
    }
    ?>};
    var ruang = <?php echo json_encode($RUANG); ?>;
    var model = <?php echo json_encode($MODEL); ?>;

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');
        create_model();
    });

    function set_panel(jk) {
        var tinggiModel = $("#panel-" + jk).height();
        var tinggiPanel = (tinggiModel / 2) - 65;
        $("#panel-ruangan-" + jk).find('.panel-body').css({'overflow': 'auto', 'height': (tinggiPanel - 25)});
        $("#panel-jumlah-" + jk).find('.panel-body').css({'overflow': 'auto', 'height': (tinggiPanel - 25)});
    }

    function create_model() {
        $.each(model, function (jk, item) {
            $.each(item.data, function (index, detail) {
                console.log('CREATE MODEL ' + jk + ' ON INDEX ' + index);

                create_tab(jk);
                var temp_id = id[jk] - 1;
                $('#jumlah-ruang-' + jk + '-' + temp_id).val(item['jumlah_ruang'][index]);
                $.each(detail, function (kursi, tingkat) {
                    $("#" + jk + '-' + temp_id + '-' + kursi).val(tingkat).trigger('change');
                });
            });
        });
    }

    function create_tab(jk) {
        $('#nav-tab-' + jk).append('<li class="new-tab"><a data-toggle="tab" href="#tab-' + jk + '-' + id[jk] + '">MODEL ' + id[jk] + '</a></li>');
        $('#content-tab-' + jk).append('<div id="tab-' + jk + '-' + id[jk] + '" data-jk="' + jk + '" data-id="' + id[jk] + '" class="tab-pane tab-pane-' + jk + ' new-tab"><div class="panel-body"><div class="row denah"></div><div class="row"><div class="col-md-10"><div class="input-group m-b"><span class="input-group-addon">Jumlah Ruang</span><input class="form-control proses-sisa" onchange="change_jumlah_ruang(this)" data-jk="' + jk + '" data-id="' + id[jk] + '" id="jumlah-ruang-' + jk + '-' + id[jk] + '" value="1"/></div></div><div class="col-md-2"><button class="btn btn-primary btn-block proses-sisa" onclick="proses_sisa(this)" data-jk="' + jk + '" data-id="' + id[jk] + '">PROSES</button></div></div></div></div>');

        $('#panel-' + jk).find(".active").removeClass('active');
        $('#panel-' + jk).find(".new-tab").removeClass('new-tab').addClass('active');

        for (var i = 1; i <= jumlah_kusi; i++) {
            create_card(jk, i);
        }

        set_panel(jk);
        add_option_model_ruangan(jk);
        change_select_ruang(jk);

        id[jk]++;

        return false;
    }

    function add_option_model_ruangan(jk) {
        $(".model-ruang-" + jk).append('<option value="' + id[jk] + '">' + id[jk] + '</option>')
    }

    function change_select_ruang(jk) {
        $('.model-ruang-' + jk).val('');
        $('.tab-pane-' + jk).each(function () {
            var id = $(this).data('id');
            var jumlah_ruang = $("#jumlah-ruang-" + jk + '-' + id).val();
            $('.model-ruang-' + jk).each(function () {
                var val = $(this).val();
                if (jumlah_ruang === 0)
                    return;
                if (val === '') {
                    $(this).val(id);
                    jumlah_ruang--;
                }
            });
        });
    }

    function create_card(jk, kursi) {
        if ((((kursi - 1) % 4) === 0) || (kursi === 1)) {
            $('.denah-kursi').removeClass('denah-kursi');
            $('#tab-' + jk + '-' + id[jk]).find(".denah").append('<div class="col-md-6"><div class="row denah-kursi"></div></div>');
        }

        $('#tab-' + jk + '-' + id[jk]).find(".denah-kursi").append('<div class="col-md-3"><div class="hpanel panel-kursi"><div class="panel-body border text-center kursi" data-kursi="' + kursi + '" style="border-top: 1px solid #e4e5e7"><p>' + kursi + '</p>' + create_option_jenjang(jk, kursi) + '</div></div></div>');
    }

    function create_option_jenjang(jk, kursi) {
        var tag_html = '<select class="form-control input-sm tingkat" id="' + jk + '-' + id[jk] + '-' + kursi + '" data-jk="' + jk + '" data-id="' + id[jk] + '" data-kursi="' + kursi + '" onchange="change_color_card(this)"><option value="">-</option>';
        $.each(jenjang, function (index, item) {
            tag_html += '<option value="' + item.ID_TINGK + '">' + item.NAMA_TINGK + ' ' + item.DEPT_TINGK + '</option>';
        });
        tag_html += '</select>';

        return tag_html;
    }

    function change_color_card(that) {
        var key = $(that).val();

        if (key === '') {
            $(that).parent().parent().removeClass().addClass('panel-kursi hpanel');
        } else {
            $(that).parent().parent().removeClass().addClass('panel-kursi hpanel ' + color[parseInt(key)]);
        }

        proses_sisa(that);
    }

    function change_jumlah_ruang(that) {
        proses_sisa(that);
        change_select_ruang($(that).data('jk'));
    }

    function proses_sisa(that) {
        var jk_pane = $(that).data('jk');

        $(".proses-sisa").prop('disabled', true);

        var jumlah_siswa_terplot = {<?php
    foreach ($TINGKAT as $detail) {
        echo $detail['ID_TINGK'] . ':{"L": 0, "P": 0},';
    }
    ?>};

        $('.tab-pane-' + jk_pane).each(function () {
            var id = $(this).data('id');
            var jumlah_ruang = $("#jumlah-ruang-" + jk_pane + '-' + id).val();
            if (jumlah_ruang === '')
                jumlah_ruang = 0;
            $(this).find('.tingkat').each(function () {
                var id_tingkat = $(this).val();
                if ((id_tingkat !== '') && (id_tingkat !== null)) {
                    try {
                        jumlah_siswa_terplot[id_tingkat][jk_pane] += parseInt(jumlah_ruang);
                    } catch (e) {
                        var kursi = $(this).data('kursi');
                        var id = $(this).data('id');
                        console.log("ERROR: >>> " + jk_pane + ' >>> ' + id + ' >>> ' + kursi + ' >>> ' + id_tingkat);
                    }
                }
            });
        });

        $.each(jenjang, function (key, value) {
            var jumlah = $("#jumlah-" + jk_pane + '-' + value.ID_TINGK).html();
            $("#sisa-" + jk_pane + '-' + value.ID_TINGK).html(jumlah);
        });

        $.each(jumlah_siswa_terplot, function (index, item) {
            $.each(jenjang, function (key, value) {
                if (index === value.ID_TINGK) {
                    var jumlah = $("#sisa-" + jk_pane + '-' + value.ID_TINGK).html();
                    var sisa = parseInt(jumlah) - parseInt(item[jk_pane]);
                    $("#sisa-" + jk_pane + '-' + value.ID_TINGK).html(sisa);
                }
            });
        });

        $(".proses-sisa").removeAttr('disabled');
    }

    function simpan_denah(that) {
        var jk = $(that).data('jk');
        var status_sisa = true;
        var status_ruangan = true;

        $(".sisa-" + jk).each(function () {
            var sisa = $(this).html();
            if (parseInt(sisa) != 0)
                status_sisa = false;
        });

        if (status_sisa) {
            var denah = [];
            var jumlah_ruang = [];
            var ruangan = [];
            var model = [];

            $('.tab-pane-' + jk).each(function () {
                var id = $(this).data('id');
                var jumlah = $("#jumlah-ruang-" + jk + '-' + id).val();
                jumlah_ruang.push(jumlah);
                $(this).find('.tingkat').each(function () {
                    var id_tingkat = $(this).val();
                    denah.push(id_tingkat);
                });
                $('.model-ruang-' + jk).each(function () {
                    var model = $(this).val();
                    if (parseInt(model) === parseInt(id))
                        jumlah--
                });
                if (jumlah != 0)
                    status_ruangan = false;
            });

            if (status_ruangan) {
                $('.model-ruang-' + jk).each(function () {
                    var temp_model = $(this).val();
                    var ruang = $(this).data('ruang');
                    if (temp_model !== '') {
                        ruangan.push(ruang);
                        model.push(temp_model);
                    }
                });
                create_ajax('<?php echo site_url('pu/denah_us/simpan_denah'); ?>', 'jk=' + jk + '&denah=' + denah + '&jumlah_ruang=' + jumlah_ruang + '&ruangan=' + ruangan + '&model=' + model, function (data) {
                    if (data.status)
                        create_homer_success(data.msg);
                    else
                        create_homer_error(data.msg);
                });
            } else {
                create_homer_error('Denah tidak dapat disimpan karena jumlah ruangan tidak cocok dengan yang dibutuhkan');
            }
        } else {
            create_homer_error('Denah tidak dapat disimpan karena ada jenjang yang lebih dari 0');
        }
    }
</script>