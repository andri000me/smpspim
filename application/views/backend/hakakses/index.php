<?php
$this->load->model('hakakses_model', 'hakakses');
$hakakses = $this->hakakses->get_hakakses();

$title = 'Hak Akses';
$subtitle = "Hak akses yang digunakan semua";
$id_datatables = 'datatable1';

$columns = array();
$columns[] = 'MENU';
foreach ($hakakses as $ha) {
    $columns[] = $ha->NAME_HAKAKSES;
}

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

?>
<script type="text/javascript">
    var table;
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
        create_form_input(id_form, id_modal, url_form, null);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('hakakses/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
    });
    
    function change_role(ID_HAKAKSES, ID_MENU) {
        var tag = $("#checked_" + ID_HAKAKSES + "_" + ID_MENU);
        var checked = tag.is(':checked');
        var data = 'ID_HAKAKSES=' + ID_HAKAKSES + "&ID_MENU=" + ID_MENU;
        if(checked)
            data += '&STATUS=1';
        else
            data += '&STATUS=0';
        var success = function(data) {
            if(data.status) 
                create_homer_success('Hak akses berhasil dirubah. Perubahan dapat dilihat setelah Anda login kembali');
            else {
                create_homer_error('Hak akses gagal dirubah');
                tag.attr('checked', !checked);
            }
        };
        create_ajax('<?php echo site_url('hakakses/change_role') ?>', data, success);
    }
</script>