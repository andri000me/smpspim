<?php
$title = 'Laporan Saldo Tagihan';
$subtitle = "Daftar semua laporan saldo pembayaran tagihan keuangan";
$id_datatables1 = 'datatable1';
$id_datatables2 = 'datatable2';

$columns = array(
    'NAMA TA',
    'NAMA TAGIHAN',
    'NAMA DETAIL',
    'JENJANG',
    'NIS',
    'SISWA',
    'NOMINAL',
    'KETERANGAN',
    'USER',
    'WAKTU',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
?>

<form class="form-horizontal">
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        Saldo Tagihan
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Filter mulai tanggal</label>
                                    <div class="col-sm-2">
                                        <input data-date-format="yyyy-mm-dd" class="form-control required" type="text" name="TANGGAL_MULAI" value=""  placeholder="YYYY-MM-DD"  id="TANGGAL_MULAI" onchange="date_changed()"/>
                                    </div>
                                    <label class="col-sm-2 control-label">sampai tanggal</label>
                                    <div class="col-sm-2">
                                        <input data-date-format="yyyy-mm-dd" class="form-control required" type="text" name="TANGGAL_AKHIR" value="<?php echo date('Y-m-d'); ?>"  placeholder="YYYY-MM-DD"  id="TANGGAL_AKHIR"  onchange="date_changed()"/>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-primary" onclick="cari_data()"><i class="fa fa-search"></i>&nbsp;&nbsp;Cari</button>
                                </div>
                            </div>
                        </div>
                        <div class="row calc-saldo">
                            <div class="col-md-12">
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 col-md-offset-2"><h4 class="text-muted font-bold">Pembayaran</h4></div>
                                    <div class="col-md-4 text-right"><h4 class="text-muted font-bold" id="nominal_pembayaran"></h4></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-md-offset-2"><h4 class="text-muted font-bold">Pengembalian</h4></div>
                                    <div class="col-md-4 text-right"><h4 class="text-muted font-bold" id="nominal_pengembalian"></h4></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-md-offset-2"><h3 class="text-primary font-extra-bold">Saldo</h3></div>
                                    <div class="col-md-4 text-right"><h3 class="text-primary font-extra-bold" id="nominal_saldo"></h3></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$this->generate->datatables($id_datatables1, 'Data Pembayaran Tagihan', $columns);
$this->generate->datatables($id_datatables2, 'Data Pengembalian Tagihan', $columns);
?>
<script type="text/javascript">
    var table1;
    var table2;
    var saldo = null;
    var pembayaran = null;
    var pengembalian = null;
    var id_table1 = '<?php echo $id_datatables1; ?>';
    var id_table2 = '<?php echo $id_datatables2; ?>';
    var columns = '';//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = '';//[[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback1 = function (settings) {
        var api = this.api();
        var json = api.ajax.json();

        pembayaran = parseInt(json.nominal);

        $(".total-pembayaran").remove();
        $('<div class="text-center total-pembayaran"><h2 class="font-extra-bold">TOTAL: ' + formattedIDR(json.nominal) + '</h2></div>').insertBefore("#<?php echo $id_datatables1; ?>");

        calc_saldo();
    };
    var functionDrawCallback2 = function (settings) {
        var api = this.api();
        var json = api.ajax.json();

        pengembalian = parseInt(json.nominal);

        $(".total-pengembalian").remove();
        $('<div class="text-center total-pengembalian"><h2 class="font-extra-bold">TOTAL: ' + formattedIDR(json.nominal) + '</h2></div>').insertBefore("#<?php echo $id_datatables2; ?>");

        calc_saldo();
    };
    var functionAddData = function (e, dt, node, config) {
//        create_form_input(id_form, id_modal, url_form_add, title, null);
    };

    $(document).ready(function () {
        $("#TANGGAL_MULAI, #TANGGAL_AKHIR").datepicker();

        $(".table-datatable2, .table-datatable1").attr('style', 'margin-top: -65px;');
        $("tfoot, .table-datatable2, .table-datatable1, .calc-saldo").hide();
        $(".buttons-add").remove();

    });

    function date_changed() {
        $(".table-datatable2, .table-datatable1, .calc-saldo").slideUp();
    }

    function cari_data() {
        var TANGGAL_MULAI = $("#TANGGAL_MULAI").val();
        var TANGGAL_AKHIR = $("#TANGGAL_AKHIR").val();

        if (TANGGAL_AKHIR === '' || TANGGAL_MULAI === '') {
            create_homer_error('Tanggal tidak boleh kosong');
        } else {
            table1 = initialize_datatables(id_table1, '<?php echo site_url('keuangan/laporan_saldo/ajax_list1'); ?>/' + TANGGAL_MULAI + '/' + TANGGAL_AKHIR, columns, orders, functionInitComplete, functionDrawCallback1, functionAddData, requestExport);
            table2 = initialize_datatables(id_table2, '<?php echo site_url('keuangan/laporan_saldo/ajax_list2'); ?>/' + TANGGAL_MULAI + '/' + TANGGAL_AKHIR, columns, orders, functionInitComplete, functionDrawCallback2, functionAddData, requestExport);

            $(".table-datatable2, .table-datatable1").slideDown();
        }
    }

    function calc_saldo() {
        if ((pengembalian !== null) && (pembayaran !== null)) {
            saldo = pembayaran - pengembalian;

            $("#nominal_pembayaran").html(formattedIDR(pembayaran));
            $("#nominal_pengembalian").html(formattedIDR(pengembalian));
            $("#nominal_saldo").html(formattedIDR(saldo));

            remove_splash();

            $(".calc-saldo").slideDown();

            pengembalian = null;
            pembayaran = null;
            saldo = null;
        }
    }
</script>