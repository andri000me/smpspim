<?php
$title = 'Absen Jamaah';
$subtitle = "Daftar semua absen jamaah";
$id_datatables = 'datatable1';

$columns = array(
    'TA',
    'CAWU',
    'TANGGAL',
    'VALIDASI',
    'AKSI',
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
    var url_delete = '<?php echo site_url('komdis/absen_jamaah/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('komdis/absen_jamaah/ajax_add'); ?>';
    var url_update = '<?php echo site_url('komdis/absen_jamaah/ajax_update'); ?>';
    var url_form = '<?php echo site_url('komdis/absen_jamaah/request_form'); ?>';
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
        create_form_input(id_form, id_modal, url_form, title, null);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/absen_jamaah/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if(status == 'add') url = url_add;
        else if(status == 'update') url = url_update;
        
        form_save(url, id_form, table);
        
        return false;
    }
    
    function update_data_<?php echo $id_datatables; ?>(id) {
        create_form_input(id_form, id_modal, url_form, title, id);
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }

    function buka_absen(ID_KAH) {
        window.open('<?php echo site_url('komdis/absen_jamaah/show_absen'); ?>/' + ID_KAH, '_blank');
    }
</script>