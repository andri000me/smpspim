<?php
$title = 'Laporan Hafalan Siswa Perpondok';
$subtitle = "Daftar hafalan siswa";
$id_datatables = 'datatable1';

$columns = array(
    'NO ABSEN',
    'NIS',
    'NAMA',
    'KEHADIRAN',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Pilih Filter
                </div>
                <div class="panel-body">
                    <h1>BELUM FIX</h1>
<!--                    <form class="form-horizontal">
                        <?php $this->generate->input_select2('Pondok', array('name' => 'PONDOK_FILTER', 'url' => site_url('master_data/pondok_siswa/auto_complete')), TRUE, 7, FALSE, NULL); ?>
                    </form>-->
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var status_updated = false;
    var ID_KELAS = null;
    var table;
    var url_delete = '<?php echo site_url('ph/nilai/ajax_delete'); ?>/';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{"targets": [-1], "orderable": false}];
    var orders = '';//[[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {
        setTimeout(function () {
//            get_pegawai();
        }, 2500);

        simpan_status();
    };
    var functionAddData = function (e, dt, node, config) {
        window.open('<?php echo site_url('ph/nilai/form'); ?>', '_blank');
    };

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');

        $(".js-source-states-PONDOK_FILTER").on("change", "", function () {
            var data_kelas = $(this).select2("data");

            $(".table-datatable1").slideUp();

            ID_KELAS = data_kelas.id;
            get_table_header(data_kelas.id);
            table.destroy();
            status_updated = false;
        });

        $(".table-datatable1").hide();
    });

    function get_table_header(ID_KELAS) {
        var success = function (data) {
            $("#datatable1").html('<thead><tr><th>NO ABSEN</th><th>NIS</th><th>NAMA</th><th>KEHADIRAN</th><th>AKSI</th></tr></thead><tfoot><tr><th>NO ABSEN</th><th>NIS</th><th>NAMA</th><th>KEHADIRAN</th><th>AKSI</th></tfoot>');
            $("#datatable1").find("tfoot").html('<tr class="table_footer"></tr>').hide();
            $("#datatable1").find("thead").html('<tr class="header"></tr><tr class="child_header"></tr>');
            $(".header").append('<th rowspan="2">NO ABSEN</th>');
            $(".table_footer").append('<th>NO ABSEN</th>');
            $(".header").append('<th rowspan="2">NIS</th>');
            $(".table_footer").append('<th>NIS</th>');
            $(".header").append('<th rowspan="2">NAMA</th>');
            $(".table_footer").append('<th>NAMA</th>');
            $(".header").append('<th rowspan="2">LARI</th>');
            $(".table_footer").append('<th>LARI</th>');
            $.each(data, function (index, item) {
                $(".header").append('<th>' + item.NAMA_KITAB + ' | ' + item.AWAL_BATASAN + ' - ' + item.AKHIR_BATASAN + '</th>');
                $(".child_header").append('<!--<th>PENYEMAK</th>--><th>NILAI [MAX=' + item.NILAI_MAKS_BATASAN + ']</th>');
                $(".table_footer").append('<!--<th>PENYEMAK</th>--><th>NILAI</th>');
            });
            $(".header").append('<th rowspan="2">NILAI AKHIR</th>');
            $(".table_footer").append('<th>NILAI AKHIR</th>');
            $(".header").append('<th rowspan="2">STATUS</th>');
            $(".table_footer").append('<th>STATUS</th>');
            $(".header").append('<th rowspan="2">AKSI</th>');
            $(".table_footer").append('<th>AKSI</th>');

            get_datatables(ID_KELAS);
        };

        create_splash("Sistem sedang mengambil data nilai");
        create_ajax('<?php echo site_url('ph/nilai/get_kitab'); ?>', 'ID_KELAS=' + ID_KELAS, success);
    }

    function get_pegawai() {
        var success = function (data) {
            var option_pegawai = $(".option-pegawai");

            option_pegawai.html('<option value="">-- Pilih Pegawai --</option>');
            $.each(data, function (index, item) {
                option_pegawai.append('<option value="' + item.id + '">' + item.text + '</option>');
            });

            $.each(option_pegawai, function () {
                var value_penyemak = $(this).data('pegawai');
                $(this).val(value_penyemak);
            });

        };

        create_ajax('<?php echo site_url('master_data/pegawai/get_all_select2'); ?>', '', success);
    }

    function get_datatables(ID_KELAS) {
        $(".table-datatable1").attr('style', 'margin-top: -60px;');

        table = initialize_datatables(id_table, '<?php echo site_url('ph/nilai/ajax_list'); ?>/' + ID_KELAS, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        remove_splash();
        $('<a class="dt-button btn btn-sm btn-default" href="#" onclick="cetak_nilai()"><span>Cetak Nilai</span></a>').insertAfter('.buttons-add');
        $(".buttons-add").remove();
        $(".table-datatable1").slideDown();
//        $(".datatables-search-NO-ABSEN").replaceWith('NO ABSEN');
//        $(".datatables-search-NIS").replaceWith('NIS');
//        $(".datatables-search-NAMA").replaceWith('NAMA');
//        $(".datatables-search-PENYEMAK").replaceWith('PENYEMAK');
//        $(".datatables-search-NILAI").replaceWith('NILAI');
//        $(".datatables-search-STATUS").replaceWith('STATUS');
        $("#datatable1_length").children().children().val('-1').trigger('change');
    }
    
    function cetak_nilai() {
        window.open('<?php echo site_url('ph/nilai/cetak_kelas'); ?>/' + ID_KELAS, '_blank');
    }

    function simpan_nilai(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var batasan = $(that).data('batasan');
        var kitab = $(that).data('kitab');
        var siswa = $(that).data('siswa');
        var nilai = $(that).data('nilai');
        var url = 'batasan=' + batasan + '&siswa=' + siswa + '&nilai_maks=' + nilai + '&kitab=' + kitab;
        var dapat_disimpan = true;

        $.each(batasan, function (index, item) {
//            var penyemak = $("#penyemak_" + siswa + "_" + item).val();
            var nilai = $("#nilai_" + siswa + "_" + item).val();

//            url += '&penyemak_' + item + '=' + penyemak;
            url += '&nilai_' + item + '=' + nilai;

            if (nilai === '') {
//            if (penyemak === '' || nilai === '') {
                create_homer_error('Ada field yang kosong. Silahkan dilengkapi terlebih dahulu.');

                dapat_disimpan = false;
            }
        });

        var success = function (data) {
            if (data.RESULT) {
                $(that).addClass('btn-success');
                $("#nilai_total_" + siswa).html(data.NILAI);
                $("#status_" + siswa).html(data.STATUS);
            } else {
                $(that).addClass('btn-error');
            }
            $(that).next().remove();
            $(that).show();
            $(that).removeClass('btn-primary');
        };

        if (dapat_disimpan) {
            $(that).hide();
            $(loading_bar).insertAfter(that);
            create_ajax('<?php echo site_url('ph/nilai/simpan_nilai'); ?>', url, success);
        }
    }

    function check_nilai(that) {
        var nilai_maks = $(that).data('nilai');
        var nilai = $(that).val();

        if (parseInt(nilai) > parseInt(nilai_maks)) {
            create_homer_error("Nilai tidak boleh lebih besar dari nilai maksimal.");
            $(that).val('0');
        }
    }

    function simpan_status() {
        var keluar = $(".siswa-keluar");
        var lari = $(".siswa-lari");
        var url = 'keluar=';
        var request = false;

        $.each(keluar, function () {
            request = true;
            url += $(this).data('siswa') + ',';
        });

        url += '&lari=';
        $.each(lari, function () {
            request = true;
            url += $(this).data('siswa') + ',';
        });
        var success = function () {
            reload_datatables(table);
            status_updated = true;
            remove_splash();
        };

        if(request && !status_updated) {
            create_splash("Sistem sedang menyimpan data siswa bermasalah");
            create_ajax('<?php echo site_url('ph/nilai/simpan_status'); ?>', url, success);
        }
    }
</script>