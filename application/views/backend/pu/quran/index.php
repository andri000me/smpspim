<?php
$title = 'Testing Quran';
$subtitle = "Daftar semua jadwal testing quran";
$id_datatables = 'datatable1';

$columns = array(
    'TA',
    'TANGGAL',
    'JAM MULAI',
    'JAM SELESAI',
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
    var url_delete = '<?php echo site_url('pu/quran/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('pu/quran/ajax_add'); ?>';
    var url_update = '<?php echo site_url('pu/quran/ajax_update'); ?>';
    var url_form = '<?php echo site_url('pu/quran/request_form'); ?>';
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
        window.open('<?php echo site_url('pu/quran/form_aturan'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('pu/quran/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
</script>