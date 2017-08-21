<?php
$title = 'Assign Tagihan';
$subtitle = "Daftar semua siswa tagihan";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NAMA',
    'ANGKATAN',
    'JK',
    'JENJANG',
    'KELAS',
    
    'WALI KELAS',
    
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<div class="modal fade" id="cetak_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <p class="pull-left">Kosongi kelas untuk mencetak semua kelas.</p>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak();" >Cetak</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ID_KELAS = 0;
    var table;
    var url_delete = '<?php echo site_url('keuangan/assign_tagihan/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('keuangan/assign_tagihan/ajax_add'); ?>';
    var url_update = '<?php echo site_url('keuangan/assign_tagihan/ajax_update'); ?>';
    var url_form = '<?php echo site_url('keuangan/assign_tagihan/request_form'); ?>';
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
//        window.open('<?php echo site_url('keuangan/assign_tagihan/cetak_kartu'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('keuangan/assign_tagihan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $('<a class="btn btn-default btn-sm buttons-khoirot" tabindex="0" aria-controls="datatable1" data-vivaldi-spatnav-clickable="1" data-toggle="modal" data-target="#cetak_modal"><span>Cetak Kartu Khoirot</span></a>').insertAfter('.buttons-add');
        $(".buttons-pdf, .buttons-print, .buttons-add").remove();

        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
    });
    
    function cetak(){
        $("#cetak_modal").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        window.open('<?php echo site_url('keuangan/assign_tagihan/cetak_kartu'); ?>/' + ID_KELAS, '_blank');
        
        ID_KELAS = 0;
    }

    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");

        if (status == 'add')
            url = url_add;
        else if (status == 'update')
            url = url_update;

        form_save(url, id_form, table);

        return false;
    }

    function proses_tagihan(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var NAMA_SISWA = $(that).data('nama');
        var HAPUS_TAGIHAN = $(that).data('hapus');
        var action = function (isConfirm) {
            if (isConfirm) {
                $(loading_bar).insertAfter(that);
                $(that).hide();
        
                if(HAPUS_TAGIHAN) proses_unsign(that);
                else proses_sign(that);
            }
        };

        create_swal_option("Apakah Anda yakin " + (HAPUS_TAGIHAN ? 'menghapus' : 'mebambah') + " tagihan siswa " + NAMA_SISWA + "?", "", action);
    }

    function proses_unsign(that) {
        var ID_SETUP = $(that).data('tagihan');
        var success = function (data) {
            reload_datatables(table);
        };
        
        create_ajax('<?php echo site_url('keuangan/assign_tagihan/proses_unsign'); ?>', 'ID_SETUP=' + ID_SETUP, success);
    }

    function proses_sign(that) {
        var DEPT_TINGK = $(that).data('dept');
        var ID_SISWA = $(that).data('siswa');
        var success = function (data) {
            reload_datatables(table);
        };
        
        create_ajax('<?php echo site_url('keuangan/assign_tagihan/proses_sign'); ?>', 'DEPT_TINGK=' + DEPT_TINGK + '&ID_SISWA=' + ID_SISWA, success);
    }


</script>