<?php
$title = 'Jenis Pelanggaran';
$subtitle = "Daftar semua jenis pelanggaran";
$id_datatables = 'datatable1';

$columns = array(
    'TA',
    'NO',
    'JENIS',
    'POIN',
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
    var url_delete = '<?php echo site_url('komdis/jenis_pelanggaran/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('komdis/jenis_pelanggaran/ajax_add'); ?>';
    var url_update = '<?php echo site_url('komdis/jenis_pelanggaran/ajax_update'); ?>';
    var url_form = '<?php echo site_url('komdis/jenis_pelanggaran/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];
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
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/jenis_pelanggaran/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        
    });
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        var INDUK_KJP = $("#INDUK_KJP").val();
        var ANAK_KJP = $("#ANAK_KJP").val();
        var POIN_KJP = $("#POIN_KJP").val();
        
        if(status == 'add') {
            url = url_add;

            var success = function(data) {
                if(data.status) {
                    if(isNaN(parseInt(POIN_KJP))) {
                        create_homer_error("Poin harus berupa angka");

                        remove_ladda();
                    } else form_save(url, id_form, table);
                } else {
                    create_homer_error("INDUK dan ANAK tidak dapat digunakan");

                    remove_ladda();
                }
            };

            if(isNaN(parseInt(INDUK_KJP)) || isNaN(parseInt(ANAK_KJP))) 
                create_homer_error("INDUK dan ANAK harus berupa angka");
            else 
                create_ajax('<?php echo site_url('komdis/jenis_pelanggaran/cek_no'); ?>', "INDUK_KJP=" + INDUK_KJP + '&ANAK_KJP=' + ANAK_KJP, success);
        } else if(status == 'update') {
            url = url_update;
            
            if(isNaN(parseInt(POIN_KJP))) {
                create_homer_error("Poin harus berupa angka");

                remove_ladda();
            } else form_save(url, id_form, table);
        }
        
        return false;
    }
    
    function update_data_<?php echo $id_datatables; ?>(id) {
        create_form_input(id_form, id_modal, url_form, title, id);
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
</script>