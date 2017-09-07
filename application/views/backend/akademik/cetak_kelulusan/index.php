<?php
$title = 'Cetak Berkas Kelulusan';
$subtitle = "Daftar semua siswa lulus yang dapat dicetak kelengkapan berkasnya";
$id_datatables = 'datatable1';

$columns = array(
    'NAMA SISWA',
    'ANGKATAN',
    'JK',
    'OTANGTUA',
    'ALAMAT',
    'KECAMATAN',
    'KABUPATEN',
    'PROVINSI',
    'MUTASI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>

<script type="text/javascript">
    var table;
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
        window.open('<?php echo site_url('master_data/kamus'); ?>', '_blank');
    };

    $(document).ready(function () {
        
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/cetak_kelulusan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").html('Kamus');
        $(".buttons-copy, .buttons-pdf").remove();
        
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="cetak_ijasah(null, 1);">Ijasah TA Aktif (Arabic)</a></li><li><a href="#" onclick="cetak_ijasah(null, 0);">Ijasah TA Aktif (Latin)</a></li><li><a href="#" onclick="cetak_transkrip(null, 1);">Transkrip TA Aktif (Arabic)</a></li><li><a href="#" onclick="cetak_transkrip(null, 0);">Transkrip TA Aktif (Latin)</a></li></ul></div>').insertAfter('.buttons-reload');
        
        $('<form class="form-horizontal">'
        + '<div class="form-group">' 
        + '<label class="col-md-4 control-label">Tanggal Cetak Masehi</label>' 
        + '<div class="col-md-3"><input class="form-control" type="text" id="TANGGAL_MASEHI" placeholder="25 September 2017"></div>'
        + '</div>'
        + '<div class="form-group">' 
        + '<label class="col-md-4 control-label">Tanggal Cetak Hijriyah</label>' 
        + '<div class="col-md-3"><input class="form-control" type="text" id="TANGGAL_HIJRIYAH" placeholder="20 Muharrom 1435"></div>'
        + '</div>'
        + '<div class="form-group">' 
        + '<label class="col-md-4 control-label">Direktur</label>' 
        + '<div class="col-md-5"><input class="form-control" type="text" id="DIREKTUR" value="H. AH. JUNAIDI MUHAMMADUN"></div>'
        + '</div>'
        + '</form><hr>').insertBefore("#datatable1_wrapper");
    });
    
    function cetak_ijasah(ID_SISWA, ARABIC) {
        var TANGGAL_MASEHI = $("#TANGGAL_MASEHI").val();
        var TANGGAL_HIJRIYAH = $("#TANGGAL_HIJRIYAH").val();
        var DIREKTUR = $("#DIREKTUR").val();
        
        if(TANGGAL_MASEHI === "" || TANGGAL_HIJRIYAH === "" || DIREKTUR === "")
            create_homer_error('Form harus diisi terlebih dahulu');
        else
            window.open('<?php echo site_url('akademik/cetak_kelulusan/cetak_ijasah'); ?>?ID_SISWA=' + ID_SISWA + '&ARABIC=' + ARABIC + '&TANGGAL_HIJRIYAH=' + TANGGAL_HIJRIYAH + '&TANGGAL_MASEHI=' + TANGGAL_MASEHI + '&DIREKTUR=' + DIREKTUR, '_blank');
    }
    
    function cetak_transkrip(ID_SISWA, ARABIC) {
        var TANGGAL_MASEHI = $("#TANGGAL_MASEHI").val();
        var TANGGAL_HIJRIYAH = $("#TANGGAL_HIJRIYAH").val();
        var DIREKTUR = $("#DIREKTUR").val();
        
        if(TANGGAL_MASEHI === "" || TANGGAL_HIJRIYAH === "" || DIREKTUR === "")
            create_homer_error('Form harus diisi terlebih dahulu');
        else
            window.open('<?php echo site_url('akademik/cetak_kelulusan/cetak_transkrip'); ?>?ID_SISWA=' + ID_SISWA + '&ARABIC=' + ARABIC + '&TANGGAL_HIJRIYAH=' + TANGGAL_HIJRIYAH + '&TANGGAL_MASEHI=' + TANGGAL_MASEHI + '&DIREKTUR=' + DIREKTUR, '_blank');
    }
</script>