<?php
$title = 'Tambah Pelanggaran Catatan';
$subtitle = "Tambah perlanggaran catatan persiswa";
$id_datatables = 'datatable1';

$columns = array(
    'NO ABSEN',
    'NIS',
    'NAMA',
    'NAMA AYAH',
    'KELAS',
    'WALI KELAS',
    'AKSI',
);
$this->generate->generate_panel_content($title, $subtitle);
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
                                <?php $this->generate->input_select2('Jenis Pelanggaran', array('name' => 'PELANGGARAN_KS', 'url' => site_url('komdis/jenis_pelanggaran/auto_complete_pelanggaran')), TRUE, 9, FALSE, NULL); ?>
                            </div>
                            <div class="col-md-5">
                                <?php $this->generate->input_date('Tanggal Pelanggaran', array('name' => 'TANGGAL_KS', 'value' => date('Y-m-d'), 'id' => 'TANGGAL_KS'), TRUE, 6); ?>
                            </div>
                            <div class="col-md-7">
                                <?php $this->generate->input_select2('Sumber Pelanggaran', array('name' => 'SUMBER_KS', 'url' => site_url('master_data/pegawai/auto_complete')), TRUE, 7, FALSE, array('id' => $this->session->userdata('ID_PEG'), 'text' => $this->session->userdata('FULLNAME_USER'))); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-primary" onclick="buka_siswa();">Buka List Siswa</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->generate->datatables($id_datatables, 'Siswa', $columns);
?>

<script type="text/javascript">
    var PELANGGARAN_KS = null;
    var SUMBER_KS = <?php echo $this->session->userdata('ID_PEG'); ?>;
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
        create_form_input(id_form, id_modal, url_form, title, null);
    };

    $(document).ready(function () {
        $(".js-source-states-SUMBER_KS").parent().prev().addClass('col-sm-4');
        $(".js-source-states-SUMBER_KS").parent().prev().removeClass('col-sm-2');

        $("#TANGGAL_KS").parent().prev().addClass('col-sm-5');
        $("#TANGGAL_KS").parent().prev().removeClass('col-sm-2');

        $(".table-datatable1").attr('style', 'margin-top: -60px;');

        $(".js-source-states-PELANGGARAN_KS").on("change", "", function () {
            var data = $(this).select2("data");

            PELANGGARAN_KS = data.id;
            $(".table-datatable1").slideUp();
        });

        $(".js-source-states-SUMBER_KS").on("change", "", function () {
            var data = $(this).select2("data");

            SUMBER_KS = data.id;
            $(".table-datatable1").slideUp();
        });
        
        $("#TANGGAL_KS").change(function(){
            $(".table-datatable1").slideUp();
        });

        $(".table-datatable1").hide();
    });

    function buka_siswa() {
        $(".table-datatable1").slideUp();
        var TANGGAL_PELANGGARAN = $("#TANGGAL_KS").val();

        if (TANGGAL_PELANGGARAN === '' || PELANGGARAN_KS === null || SUMBER_KS === null) {
            create_homer_error('Silahkan lengkapi terlebih dahulu filter yang ada');
        } else {
            table = initialize_datatables(id_table, '<?php echo site_url('komdis/pelanggaran_catatan/ajax_list_perpelanggaran'); ?>/' + TANGGAL_PELANGGARAN + '/' + PELANGGARAN_KS, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

            $(".buttons-add").remove();

            $(".table-datatable1").slideDown();

            remove_splash();
        }
    }

    function simpan_pelanggaran(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var ID = $(that).data('id');
        var STATUS = $(that).data('status');
        var TANGGAL_PELANGGARAN = $("#TANGGAL_KS").val();
        var success = function (data) {
            reload_datatables(table);
        };
        
        $(loading_bar).insertAfter(that);
        $(that).hide();
        
        if(parseInt(STATUS))
            create_ajax('<?php echo site_url('komdis/pelanggaran_catatan/ajax_add'); ?>', 'TANGGAL_KS=' + TANGGAL_PELANGGARAN + '&PELANGGARAN_KS=' + PELANGGARAN_KS + '&SUMBER_KS=' + SUMBER_KS + '&STATUS=' + STATUS + '&SISWA_KS=' + ID + '&KETERANGAN_KS=', success);
        else
            create_ajax('<?php echo site_url('komdis/pelanggaran_catatan/ajax_delete'); ?>', 'ID=' + ID, success);
    }
</script>