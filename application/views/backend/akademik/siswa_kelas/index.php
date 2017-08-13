<?php
$title = 'Kelas Siswa';
$subtitle = "Daftar semua kelas siswa";
$id_datatables = 'datatable1';

$columns = array(
//    'TA',
    'NIS',
    'NO ABSEN',
    'NAMA SISWA',
    'ANGKATAN',
    'JK',
    'TINGKAT',
    'NAMA KELAS',
    'WALI KELAS',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>

<div id="modal_view"></div>
<div id="view_photo"></div>

<script type="text/javascript">
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
        proses_NIS();
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/siswa_kelas/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-pdf, .buttons-copy, .buttons-print").remove();
        $(".buttons-add").html("Proses NIS");
        $('<a class="btn btn-default btn-sm buttons-absen" tabindex="0" aria-controls="datatable1" data-vivaldi-spatnav-clickable="1" onclick="proses_absen();">Proses Absen</a>').insertAfter('.buttons-add');
        $('<a class="btn btn-default btn-sm buttons-kelas" tabindex="0" aria-controls="datatable1" data-vivaldi-spatnav-clickable="1" onclick="proses_kelas();">Proses Kelas</a>').insertAfter('.buttons-add');
    });
    
    function proses_kelas() {
        var success = function(data) {
            remove_splash();
            
            create_homer_success("Berhasil memasukan siswa kekelas.");
            reload_datatables(table);
        };
        var action = function(isConfirm){
            if(isConfirm) {
                create_splash("Sedang memproses kelas siswa");
                
                create_ajax('<?php echo site_url('akademik/siswa_kelas/proses_kelas'); ?>','',success);
            }
        };
        
        create_swal_option("Apakah Anda yakin?", "Proses ini akan men-generate kelas berdasarkan nilai tertinggi.", action);
    }
    
    function proses_absen() {
        var success = function(data) {
            remove_splash();
            
            create_homer_success("Berhasil membuat nomor absen sebanyak " + data.count + " siswa.");
            reload_datatables(table);
        };
        var action = function(isConfirm){
            if(isConfirm) {
                create_splash("Sedang memproses nomor absen siswa");
                
                create_ajax('<?php echo site_url('akademik/siswa_kelas/proses_absen'); ?>','',success);
            }
        };
        
        create_swal_option("Apakah Anda yakin?", "Proses ini akan men-generate nomor absen pada siswa yang telah memiliki kelas secara otomatis.", action);
    }
    
    function proses_NIS() {
        var success = function(data) {
            remove_splash();
            
            create_homer_success("Berhasil membuat NIS sebanyak " + data.count + " siswa.");
            reload_datatables(table);
        };
        var action = function(isConfirm){
            if(isConfirm) {
                create_splash("Sedang memproses NIS siswa");
                
                create_ajax('<?php echo site_url('akademik/siswa_kelas/proses_nis'); ?>','',success);
            }
        };
        
        create_swal_option("Apakah Anda yakin?", "Proses ini akan men-generate NIS pada siswa yang telah memiliki kelas secara otomatis. Proses tidak dapat diulang.", action);
    }
    
    function proses_random() {
        create_splash("Sistem sedang menempatkan siswa ke kelas secara random");
        var success = function(data){
            create_homer_success("Siswa berhasil dimasukan ke kelas");
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/siswa_kelas/random_siswa'); ?>','',success);
    }
    
    function random_siswa_kekelas() {
        var action = function(isConfirm){
            if(isConfirm) proses_random();
        };
        
        create_swal_option("Apakah Anda yakin?", "Proses ini akan menempatkan siswa yang belum mendapatkan kelas ke kelas secara otomatis. Proses tidak dapat diulang.", action);
    }
    
    function proses_random() {
        create_splash("Sistem sedang menempatkan siswa ke kelas secara random");
        var success = function(data){
            create_homer_success("Siswa berhasil dimasukan ke kelas");
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/siswa_kelas/random_siswa'); ?>','',success);
    }
    
    function proses_lulus(t) {
        var ID_AS = $(t).data("id");
        var KELAS_AS = $(t).parent().prev().prev().children().val();
        var TINGKAT_AS = $(t).parent().prev().prev().prev().children().val();
        var success = function(data) {
            if(data.status) {
                create_homer_info("Berhasil memasukan siswa ke kelas.");
                
                reload_datatables(table);
            } else {
                create_homer_error("Gagal memasukan siswa ke kelas. " + data.msg);
            }
            
            remove_splash();
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                create_splash("Sistem sedang memasukan siswa ke kelas.");
                
                create_ajax('<?php echo site_url('akademik/siswa_kelas/proses_lulus') ?>', "ID_AS=" + ID_AS + "&KELAS_AS=" + KELAS_AS + "&TINGKAT_AS=" + TINGKAT_AS, success);
            }
        };
        
        if(KELAS_AS === "") {
            create_homer_error("Silahkan pilih kelas terlebih dahulu.");
        } else {
            create_swal_option("Apakah Anda yakin?", "Peroses ini tidak dapat diulang. Pastikan data yang Anda pilih adalah benar.", action);
        }
    }
    
    function change_tingkat(t, JK_SISWA) {
        create_splash("Sistem sedang mengambil data kelas");
        var ID_TINGK = $(t).val();
        var tag_kelas = $(t).parent().next().children();
        var success = function(data) {
            tag_kelas.html("<option value='' >-- Pilih Kelas --</option>");
            
            $.each(data.kelas, function(index, detail){
                tag_kelas.append("<option value='" + detail.ID_KELAS + "' >" + detail.NAMA_KELAS + "</option>");
            });
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/siswa_kelas/change_tingkat'); ?>','ID_TINGK=' + ID_TINGK + "&JK_SISWA=" + JK_SISWA, success);
    }
    
    function hapus_kelas(t) {
        var ID_AS = $(t).data('id');
        var success = function(data) {
            if(data.status) create_homer_success("Kelas berhasil di hapus.");
            else create_homer_success("Kelas gagal di hapus.");
            
            remove_splash();
            
            reload_datatables(table);
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                create_splash("Sistem sedang menghapus data kelas");
                create_ajax('<?php echo site_url('akademik/siswa_kelas/hapus_kelas'); ?>','ID_AS=' + ID_AS, success);
            }
        };
        
        create_swal_option('Apakah Anda yakin menghapus kelas siswa?', 'Proses ini tidak dapat diulang. Selain kelas siswa yang dihapus, no absen siswa juga akan dihapus. ', action);
    }
</script>