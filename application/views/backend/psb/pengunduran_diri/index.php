<?php
$title = 'Calon Siswa yang Mengundurkan Diri';
$subtitle = "Daftar semua calon siswa yang mendaftar";
$id_datatables = 'datatable1';

$columns = array(
    'NAMA',
    'ANGKATAN',
    'JK',
    'TEMPAT LAHIR',
    'TANGGAL LAHIR',
    'ALAMAT',
    'KECAMATAN',
    'KABUPATEN',
    'PROVINSI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('psb/pengunduran_diri/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('psb/pengunduran_diri/ajax_add'); ?>';
    var url_update = '<?php echo site_url('psb/pengunduran_diri/ajax_update'); ?>';
    var url_form = '<?php echo site_url('psb/pengunduran_diri/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('psb/pengunduran_diri/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });

    function view_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('psb/pengunduran_diri/form'); ?>/' + id + '/TRUE', '_blank');
    }

    function kembalikan_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('psb/pengunduran_diri/form'); ?>/' + id, '_blank');
    }
</script>