<?php
$title = 'Peserta Ujian Masuk';
$subtitle = "Daftar semua calon siswa yang harus mengikuti ujian masuk";
$id_datatables = 'datatable1';

$columns = array(
    'NO UM',
    'NAMA',
    'ANGKATAN',
    'JK',
    'JENJANG',
    'TINGKAT',
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
    var url_delete = '<?php echo site_url('psb/peserta_um/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('psb/peserta_um/ajax_add'); ?>';
    var url_update = '<?php echo site_url('psb/peserta_um/ajax_update'); ?>';
    var url_form = '<?php echo site_url('psb/peserta_um/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": true}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        <?php if($this->session->userdata('ID_HAKAKSES') == 3) { ?>create_homer_info('Untuk menambahkan data, silahkan menuju kemenu calon siswa.');
        <?php } elseif($this->session->userdata('ID_HAKAKSES') == 6) { ?>create_homer_error('Anda tidak memiliki hak akses untuk menambah data.');
        <?php } ?>
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('psb/peserta_um/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
</script>