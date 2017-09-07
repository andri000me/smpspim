<?php
$title = 'Nilai Testing Quran';
$subtitle = "Daftar semua siswa yang akan mengikuti testing quran";
$id_datatables_2 = 'datatable2';

$columns = array(
    'NIS',
    'NAMA SISWA',
    'JK',
    'JENJANG',
    'TINGKAT',
    'NAMA KELAS',
    'WALI KELAS',
    'NILAI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables_2, 'Peserta Testing Quran', $columns);
?>

<script type="text/javascript">
    var table;
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
        table = initialize_datatables(id_table2, '<?php echo site_url('pu/quran/ajax_list_quran'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });
    
    function simpan_nilai(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var SISWA_TN = $(that).data('siswa');
        var NILAI_TN = $(that).val();
        var success = function(data){
            $(that).show();
            $(that).next().remove();
            
            if(data.status) {
                $(that).addClass('success');
            } else {
                $(that).addClass('error');
            }
        };
        
        if(isNaN(parseFloat(NILAI_TN))) {
            create_homer_error('Input nilai harus angka');
            $(that).val('');
        } else {
            $(loading_bar).insertAfter(that);
            $(that).hide();
            create_ajax('<?php echo site_url('pu/quran/simpan_nilai'); ?>', 'SISWA_TN=' + SISWA_TN + "&NILAI_TN=" + NILAI_TN, success);
        }
    }
</script>