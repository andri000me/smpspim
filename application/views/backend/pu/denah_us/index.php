<?php
$title = 'Denah ' . $TITLE;
$subtitle = "Daftar semua denah " . strtolower($TITLE);
$id_datatables = 'datatable1';

$columns = array(
    'TAHUN AJARAN',
    'CAWU',
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
    var url_delete = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/ajax_delete' : 'pu/denah_us/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/ajax_add' : 'pu/denah_us/ajax_add'); ?>';
    var url_update = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/ajax_update' : 'pu/denah_us/ajax_update'); ?>';
    var url_form = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/request_form' : 'pu/denah_us/request_form'); ?>';
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
        var action = function (isConfirm) {
            if (isConfirm) {
                create_splash("Sedang sedang mengecek data denah pada Tahun Ajaran yang aktif.");

                var success = function (data) {
                    remove_splash();

                    if (data.status)
                        create_homer_success(data.msg);
                    else
                        window.location = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/buat_denah' : 'pu/denah_us/buat_denah'); ?>';
//                        window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/buat_denah' : 'pu/denah_us/buat_denah'); ?>');
                };

                create_ajax('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/cek_denah' : 'pu/denah_us/cek_denah'); ?>', '', success);
            }
        };

        create_swal_option('Apakah Anda yakin melanjutkan?', 'Sistem akan mengecek denah pada Tahun Ajaran dan Catur Wulan yang aktif. Jika denah telah dibuat, Anda tidak dapat membuat denah baru melainkan merubah denah yang telah ada.', action);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/ajax_list' : 'pu/denah_us/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });

    function update_data_<?php echo $id_datatables; ?>(id) {
        window.location = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/buat_denah' : 'pu/denah_us/buat_denah'); ?>';
//        window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/buat_denah' : 'pu/denah_us/buat_denah'); ?>', '_blank');
    }

    function view_data_<?php echo $id_datatables; ?>(id) {
        window.location = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/show_denah' : 'pu/denah_us/show_denah'); ?>';
//        window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/show_denah' : 'pu/denah_us/show_denah'); ?>', '_blank');
    }

    function hapus_aturan(id) {
        window.location = '<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/hapus_aturan' : 'pu/denah_us/hapus_aturan'); ?>';
//        window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/show_denah' : 'pu/denah_us/show_denah'); ?>', '_blank');
    }

    function hapus_denah(id) {
        var action = function (isConfirm) {
            if (isConfirm) {
                var success = function (data) {
                    remove_splash();

                    if (data.status)
                        create_homer_success("Sistem berhasil menghapus denah");
                    else
                        create_homer_error("Sistem gagal menghapus denah");
                    
                    reload_datatables(table);
                };
                create_splash("Sistem sedang menghapus denah Tahun Ajaran dan CAWU yang aktif.");
                create_ajax('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/hapus_denah' : 'pu/denah_us/hapus_denah'); ?>', '', success);
            }
        };

        create_swal_option('Apakah Anda yakin melanjutkan?', 'Sistem akan menghapus denah pada Tahun Ajaran dan Catur Wulan yang aktif.', action);
    }

    function jadwal_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url($MODE == 'UM' ? 'pu/denah_um/jadwal' : 'pu/denah_us/jadwal'); ?>', '_blank');
    }
</script>