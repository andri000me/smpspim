<?php
$title = 'Peserta Testing Kitab dan Quran';
$subtitle = "Daftar semua siswa yang akan mengikuti testing Kitab dan Quran";
$id_datatables_1 = 'datatable1';
$id_datatables_2 = 'datatable2';

$columns = array(
    'NIS',
    'NAMA SISWA',
    'JK',
    'JENJANG',
    'TINGKAT',
    'NAMA KELAS',
    'WALI KELAS',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables_2, 'Peserta Testing Kitab', $columns);
$this->generate->datatables($id_datatables_1, 'Peserta Testing Quran', $columns);
?>

<script type="text/javascript">
    var table;
    var id_table1 = '<?php echo $id_datatables_1; ?>';
    var id_table2 = '<?php echo $id_datatables_2; ?>';
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
        table = initialize_datatables(id_table1, '<?php echo site_url('pu/peserta_testing/ajax_list_quran'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        table = initialize_datatables(id_table2, '<?php echo site_url('pu/peserta_testing/ajax_list_kitab'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
        $(".table-datatable1").attr('style', 'margin-top: -50px');
    });
</script>