<?php
$this->generate->generate_panel_content("Tambah Absen Siswa", "Form tambah absen siswa");
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
                        <?php $this->generate->input_date('Tanggal', array('name' => 'TANGGAL_FILTER', 'value' => $this->date_format->to_view(date('Y-m-d')), 'onchange' => 'date_changed();'), TRUE, 2); ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status Validasi KBM</label>
                            <div class="col-md-10">
                                <h4 id="status_validasi" class="text-info"></h4>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2 col-md-offset-2">
                                <button type="button" class="btn btn-save btn-primary btn-block" onclick="tetapkan_filter();"><i class="fa fa-book"></i>&nbsp;&nbsp;Buka</button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-save btn-success btn-block" id="btn-validasi" disabled onclick="validasi_kelas();"><i class="fa fa-check"></i>&nbsp;&nbsp;Validasi</button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-save btn-info btn-block" id="btn-validasi-semua" onclick="validasi_semua_kelas();"><i class="fa fa-check"></i>&nbsp;&nbsp;Validasi Semua Kelas</button>
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
    var STATUS_VALIDASI = null;
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

            get_status_validasi();
        });

        $(".js-source-states-JENIS_FILTER").on("change", "", function () {
            var data_jenis = $(this).select2("data");

            JENIS_FILTER = data_jenis.id;
            $(".table-datatable1").slideUp();
        });

        $(".table-datatable1").hide();

        $("#status_validasi").html('Pilih kelas dan tanggal terlebih dahulu');
    });

    function get_status_validasi() {
        var TANGGAL_AKH = $("#TANGGAL_FILTER").val();
        var success = function (data) {
            $("#btn-validasi").removeAttr('disabled');

            if (data.status) {
                STATUS_VALIDASI = true;
                
                $("#btn-validasi").html('<i class="fa fa-trash"></i>&nbsp;&nbsp;Hapus Validasi');
                $("#btn-validasi").addClass('btn-danger');
                $("#btn-validasi").removeClass('btn-success');

                $("#status_validasi").html('YA');
                $("#status_validasi").addClass('text-success');
                $("#status_validasi").removeClass('text-info');
                $("#status_validasi").removeClass('text-danger');
            } else {
                STATUS_VALIDASI = false;
                
                $("#btn-validasi").html('<i class="fa fa-check"></i>&nbsp;&nbsp;Validasi');
                $("#btn-validasi").addClass('btn-success');
                $("#btn-validasi").removeClass('btn-danger');

                $("#status_validasi").html('TIDAK');
                $("#status_validasi").addClass('text-danger');
                $("#status_validasi").removeClass('text-success');
                $("#status_validasi").removeClass('text-info');
            }
        };

        STATUS_VALIDASI = null;
        $("#btn-validasi").attr('disabled', 'true');

        $("#status_validasi").addClass('text-info');
        $("#status_validasi").removeClass('text-success');
        $("#status_validasi").removeClass('text-danger');
        $("#status_validasi").html('Pilih kelas dan tanggal terlebih dahulu');

        if (TANGGAL_AKH !== '' && KELAS_FILTER !== null)
            create_ajax('<?php echo site_url('akademik/kehadiran/cek_status_validasi'); ?>', 'TANGGAL_AKH=' + TANGGAL_AKH + '&KELAS_FILTER=' + KELAS_FILTER, success);
    }
    
    function validasi_kelas() {
        create_splash('Sistem sedang memvalidasi absensi KBM');
        var TANGGAL_AKH = $("#TANGGAL_FILTER").val();
        var success = function(data) {
            get_status_validasi();
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/kehadiran/validasi_kelas'); ?>', 'TANGGAL_AKH=' + TANGGAL_AKH + '&KELAS_FILTER=' + KELAS_FILTER, success);
    }
    
    function validasi_semua_kelas() {
        create_splash('Sistem sedang memvalidasi absensi KBM');
        var TANGGAL_AKH = $("#TANGGAL_FILTER").val();
        var success = function(data) {
            get_status_validasi();
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/kehadiran/validasi_semua_kelas'); ?>', 'TANGGAL_AKH=' + TANGGAL_AKH, success);
    }

    function date_changed() {
        var TANGGAL_FILTER = $("#TANGGAL_FILTER").val();

        $(".table-datatable1").slideUp();

        get_status_validasi();

        if (TANGGAL_FILTER !== '')
            $("#btn-validasi-semua").removeAttr('disabled');
        else
            $("#btn-validasi-semua").attr('disabled', true);
    }

    function tetapkan_filter() {
        var TANGGAL_FILTER = $("#TANGGAL_FILTER").val();
        
        if (KELAS_FILTER === null || JENIS_FILTER === null || TANGGAL_FILTER === '' || STATUS_VALIDASI === null) {
            create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
            $(".table-datatable1").slideUp();
        } else if(STATUS_VALIDASI === false && parseInt(JENIS_FILTER) > 1) {
            create_homer_error('Tidak dapat membuka absensi karena KBM belum divalidasi.');
        } else {
            $(".table-datatable1").attr('style', 'margin-top: -60px;');

            table = initialize_datatables(id_table, '<?php echo site_url('akademik/kehadiran/ajax_form'); ?>/' + KELAS_FILTER + '/' + JENIS_FILTER + '/' + TANGGAL_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

            remove_splash();

            $(".buttons-add").remove();

            $(".table-datatable1").slideDown();

            $("#datatable1_length").children().children().val('-1').trigger('change');
        }
    }

    function simpan_absen(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var TANGGAL_AKH = $("#TANGGAL_FILTER").val();
        var SISWA_AKH = $(that).data('siswa');
        var KETERANGAN_AKH = $(that).parent().prev().children();
        var ALASAN_AKH = $(that).parent().prev().prev().children();
        var success = function (data) {
            $(that).next().remove();

            if (data.status) {
//                $(that).next().data('siswa', data.status).show();
                KETERANGAN_AKH.attr('disabled', 'true');
                ALASAN_AKH.attr('disabled', 'true');
            } else {
                $(that).show();
                create_homer_error("Gagal menyimpan ketidakhadiran siswa");
                KETERANGAN_AKH.val('');
                ALASAN_AKH.val('-');
            }

            reload_datatables(table);
        };

        if (ALASAN_AKH.val() === '-') {
            create_homer_error("Pilih alasan terlebih dahulu");
        } else {
            $(loading_bar).insertAfter(that);
            $(that).hide();

            create_ajax('<?php echo site_url('akademik/kehadiran/ajax_form_add'); ?>', 'TANGGAL_AKH=' + TANGGAL_AKH + '&SISWA_AKH=' + SISWA_AKH + '&KETERANGAN_AKH=' + KETERANGAN_AKH.val() + '&ALASAN_AKH=' + ALASAN_AKH.val() + '&JENIS_AKH=' + JENIS_FILTER, success);
        }
    }

    function hapus_absen(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var ID = $(that).data('siswa');
        var KETERANGAN_AKH = $(that).parent().prev().children();
        var ALASAN_AKH = $(that).parent().prev().prev().children();
        var success = function (data) {
            $(that).next().remove();

            if (data.status) {
//                $(that).prev().show();
                KETERANGAN_AKH.removeAttr('disabled').val('');
                ALASAN_AKH.removeAttr('disabled').val('-');
            } else {
//                $(that).show();
                create_homer_error("Gagal menyimpan ketidakhadiran siswa");
            }

            reload_datatables(table);
        };

        $(loading_bar).insertAfter(that);
        $(that).hide();

        create_ajax('<?php echo site_url('akademik/kehadiran/ajax_form_delete'); ?>', 'ID=' + ID, success);
    }

</script>
