<?php
$this->generate->generate_panel_content("Rapor", "Daftar nilai matapelajaran siswa");
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
                            <?php $this->generate->input_select2('Wali Kelas', array('name' => 'WALI_KELAS_FILTER', 'url' => site_url('akademik/rapor/list_wali_kelas')), TRUE, 7, FALSE, NULL); ?>
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
    'NIS',
    'NAMA',
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
        if(!(KELAS_FILTER === null)) window.open('<?php echo site_url('akademik/rapor/cetak'); ?>/' + KELAS_FILTER_GLOBAL, '_blank');
    };
    var ID_PEG = <?php if ($this->session->userdata('ID_HAKAKSES') == 2) echo 'null';
else echo $this->session->userdata('ID_USER'); ?>;

    $(function () {
<?php if ($this->session->userdata('ID_HAKAKSES') == 2) { ?>
            $(".js-source-states-WALI_KELAS_FILTER").on("change", "", function () {
                var data_guru = $(this).select2("data");

                ID_PEG = data_guru.id;

                get_list(data_guru.id);
            });
<?php } else { ?>
            get_list(ID_PEG);
<?php } ?>

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
        create_ajax('<?php echo site_url('akademik/rapor/list_kelas'); ?>', 'ID_PEG=' + guru, success);
    }

    function tetapkan_filter() {
        var KELAS_FILTER = $("#KELAS_FILTER").val();
        $(".table-datatable1").slideUp();

        if (KELAS_FILTER === '' || ID_PEG === null) {
            create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
        } else {
            KELAS_FILTER_GLOBAL = KELAS_FILTER;
            
            list_mapel(KELAS_FILTER);
        }
    }
    
    function list_mapel(KELAS_FILTER) {
        create_splash("Sistem sedang mengambil data matapelajaran");
        var success = function(data) {
            $("tfoot").remove();
            if(data.length > 0) {
                $(".header-mapel").remove();
                $.each(data, function(index, detail){
                    $("thead tr").append("<th class='header-mapel'>" + detail.NAMA_MAPEL + "</th>");
                });
                $("thead tr").append("<th class='header-mapel'>Jumlah</th>");
                $("thead tr").append("<th class='header-mapel'>Rata-rata</th>");
                $("thead tr").append("<th class='header-mapel'>Sakit</th>");
                $("thead tr").append("<th class='header-mapel'>Izin</th>");
                $("thead tr").append("<th class='header-mapel'>Alpha</th>");
                list_nilai(KELAS_FILTER);
            } else {
                create_homer_error("Kelas tidak mempunyai matapelajaran");
            }
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/rapor/list_mapel'); ?>', 'ID_KELAS=' + KELAS_FILTER, success);
    }
    
    function list_nilai(KELAS_FILTER) {
        $(".table-datatable1").attr('style', 'margin-top: -60px;');

        table = initialize_datatables(id_table, '<?php echo site_url('akademik/rapor/ajax_list'); ?>/' + KELAS_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        remove_splash();
        $(".buttons-copy").remove();
        $(".buttons-add").html('Cetak Rapor');
        $(".table-datatable1").slideDown();
    }

</script>