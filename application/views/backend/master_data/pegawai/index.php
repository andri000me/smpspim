<?php
$title = 'Pegawai';
$subtitle = "Daftar semua pegawai";
$id_datatables = 'datatable1';

$columns = array(
    'NIP',
    'NAMA',
    'JK',
    'ALAMAT',
    'KECAMATAN',
    'KABUPATEN',
    'AKTIF',
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
    var url_delete = '<?php echo site_url('master_data/pegawai/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('master_data/pegawai/ajax_add'); ?>';
    var url_update = '<?php echo site_url('master_data/pegawai/ajax_update'); ?>';
    var url_form = '<?php echo site_url('master_data/pegawai/request_form'); ?>';
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
        window.open('<?php echo site_url('master_data/pegawai/form'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('master_data/pegawai/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });

    function update_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('master_data/pegawai/form'); ?>/' + id, '_blank');
    }

    function view_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('master_data/pegawai/form'); ?>/' + id + '/TRUE', '_blank');
    }

    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }

    function change_status(status, id) {
        var action = function(isConfirm) {
            if(isConfirm) {
                var success = function (data) {
                    if(data.status) create_homer_success('Berhasil merubah status pegawai.');
                    else create_homer_error('Gagal merubah status pegawai.');

                    reload_datatables(table);
                };

                create_ajax('<?php echo site_url('master_data/pegawai/change_status'); ?>', 'AKTIF_PEG=' + status + '&ID_PEG=' + id, success);
            }
        };
        
        create_swal_option('Apakah Anda yakin?', (status === 0) ? 'Menonaktifkan pegawai akan mengakibatkan tertutupnya semua referensi yang menuju pegawai ini.' : '', action);
    }
</script>