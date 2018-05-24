<?php
$this->generate->generate_panel_content("Kenaikan Kelas", "Daftar siswa yang akan diproses");

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
                            <?php if ($this->session->userdata('ID_HAKAKSES') == 2) { ?>
                                <?php $this->generate->input_select2('Wali Kelas', array('name' => 'WALI_KELAS_FILTER', 'url' => site_url('akademik/kenaikan/list_wali_kelas')), TRUE, 7, FALSE, NULL); ?>
                            <?php } ?>
                            <?php
                            $this->generate->input_dropdown('Kelas', 'KELAS_FILTER', array(
                                array('id' => 0, 'text' => "-- Pilih wali kelas terlebih dahulu --", 'selected' => TRUE)
                                    ), TRUE, 4);
                            ?>
                            <?php
                            $this->generate->input_dropdown('Status Tagihan', 'STATUS_TAG', array(
                                array('id' => 1, 'text' => "CEK TAGIHAN", 'selected' => TRUE),
                                array('id' => 0, 'text' => "TIDAK CEK TAGIHAN", 'selected' => FALSE),
                                    ), TRUE, 5);
                            ?>
                            <?php $this->generate->input_select2('Tahun Ajaran berikutnya', array('name' => 'NEXT_TA_FILTER', 'url' => site_url('master_data/tahun_ajaran/auto_complete')), TRUE, 4, FALSE, NULL); ?>
                            <div class="form-group">
                                <label class="col-md-2 control-label">&nbsp;</label>
                                <div class="col-md-10">
                                    <button type="button" class="btn btn-save btn-primary" onclick="tetapkan_filter(1);"><i class="fa fa-book"></i>&nbsp;&nbsp;Proses Kenaikan Kelas</button>
                                    <button type="button" class="btn btn-save btn-info" onclick="tetapkan_filter(0);"><i class="fa fa-book"></i>&nbsp;&nbsp;Rekapitulasi Kenaikan Kelas</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $title = 'Rangkuman Hasil Siswa';
    $id_datatables = 'datatable1';

    $columns = array(
        'NO ABSEN',
        'NIS',
        'NAMA',
//    'RATA-RATA UMUM CAWU 1',
//    'RATA-RATA UMUM CAWU 2',
//    'RATA-RATA UMUM CAWU 3',
        'RATA-RATA UMUM',
//    'RATA-RATA AGAMA CAWU 1',
//    'RATA-RATA AGAMA CAWU 2',
//    'RATA-RATA AGAMA CAWU 3',
        'RATA-RATA AGAMA',
        'DAUROH',
        'HAFALAN',
        'SAKIT',
        'IZIN',
        'LARI',
        'POIN',
        'PILIHAN',
        'AKSI',
    );

    $this->generate->datatables($id_datatables, $title, $columns);
    ?>

    <?php
    $title_rekap = 'Rekapitulasi Kenaikan Kelas Siswa';
    $id_datatables_rekap = 'datatable2';

    $columns_rekap = array(
        'NO ABSEN',
        'NIS',
        'NAMA',
        'JK',
        'JENJANG',
        'TINGKAT',
        'STATUS',
    );

    $this->generate->datatables($id_datatables_rekap, $title_rekap, $columns_rekap);
    ?>
    <script type="text/javascript">
        var KELAS_FILTER_GLOBAL = null;
        var NEXT_TA_FILTER = null;
        var table;
        var id_table = '<?php echo $id_datatables; ?>';
        var title = '<?php echo $title; ?>';
        var id_table_rekap = '<?php echo $id_datatables_rekap; ?>';
        var title_rekap = '<?php echo $title_rekap; ?>';
        var columns = '';//[{"targets": [-1], "orderable": false}];
        var orders = '';//[[0, "ASC"]];
        var requestExport = true;
        var functionInitComplete = function (settings, json) {

        };
        var functionDrawCallback = function (settings) {

        };
        var functionAddData = function (e, dt, node, config) {
            if (!(KELAS_FILTER === null))
                window.open('<?php echo site_url('akademik/rapor'); ?>/');
        };
        var ID_PEG = <?php
    if ($this->session->userdata('ID_HAKAKSES') == 2)
        echo 'null';
    else
        echo $this->session->userdata('ID_USER');
    ?>;

        $(function () {
    <?php if ($this->session->userdata('ID_HAKAKSES') == 2) { ?>
                $(".js-source-states-WALI_KELAS_FILTER").on("change", "", function () {
                    var data_guru = $(this).select2("data");
                    $(".table-datatable1, .table-datatable2").slideUp();

                    ID_PEG = data_guru.id;

                    get_list(data_guru.id);
                });
    <?php } else { ?>
                get_list(ID_PEG);
    <?php } ?>

            $(".table-datatable1, .table-datatable2").hide();
            $("body").addClass('hide-sidebar');

            $(".js-source-states-NEXT_TA_FILTER").on("change", "", function () {
                var data_ta = $(this).select2("data");
                NEXT_TA_FILTER = data_ta.id;

                $(".table-datatable1, .table-datatable2").slideUp();

                if (parseInt(NEXT_TA_FILTER) === <?php echo $this->session->userdata('ID_TA_ACTIVE'); ?>) {
                    create_homer_error("Tidak boleh memilih Tahun Ajaran yang sedang aktif. Halaman akan dimuat ulang.");
                    reload_window();
                }
            });
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
            create_ajax('<?php echo site_url('akademik/kenaikan/list_kelas'); ?>', 'ID_PEG=' + guru, success);
        }

        function tetapkan_filter(proses) {
            var KELAS_FILTER = $("#KELAS_FILTER").val();
            $(".table-datatable1, .table-datatable2").slideUp();

            if (KELAS_FILTER === '' || ID_PEG === null || NEXT_TA_FILTER === null) {
                create_homer_error("Silahkan lengkapi kolom terlebih dahulu.");
            } else {
                KELAS_FILTER_GLOBAL = KELAS_FILTER;

                if (proses)
                    list_nilai(KELAS_FILTER);
                else
                    list_siswa(KELAS_FILTER);
            }
        }

        function list_siswa(KELAS_FILTER) {
            $(".table-datatable2").attr('style', 'margin-top: -60px;');

            table = initialize_datatables(id_table_rekap, '<?php echo site_url('akademik/kenaikan/ajax_list_rekap'); ?>/' + KELAS_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
            $("#" + id_table_rekap + "_length").children().children().val('-1').trigger('change');

            remove_splash();
            $(".buttons-add").remove();
            $(".table-datatable2").slideDown();
            $(".datatables-search-STATUS").replaceWith('<select class="form-control input-sm datatables-search datatables-search-STATUS" style="width:100%" onchange="refresh_datatables(this, 6);"><option value="">Pilih Opsi</option><option value="1">Naik</option><option value="0">Tidak Naik</option></select>');
        }

        function list_nilai(KELAS_FILTER) {
            $(".table-datatable1").attr('style', 'margin-top: -60px;');

            table = initialize_datatables(id_table, '<?php echo site_url('akademik/kenaikan/ajax_list'); ?>/' + KELAS_FILTER, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
            $("#" + id_table + "_length").children().children().val('-1').trigger('change');

            remove_splash();
            $(".buttons-add").remove();
            $(".buttons-add").html('Cetak Rapor');
            $('<a class="dt-button btn btn-sm btn-default" tabindex="0" aria-controls="datatable1" href="#" onclick="prosesKenaikanAll()"><span>Proses Semua</span></a>').insertAfter(".buttons-reload");
            $(".table-datatable1").slideDown();
        }

        function prosesKenaikanAll() {
            var STATUS_TAG = $("#STATUS_TAG").val();
            if (parseInt(STATUS_TAG) === 0) {
                $(".btn-proses").data("all", "1");
                $(".btn-proses").trigger("click");
            } else {
                create_homer_error("Untuk menggunakan fungsi ini harus memilih STATUS TAGIHAN menjadi TIDAK CEK TAGIHAN");
            }
        }

        function proses_naik(that) {
            var STATUS_TAG = $("#STATUS_TAG").val();
            var ID_AS = $(that).data('id');
            var STATUS_RATA_RATA = $(that).data('rata');
            var SISWA = $(that).data('siswa');
            var STATUS_KENAIKAN = $(that).parent().prev().children().val();
            var success = function (data) {
                remove_splash();

                if (data.status)
                    create_homer_success('Berhasil memproses siswa');
                else
                    create_homer_error('Gagal memproses siswa. ' + data.msg);

                reload_datatables(table);
            };
            var action = function (isConfirm) {
                if (isConfirm) {
                    create_splash("Sistem sedang memproses siswa");
                    create_ajax('<?php echo site_url('akademik/kenaikan/proses_naik'); ?>', 'ID_AS=' + ID_AS + '&STATUS_KENAIKAN=' + STATUS_KENAIKAN + '&NEXT_TA_FILTER=' + NEXT_TA_FILTER + '&STATUS_TAG=' + STATUS_TAG, success);
                }
            };

            if (STATUS_KENAIKAN === '')
                create_homer_error("Silahkan pilih pilihan terlebih dahulu");
            else if (parseInt(STATUS_TAG) === 1)
                create_swal_option('Apakah Anda yakin melanjurkan?', 'Anda ' + ((parseInt(STATUS_KENAIKAN) === 1) ? 'menaikan' : 'tidak menaikan') + ' siswa ' + SISWA + ' ke tingkat yang lebih tinggi. ' + ((parseInt(STATUS_RATA_RATA) === 0) ? 'Siswa memiliki nilai rata-rata dibawah KKM.' : ''), action);
            else if (parseInt(STATUS_TAG) === 0)
                create_ajax('<?php echo site_url('akademik/kenaikan/proses_naik'); ?>', 'ID_AS=' + ID_AS + '&STATUS_KENAIKAN=' + STATUS_KENAIKAN + '&NEXT_TA_FILTER=' + NEXT_TA_FILTER + '&STATUS_TAG=' + STATUS_TAG, success);
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