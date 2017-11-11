<?php
$this->generate->generate_panel_content("Tambah Absen Siswa Bulanan", "Form tambah absen siswa bulanan");
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
                        <?php $this->generate->input_select2('Kelas', array('name' => 'KELAS_FILTER', 'url' => site_url('akademik/kelas/auto_complete')), TRUE, 7, FALSE, NULL); ?>
                        <?php $this->generate->input_select2('Jenis Kegiatan', array('name' => 'JENIS_FILTER', 'url' => site_url('master_data/jenis_absensi/auto_complete')), TRUE, 8, FALSE, NULL); ?>
                        <div class="form-group">
                            <label class="control-label col-md-2">Bulan *</label>
                            <div class="col-md-1">
                                <input type="number" class="form-control" name="BULAN_FILTER" value="<?php echo date('j'); ?>" onchange="date_changed();" />
                            </div>
                            <label class="control-label col-md-1">Tahun *</label>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="TAHUN_FILTER" value="<?php echo date('Y'); ?>" onchange="date_changed();" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2 col-md-offset-2">
                                <button type="button" class="btn btn-save btn-primary btn-block" onclick="tetapkan_filter();"><i class="fa fa-book"></i>&nbsp;&nbsp;Buka</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$title = 'Siswa';
$id_datatables = 'datatable1';

$columns = array(
    'NO ABSEN',
    'NIS',
    'NAMA',
    'ALASAN',
    'KETERANGAN',
    'AKSI',
);

$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var KELAS_FILTER = null;
    var JENIS_FILTER = null;
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1], "orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings) {

    };
    var functionAddData = function (e, dt, node, config) {

    };

    $(function () {
        $(".js-source-states-KELAS_FILTER").on("change", "", function () {
            var data_kelas = $(this).select2("data");

            KELAS_FILTER = data_kelas.id;
            $(".table-datatable1").slideUp();
        });

        $(".js-source-states-JENIS_FILTER").on("change", "", function () {
            var data_jenis = $(this).select2("data");

            JENIS_FILTER = data_jenis.id;
            $(".table-datatable1").slideUp();
        });

        $(".table-datatable1").hide();
    });
    
    function date_changed() {
        $(".table-datatable1").slideUp();
    }

    function tetapkan_filter() {
        var BULAN_FILTER = $("#BULAN_FILTER").val();
        var TAHUN_FILTER = $("#TAHUN_FILTER").val();

        if (KELAS_FILTER === null || JENIS_FILTER === null || BULAN_FILTER === '' || TAHUN_FILTER === null) {
            create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
            
            $(".table-datatable1").slideUp();
        } else if ((parseInt(BULAN_FILTER) >= 1) && (parseInt(BULAN_FILTER) <= 12)) {
            create_homer_error('Bulan harus antara 1 sampai 12.');
        } else {
            $(".table-datatable1").attr('style', 'margin-top: -60px;');

            table = initialize_datatables(id_table, '<?php echo site_url('akademik/kehadiran/ajax_form_bulanan'); ?>/' + KELAS_FILTER + '/' + JENIS_FILTER + '/' + BULAN_FILTER + '/' + TAHUN_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

            remove_splash();

            $(".buttons-add").remove();

            $(".table-datatable1").slideDown();

            $("#datatable1_length").children().children().val('-1').trigger('change');
        }
    }

</script>
