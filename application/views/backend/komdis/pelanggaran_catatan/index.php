<?php
$title = 'Pelanggaran Catatan';
$subtitle = "Daftar semua pelanggaran yang tidak masuk kedalam tindakan";
$id_datatables = 'datatable1';

$columns = array(
    'CAWU',
    'TANGGAL',
    'NO ABSEN',
    'NIS',
    'NAMA',
    'KELAS',
    'WALI KELAS',
    'DOMISILI',
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
    var ID_KELAS = 0;
    var table;
    var url_delete = '<?php echo site_url('komdis/pelanggaran_catatan/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('komdis/pelanggaran_catatan/ajax_add'); ?>';
    var url_update = '<?php echo site_url('komdis/pelanggaran_catatan/ajax_update'); ?>';
    var url_form = '<?php echo site_url('komdis/pelanggaran_catatan/request_form'); ?>';
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
        
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/pelanggaran_catatan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add, .buttons-print").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Tambah <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="add_individu();" >Pelanggaran Persiswa</a></li><!--<li><a href="<?php echo site_url('komdis/pelanggaran_catatan/form'); ?>" target="_blank">Pelanggaran dengan Barcode</a></li>--></ul></div><a class="dt-button buttons-cetak buttons-html5 btn btn-sm btn-default" data-toggle="modal" data-target="#cetak_modal_kelas" tabindex="0" href="#" ><span>Cetak Perkelas</span></a>').insertAfter('.buttons-reload');
        
        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
    });
    
    function cetak_modal_kelas(){
        $("#cetak_modal_kelas").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        window.open('<?php echo site_url('komdis/pelanggaran_catatan/cetak_perkelas'); ?>/' + ID_KELAS, '_blank');
        
        ID_KELAS = 0;
    }
    
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

<div class="modal fade" id="cetak_modal_kelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Pelanggaran Perkelas</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <p class="pull-left">Kosongi kelas untuk mencetak semua kelas.</p>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_modal_kelas();" >Cetak</button>
            </div>
        </div>
    </div>
</div>