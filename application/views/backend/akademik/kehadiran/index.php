<?php
$title = 'Ketidakhadiran Siswa';
$subtitle = "Daftar semua ketidakhadiran siswa";
$id_datatables = 'datatable1';

$columns = array(
//    'TA',
    'CAWU',
    'NIS',
    'NAMA',
    'KELAS',
    
    'WALI KELAS',
    
    'TANGGAL',
    'JENIS',
    'ALASAN',
    'KETERANGAN',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);

?>


<div class="modal fade" id="cetak_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Absensi Kelas</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <p class="pull-left">Kosongi kelas untuk mencetak semua kelas.</p>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_kehadiran_siswa();" >Cetak</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ID_KELAS = 0;
    var JENIS_CETAK = null;
    var table;
    var url_delete = '<?php echo site_url('akademik/kehadiran/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('akademik/kehadiran/ajax_add'); ?>';
    var url_update = '<?php echo site_url('akademik/kehadiran/ajax_update'); ?>';
    var url_form = '<?php echo site_url('akademik/kehadiran/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];
    var orders = [[ 0, "DESC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
//        create_form_input(id_form, id_modal, url_form, title, null);
        window.open('<?php echo site_url('akademik/kehadiran/tambah_absen'); ?>', '_blank');
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/kehadiran/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
//        $('<a class="btn btn-default btn-sm buttons-kehadiran" tabindex="0" aria-controls="datatable1" data-vivaldi-spatnav-clickable="1" data-toggle="modal" data-target="#cetak_modal"><span>Cetak Kehadiran</span></a>').insertAfter('.buttons-add');
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" data-toggle="modal" data-target="#cetak_modal" onclick="set_type(0)"><span>Kehadiran KBM</span></a></li><li><a href="#" data-toggle="modal" data-target="#cetak_modal" onclick="set_type(1)"><span>Kehadiran Dauroh</span></a></li></ul></div>').insertAfter('.buttons-add');
//        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Add <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="<?php echo site_url('akademik/kehadiran/tambah_absen'); ?>" target="_blank"><span>Pertanggal</span></a></li><li><a href="<?php echo site_url('akademik/kehadiran/tambah_absen_bulan'); ?>" target="_blank"><span>Perbulan</span></a></li></ul></div>').insertAfter('.buttons-add');
//        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="cetak_kehadiran_siswa();">Kehadiran</a></li><!--<li><a href="#" onclick="cetak_rekap_semua();">Rekap Semua</a></li><li><a href="#" onclick="cetak_rekap_perkelas_perbulan();">Rekap Perkelas Perbulan</a></li><li><a href="#" onclick="cetak_rekap_perkelas_percawu();">Rekap Perkelas Percawu</a></li><li><a href="#" onclick="cetak_rekap_persiswa_perbulan();">Rekap Persiswa Perbulan</a></li><li><a href="#" onclick="cetak_rekap_persiswa_percawu();">Rekap Persiswa Percawu</a></li>--></ul></div>').insertAfter('.buttons-add');
        
        $(".buttons-copy, .buttons-pdf").remove();


        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
        
    });
    
    function set_type(type) {
        JENIS_CETAK = type;
    }
    
    function cetak_kehadiran_siswa(){
        $("#cetak_modal").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        window.open('<?php echo site_url('akademik/kehadiran/cetak_absen'); ?>/' + ID_KELAS + '/' + JENIS_CETAK, '_blank');
        
        ID_KELAS = 0;
    }
    
    function cetak_rekap_semua(){
        window.open('<?php echo site_url('akademik/kehadiran/cetak_rekap_semua'); ?>', '_blank');
    }
    
    function cetak_rekap_perkelas_perbulan(){
        window.open('<?php echo site_url('akademik/kehadiran/cetak_rekap_perkelas_perbulan'); ?>', '_blank');
    }
    
    function cetak_rekap_perkelas_percawu(){
        window.open('<?php echo site_url('akademik/kehadiran/cetak_rekap_perkelas_percawu'); ?>', '_blank');
    }
    
    function cetak_rekap_persiswa_perbulan(){
        window.open('<?php echo site_url('akademik/kehadiran/cetak_rekap_persiswa_perbulan'); ?>', '_blank');
    }
    
    function cetak_rekap_persiswa_percawu(){
        window.open('<?php echo site_url('akademik/kehadiran/cetak_rekap_persiswa_percawu'); ?>', '_blank');
    }
    
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