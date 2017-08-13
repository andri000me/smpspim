<?php
$title = 'Peserta Siswa Tidak Tamat';
$subtitle = "Daftar semua siswa yang tidak tamat kitab dan quran";
$id_datatables_1 = 'datatable1';

$columns = array(
    'TA',
    'NIS',
    'NAMA SISWA',
    'JK',
    'JENJANG',
    'TINGKAT',
    'NAMA KELAS',
    
    'WALI KELAS',
    
    'STATUS',
    'NILAI KITAB',
    'NILAI QURAN',
    'PILIHAN',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables_1, 'Peserta Testing Quran', $columns);
?>

<script type="text/javascript">
    var table;
    var id_table = '<?php echo $id_datatables_1; ?>';
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
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/kelulusan_tt/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });
    
    function proses_lulus(that) {
        var ID_AS = $(that).data('id');
        var STATUS_KELULUSAN = $(that).parent().prev().children().val();
        var success = function(data){
            remove_splash();
            
            if(data.status) create_homer_success('Berhasil memproses siswa');
            else create_homer_error('Gagal memproses siswa. ' + data.msg);
            
            reload_datatables(table);
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                create_splash("Sistem sedang memproses siswa");
                create_ajax('<?php echo site_url('akademik/kelulusan/proses_lulus'); ?>', 'ID_AS=' + ID_AS + '&STATUS_KELULUSAN=' + STATUS_KELULUSAN + '&NEXT_TA_FILTER=' + null, success);
            }
        };
        
        if(STATUS_KELULUSAN === '')
            create_homer_error("Silahkan pilih pilihan terlebih dahulu");
        else
            create_swal_option('Apakah Anda yakin melanjutkan?', '', action);
    }
</script>