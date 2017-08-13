<?php
$this->generate->generate_panel_content("Nilai", "Daftar nilai guru mata pelajaran");
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
                            <?php $this->generate->input_select2('Guru', array('name' => 'GURU_FILTER', 'url' => site_url('master_data/pegawai/auto_complete')), TRUE, 7, FALSE, NULL); ?>
                        <?php } ?>
                        <?php
                        $this->generate->input_dropdown('Mata Pelajaran', 'MAPEL_FILTER', array(
                            array('id' => 0, 'text' => "-- Pilih guru terlebih dahulu --", 'selected' => TRUE)
                                ), TRUE, 4);
                        ?>
                        <?php
                        $this->generate->input_dropdown('Kelas', 'KELAS_FILTER', array(
                            array('id' => 0, 'text' => "-- Pilih mapel terlebih dahulu --", 'selected' => TRUE)
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
$title = 'Siswa';
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NAMA',
    'NILAI',
);

$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var STATUS_MAPEL = 'mapel';
    var STATUS_KELAS = 'kelas';
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
        create_homer_error("Anda tidak memiliki hak akses untuk menambah guru.");
    };
    var ID_PEG = <?php if ($this->session->userdata('ID_HAKAKSES') == 2) echo 'null';
else echo $this->session->userdata('ID_USER'); ?>;

    $(function () {
<?php if ($this->session->userdata('ID_HAKAKSES') == 2) { ?>
    //        $("#MAPEL_FILTER, .btn-save, #KELAS_FILTER").prop("disabled", true);

            $(".js-source-states-GURU_FILTER").on("change", "", function () {
                var data_guru = $(this).select2("data");

                ID_PEG = data_guru.id;

                get_list(STATUS_MAPEL, data_guru.id);
            });
<?php } else { ?>
            get_list(STATUS_MAPEL, ID_PEG);
<?php } ?>

        $(".table-datatable1").hide();

        $("#MAPEL_FILTER").change(function () {
            get_list(STATUS_KELAS, ID_PEG);
        });
    });

    function get_list(status, guru) {
        var ID_FILTER = null;
        if (status === STATUS_MAPEL)
            ID_FILTER = $("#MAPEL_FILTER");
        else {
            ID_FILTER = $("#KELAS_FILTER");
            var ID_MAPEL = $("#MAPEL_FILTER").val();
        }

        var success = function (data) {
            if (data.length > 0) {
                ID_FILTER.html("<option value=''>-- Pilih " + status + " --</option>");
                ID_FILTER.removeAttr("disabled");
            } else {
                ID_FILTER.html("<option value=''>Guru tidak mempunyai " + status + "</option>");
            }

            $.each(data, function (index, detail) {
                ID_FILTER.append("<option value='" + ((status === STATUS_MAPEL) ? detail.ID_MAPEL : detail.ID_KELAS) + "'>" + ((status === STATUS_MAPEL) ? detail.NAMA_MAPEL : detail.NAMA_KELAS) + "</option>");
            });

            remove_splash();
        };

        if (status == STATUS_MAPEL) {
            create_splash("Sedang mengambil data " + status);
            create_ajax('<?php echo site_url('akademik/nilai/list_mapel_guru'); ?>', 'ID_PEG=' + guru, success);
        } else if (ID_MAPEL === '') {
            ID_FILTER.html("<option value=''>-- Pilih " + status + " --</option>");
        } else {
            create_splash("Sedang mengambil data " + status);
            create_ajax('<?php echo site_url('akademik/nilai/list_kelas_mapel'); ?>', 'ID_PEG=' + guru + "&ID_MAPEL=" + ID_MAPEL, success);
        }
    }

    function tetapkan_filter() {
        var MAPEL_FILTER = $("#MAPEL_FILTER").val();
        var KELAS_FILTER = $("#KELAS_FILTER").val();

        if (KELAS_FILTER === '' || MAPEL_FILTER === '' || ID_PEG === null) {
            create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
            $(".table-datatable1").slideUp();
        } else {
            $(".table-datatable1").attr('style', 'margin-top: -60px;');

            table = initialize_datatables(id_table, '<?php echo site_url('akademik/nilai/ajax_list'); ?>/' + MAPEL_FILTER + '/' + ID_PEG + '/' + KELAS_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

            remove_splash();

            $(".buttons-add").remove();

            $(".table-datatable1").slideDown();
        }
    }
    
    function simpan_nilai(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var ID_AGM = $(that).data('gurumapel');
        var ID_SISWA = $(that).data('siswa');
        var ID_NILAI = $(that).data('nilai');
        var NILAI_AN = $(that).val();
        var success = function(data){
            $(that).show();
            $(that).next().remove();
            
            if(data.status) {
                $(that).addClass('success');
                if(ID_NILAI === 'NONE') $(that).data('nilai', data.status);
            } else $(that).addClass('error');
        };
        
        if(isNaN(parseFloat(NILAI_AN))) {
            create_homer_error('Input nilai harus angka');
            $(that).val('');
        } else {
            $(loading_bar).insertAfter(that);
            $(that).hide();
            create_ajax('<?php echo site_url('akademik/nilai/simpan_nilai'); ?>', 'ID_AGM=' + ID_AGM + '&ID_SISWA=' + ID_SISWA + '&NILAI_AN=' + NILAI_AN + '&ID_NILAI=' + ID_NILAI, success);
        }
    }

</script>