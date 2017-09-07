<?php
$title = 'Log Query Database';
$subtitle = "Daftar semua log query";
$id_datatables = 'datatable1';

$columns = array(
    'ID',
    'TANGGAL',
    'IP',
    'URI',
    'SESSION',
    'QUERY',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);

?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('master_data/log_query/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('master_data/log_query/ajax_add'); ?>';
    var url_update = '<?php echo site_url('master_data/log_query/ajax_update'); ?>';
    var url_form = '<?php echo site_url('master_data/log_query/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = '';//[[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_ajax('<?php echo site_url('master_data/log_query/clear_log'); ?>', '', function(data){
            create_homer_success('Berhasil membersihkan log.');
        });
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('master_data/log_query/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $(".buttons-add").html('Clear Log');
        $(".buttons-copy, .buttons-print").remove();
    });
</script>