<?php
$title = 'Laporan Pembayaran Tagihan';
$subtitle = "Daftar semua laporan pembayaran tagihan keuangan";
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
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<script type="text/javascript">
    var table;
    var url_delete = '<?php echo site_url('keuangan/laporan/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('keuangan/laporan/ajax_add'); ?>';
    var url_update = '<?php echo site_url('keuangan/laporan/ajax_update'); ?>';
    var url_form_add = '<?php echo site_url('keuangan/laporan/request_form_add'); ?>';
    var url_form_update = '<?php echo site_url('keuangan/laporan/request_form_update'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = '';//[[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {
        
    };
    var functionDrawCallback = function (settings) {
        var api = this.api();
        var json = api.ajax.json();
        
        $(".total-pembayaran").remove();
        $('<div class="text-center total-pembayaran"><h2 class="font-extra-bold">TOTAL: ' + formattedIDR(json.nominal) + '</h2></div>').insertBefore("#<?php echo $id_datatables; ?>");
    };
    var functionAddData = function (e, dt, node, config) {
//        create_form_input(id_form, id_modal, url_form_add, title, null);
    };
    
    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('keuangan/laporan/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $("body").addClass('hide-sidebar');
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" aria-expanded="false">Laporan <span class="caret"></span></button><ul class="dropdown-menu"><li><a href=#" onclick="laporan_harian(this)">Laporan Harian</a></li><?php if($this->session->userdata('ADMINISTRATOR')) { ?><li><a href="<?php echo site_url('keuangan/laporan/laporan_tagihan'); ?>" target="_blank">Laporan Tagihan</a></li><?php } ?></ul></div>').insertAfter(".buttons-add");
        $(".buttons-add").remove();
    });
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if (status == 'add')
            url = url_add;
        else if (status == 'update')
            url = url_update;
        
        form_save(url, id_form, table);
        
        return false;
    }
    
    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }
    
    function laporan_harian(t) {
        var status = $(t).data('modal');
        
        $("#laporan_harian").modal(status);
        
        if (status == 'hide') {
            window.open("<?php echo site_url('keuangan/laporan/harian'); ?>?start=" + $('#DATE_START').val() + "&end=" + $('#DATE_END').val(), '_blank');
        }
    }
</script>

<div class="modal fade" id="laporan_harian" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cetak Laporan Harian</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php $this->generate->input_date('Tanggal Awal', array('name' => 'DATE_START', 'id' => 'DATE_START', 'value' => date('Y-m-d', strtotime('-15 days'))), TRUE, 3); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php $this->generate->input_date('Tanggal Akhir', array('name' => 'DATE_END', 'id' => 'DATE_END', 'value' => date('Y-m-d')), TRUE, 3); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="laporan_harian(this)" data-modal="hide">Cetak</button>
            </div>
        </div>
    </div>
</div>