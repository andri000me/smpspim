<?php
$title = 'Laporan Pengembalian Tagihan';
$subtitle = "Daftar semua laporan pengembalian tagihan";
$id_datatables = 'datatable1';

$columns = array(
    'NAMA TA',
    'NAMA TAGIHAN',
    'NAMA DETAIL',
    'KELAS',
    'NIS',
    'SISWA',
    'NOMINAL',
    'KETERANGAN',
    
    'USER',
    
    'WAKTU',
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
    var table;
    var url_delete = '<?php echo site_url('keuangan/laporan_pengembalian/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('keuangan/laporan_pengembalian/ajax_add'); ?>';
    var url_update = '<?php echo site_url('keuangan/laporan_pengembalian/ajax_update'); ?>';
    var url_form_add = '<?php echo site_url('keuangan/laporan_pengembalian/request_form_add'); ?>';
    var url_form_update = '<?php echo site_url('keuangan/laporan_pengembalian/request_form_update'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = '';//[[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings) {
        var api = this.api();
        var json = api.ajax.json();
        
        $(".total-pembayaran").remove();
        $('<div class="text-center total-pembayaran"><h2 class="font-extra-bold">TOTAL: ' + formattedIDR(json.nominal) + '</h2></div>').insertBefore("#<?php echo $id_datatables; ?>");
    };
    var functionAddData = function (e, dt, node, config) {
//        create_form_input(id_form, id_modal, url_form_add, title, null);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('keuangan/laporan_pengembalian/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $("body").addClass('hide-sidebar');
        $(".buttons-add").remove();
    });
    
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