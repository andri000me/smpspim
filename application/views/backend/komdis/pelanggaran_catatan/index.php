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
    var status_check = false;
    var status_show = false;
    var checkbox_kelas = [];
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
        get_data_kelas();
        $(".status-show").hide();
        $("body").addClass('hide-sidebar');
        
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/pelanggaran_catatan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-print").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Tambah <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="add_individu();" >Pelanggaran Persiswa</a></li><!--<li><a href="<?php echo site_url('komdis/pelanggaran_catatan/form'); ?>" target="_blank">Pelanggaran dengan Barcode</a></li>--></ul></div><a class="dt-button buttons-cetak buttons-html5 btn btn-sm btn-default" data-toggle="modal" data-target="#cetak_modal_kelas" tabindex="0" href="#" ><span>Cetak Perkelas</span></a>').insertAfter('.buttons-reload');
        
        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
    });
    
    function cetak_modal_kelas(){
        $("#cetak_modal_kelas").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        if (ID_KELAS > 0) {
            checkbox_kelas = [];
            checkbox_kelas.push(ID_KELAS);
        }

        window.open('<?php echo site_url('komdis/pelanggaran_catatan/cetak_perkelas'); ?>?KELAS=' + checkbox_kelas, '_blank');
        
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

    function get_data_kelas() {
        var success = function (data) {
            console.log(data.length);
            var maks_perkolom = Math.round(data.length / 4) - 1;
            var x = 0;
            var posisi = 0;

            $.each(data, function (key, value) {
                if (x == 0)
                    posisi++;

                $("#checkbox-kelas-" + posisi).append('<label> <input type="checkbox" value="' + value.value + '" class="checkbox-kelas" onchange="checkbox_changed()">&nbsp;&nbsp;' + value.label + '</label>');

                if (x == maks_perkolom)
                    x = 0;
                else
                    x++;
            });
        };
        create_ajax('<?php echo site_url('akademik/kelas/get_all'); ?>', '', success);
    }

    function reset_select2() {
        $(".js-source-states-ID_KELAS").select2('data', null);
        ID_KELAS = 0;
    }

    function checkbox_changed() {
        checkbox_kelas = [];

        reset_select2();

        $(".checkbox-kelas").each(function (index) {
            if ($(this).is(':checked'))
                checkbox_kelas.push($(this).val());
        });
    }

    function toggle_click(that) {
        checkbox_kelas = [];

        reset_select2();

        if (status_check) {
            $(".checkbox-kelas").removeAttr('checked');
            status_check = false;
            $(that).html('Check All');
        } else {
            $(".checkbox-kelas").prop('checked', true);
            status_check = true;
            $(that).html('Uncheck All');

            $(".checkbox-kelas").each(function (index) {
                checkbox_kelas.push($(this).val());
            });
        }
    }
    
    function toggle_show(that) {
        $(".checkbox-kelas").removeAttr('checked');
        checkbox_kelas = [];
        reset_select2();
        
        if(status_show) {
            $(".status-show").slideUp();
            $(that).html('Tampilkan Semua Kelas');
        } else {
            $(".status-show").slideDown();
            $(that).html('Sembunyikan Semua Kelas');
        }
        
        status_show = !status_show;
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
                <div class="row status-show">
                    <div class="col-md-12 text-center">
                        <hr>
                        <button type="button" class="btn btn-primary btn-sm" onclick="toggle_click(this)">Check All</button>
                    </div>
                </div>
                <div class="row status-show">
                    <div class="col-md-3"  id="checkbox-kelas-1"></div>
                    <div class="col-md-3"  id="checkbox-kelas-2"></div>
                    <div class="col-md-3"  id="checkbox-kelas-3"></div>
                    <div class="col-md-3"  id="checkbox-kelas-4"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm pull-left" onclick="toggle_show(this)">Tampilkan Semua Kelas</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_modal_kelas();" >Cetak</button>
            </div>
        </div>
    </div>
</div>