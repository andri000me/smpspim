<?php
$title = 'Surat Segera';
$subtitle = "Daftar semua surat yang harus segera dicetak";
$id_datatables = 'datatable1';

$columns = array(
    'CAWU',
    'NIS',
    'NAMA',
    'KELAS',
    'POIN SISWA',
    'TINDAKAN',
    'POIN MINIMAL',
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
    var url_add = '<?php echo site_url('komdis/laporan_poin/ajax_add'); ?>';
    var url_form = '<?php echo site_url('komdis/laporan_poin/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [
        {"targets": [-1],"orderable": false}
    ];
    var orders = [[ 4, "DESC" ]];
    var requestExport = true;
    var functionInitComplete = function(settings, json) {
        
    };
    var functionDrawCallback = function(settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('komdis/laporan_surat_segera/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");
        
        if(status == 'add') {
            url = url_add;
            
            form_save(url, id_form, table);
        }
        
        return false;
    }
    
    function cetak_surat(ID_KJT, URL_KJT, ID_KSH, KOLEKTIF_KJT) {
        window.open('<?php echo site_url('komdis/laporan_poin/ajax_add'); ?>?TINDAKAN_KT=' + ID_KJT + '&URL_KJT=' + URL_KJT + '&PELANGGARAN_HEADER_KT=' + ID_KSH + '&KOLEKTIF_KJT=' + KOLEKTIF_KJT + '&PENANGGUNGJAWAB_KT=<?php echo $this->pengaturan->getDataKetuaKomdis()->ID_PEG; ?>', '_blank');
        
        window.location.reload();
    }
    
    
</script>