
<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                PROSES PEMBUATAN ATURAN DENAH <?php echo strtoupper($TITLE); ?>
            </h2>
            <small>Pembuatan denah <?php echo strtolower($TITLE); ?> pada tahun ajaran dan catur wulan aktif</small>
        </div>
    </div>
</div>
<?php echo $this->generate->form_open('form-denah', 'denah'); ?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    PENGATURAN
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="jumlah_perruang" class="form-control input-block-level" onkeyup="change_capacity(this);" onchange="change_capacity(this);" placeholder="Kapasitas Ruang" />
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" id="btn_request" disabled="true" onclick="proses_aturan();">BUAT ATURAN BARU</button>
                        </div>
                        <div class="col-md-2 col-md-offset-3">
                            <button type="button" class="btn btn-danger btn-block" onclick="reset_aturan();">RESET ATURAN</button>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block" id="btn_buat">BUAT DENAH</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hblue">
                <div class="panel-heading hbuilt">
                    ATURAN PEMBUATAN DENAH PESERTA LAKI-LAKI
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="lk" cellpadding="1" cellspacing="1" class="table table-bordered table-striped">
                            <thead id="table_jenjang"></thead>
                            <thead id="table_tingkat"></thead>
                            <tbody id="table_denah"></tbody>
                            <tfoot id="table_jumlah"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hviolet">
                <div class="panel-heading hbuilt">
                    ATURAN PEMBUATAN DENAH PESERTA PEREMPUAN
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="pr" cellpadding="1" cellspacing="1" class="table table-bordered table-striped">
                            <thead id="table_jenjang"></thead>
                            <thead id="table_tingkat"></thead>
                            <tbody id="table_denah"></tbody>
                            <tfoot id="table_jumlah"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');

        proses_aturan();
    });

    function change_capacity(t) {
        var kapasitas = $(t).val();

        if ((parseInt(kapasitas) == 0) || (kapasitas == ""))
            $("#btn_request").prop('disabled', true);
        else
            $("#btn_request").removeAttr('disabled');
    }

    function proses_aturan() {
        create_splash('Sistem sedang memroses aturan denah ruangan ujian');

        var jumlah_perruang = $("#jumlah_perruang").val();
        var success = function (data) {
            if (data.status) {
                create_homer_success(data.msg);
                show_data_denah(data.data);
            } else {
                create_swal_error('Gagal membuat aturan denah', data.msg);
            }

            remove_splash();
        };

        create_ajax('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/proses_aturan' : 'pu/denah_us/proses_aturan' ); ?>', 'jumlah_perruang=' + jumlah_perruang, success);
    }

    function show_data_denah(data) {
        create_splash('Sistem sedang menampilkan data aturan denah.')

        var data_lk = data.lk;
        var id_lk = 'lk';

        $("#" + id_lk + " #table_jenjang").html(build_tag_html(data_lk.jenjang, 'jenjang', 'Jenjang', null, null, id_lk));
        $("#" + id_lk + " #table_tingkat").html(build_tag_html(data_lk.tingkat, 'tingkat', 'Tingkat', null, null, id_lk));
        $("#" + id_lk + " #table_denah").html(create_tag_html(data_lk, id_lk));
        $("#" + id_lk + " #table_jumlah").html(build_tag_html(data_lk.jumlah, 'jumlah', 'Jumlah', null, null, id_lk));

        var data_pr = data.pr;
        var id_pr = 'pr';

        $("#" + id_pr + " #table_jenjang").html(build_tag_html(data_pr.jenjang, 'jenjang', 'Jenjang', null, null, id_pr));
        $("#" + id_pr + " #table_tingkat").html(build_tag_html(data_pr.tingkat, 'tingkat', 'Tingkat', null, null, id_pr));
        $("#" + id_pr + " #table_denah").html(create_tag_html(data_pr, id_pr));
        $("#" + id_pr + " #table_jumlah").html(build_tag_html(data_pr.jumlah, 'jumlah', 'Jumlah', null, null, id_pr));

        remove_splash();
    }

    function create_tag_html(data, jk) {
        var tag_html = '';
        var key_ruang_sisa = data.data_aturan.length;

        $.each(data.data_aturan, function (key, value) {
            tag_html += build_tag_html(value, key, 'Data', data.ruang, key_ruang_sisa, jk);
        });

        return tag_html;
    }

    function build_tag_html(data, key, detail, ruang, key_ruang_sisa, jk) {
        var tag_html = '<tr>';
        var total = 0;
        var kapasitas = 0;

        if (detail === 'Data') {
            kapasitas = parseInt(ruang[key]['KAPASITAS_RUANG']);
            
            if (key === (key_ruang_sisa - 1))
                tag_html += '<td>' + (key + 1) + '.&nbsp;&nbsp;&nbsp;SISA</td>';
            else
                tag_html += '<td>' + (key + 1) + '.&nbsp;&nbsp;&nbsp;Ruang ' + ruang[key]['KODE_RUANG'] + ' [' + kapasitas + ' siswa]</td>';
        } else {
            tag_html += '<td>' + detail + '</td>';
        }

        $.each(data, function (index, item) {
            if ((detail === 'Data') || (detail === 'Jumlah'))
                total += parseInt(item);

            if (detail === 'Jumlah') {
                tag_html += '<td class="data_count data_count_col_' + jk + ' count_col_' + index + '_' + jk + '" data-count="' + item + '" data-perubahan="' + item + '">' + item + '</td>';
            } else {
                tag_html += '<td>';

                if (detail === 'Data')
                    tag_html += '<input type="text" class="form-control input-sm data_nilai data_' + key + '_' + index + '_' + jk + ' col_' + index + '_' + jk + ' row_' + key + '_' + jk + '" style="width:40px" name="aturan_' + jk + '[' + key + '][' + index + ']" value="' + item + '" data-nilai="' + item + '" onkeydown="return haltnondigit(event);" onchange="change_value(' + key + ', ' + index + ', \'' + jk + '\')"/>';
                else
                    tag_html += item;

                tag_html += '</td>';
            }
        });

        if (detail === 'Jenjang')
            tag_html += '<td>#</td>';
        else if (detail === 'Tingkat')
            tag_html += '<td>Total</td>';
        else if ((detail === 'Data') || (detail === 'Jumlah'))
            tag_html += '<td class="data_count count_row_' + key + '_' + jk + '" data-count="' + total + '">' + (kapasitas < total ? '<strong>' : '') + total + (kapasitas < total ? '</strong>' : '') + '</td>';

        tag_html += '</tr>';

        return tag_html;
    }

    function change_value(row, col, jk) {
        proses_change('row', row, jk);
        proses_change('col', col, jk);
    }

    function proses_change(status, value, jk) {
        var jumlah_new = 0;

        $('.' + status + '_' + value + "_" + jk).each(function () {
            jumlah_new += parseInt($(this).val());
        });
        var jumlah_old = $(".count_" + status + "_" + value + "_" + jk).data('count');
        var selisih = jumlah_new - jumlah_old;

        if (selisih == 0)
            $(".count_" + status + "_" + value + "_" + jk).html(jumlah_old);
        else if (selisih > 0)
            $(".count_" + status + "_" + value + "_" + jk).html(jumlah_old + ' + ' + selisih + ' = ' + (jumlah_old + (jumlah_new - jumlah_old)));
        else if (selisih < 0)
            $(".count_" + status + "_" + value + "_" + jk).html(jumlah_old + ' - ' + Math.abs(selisih) + ' = ' + (jumlah_old + (jumlah_new - jumlah_old)));

        $(".count_" + status + "_" + value + "_" + jk).data('perubahan', jumlah_new);

        if (status === 'col') {
            var jumlah_peserta_perubahan = 0;
            var jumlah_peserta = 0;
            $(".data_count_col_" + jk).each(function () {
                jumlah_peserta_perubahan += parseInt($(this).data('perubahan'));
                jumlah_peserta += parseInt($(this).data('count'));
                console.log(jumlah_peserta_perubahan);
            });

            $(".count_row_jumlah" + "_" + jk).html(jumlah_peserta_perubahan);

            if (jumlah_peserta === jumlah_peserta_perubahan)
                $("#btn_buat").removeAttr('disabled');
            else
                $("#btn_buat").prop('disabled', true);
        }
    }

    function reset_aturan() {
        proses_reset('count');
        proses_reset('nilai');
    }

    function proses_reset(data) {
        $('.data_' + data).each(function () {
            $(this).html($(this).data(data));
        });
    }

    function action_save_denah(id) {
        var action = function (isConfirm) {
            if (isConfirm) {
                saving_denah(id);
            }
        };

        create_swal_option('Apakah Anda yakin melanjutkan?', 'Perubahan aturan akan disimpan dan denah akan dibuat secara otomatis dengan menggunakan aturan ini.', action);

        return false;
    }

    function saving_denah(id) {
        var msg = "Sistem sedang menyimpan aturan denah dan membuat denah secara otomatis.";

        var success = function (data) {
            if (data.status) {
//                window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/show_denah' : 'pu/denah_us/proses_aturan'); ?>', '_blank');
                create_homer_success("Berhasil menyimpan denah. Halaman ini akan ditutup secara otomatis.");

                setTimeout(function () {
                    window.close();
                }, 2500);
            } else {
                create_homer_error(data.msg);
            }

            remove_splash();
        };

        create_form_ajax('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/simpan_denah' : 'pu/denah_us/simpan_denah'); ?>', id, success, msg);
    }

</script>