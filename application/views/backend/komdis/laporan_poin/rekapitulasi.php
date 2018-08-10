<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi SIMAPES
 * PIM KAJEN
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */
$id_datatables = 'table-rekapitulasi';
$title = 'rekapitulasi pelanggaran';
$columns = array(
    'ID'
);

$this->generate->generate_panel_content("Rekapitulasi Pelanggaran", 'Data rekapitulasi pelanggaran');
?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Pilih Filter
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-sm-1 control-label">Baris</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="baris" onchange="changeRow(this)">
                                            <option value="siswa">Siswa</option>
                                            <option value="kelas">Kelas</option>
                                            <option value="bulan">Bulan</option>
                                            <option value="jenis_pelanggaran">Jenis Pelanggaran</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-1 control-label">Kolom</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="kolom" onchange="changeColumn(this)">
                                            <option value="bulan">Bulan</option>
                                            <option value="jenis_pelanggaran">Jenis Pelanggaran</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-2 control-label">Data Kolom</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="data_kolom" onchange="changeColumn(this)">
                                            <option value="poin">Poin</option>
                                            <option value="jumlah_pelanggar">Jumlah Pelanggar</option>
                                        </select>
                                    </div>
                                    <!--                                    <label class="col-sm-2 control-label">Jumlah Baris</label>
                                                                        <div class="col-sm-1">
                                                                            <input type="text" id="jumlah_baris" class="form-control" value="25"/>
                                                                        </div>-->
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Header</label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <input type="checkbox" class="field_header" checked="" value="POIN_TAHUN_LALU_KSH">Point Thn Lalu&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" class="field_header" checked="" value="LARI_KSH">Lari&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" class="field_header" checked="" value="NAMA_KJT">Surat&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" class="field_header" checked="" value="JUMLAH_SISWA_AKTIF">Jumlah Siswa Aktif Melanggar&nbsp;&nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="button" class="btn btn-primary btn-block" onclick="get_datatables();">PROSES</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var table;
    var baris = null;
    var kolom = null;
    var data_kolom = null;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [];
    var orders = [];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {

    };
    var field_row = {
        'siswa': [
            'NO_ABSEN_AS',
            'NIS_SISWA',
            'NAMA_SISWA',
        ],
        'kelas': [
            'KODE_KELAS',
            'NAMA_KELAS',
            'NAMA_PEG',
        ],
        'jenis_pelanggaran': [
            'KODE_KJP',
            'NAMA_KJP'
        ],
    };

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');
        $(".table-table-rekapitulasi").css('margin-top', '-70px').hide().find('tfoot').remove();
        $(".table-table-rekapitulasi").slideDown();
    });

    function get_datatables() {
        kolom = $("#kolom").val();
        baris = $("#baris").val();
        data_kolom = $("#data_kolom").val();
        var field_header = [];
        $(".field_header").each(function () {
            if ($(this).is(':checked'))
                field_header.push($(this).val());
        });

        if (kolom === baris) {
            create_homer_error('Kolom dan baris tidak boleh sama');
        } else {
            $("#table-rekapitulasi").find("thead").html('<tr id="header_rekapitulasi"></tr><tr id="subheader_rekapitulasi"></tr>');

            if (baris === 'siswa')
                $("#header_rekapitulasi").append('<th rowspan="2">Absen</th><th rowspan="2">NIS</th><th rowspan="2">Nama</th>');
            else if (baris === 'kelas')
                $("#header_rekapitulasi").append('<th rowspan="2">Kelas</th><th rowspan="2">Wali</th>');
            else if (baris === 'bulan')
                $("#header_rekapitulasi").append('<th rowspan="2">Tahun</th><th rowspan="2">Bulan</th>');
            else if (baris === 'jenis_pelanggaran')
                $("#header_rekapitulasi").append('<th rowspan="2">Kode</th>');

            $(".field_header").each(function () {
                if ($(this).is(':checked')) {
                    var name_field = $(this).val();

                    if (name_field === 'POIN_TAHUN_LALU_KSH')
                        $("#header_rekapitulasi").append('<th rowspan="2">Poin Thn Lalu</th>');
                    else if (name_field === 'LARI_KSH')
                        $("#header_rekapitulasi").append('<th rowspan="2">Lari</th>');
                    else if (name_field === 'NAMA_KJT')
                        $("#header_rekapitulasi").append('<th rowspan="2">Surat</th>');
                    else if (name_field === 'JUMLAH_SISWA_AKTIF')
                        $("#header_rekapitulasi").append('<th rowspan="2" style="word-wrap: break-word">Jumlah Siswa Aktif Melanggar</th>');
                }
            });

            if (data_kolom === 'poin')
                $("#header_rekapitulasi").append('<th colspan="12" id="header_poin">Poin </th>');
            else if (data_kolom === 'jumlah_pelanggar')
                $("#header_rekapitulasi").append('<th colspan="15" id="header_jumlah_pelanggar">Jumlah pelanggar </th>');

            if (kolom === 'bulan') {
                $("#header_poin").append('bulan ke-');
                for (var i = 1; i <= 12; i++) {
                    $("#subheader_rekapitulasi").append('<th>' + i + '</th>');
                }
            } else if (kolom === 'jenis_pelanggaran') {
                $("#header_poin").append('jenis ke-');
                for (var i = 1; i <= 15; i++) {
                    $("#subheader_rekapitulasi").append('<th>' + i + '</th>');
                }
            }

            table = initialize_datatables(id_table, '<?php echo site_url('komdis/laporan_poin/datatables_rekapilutasi'); ?>?kolom=' + kolom + '&baris=' + baris + '&field_header=' + JSON.stringify(field_header) + '&data_kolom=' + data_kolom, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
            remove_splash();

            $(".table-table-rekapitulasi").slideDown();
        }

        return false;
    }

    function changeColumn(t) {

    }

    function changeRow(t) {

    }
</script>