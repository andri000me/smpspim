<?php
$title = 'Ketidakhadiran Guru';
$subtitle = "Daftar semua ketidakhadiran guru";
$id_datatables = 'datatable1';

$columns = array(
    'CAWU',
    'NIP',
    
    'NAMA',
    
    'TANGGAL',
    'ALASAN',
    'KETERANGAN',
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
    var url_delete = '<?php echo site_url('akademik/kehadiran_guru/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('akademik/kehadiran_guru/ajax_add'); ?>';
    var url_update = '<?php echo site_url('akademik/kehadiran_guru/ajax_update'); ?>';
    var url_form = '<?php echo site_url('akademik/kehadiran_guru/request_form'); ?>';
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
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/kehadiran_guru/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-copy, .buttons-pdf").remove();
        
        $('<a class="btn btn-default btn-sm buttons-kehadiran" tabindex="0" aria-controls="datatable1" data-vivaldi-spatnav-clickable="1" onclick="cetak_kehadiran();"><span>Cetak Kehadiran</span></a>').insertAfter('.buttons-add');
    });
    
    function cetak_kehadiran(){
        window.open('<?php echo site_url('akademik/jadwal/cetak_kehadiran_guru'); ?>', '_blank');
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
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
</script>