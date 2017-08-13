<?php
$title = 'Jenis Kelompok';
$subtitle = "Daftar semua jenis kelompok";
$id_datatables = 'datatable1';

$columns = array(
    'JENIS',
    'NAMA',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);

if($COUNT > 0) {

$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);

?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('tuk/jenis_kelompok/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('tuk/jenis_kelompok/ajax_add'); ?>';
    var url_update = '<?php echo site_url('tuk/jenis_kelompok/ajax_update'); ?>';
    var url_form = '<?php echo site_url('tuk/jenis_kelompok/request_form'); ?>';
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
        table = initialize_datatables(id_table, '<?php echo site_url('tuk/jenis_kelompok/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
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
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
</script>

<?php } else { ?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel hbggreen">
                <div class="panel-body text-center">
                    <h1>BELUM ADA KELOMPOK PADA TAHUN AJARAN AKTIF</h1>
                    <h3>Klik untuk mempersiapkan kelompok</h3>
                    <a href="<?php echo site_url('tuk/jenis_kelompok/prepare'); ?>" class="btn btn-primary">MULAI&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>