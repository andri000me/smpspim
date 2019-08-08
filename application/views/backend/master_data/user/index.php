<?php
$title = 'User';
$subtitle = "Daftar semua user";
$id_datatables = 'datatable1';

$columns = array(
    'ID',
    'USERNAME',
    'NAMA',
    'LEVEL',
    'STATUS',
    'LOGIN TERAKHIR',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Ubah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);

?>
<script type="text/javascript">
    var status_change = null;
    var table;
    var url_delete = '<?php echo site_url('master_data/user/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('master_data/user/ajax_add'); ?>';
    var url_update = '<?php echo site_url('master_data/user/ajax_update'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_homer_info("Anda dapat menambah user dengan menambah pegawai.");
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('master_data/user/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        $(".datatables-search-LEVEL").html('LEVEL');
    });
    
    function save_form(id_form) {
        create_ladda("ladda-button-save");
        
        if(status_change === 'password') saving_update_password(id_form);
        else if(status_change === 'hakakses') saving_update_hakakses(id_form);
        else if(status_change === 'keuangan') saving_update_keuangan(id_form);
        else if(status_change === 'status') saving_update_status(id_form);
        
        return false;
    }
    
    function update_password_<?php echo $id_datatables; ?>(id) {
        var url_form = '<?php echo site_url('master_data/user/request_form_password'); ?>';
        status_change = 'password';
    
        create_form_input(id_form, id_modal, url_form, title, id);
        $(".modal-title").html('Ubah Password User');
    }
    
    function saving_update_password(id_form) {
        var success = function(data) {
            if(data.status) {
                create_homer_success(data.msg);
                
                $("#" + id_modal).modal('hide');
            } else {
                create_homer_error(data.msg);
            }
            
            remove_ladda();
        };
        
        create_ajax('<?php echo site_url('master_data/user/ajax_update_password'); ?>', $('#' + id_form).serialize(), success);
    }
    
    function update_hakakses_<?php echo $id_datatables; ?>(id) {
        var url_form = '<?php echo site_url('master_data/user/request_form_hakakses'); ?>';
        status_change = 'hakakses';
        
        create_form_input(id_form, id_modal, url_form, title, id);
        $(".modal-title").html('Ubah Hakakses User');
    }
    
    function atur_keuangan_<?php echo $id_datatables; ?>(id) {
        var url_form = '<?php echo site_url('master_data/user/request_form_keuangan'); ?>';
        status_change = 'keuangan';
        
        create_form_input(id_form, id_modal, url_form, title, id);
        $(".modal-title").html('Atur Hakakses Keuangan User');
    }
    
    function update_status_<?php echo $id_datatables; ?>(id) {
        var url_form = '<?php echo site_url('master_data/user/request_form_status'); ?>';
        status_change = 'status';
        
        create_form_input(id_form, id_modal, url_form, title, id);
        $(".modal-title").html('Atur Status User');
    }
    
    function saving_update_hakakses(id_form) {
        var success = function(data) {
            if(data.status) {
                create_homer_success(data.msg);
                
                $("#" + id_modal).modal('hide');
            } else {
                create_homer_error(data.msg);
            }
            
            remove_ladda();
        };
        
        create_ajax('<?php echo site_url('master_data/user/ajax_update_hakakses'); ?>', $('#' + id_form).serialize(), success);
    }
    
    function saving_update_keuangan(id_form) {
        var success = function(data) {
            if(data.status) {
                create_homer_success(data.msg);
                
                $("#" + id_modal).modal('hide');
            } else {
                create_homer_error(data.msg);
            }
            
            remove_ladda();
        };
        
        if($('#tagihan').val() === '' || $('.checkbox_simple').val() === '')  {
            create_homer_error('Silahkan lengkapi form terlebih dahulu');
            remove_ladda();
        } else
            create_ajax('<?php echo site_url('master_data/user/ajax_update_keuangan'); ?>', $('#' + id_form).serialize(), success);
    }
    
    function saving_update_status(id_form) {
        var success = function(data) {
            if(data.status) {
                create_homer_success(data.msg);
                
                $("#" + id_modal).modal('hide');
                
                reload_datatables(table);
            } else {
                create_homer_error(data.msg);
            }
            
            remove_ladda();
        };
        
        create_ajax('<?php echo site_url('master_data/user/ajax_update_status'); ?>', $('#' + id_form).serialize(), success);
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
</script>