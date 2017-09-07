<?php
$title = 'Tambah Pelanggaran dengan Barcode';
$subtitle = "Daftar tanggal pelanggaran";

$this->generate->generate_panel_content("Data " . $title, $subtitle);
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form Tambah Pelanggaran dengan Scanner Barcode
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" onsubmit="return false;">
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
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-5 control-label">NIS</label>
                                    <div class="col-md-3">
                                        <input type="text" name="nis" id="nis" class="form-control" placeholder="NIS atau Scan Barcode disini">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr class="detail-nis">
                    <div class="row detail-nis">
                        <div class="col-md-4">
                            <small class="stat-label">NIS</small>
                            <h4 class="detail_siswa" id="detail_NIS_SISWA"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">NAMA SISWA</small>
                            <h4 class="detail_siswa" id="detail_NAMA_SISWA"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">NAMA AYAH</small>
                            <h4 class="detail_siswa" id="detail_AYAH_NAMA_SISWA"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">NO ABSEN</small>
                            <h4 class="detail_siswa" id="detail_NO_ABSEN_AS"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">KELAS</small>
                            <h4 class="detail_siswa" id="detail_NAMA_KELAS"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">WALI KELAS</small>
                            <h4 class="detail_siswa" id="detail_NAMA_PEG"></h4>
                        </div>
                        <!-- <div class="col-md-4">
                            <small class="stat-label">ALAMAT</small>
                            <h4 class="detail_siswa" id="detail_ALAMAT_SISWA"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">KECAMATAN</small>
                            <h4 class="detail_siswa" id="detail_NAMA_KEC"></h4>
                        </div>
                        <div class="col-md-4">
                            <small class="stat-label">KABUPATEN</small>
                            <h4 class="detail_siswa" id="detail_NAMA_KAB"></h4>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var table;
    var PELANGGARAN_KS = null;
    var SUMBER_KS = <?php echo $this->session->userdata('ID_PEG'); ?>;
    var title = '<?php echo $title; ?>';

    $(document).ready(function () {
        $(".js-source-states-SUMBER_KS").parent().prev().addClass('col-sm-4');
        $(".js-source-states-SUMBER_KS").parent().prev().removeClass('col-sm-2');

        $("#TANGGAL_KS").parent().prev().addClass('col-sm-5');
        $("#TANGGAL_KS").parent().prev().removeClass('col-sm-2');

        $(".detail-nis").hide();
        $("#nis").focus();

//        table = initialize_datatables(id_table, '<?php echo site_url('komdis/absen_jamaah/ajax_list_siswa/'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $('#nis').keypress(function (e) {
            if (e.which == 13) {
                var NIS_SISWA = $(this).val();
                $(".detail-nis").slideUp();
                $(".detail_siswa").html(" ");
                get_data_scanner(NIS_SISWA);
                $(this).val("").focus();
            }
        });

        $(".js-source-states-PELANGGARAN_KS").on("change", "", function () {
            var data = $(this).select2("data");

            PELANGGARAN_KS = data.id;
        });

        $(".js-source-states-SUMBER_KS").on("change", "", function () {
            var data = $(this).select2("data");

            SUMBER_KS = data.id;
        });
    });

    function get_data_scanner(NIS_SISWA) {
        var TANGGAL_KS = $("#TANGGAL_KS").val();
        var success = function (data) {
            if (data.status) {
                $.each(data.data, function (index, item) {
                    $("#detail_" + index).html(item);
                });

                $(".detail-nis").slideDown();
                create_homer_success('Data berhasil disimpan');
            } else {
                create_homer_error('Data tidak ditemukan.');
            }
        };

        if (TANGGAL_KS === '' || PELANGGARAN_KS === null || SUMBER_KS === null)
            create_homer_error('Silahkan lengkapi terlebih dahulu field yang ada.');
        else
            create_ajax('<?php echo site_url('komdis/pelanggaran/get_data_scanner'); ?>', 'NIS_SISWA=' + NIS_SISWA + '&TANGGAL_KS=' + TANGGAL_KS + '&PELANGGARAN_KS=' + PELANGGARAN_KS + '&SUMBER_KS=' + SUMBER_KS, success);
    }

    function simpan_absen(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var FIELD = $(that).data('field');
        var SISWA_KA = $(that).data('siswa');
        var KAH_KA = $(that).data('kah');
        var VALUE = $(that).val();

        var success = function (data) {
            $(that).next().remove();
            $(that).show();

            if (data.status)
                $(that).addClass('success');
            else
                $(that).addClass('error');
        };

        $(loading_bar).insertAfter(that);
        $(that).hide();

        create_ajax('<?php echo site_url('komdis/absen_jamaah/simpan_absen'); ?>', 'FIELD=' + FIELD + '&SISWA_KA=' + SISWA_KA + '&KAH_KA=' + KAH_KA + '&VALUE=' + VALUE, success);
    }
</script>