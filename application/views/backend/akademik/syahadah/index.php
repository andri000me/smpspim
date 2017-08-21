<?php
$title = 'Translasi Latin ke Arab';
$subtitle = "Daftar semua siswa yang ditranslasi ke tulisan arab";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NO ABSEN',
    'NAMA SISWA',
    'NAMA SISWA',
    'NAMA ORTU',
    'NAMA ORTU',
    'JK',
    'TINGKAT',
    'NAMA KELAS',
    
    'WALI KELAS',
    
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, 'Data Siswa', $columns);

$id_modal = "modal-data";
$title_form = "Tambah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>

<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('akademik/syahadah/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('akademik/syahadah/ajax_add'); ?>';
    var url_update = '<?php echo site_url('akademik/syahadah/ajax_update'); ?>';
    var url_form = '<?php echo site_url('akademik/syahadah/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
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
        window.open('<?php echo site_url('master_data/kamus'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/syahadah/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").html('Kamus');
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
        
        $('.info-field').children().append(" Gunakan spasi pada akhir kalimah untuk memisah dengan kalimah setelahnya.");
        $('.modal-title').html("Tambah/Ubah Kamus");
    }
</script>