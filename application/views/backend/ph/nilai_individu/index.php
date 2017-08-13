<?php
$title = 'Nilai';
$subtitle = "Daftar semua nilai";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NAMA',
    'KELAS',
    'WALI KELAS',
    'NILAI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('ph/nilai/ajax_delete'); ?>/';
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
        window.open('<?php echo site_url('ph/nilai/form'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('ph/nilai/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
    
    function update_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('ph/nilai/form'); ?>/' + id, '_blank');
    }
</script>