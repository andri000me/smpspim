<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Keuangan', 'Laporan Keuangan Siswa');

$options = array(
    'PELANGGARAN_KS#Kode Pelanggaran#Jumlah (siswa)#Kode Pelanggaran' => 'Kode Pelanggaran',
    'SUMBER_KS#Sumber Informasi#Jumlah (siswa)#Sumber Informasi' => 'Sumber Informasi',
    'LARI_KSH#Frekuensi Lari#Jumlah (siswa)#Frekuensi Lari' => 'Frekuensi Lari',
    'POIN_KSH#Jumlah Poin#Jumlah (siswa)#Poin' => 'Poin',
);
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
                        <!--                        <div class="form-group">
                                                    <label class="col-sm-3 control-label">Kelompokan berdasarkan</label>
                                                    <div class="col-sm-4">
                                                        <select id="kelompok" class="form-control">
                                                            <option value="">-- Pilih Kelompok --</option>
                        <?php
                        foreach ($options as $index => $value) {
                            echo '<option value="' . $index . '">' . $value . '</option>';
                        }
                        ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-success" onclick="filter_lanjutan();"><i class="fa fa-filter"></i>&nbsp;&nbsp;Filter Lanjutan</button>
                                                    </div>
                                                </div>-->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-2">
                                <select id="ta" class="form-control" onchange="ta_changed();">
                                    <?php
                                    foreach ($TA as $value) {
                                        echo '<option value="' . $value->ID_TA . '" ' . ($value->ID_TA == $this->session->userdata("ID_TA_ACTIVE") ? 'selected' : '') . '>' . $value->NAMA_TA . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Tagihan</label>
                            <div class="col-sm-4">
                                <select id="tagihan" class="form-control" onchange="get_detail_tag();">
                                    <option value="">-- Pilih TA terlabih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Detail Tagihan</label>
                            <div class="col-sm-4">
                                <select id="detail_tagihan" class="form-control">
                                    <option value="">-- Pilih Tagihan terlabih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Jenjang</label>
                            <div class="col-sm-4">
                                <select id="jenjang" class="form-control">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <?php
                                    foreach ($JENJANG as $value) {
                                        echo '<option value="' . $value->ID_DEPT. '">' . $value->NAMA_DEPT . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Tingkat</label>
                            <div class="col-sm-4">
                                <select id="tingkat" class="form-control">
                                    <option value="">-- Pilih Tingkat --</option>
                                    <?php
                                    foreach ($TINGKAT as $value) {
                                        echo '<option value="' . $value->ID_TINGK . '">' . $value->KETERANGAN_TINGK . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Kelas</label>
                            <div class="col-sm-4">
                                <select id="kelas" class="form-control">
                                    <option value="">-- Pilih TA terlebih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Pegawai</label>
                            <div class="col-sm-6">
                                <input class="form-control js-source-states-input" type="text" multiple="multiple">
                            </div>
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-default" onclick="clear_select2();"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!--                        <div class="form-group filter-lanjutan">
                                                    <label class="col-sm-3 control-label">Kelompok</label>
                                                    <div class="col-sm-4">
                                                        <select id="kelompok" class="form-control">
                                                            <option value="0">Harian</option>
                                                            <option value="1">Bulanan</option>
                                                        </select>
                                                    </div>
                                                </div>-->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tanggal</label>
                            <div class="col-sm-7">
                                <div class="input-group m-b">
                                    <span class="input-group-addon">mulai tanggal</span> 
                                    <input type="text" data-date-format="yyyy-mm-dd"  id="mulai_tanggal" class="form-control">
                                    <span class="input-group-addon">sampai tanggal</span>
                                    <input type="text" data-date-format="yyyy-mm-dd"  id="akhir_tanggal" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="download_csv();"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;Unduh Data Grafik</button>
                                <button type="button" class="btn btn-success" onclick="datatables_data();"><i class="fa fa-list"></i>&nbsp;&nbsp;Lihat Data Keuangan</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Keuangan</button>
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
$this->generate->chart($id, 'Grafik Keuangan', $single);

$columns = array(
    'TANGGAL BAYAR',
    'JENIS',
    'TA',
    'NIS',
    'NAMA',
    'JENJANG TINGKAT',
    'KELAS',
    'TAGIHAN',
    'NOMINAL',
    'USER INPUT',
);
$id_datatables = 'datatable1';
$title = 'Data Keuangan';
$this->generate->datatables($id_datatables, $title, $columns);
?>

<script type="text/javascript">

    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1], "orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_homer_error("Anda tidak memiliki hak akses untuk menambah guru.");
    };

    // =================================================================================

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/keuangan/get_data'); ?>';
    var toogle_filter = true;
    var pegawai = '';

    var data_response = null;
    var xls_content = "data:application/vnd.ms-excel;charset=utf-8,";

    $(document).ready(function () {
//        $(".filter-lanjutan").hide();

        ta_changed();

        $("#mulai_tanggal").datepicker().datepicker("setDate", new Date());
        $("#akhir_tanggal").datepicker().datepicker("setDate", new Date());

        $(".table-datatable1").attr('style', 'margin-top: -55px;').hide();

        $(".js-source-states-input").select2({
            minimumInputLength: 1,
            escapeMarkup: function (markup) {
                return markup;
            },
            ajax: {
                url: '<?php echo site_url('master_data/pegawai/auto_complete'); ?>',
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
            formatResult: function (element) {
                return element.text;
            },
            formatSelection: function (element) {
                return element.text;
            },
        }).on("change", function (e) {
            var data = $(".js-source-states-input").select2('data');
            pegawai = data.id;
        });
    });

    function download_csv() {
        if (data_response === null) {
            create_homer_error('Silahkan tampilkan grafik terlebih dahulu sebelum mengunduh data');
        } else {
            xls_content += '<table>';
            xls_content += '<tr><td>Tanggal</td><td>Pembayaran</td><td>Pengembalian</td><td>Saldo</td></tr>';
            $.each(data_response.data.x_label, function (key, value) {
                xls_content += '<tr><td>' + value + '</td><td>' + data_response.data.data0[key] + '</td><td>' + data_response.data.data1[key] + '</td><td>' + data_response.data.data2[key] + '</td></tr>';
            });
            xls_content += '</table>';

            var encoded_uri = encodeURI(xls_content);
//            window.open(encoded_uri, '_blank');

            var link = document.createElement("a");
            link.href = encoded_uri;

            link.style = "visibility:hidden";
            link.download = "data_keuangan.xls";

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    function request_chart_<?php echo $id; ?>(type) {
//        var kelompok = $("#kelompok").val();
        var ta = $("#ta").val();
        var jenjang = $("#jenjang").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var mulai_tanggal = $("#mulai_tanggal").val();
        var akhir_tanggal = $("#akhir_tanggal").val();
        var pie_donut = 0;
        var success = function (data) {
            data_response = data;

            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };

        if ((type === 'pie') || (type === 'donut'))
            pie_donut = 1;

        if (moment(mulai_tanggal, "YYYY-MM-DD") > moment(akhir_tanggal, "YYYY-MM-DD")) {
            create_homer_error("Tanggal mulai tidak boleh lebih besar dari tanggal akhir");
            return;
        }

        $(".table-datatable1").slideUp();

//        if(kelompok !== '')
        create_ajax(url, "pie_donut=" + pie_donut + "&ta=" + ta + "&jenjang=" + jenjang + "&tingkat=" + tingkat + "&kelas=" + kelas + "&akhir_tanggal=" + akhir_tanggal + "&mulai_tanggal=" + mulai_tanggal + "&pegawai=" + pegawai, success);
//        else {
//            $("#" + id).html(" ");
//            
//            create_homer_info("Pilih kelompok terlebih dahulu");
//        }
    }

    function export_data() {
        var ta = $("#ta").val();
        var jenjang = $("#jenjang").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var akhir_tanggal = $("#akhir_tanggal").val();
        var mulai_tanggal = $("#mulai_tanggal").val();

        var req = "&ta=" + ta + "&jenjang=" + jenjang + "&tingkat=" + tingkat + "&kelas=" + kelas + "&akhir_tanggal=" + akhir_tanggal + "&mulai_tanggal=" + mulai_tanggal + "&pegawai=" + pegawai;

        window.open("<?php echo site_url('laporan/keuangan/export'); ?>?detail=detail" + req);
    }

    function ta_changed() {
        create_splash("Sistem sedang mengambil kelas dan tagihan");
        var ta = $("#ta").val();
        var success = function (data) {
            var kelas = $("#kelas");
            var tagihan = $("#tagihan");

            kelas.html('<option value="">-- Pilih Kelas --</option>');
            tagihan.html('<option value="">-- Pilih Tagihan --</option>');

            $.each(data.kelas, function (index, item) {
                kelas.append('<option value="' + item.ID_KELAS + '">' + item.NAMA_KELAS + '</option>');
            });

            $.each(data.tagihan, function (index, item) {
                tagihan.append('<option value="' + item.ID_TAG + '">' + item.NAMA_TAG + '</option>');
            });

            remove_splash();
        };

        create_ajax('<?php echo site_url('laporan/keuangan/ta_changed'); ?>', 'ta=' + ta, success);
    }

    function get_detail_tag() {
        create_splash("Sistem sedang mengambil detail tagihan");
        var tagihan = $("#tagihan").val();
        var success = function (data) {
            var detail_tagihan = $("#detail_tagihan");

            detail_tagihan.html('<option value="">-- Pilih Detail Tagihan --</option>');

            $.each(data.detail_tagihan, function (index, item) {
                detail_tagihan.append('<option value="' + item.ID_DT + '">' + item.NAMA_DT + '</option>');
            });

            remove_splash();
        };

        create_ajax('<?php echo site_url('laporan/keuangan/get_detail_tag'); ?>', 'tagihan=' + tagihan, success);
    }

    function filter_lanjutan() {
        toogle_filter = !toogle_filter;

        if (toogle_filter)
            $(".filter-lanjutan").slideUp();
        else
            $(".filter-lanjutan").slideDown();
    }

    function clear_select2() {
        $(".js-source-states-input").select2('data', null);
        pegawai = '';
    }

    function datatables_data() {
        var ta = $("#ta").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var akhir_tanggal = $("#akhir_tanggal").val();
        var mulai_tanggal = $("#mulai_tanggal").val();

        var req = "?ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&akhir_tanggal=" + akhir_tanggal + "&mulai_tanggal=" + mulai_tanggal + "&pegawai=" + pegawai;

        table = initialize_datatables(id_table, '<?php echo site_url('laporan/keuangan/ajax_list'); ?>' + req, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        remove_splash();
        $(".table-datatable1").slideDown();
        $(".buttons-add").remove();
    }
    ;

</script>