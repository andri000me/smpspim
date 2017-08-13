<?php
$title = 'Peserta Ujian Masuk';
$subtitle = "Daftar semua calon siswa yang harus mengikuti ujian masuk";
$id_datatables = 'datatable1';

$columns = array(
    'NO UM',
    'NAMA',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
?>

<div class="content animate-panel" style="margin-bottom: -60px">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    Pilih Filter
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2 col-md-offset-1">
                            <select id="ID_TINGK" class="form-control">
                                <?php
                                foreach ($JENJANG_DEPT as $detail) {
                                    ?>
                                    <option value="<?php echo $detail['VALUE']; ?>"><?php echo $detail['TEXT']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="JK_SISWA" class="form-control">
                                <option value="L">BANIN</option>
                                <option value="P">BANAT</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary" onclick="set_filter();">TAMPILKAN</button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#cetak_modal">CETAK SURAT KELULUSAN</button>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn btn-success dropdown-toggle">CETAK PENGUMUMAN <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="cetak_pengumuman(1);">Versi PDF</a></li>
                                    <li><a href="#" onclick="cetak_pengumuman(0);">Versi XLS</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content animate-panel" style="margin-bottom: -60px">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
                    Info Kapasitas Total Kelas Tersisa
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 border-right" id="kapasitas_kelas_left"></div>
                        <div class="col-md-6" id="kapasitas_kelas_right"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cetak_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Tanggal Undian Kelas</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_date('Miladiyah', array('name' => 'MILADIYAH', 'id' => 'MILADIYAH'), TRUE, 4); ?>
                    <?php $this->generate->input_text('Hijriyah', array('name' => 'HIJRIYAH', 'id' => 'HIJRIYAH'), TRUE, 7); ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_surat();" >Cetak</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var loading = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" id="loading" width="32px" height="32px" alt="loading..."/>';
    var table;
    var url_delete = '<?php echo site_url('pu/nilai_um/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('pu/nilai_um/ajax_add'); ?>';
    var url_update = '<?php echo site_url('pu/nilai_um/ajax_update'); ?>';
    var url_form = '<?php echo site_url('pu/nilai_um/request_form'); ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [
        {"width": "100px", "targets": 0},
        {"width": "500px", "targets": 1},
//        { "width": "10%", "targets": 2 , "orderable": false},
//        { "width": "10%", "targets": 3 , "orderable": false},
//        { "width": "10%", "targets": 4 , "orderable": false},
//        { "width": "10%", "targets": 5 , "orderable": false},
//        { "width": "10%", "targets": 6 , "orderable": false},
        {"targets": [-1], "orderable": true}
    ];
    var orders = [[0, "ASC"]];
    var requestExport = false;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        // proses_lulus_all();
        var ID_TINGK = $("#ID_TINGK").val();
        var JK_SISWA = $("#JK_SISWA").val();

        window.open('<?php echo site_url('pu/nilai_um/export_csv'); ?>/' + ID_TINGK + '/' + JK_SISWA, '_blank');
    };

    $(document).ready(function () {
        $(".table-" + id_table).hide();
        $("tfoot").remove();
        get_kapasitas_kelas();
    });

    function set_filter() {
        var ID_TINGK = $("#ID_TINGK").val();
        var JK_SISWA = $("#JK_SISWA").val();

<?php if ($validasi_denah && $STATUS_JADWAL) { ?>
            get_mapel(ID_TINGK, JK_SISWA);
<?php } else if (!$STATUS_JADWAL) { ?>
            create_homer_error('Jadwal pada tahun ajaran aktif belum ada.');
<?php } else if (!$validasi_denah) { ?>
            create_homer_error('Denah pada tahun ajaran aktif belum divalidasi.');
<?php } ?>
    }

    function get_mapel(ID_TINGK, JK_SISWA) {
        create_splash('Sistem sedang mengambil data matapelajaran.');
        var success = function (data) {
            var position = "table thead tr";

            $(".mapel, .lulus, .proses").remove();
            $.each(data.DATA, function (key, value) {
                $(position).append("<th class='mapel'>" + value.NAMA_MAPEL + " [" + value.JENIS_PUM + "]</th>");
            });
            $(position).append("<th class='lulus'>RATA-RATA TULIS</th>");
            $(position).append("<th class='lulus'>RATA-RATA LISAN</th>");
            $(position).append("<th class='lulus'>TINGKAT LULUS</th>");
            $(position).append("<th class='proses'>PROSES LULUS</th>");

            remove_splash();

            load_datatables(ID_TINGK, JK_SISWA);
        };

        create_ajax('<?php echo site_url('pu/nilai_um/get_mapel'); ?>', 'ID_TINGK=' + ID_TINGK, success);
    }

    function get_kapasitas_kelas() {
        var success = function (data) {
            $("#kapasitas_kelas_left, #kapasitas_kelas_right").html(" ");

            var i = 0;
            $.each(data.kelas, function (key, value) {
                $("#" + (i > 7 ? 'kapasitas_kelas_right' : 'kapasitas_kelas_left')).append("<p>" + value.KETERANGAN_TINGK + " memiliki sisa kapasitas sebanyak <strong>" + (parseInt(value.JUMLAH_KAPASITAS) - parseInt(data.jumlah_siswa[value.ID_TINGK])) + "</strong> siswa</p>");
                i++;
            });
        };

        create_ajax('<?php echo site_url('pu/nilai_um/get_kapasitas_kelas'); ?>', '', success);
    }

    function load_datatables(ID_TINGK, JK_SISWA) {
        table = initialize_datatables(id_table, '<?php echo site_url('pu/nilai_um/ajax_list'); ?>/' + ID_TINGK + '/' + JK_SISWA, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $(".table-" + id_table).slideDown();
        $(".buttons-add").html("Export Nilai");

        remove_splash();
    }

    function simpan_nilai(t) {
        var JADWAL_PNU = $(t).data("id");
        var SISWA_PNU = $(t).data("siswa");
        var MAPEL_PNU = $(t).data("mapel");
        var NILAI_PNU = $(t).val();

        $(t).hide().parent().append(loading);
        $(t).removeClass('error');
        $(t).removeClass('success');

        var success = function (data) {
            if (data.status) {
                $(t).addClass('success');
            } else {
                $(t).addClass('error');
            }

            $(t).show().parent().children("#loading").remove();
        };

        create_ajax('<?php echo site_url('pu/nilai_um/simpan_nilai') ?>', 'JADWAL_PNU=' + JADWAL_PNU + "&SISWA_PNU=" + SISWA_PNU + "&MAPEL_PNU=" + MAPEL_PNU + "&NILAI_PNU=" + NILAI_PNU, success);
    }

    function proses_lulus(t) {
        var nilai_lengkap = true;
        var btn_ladda = $(t).ladda();
        var ID_SISWA = $(t).data("siswa");
        var ID_TINGK_FILTER = $("#ID_TINGK").val();
        var ID_TINGK = $(t).parent().prev().children().val();
        var success = function (data) {
            $(t).removeClass('btn-info');

            if (data.status) {
                reload_datatables(table);

                get_kapasitas_kelas();
            } else {
                $(t).addClass('btn-danger');
                btn_ladda.ladda('stop');
            }
        };

//        $(t).parent('tr').children('.input-nilai').each(function(index){
//            console.log(this);
//            if($(this).val() === "") {
//                nilai_lengkap = false;
//                $(this).addClass('error');
//            }
//        });

        if (nilai_lengkap) {
            btn_ladda.ladda('start');
            console.log(ID_TINGK);
            create_ajax('<?php echo site_url('pu/nilai_um/proses_kelulusan'); ?>', 'ID_SISWA=' + ID_SISWA + '&ID_TINGK=' + ID_TINGK + '&ID_TINGK_FILTER=' + ID_TINGK_FILTER, success);
        } else {
            create_homer_error('Silahkan lengkapi nilai siswa tersebut terlebih dahulu.');
        }
    }

    function proses_lulus_all() {
        var ID_TINGK = $("#ID_TINGK").val();
        var JK_SISWA = $("#JK_SISWA").val();
        var success = function (data) {
            reload_datatables(table);

            remove_splash();
        };
        var action = function (isConfirm) {
            if (isConfirm) {
                create_splash("Sistem sedang memproses...");

                create_ajax('<?php echo site_url('pu/nilai_um/proses_kelulusan_all'); ?>', 'JK_SISWA=' + JK_SISWA + '&ID_TINGK=' + ID_TINGK, success);
            }
        };

        create_swal_option("Apakah Anda yakin?", "Semua calon siswa pada filter akan diluluskan secara serentak. Proses ini tidak dapat diulang.", action);
    }

    function cetak_surat() {
        var ID_TINGK = $("#ID_TINGK").val();
        var JK_SISWA = $("#JK_SISWA").val();
        var HIJRIYAH = $("#HIJRIYAH").val();
        var MILADIYAH = $("#MILADIYAH").val();

        $("#cetak_modal").modal("hide");

        window.open('<?php echo site_url('pu/nilai_um/cetak_surat'); ?>?ID_TINGK=' + ID_TINGK + '&JK_SISWA=' + JK_SISWA + "&MILADIYAH=" + MILADIYAH + "&HIJRIYAH=" + HIJRIYAH, '_blank');
    }

    function cetak_pengumuman(pdf) {
        window.open('<?php echo site_url('pu/nilai_um/cetak_pengumuman'); ?>/' + pdf, '_blank');
    }
</script>