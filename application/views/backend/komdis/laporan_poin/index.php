<?php
$title = 'Poin Siswa';
$subtitle = "Daftar semua poin siswa";
$id_datatables = 'datatable1';

$columns = array(
//    'CAWU',
    'NO ABSEN',
    'NIS',
    'NAMA',
    'KELAS',
    'WALI KELAS',
    'POIN',
    'LARI',
    'SURAT',
);
if ($this->session->userdata('ID_HAKAKSES') == 7) {
    $columns[] = 'AKSI';
    $columns[] = 'AKSI';
}

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<script type="text/javascript">
    var status_check = false;
    var status_show = false;
    var checkbox_kelas = [];
    var checkbox_pondok = [];
    var checkbox_siswa = [];
    var ID_KELAS = 0;
    var ID_PONDOK = 0;
    var ID_TINDAKAN = 0;
    var TYPE_KELAS = null;
    var table;
    var url_add = '<?php echo site_url('komdis/laporan_poin/ajax_add'); ?>';
    var url_form = '<?php echo site_url('komdis/laporan_poin/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
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

    };

    $(document).ready(function () {
<?php if ($this->session->userdata('ID_HAKAKSES') == 7) { ?>
            get_data_checkbox('kelas');
            get_data_checkbox('pondok');
<?php } ?>

        $(".status-show").hide();

        table = initialize_datatables(id_table, '<?php echo site_url('komdis/laporan_poin/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $(".buttons-add").remove();

<?php if ($this->session->userdata('ID_HAKAKSES') == 7) { ?>
            $(".js-source-states-ID_KELAS").on("change", "", function () {
                var data = $(this).select2("data");

                $('.checkbox-kelas').removeAttr('checked');

                ID_KELAS = data.id;
            });

            $(".js-source-states-PONDOK_SISWA").on("change", "", function () {
                var data = $(this).select2("data");

                $('.checkbox-pondok').removeAttr('checked');

                ID_PONDOK = data.id;
            });

            $(".js-source-states-TINDAKAN_SISWA").on("change", "", function () {
                var data = $(this).select2("data");

                ID_TINDAKAN = data.id;
            });

            $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="cetak_siswa_multi()">Pelanggaran Persiswa</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_kelas" onclick="set_type_kelas(0)">Pelanggaran Perkelas</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_kelas" onclick="set_type_kelas(2)">Pelanggaran Pondok Perkelas</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_pondok" >Pelanggaran Perpondok</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_tindakan" >Pelanggaran Pertindakan</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_kelas" onclick="set_type_kelas(1)">Pelanggaran Ringan Perkelas</a></li><li><a href="<?php echo site_url('komdis/laporan_poin/rekapitulasi'); ?>">Rekapitulasi</a></li><li><a href="#" onclick="download_statistik(0)">Download Statistik Bulanan</a></li><li><a href="#" onclick="download_statistik(1)">Download Statistik Cawu dan Tahunan</a></li></ul></div><a class="dt-button btn btn-sm btn-default buttons-fix" tabindex="0" aria-controls="datatable1" href="#" onclick="fix_data()"><span>Fix Data</span></a>').insertAfter('.buttons-reload');
<?php } ?>
    });
<?php if ($this->session->userdata('ID_HAKAKSES') == 7) { ?>
        function fix_data() {
            create_ajax('<?php echo site_url('komdis/laporan_poin/fix_lari_dan_poin'); ?>', '', function (data) {
                create_homer_success('Data berhasil dibenahi');
            });
        }

        function download_statistik(status) {
            window.open('<?php echo site_url('komdis/laporan_poin/download_statistik'); ?>/' + status, '_blank');
        }

        function set_type_kelas(TYPE) {
            TYPE_KELAS = TYPE;
        }

        function check_cetak_siswa(that) {
            checkbox_siswa = [];
            $(".checkbox").each(function (index) {
                if ($(this).is(':checked')) {
                    checkbox_siswa.push($(this).val());
                }
            });
        }

        function cetak_siswa_multi() {
            if (checkbox_siswa.length > 0)
                window.open('<?php echo site_url('komdis/laporan_poin/cetak_siswa_multi'); ?>?ID_KSH=' + checkbox_siswa, '_blank');
            else
                create_homer_error('Silahkan pilih siswa terlebih dahulu');

            checkbox_siswa = [];
            reload_datatables(table);
        }

        function cetak_modal_kelas() {
            $("#cetak_modal_kelas").modal("hide");
            $(".js-source-states-ID_KELAS").select2('data', null);

            if (ID_KELAS > 0) {
                checkbox_kelas = [];
                checkbox_kelas.push(ID_KELAS);
            }

            if (TYPE_KELAS === 0)
                window.open('<?php echo site_url('komdis/laporan_poin/cetak_perkelas'); ?>?KELAS=' + checkbox_kelas, '_blank');
            if (TYPE_KELAS === 1)
                window.open('<?php echo site_url('komdis/laporan_poin/cetak_ringan_perkelas'); ?>?KELAS=' + checkbox_kelas, '_blank');
            if (TYPE_KELAS === 2)
                window.open('<?php echo site_url('komdis/laporan_poin/cetak_pondok_perkelas'); ?>?KELAS=' + checkbox_kelas, '_blank');
            else if (TYPE_KELAS === null)
                create_homer_error("Ada kesalahan di javascript");

            TYPE_KELAS = null;
            ID_KELAS = 0;
        }

        function cetak_modal_pondok() {
            $("#cetak_modal_pondok").modal("hide");
            $(".js-source-states-PONDOK_SISWA").select2('data', null);

            if (ID_PONDOK > 0) {
                checkbox_pondok = [];
                checkbox_pondok.push(ID_PONDOK);
            }

            window.open('<?php echo site_url('komdis/laporan_poin/cetak_perpondok'); ?>?PONDOK=' + checkbox_pondok, '_blank');

            ID_PONDOK = 0;
        }

        function cetak_modal_tindakan() {
            $("#cetak_modal_tindakan").modal("hide");
            $(".js-source-states-TINDAKAN_SISWA").select2('data', null);

            window.open('<?php echo site_url('komdis/laporan_poin/cetak_pertindakan'); ?>/' + ID_TINDAKAN, '_blank');

            ID_TINDAKAN = 0;
        }

        function action_save_<?php echo $id_datatables; ?>(id_form) {
            var status = $("#" + id_form).data("status");

            if (status == 'add') {
                url = url_add;

                form_save(url, id_form, table);
            }

            return false;
        }

        function cetak(ID_KSH) {
            window.open('<?php echo site_url('komdis/laporan_poin/cetak'); ?>/' + ID_KSH, '_blank');
        }

        function get_data_checkbox(title) {
            var success = function (data) {
                var pembagi = (title === 'kelas') ? 4 : 3;
                var maks_perkolom = Math.round(data.length / pembagi) - 1;
                var x = 0;
                var posisi = 0;

                $.each(data, function (key, value) {
                    if (x == 0)
                        posisi++;

                    $("#checkbox-" + title + "-" + posisi).append('<label> <input type="checkbox" value="' + value.value + '" class="checkbox-' + title + '" onchange="checkbox_changed(\'' + title + '\')">&nbsp;&nbsp;' + value.label + '</label><br>');

                    if (x == maks_perkolom)
                        x = 0;
                    else
                        x++;
                });
            };

            if (title === 'kelas')
                create_ajax('<?php echo site_url('akademik/kelas/get_all'); ?>', '', success);
            else if (title === 'pondok')
                create_ajax('<?php echo site_url('master_data/pondok_siswa/get_all'); ?>', '', success);
        }

        function reset_select2() {
            $(".js-source-states-ID_KELAS").select2('data', null);
            ID_KELAS = 0;
            $(".js-source-states-PONDOK_SISWA").select2('data', null);
            ID_PONDOK = 0;
        }

        function checkbox_changed(title) {
            checkbox_kelas = [];
            checkbox_pondok = [];

            reset_select2();

            $(".checkbox-" + title).each(function (index) {
                if ($(this).is(':checked')) {
                    var val_checkbox = $(this).val();

                    if (title === 'kelas')
                        checkbox_kelas.push(val_checkbox);
                    else if (title === 'pondok')
                        checkbox_pondok.push(val_checkbox);
                }
            });
        }

        function toggle_click(that, title) {
            checkbox_kelas = [];
            checkbox_pondok = [];

            reset_select2();

            if (status_check) {
                $(".checkbox-" + title).removeAttr('checked');
                status_check = false;
                $(that).html('Check All');
            } else {
                $(".checkbox-" + title).prop('checked', true);
                status_check = true;
                $(that).html('Uncheck All');

                $(".checkbox-" + title).each(function (index) {
                    var val_checkbox = $(this).val();

                    if (title === 'kelas')
                        checkbox_kelas.push(val_checkbox);
                    else if (title === 'pondok')
                        checkbox_pondok.push(val_checkbox);
                });
            }
        }

        function toggle_show(that, title) {
            $(".checkbox-" + title).removeAttr('checked');
            checkbox_kelas = [];
            reset_select2();

            if (status_show) {
                $(".status-show").slideUp();
                $(that).html('Tampilkan semua ' + title);
            } else {
                $(".status-show").slideDown();
                $(that).html('Sembunyikan semua ' + title);
            }

            status_show = !status_show;
        }

<?php } ?>
</script>

<?php if ($this->session->userdata('ID_HAKAKSES') == 7) { ?>
    <div class="modal fade" id="cetak_modal_kelas" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Form Cetak Pelanggaran Perkelas</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                    </form>
                    <div class="row status-show">
                        <div class="col-md-12 text-center">
                            <hr>
                            <button type="button" class="btn btn-primary btn-sm" onclick="toggle_click(this, 'kelas')">Check All</button>
                        </div>
                    </div>
                    <div class="row status-show">
                        <div class="col-md-3"  id="checkbox-kelas-1"></div>
                        <div class="col-md-3"  id="checkbox-kelas-2"></div>
                        <div class="col-md-3"  id="checkbox-kelas-3"></div>
                        <div class="col-md-3"  id="checkbox-kelas-4"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info btn-sm pull-left" onclick="toggle_show(this, 'kelas')">Tampilkan semua kelas</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                    <button type="button" class="btn btn-primary" onclick="cetak_modal_kelas();" >Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cetak_modal_pondok" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Form Cetak Pelanggaran Perpondok</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <?php $this->generate->input_select2('Pondok', array('name' => 'PONDOK_SISWA', 'url' => site_url('master_data/pondok_siswa/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                    </form>
                    <div class="row status-show">
                        <div class="col-md-12 text-center">
                            <hr>
                            <button type="button" class="btn btn-primary btn-sm" onclick="toggle_click(this, 'pondok')">Check All</button>
                        </div>
                    </div>
                    <div class="row status-show">
                        <div class="col-md-4"  id="checkbox-pondok-1"></div>
                        <div class="col-md-4"  id="checkbox-pondok-2"></div>
                        <div class="col-md-4"  id="checkbox-pondok-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info btn-sm pull-left" onclick="toggle_show(this, 'pondok')">Tampilkan semua pondok</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                    <button type="button" class="btn btn-primary" onclick="cetak_modal_pondok();" >Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cetak_modal_tindakan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Form Cetak Pelanggaran Pertindakan</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <?php $this->generate->input_select2('Jenis Tindakan', array('name' => 'TINDAKAN_SISWA', 'url' => site_url('komdis/jenis_tindakan/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                    <button type="button" class="btn btn-primary" onclick="cetak_modal_tindakan();" >Cetak</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>