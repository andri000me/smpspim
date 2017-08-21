<?php
$title = 'Peserta Siswa Tidak Tamat';
$subtitle = "Daftar semua siswa yang tidak tamat kitab dan quran";
$id_datatables_1 = 'datatable1';
$id_datatables_2 = 'datatable2';

$columns = array(
    'TA',
    'NIS',
    'NAMA SISWA',
    'JK',
    'JENJANG',
    'TINGKAT',
    'NAMA KELAS',
    'WALI KELAS',
    'STATUS',
    'NILAI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables_2, 'Peserta Testing Kitab', $columns);
$this->generate->datatables($id_datatables_1, 'Peserta Testing Quran', $columns);
?>

<script type="text/javascript">
    var table_kitab;
    var table_quran;
    var id_table1 = '<?php echo $id_datatables_1; ?>';
    var id_table2 = '<?php echo $id_datatables_2; ?>';
    var columns = [{"targets": [-1], "orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {
        
    };
    var functionDrawCallback = function (settings, json) {
    };
    var functionAddData = function (e, dt, node, config) {
        
    };

    $(document).ready(function () {
        table_quran = initialize_datatables(id_table1, '<?php echo site_url('pu/peserta_testing_tt/ajax_list_quran'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        table_kitab = initialize_datatables(id_table2, '<?php echo site_url('pu/peserta_testing_tt/ajax_list_kitab'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
        $(".table-datatable1").attr('style', 'margin-top: -50px');
    });
    
    function simpan_nilai(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var ID_TN = $(that).data('id');
        var NILAI_TN = $(that).val();
        var success = function(data){
            $(that).show();
            $(that).next().remove();
            
            if(data.status) {
                $(that).addClass('success');
            } else {
                $(that).addClass('error');
            }
            
//            reload_datatables(table_kitab);
//            reload_datatables(table_quran);
        };
        
        if(isNaN(parseFloat(NILAI_TN))) {
            create_homer_error('Input nilai harus angka');
            $(that).val('');
        } else {
            $(loading_bar).insertAfter(that);
            $(that).hide();
            create_ajax('<?php echo site_url('pu/peserta_testing_tt/update_nilai'); ?>', 'ID_TN=' + ID_TN + '&NILAI_TN=' + NILAI_TN, success);
        }
    }
</script>