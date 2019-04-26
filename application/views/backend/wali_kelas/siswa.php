<?php
$this->generate->generate_panel_content("Data Siswa", "Daftar semua siswa");
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
                        <?php if ($this->session->userdata('ID_HAKAKSES') == 2) { ?>
                            <?php $this->generate->input_select2('Wali Kelas', array('name' => 'WALI_KELAS_FILTER', 'url' => site_url('wali_kelas/siswa/list_wali_kelas')), TRUE, 7, FALSE, NULL); ?>
                        <?php } ?>
                        <?php
                        $this->generate->input_dropdown('Kelas', 'KELAS_FILTER', array(
                            array('id' => 0, 'text' => "-- Pilih wali kelas terlebih dahulu --", 'selected' => TRUE)
                                ), TRUE, 4);
                        ?>
                        <div class="form-group">
                            <label class="col-md-2 control-label">&nbsp;</label>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-save btn-info btn-block" onclick="tetapkan_filter();"><i class="fa fa-book"></i>&nbsp;&nbsp;Buka</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$title = 'Nilai Siswa';
$id_datatables = 'datatable1';

$columns = array(
    'NO ABSEN',
    'NIS',
    'NAMA',
    'AKSI',
);

$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var KELAS_FILTER_GLOBAL = null;
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{"targets": [-1], "orderable": false}];
    var orders = '';//[[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings) {
        
    };
    var functionAddData = function (e, dt, node, config) {
        
    };
    var ID_PEG = <?php if ($this->session->userdata('ID_HAKAKSES') == 2) echo 'null';
else echo $this->session->userdata('ID_PEG'); ?>;

    $(function () {
        get_list(ID_PEG);

        $(".table-datatable1").hide();
    });

    function get_list(guru) {
        var success = function (data) {
            if (data.length > 0) {
                $("#KELAS_FILTER").html("<option value=''>-- Pilih kelas --</option>");
            } else {
                $("#KELAS_FILTER").html("<option value=''>Wali kelas tidak mempunyai kelas</option>");
            }

            $.each(data, function (index, detail) {
                $("#KELAS_FILTER").append("<option value='" + detail.ID_KELAS + "'>" + detail.NAMA_KELAS + "</option>");
            });

            remove_splash();
        };

        create_splash("Sedang mengambil data kelas");
        create_ajax('<?php echo site_url('wali_kelas/siswa/list_kelas'); ?>', 'ID_PEG=' + guru, success);
    }

    function tetapkan_filter() {
        var KELAS_FILTER = $("#KELAS_FILTER").val();
        $(".table-datatable1").slideUp();

        if (KELAS_FILTER === '' || ID_PEG === null) {
            create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
        } else {
            KELAS_FILTER_GLOBAL = KELAS_FILTER;
            
            list_nilai(KELAS_FILTER);
        }
    }
    
    function list_nilai(KELAS_FILTER) {
        $(".table-datatable1").attr('style', 'margin-top: -60px;');

        table = initialize_datatables(id_table, '<?php echo site_url('wali_kelas/siswa/ajax_list'); ?>/' + KELAS_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        remove_splash();
        $(".buttons-add").remove();
        $(".table-datatable1").slideDown();
    }

</script>