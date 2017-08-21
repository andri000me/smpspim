<?php
$title = 'Kamus';
$subtitle = "Daftar semua kamus";
$id_datatables = 'datatable1';

$columns = array(
    'LATIN',
    'ARAB',
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
    var url_delete = '<?php echo site_url('master_data/kamus/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('master_data/kamus/ajax_add'); ?>';
    var url_update = '<?php echo site_url('master_data/kamus/ajax_update'); ?>';
    var url_form = '<?php echo site_url('master_data/kamus/request_form'); ?>';
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
        table = initialize_datatables(id_table, '<?php echo site_url('master_data/kamus/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(document).on('change', '#LATIN_GK', function () {
            cek_kata(this);
        });
    });
    
    function cek_kata(that) {
        var kata = $(that).val();
        var success = function(data) {
            if(data.status) {
                create_homer_error("Kata sudah terdaftar.");
                $(that).val("");
                $('#ARAB_GK').attr('readonly', 'true');
            } else {
                create_homer_success("Kata dapat digunakan.");
                $('#ARAB_GK').removeAttr('readonly');
            }
        };
        
        create_homer_success("Sistem sedangan mengecek ketersediaan kata.");
        create_ajax('<?php echo site_url('master_data/kamus/cek_kata'); ?>', 'kata=' + kata, success);
    }
    
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
</script>