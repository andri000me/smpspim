<?php
$title = 'Tambah Pelanggaran dengan Barcode';
$subtitle = "Daftar tanggal pelanggaran";
$id_datatables = 'datatable1';

$columns = array(
    'TA',
    'CAWU',
    'TANGGAL',
    'NIS',
    'NO ABSEN',
    'NAMA',
    'NAMA AYAH',
    'KELAS',
    'WALI KELAS',
    'ALASAN',
    'KETERANGAN',
    );

$this->generate->generate_panel_content("Data " . $title, $subtitle);
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form Kehadiran dengan Scanner Barcode
                </div>
                <div class="panel-body">
                    <h3 class="text-center">ABSEN JAMAAH TANGGAL: <?php echo strtoupper($this->date_format->to_print_text($header->TANGGAL_KAH)); ?></h3>
                    <form class="form-horizontal" onsubmit="return false;">
                        <div class="form-group">
                            <label class="col-md-5 control-label">NIS</label>
                            <div class="col-md-3">
                                <input type="text" name="nis" id="nis" class="form-control" placeholder="NIS atau Scan Barcode disini">
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

<?php
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('komdis/absen_jamaah/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('komdis/absen_jamaah/ajax_add'); ?>';
    var url_update = '<?php echo site_url('komdis/absen_jamaah/ajax_update'); ?>';
    var url_form = '<?php echo site_url('komdis/absen_jamaah/request_form'); ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {

    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        validasi_absen();
    };
    var data_alasan = {"":"PILIH OPSI","-":"-","HADIR":"HADIR","SAKIT":"SAKIT","IZIN":"IZIN","ALPHA":"ALPHA"};

    $(document).ready(function () {
        $(".table-datatable1").attr('style', 'margin-top: -60px;');
        $(".detail-nis").hide();
        $("#nis").focus();

        table = initialize_datatables(id_table, '<?php echo site_url('komdis/absen_jamaah/ajax_list_siswa/'.$header->ID_KAH); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        dropdown_searching('ALASAN', 9, data_alasan);

        $("body").addClass('hide-sidebar');
        $('.buttons-add').html("Validasi Absen");

        $('#nis').keypress(function(e) {
            if (e.which == 13) {
                var NIS_SISWA = $(this).val();
                $(".detail-nis").slideUp();
                $(".detail_siswa").html(" ");
                get_data_scanner(NIS_SISWA);
                $(this).val("").focus();
            }
        });
    });

    function get_data_scanner(NIS_SISWA) {
        var success = function(data){
            if(data.status) {
                $.each(data.data, function(index, item) {
                    $("#detail_" + index).html(item);
                });

                $(".detail-nis").slideDown();

                reload_datatables(table);
            } else {
                create_homer_error('Data tidak ditemukan.');
            }
        };

        create_ajax('<?php echo site_url('komdis/absen_jamaah/get_data_scanner'); ?>', 'NIS_SISWA=' + NIS_SISWA + '&ID_KAH=<?php echo $header->ID_KAH; ?>', success);
    }
    
    function simpan_absen(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var FIELD = $(that).data('field');
        var SISWA_KA = $(that).data('siswa');
        var KAH_KA = $(that).data('kah');
        var VALUE = $(that).val();

        var success = function(data){
            $(that).next().remove();
            $(that).show();
            
            if(data.status) $(that).addClass('success');
            else $(that).addClass('error');
        };

        $(loading_bar).insertAfter(that);
        $(that).hide();

        create_ajax('<?php echo site_url('komdis/absen_jamaah/simpan_absen'); ?>', 'FIELD=' + FIELD + '&SISWA_KA=' + SISWA_KA + '&KAH_KA=' + KAH_KA + '&VALUE=' + VALUE, success);
    }

    function validasi_absen() {
        var success = function(data){
            remove_splash();
            if(data.status) {
                create_homer_success('Data berhasil diproses. Halaman ini akan di tutup secara otomatis.');

                setTimeout(function () {
                    window.close();
                }, 1500);
            } else {
                create_homer_error('Data gagal diproses.');
            }
        };
        var success_check = function(data){
            remove_splash();
            if(data.status) {
                create_splash("Sistem sedang memproses validasi.");

                create_ajax('<?php echo site_url('komdis/absen_jamaah/proses_validasi'); ?>', 'ID_KAH=<?php echo $header->ID_KAH; ?>', success);
            } else {
                create_homer_error('Masih ada siswa yang belum memiliki alasan kehadiran. Silahkan cek kembali data absen siswa.');
            }
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                create_splash("Sistem sedang megecek data yang belum diisi.");
                create_ajax('<?php echo site_url('komdis/absen_jamaah/cek_absen'); ?>', 'ID_KAH=<?php echo $header->ID_KAH; ?>', success_check);
            }
        };

        create_swal_option('Apakah Anda yakin melanjutkan?', 'Proses ini akan memindahkan data pada tabel ini ke kehadiran dan pelanggaran. Pastikan data yang dimasukan benar. Proses ini tidak dapat diulang.', action);
    }
</script>