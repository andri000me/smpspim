<?php
$title = 'Lanjut Jenjang';
$subtitle = "Daftar semua siswa lulus yang akan melanjutkan jenjang diatasnya";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NO ABSEN',
    'NAMA SISWA',
    'JK',
//    'TINGKAT',
//    'NAMA KELAS',
//    'WALI KELAS',
    'KELAS LANJUT',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
if ($this->session->userdata('ID_CAWU_ACTIVE') == 3) {
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
                            <?php
                            $TA = json_decode(json_encode($TA), TRUE);
                            array_unshift($TA, array('id' => "", 'text' => "-- Pilih wali kelas terlebih dahulu --", 'selected' => TRUE));
                            $this->generate->input_dropdown('TA Berikutnya', 'ID_TA', $TA, TRUE, 4);
                            $this->generate->input_select2('Kelas', array('name' => 'KELAS_FILTER', 'url' => site_url('akademik/kelas/auto_complete')), TRUE, 7, FALSE, NULL);
                            $this->generate->input_dropdown('Mode Simpan', 'AUTO_SAVE', array(
                                array('id' => 1, 'text' => 'Pilih kelas langsung simpan', 'selected' => true),
                                array('id' => 0, 'text' => 'Klik AKSI untuk menyimpan', 'selected' => false),
                                    ), TRUE, 5);
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
    $this->generate->datatables($id_datatables, $title, $columns);
    ?>
                                        <!--<i class="fa fa-check-circle-o"></i>
                                        <i class="fa fa-warning"></i>-->
    <script type="text/javascript">
        var ID_KELAS = null;
        var ID_TA = <?php echo $this->session->userdata('ID_TA_ACTIVE'); ?>;
        var table;
        var id_table = '<?php echo $id_datatables; ?>';
        var title = '<?php echo $title; ?>';
        var columns = [{"targets": [-1], "orderable": false}];
        var orders = [[1, "ASC"]];
        var requestExport = true;
        var functionInitComplete = function (settings, json) {

        };
        var functionDrawCallback = function (settings, json) {
            $(".table-datatable1").slideDown();
            remove_splash();
        };
        var functionAddData = function (e, dt, node, config) {

        };

        $(document).ready(function () {
            $(".table-datatable1").attr('style', 'margin-top: -60px;');
            $(".table-datatable1").hide();

            $(".js-source-states-KELAS_FILTER").on("change", "", function () {
                var data = $(this).select2("data");
                $(".table-datatable1").slideUp();

                ID_KELAS = data.id;
            });
            $("#ID_TA").change(function () {
                $(".table-datatable1").slideUp();

                if (parseInt($(this).val()) === ID_TA) {
                    create_homer_error('TA berikutnya tidak boleh sama dengan TA Aktif');
                    $(".btn-save").prop('disabled', true);
                } else {
                    $(".btn-save").removeAttr('disabled');
                }
            });
        });

        function list_siswa() {
            table = initialize_datatables(id_table, '<?php echo site_url('akademik/lanjut_jenjang/ajax_list'); ?>/' + ID_KELAS + '/' + $("#ID_TA").val(), columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
            $("#" + id_table + "_length").children().children().val('-1').trigger('change');

            $(".buttons-add").remove();
        }

        function tetapkan_filter() {
            if ($("#ID_TA").val() === "" || ID_KELAS === null) {
                create_homer_error("Silahkan pilih filter terlebih dahulu");
            } else {
                list_siswa();
            }
        }

        function reload_table() {
            reload_datatables(table);
        }

        function changeKelas(t) {
            var autoSave = $("#AUTO_SAVE").val();

            if (parseInt(autoSave) === 1) {
                $(t).parent().next().children().trigger('click');
            }
        }

        function proses_siswa(that) {
            var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
            var htmlKelas = $(that).parent().prev().children();
            var ID_KELAS = htmlKelas.val();
            var ID_AS = $(that).data('siswa');
            var ID_SISWA = $(that).data('id');
            var NAMA_SISWA = $(that).data('nama');
            var success = function (data) {
                htmlKelas.removeAttr('disabled');
                reload_datatables(table);

                if (data.status)
                    create_homer_success(data.msg);
                else
                    create_homer_error(data.msg);
            };

            $(loading_bar).insertAfter(that);
            $(that).hide();
            create_ajax('<?php echo site_url('akademik/lanjut_jenjang/proses_siswa'); ?>', 'ID_AS=' + ID_AS + "&ID_SISWA=" + ID_SISWA + "&ID_TA=" + $("#ID_TA").val() + "&ID_KELAS=" + ID_KELAS, success);
            htmlKelas.prop('disabled', true);
        }
    </script>

<?php } else { ?>
    <div class="content animate-panel">
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel hbggreen">
                    <div class="panel-body text-center">
                        <h1>MENU HANYA AKTIF HANYA PADA CAWU 3</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>