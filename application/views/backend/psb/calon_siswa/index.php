<?php
$title = 'Calon Siswa';
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
    var url_delete = '<?php echo site_url('psb/calon_siswa/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('psb/calon_siswa/ajax_add'); ?>';
    var url_update = '<?php echo site_url('psb/calon_siswa/ajax_update'); ?>';
    var url_form = '<?php echo site_url('psb/calon_siswa/request_form'); ?>';
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
        window.open('<?php echo site_url('psb/calon_siswa/form'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('psb/calon_siswa/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-copy, .buttons-pdf").remove();
        $(".buttons-add").parent().append('<a class="btn btn-default btn-sm buttons-add-nik" tabindex="0" aria-controls="datatable1" onclick="add_from_alumnus();" data-vivaldi-spatnav-clickable="1"><span>Add dari Alumni</span></a>');
    });
    
    function add_from_alumnus() {
        window.open('<?php echo site_url('psb/calon_siswa/alumni'); ?>', '_blank');
    }

    function update_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('psb/calon_siswa/form'); ?>/' + id, '_blank');
    }

    function view_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('psb/calon_siswa/form'); ?>/' + id + '/TRUE', '_blank');
    }

    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }

    function change_status_um(status, id) {
        var success = function (data) {
            if(data.status) create_homer_success('Berhasil merubah status ujian masuk siswa');
            else create_homer_error('Gagal merubah status ujian masuk siswa');
            
            reload_datatables(table);
        };

        create_ajax('<?php echo site_url('psb/calon_siswa/change_status_um'); ?>', 'PSB_TEST_SISWA=' + status + '&ID_SISWA=' + id, success);

    }
    
    function mengundurkan_diri(ID_SISWA) {
        var action = function(isConfirm) {
            if(isConfirm) proses_mengundurkan_diri(ID_SISWA, 1);
        };
        
        create_swal_option("Apakah Anda yakin?", "Calon siswa akan dikeluarkan dari Proses PSB. Proses ini tidak dapat diulang.", action);
    }
    
    function proses_mengundurkan_diri(ID_SISWA, FORCE_PROCCESS) {
        create_splash("Sistem sedang memproses pendunduran diri siswa");
        
        var action = function(isConfirm) {
            if(isConfirm) proses_mengundurkan_diri(ID_SISWA, 1);
        };
        var success = function(data) {
            setTimeout(function(){
                remove_splash();

                if(data.status) {
                    create_homer_success(data.msg);

                    reload_datatables(table);
                } else {
                    //if(data.option) create_swal_option("Apakah Anda yakin?", data.msg, action);
                    //else create_homer_error(data.msg);

			create_homer_error(data.msg)
                }
            }, 1500);
            
        };
        
        create_ajax('<?php echo site_url('psb/calon_siswa/mengundurkan_diri'); ?>', 'ID_SISWA=' + ID_SISWA + "&FORCE_PROCCESS=" + FORCE_PROCCESS, success);
    }
</script>
