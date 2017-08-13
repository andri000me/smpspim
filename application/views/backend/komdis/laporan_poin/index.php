<?php
$title = 'Poin Siswa';
$subtitle = "Daftar semua poin siswa";
$id_datatables = 'datatable1';

$columns = array(
    'CAWU',
    'NO ABSEN',
    'NIS',
    'NAMA',
    'KELAS',
    'WALI KELAS',
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
    var ID_PONDOK = 0;
    var ID_TINDAKAN = 0;
    var table;
    var url_add = '<?php echo site_url('komdis/laporan_poin/ajax_add'); ?>';
    var url_form = '<?php echo site_url('komdis/laporan_poin/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{"targets": [-1],"orderable": false}];
    var orders = '';//[[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/laporan_poin/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        
        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
        
        $(".js-source-states-PONDOK_SISWA").on("change", "", function(){
            var data = $(this).select2("data");

            ID_PONDOK = data.id;
        });
        
        $(".js-source-states-TINDAKAN_SISWA").on("change", "", function(){
            var data = $(this).select2("data");

            ID_TINDAKAN = data.id;
        });
        
        $(".buttons-add").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" data-toggle="modal" data-target="#cetak_modal_kelas" >Pelanggaran Perkelas</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_pondok" >Pelanggaran Perpondok</a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal_tindakan" >Pelanggaran Pertindakan</a></li></ul></div>').insertAfter('.buttons-reload');
    });
    
    function cetak_modal_kelas(){
        $("#cetak_modal_kelas").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        window.open('<?php echo site_url('komdis/laporan_poin/cetak_perkelas'); ?>/' + ID_KELAS, '_blank');
        
        ID_KELAS = 0;
    }
    
    function cetak_modal_pondok(){
        $("#cetak_modal_pondok").modal("hide");
        $(".js-source-states-PONDOK_SISWA").select2('data', null);

        window.open('<?php echo site_url('komdis/laporan_poin/cetak_perpondok'); ?>/' + ID_PONDOK, '_blank');
        
        ID_PONDOK = 0;
    }
    
    function cetak_modal_tindakan(){
        $("#cetak_modal_tindakan").modal("hide");
        $(".js-source-states-TINDAKAN_SISWA").select2('data', null);

        window.open('<?php echo site_url('komdis/laporan_poin/cetak_pertindakan'); ?>/' + ID_TINDAKAN, '_blank');
        
        ID_TINDAKAN = 0;
    }
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if(status == 'add') {
            url = url_add;
            
            form_save(url, id_form, table);
        }
        
        return false;
    }
    
    function cetak(ID_KSH) {
        window.open('<?php echo site_url('komdis/laporan_poin/cetak'); ?>/' + ID_KSH, '_blank');
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

<div class="modal fade" id="cetak_modal_pondok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Pelanggaran Perpondok</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Pondok', array('name' => 'PONDOK_SISWA', 'url' => site_url('master_data/pondok_siswa/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_modal_pondok();" >Cetak</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cetak_modal_tindakan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Pelanggaran Pertindakan</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Jenis Tindakan', array('name' => 'TINDAKAN_SISWA', 'url' => site_url('komdis/jenis_tindakan/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_modal_tindakan();" >Cetak</button>
            </div>
        </div>
    </div>
</div>