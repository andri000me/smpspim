<?php
$title = 'Peserta Ujian Sekolah';
$subtitle = "Daftar semua siswa yang akan mengikuti ujian sekolah";
$id_datatables = 'datatable1';

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
        
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('pu/data_us/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });
</script>