<?php
$title = 'Pelanggaran';
$subtitle = "Daftar semua pelanggaran";
$id_datatables = 'datatable1';

$columns = array(
    'CAWU',
    'TANGGAL',
    'NO ABSEN',
    'NIS',
    'NAMA',
    'KELAS',
    'WALI KELAS',
    'SUMBER',
    'PELANGGARAN',
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
    var url_delete = '<?php echo site_url('komdis/pelanggaran/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('komdis/pelanggaran/ajax_add'); ?>';
    var url_update = '<?php echo site_url('komdis/pelanggaran/ajax_update'); ?>';
    var url_form = '<?php echo site_url('komdis/pelanggaran/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
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
        create_form_input(id_form, id_modal, url_form, title, null);
    };

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');
        
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/pelanggaran/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Tambah <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="add_individu();" >Pelanggaran Persiswa</a></li><li><a href="<?php echo site_url('komdis/absen_jamaah'); ?>" target="_blank">Pelanggaran dengan Barcode</a></li></ul></div>').insertAfter('.buttons-reload');
    });
    
    function add_individu() {
        create_form_input(id_form, id_modal, url_form, title, null);
    }
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if(status == 'add') url = url_add;
        else if(status == 'update') url = url_update;
        
        form_save(url, id_form, table);
        
        return false;
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
</script>