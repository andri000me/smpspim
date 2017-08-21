<?php
$title = 'Detail Tagihan';
$subtitle = "Daftar semua detail tagihan";
$id_datatables = 'datatable1';

$columns = array(
    'NAMA TA',
    'NAMA TAGIHAN',
    'NAMA DETAIL',
    'JENJANG',
    'NOMINAL',
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
    var url_delete = '<?php echo site_url('keuangan/detail_tagihan/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('keuangan/detail_tagihan/ajax_add'); ?>';
    var url_update = '<?php echo site_url('keuangan/detail_tagihan/ajax_update'); ?>';
    var url_form_add = '<?php echo site_url('keuangan/detail_tagihan/request_form_add'); ?>';
    var url_form_update = '<?php echo site_url('keuangan/detail_tagihan/request_form_update'); ?>';
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
        create_form_input(id_form, id_modal, url_form_add, title, null);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('keuangan/detail_tagihan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if(status == 'add') url = url_add;
        else if(status == 'update') url = url_update;
        
        form_save(url, id_form, table);
        
        return false;
    }
    
    function update_data_<?php echo $id_datatables; ?>(id) {
        create_form_input(id_form, id_modal, url_form_update, title, id);
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
    
    function to_currency_nominal(id, t) {
        var nominal = $(t).val();
        
        $('#' + id).val(nominal);
        
        $(t).val(formattedIDR(nominal));
    }
    
    function to_number_nominal(id, t) {
        var nominal = $('#' + id).val();
        
        $(t).val(nominal);
    }
</script>