<?php
$title = 'Peserta Tanpa Ujian Masuk';
$subtitle = "Daftar semua calon siswa yang tanpa mengikuti ujian masuk";
$id_datatables = 'datatable1';

$columns = array(
    'NAMA',
    'ANGKATAN',
    'JK',
    'JENJANG',
    'TINGKAT',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('psb/peserta_non_um/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('psb/peserta_non_um/ajax_add'); ?>';
    var url_update = '<?php echo site_url('psb/peserta_non_um/ajax_update'); ?>';
    var url_form = '<?php echo site_url('psb/peserta_non_um/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": true}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        luluskan_semua();
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('psb/peserta_non_um/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-copy").remove();
        $(".buttons-add").html("Luluskan semua");
    });
    
    function luluskan_semua() {
        var action = function(isConfirm) {
            if(isConfirm) proses_kelulusan_semua();
        };
        
        create_swal_option("Apakah Anda yakin?", "Semua calon siswa akan dimasukan ke akademik untuk penentuan kelas. Proses ini tidak dapat diulang.", action);
    }
    
    function proses_kelulusan_semua() {
        create_splash("Sistem sedang meluluskan siswa");
        
        var success = function(data) {
            create_homer_info('Semua siswa telah berhasil diluluskan.');
            reload_datatables(table);
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('psb/peserta_non_um/luluskan_semua'); ?>', '', success);
    }
    
    function luluskan(ID_SISWA) {
        var action = function(isConfirm) {
            if(isConfirm) proses_kelulusan(ID_SISWA);
        };
        
        create_swal_option("Apakah Anda yakin?", "Calon siswa akan dimasukan ke akademik untuk penentuan kelas. Proses ini tidak dapat diulang.", action);
    }
    
    function proses_kelulusan(ID_SISWA) {
        create_splash("Sistem sedang meluluskan siswa");
        
        var success = function(data) {
            reload_datatables(table);
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('psb/peserta_non_um/luluskan'); ?>', 'ID_SISWA=' + ID_SISWA, success);
    }
</script>