<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Hafalan Siswa', 'Laporan Hafalan Siswa');
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Pengaturan Grafik
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-2">
                                <select id="ta" class="form-control" onchange="get_kelas();">
                                    <?php
                                    foreach ($TA as $value) {
                                        echo '<option value="' . $value->ID_TA . '" ' . ($value->ID_TA == $this->session->userdata("ID_TA_ACTIVE") ? 'selected' : '') . '>' . $value->NAMA_TA . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Jenjang</label>
                            <div class="col-sm-4">
                                <select id="jenjang" class="form-control" onchange="get_tingkat()">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <?php
                                    foreach ($DEPT as $value) {
                                        echo '<option value="' . $value->ID_DEPT . '">' . $value->NAMA_DEPT . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Tingkat</label>
                            <div class="col-sm-4">
                                <select id="tingkat" class="form-control">
                                    <option value="">-- Pilih Jenjang Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="download_csv();"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;Unduh Data Grafik</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Hafalan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$single = FALSE;
$this->generate->chart($id, 'Grafik Hafalan', $single);
?>

<script type="text/javascript">

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/hafalan/get_data'); ?>';
    var toogle_filter = true;

    var data_response = null;
    var xls_content = "data:application/vnd.ms-excel;charset=utf-8,";

    $(document).ready(function () {
        $(".filter-lanjutan").show();

        get_kelas();
    });

    function download_csv() {
        if (data_response === null) {
            create_homer_error('Silahkan tampilkan grafik terlebih dahulu sebelum mengunduh data');
        } else {
            xls_content += '<table>';
            xls_content += '<tr><td>Tanggal</td><td>Jumlah Siswa</td><td>Jumlah Siswa Setoran</td><td>Jumlah Siswa Hafal</td><td>Jumlah Siswa Tidak Hafal</td><td>Jumlah Siswa Gugur</td><td>Jumlah Siswa Keluar</td></tr>';
            $.each(data_response.data.x_label, function (key, value) {
                xls_content += '<tr><td>' + value + '</td><td>' + data_response.data.data0[key] + '</td><td>' + data_response.data.data1[key] + '</td><td>' + data_response.data.data2[key] + '</td><td>' + data_response.data.data3[key] + '</td><td>' + data_response.data.data4[key] + '</td><td>' + data_response.data.data5[key] + '</td></tr>';
            });
            xls_content += '</table>';

            var encoded_uri = encodeURI(xls_content);
//            window.open(encoded_uri, '_blank');

            var link = document.createElement("a");
            link.href = encoded_uri;

            link.style = "visibility:hidden";
            link.download = "data_hafalan.xls";

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    function request_chart_<?php echo $id; ?>(type) {
        var ta = $("#ta").val();
        var jenjang = $("#jenjang").val();
        var tingkat = $("#tingkat").val();
        var pie_donut = 0;
        var success = function (data) {
            data_response = data;

            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };

        if ((type === 'pie') || (type === 'donut'))
            pie_donut = 1;

        create_ajax(url, "pie_donut=" + pie_donut + "&ta=" + ta + "&tingkat=" + tingkat + "&jenjang=" + jenjang, success);
    }

    function export_data() {
        var kelompok = $("#kelompok").val();
        var ta = $("#ta").val();
        var cawu = $("#cawu").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var bulan = $("#bulan").val();
        var tahun = $("#tahun").val();

        var req = "&ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&cawu=" + cawu + "&bulan=" + bulan + "&tahun=" + tahun;

        window.open("<?php echo site_url('laporan/hafalan/export'); ?>?" + req);
    }

    function get_tingkat() {
        create_splash("Sistem sedang mengambil tingkat");
        var jenjang = $('#jenjang').val();
        var success = function (data) {
            var tingkat = $("#tingkat");

            tingkat.html('<option value="">-- Pilih Tingkat --</option>');

            $.each(data, function (index, item) {
                tingkat.append('<option value="' + item.ID_TINGK + '">' + item.KETERANGAN_TINGK + '</option>');
            });

            remove_splash();
        };

        create_ajax('<?php echo site_url('laporan/hafalan/get_tingkat'); ?>', 'jenjang=' + jenjang, success);
    }

    function filter_lanjutan() {
        toogle_filter = !toogle_filter;

        if (toogle_filter)
            $(".filter-lanjutan").slideUp();
        else
            $(".filter-lanjutan").slideDown();
    }

</script>