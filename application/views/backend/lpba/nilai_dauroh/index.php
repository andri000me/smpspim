<?php
$title = 'Nilai Dauroh';
$subtitle = "Daftar semua nilai dauroh";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NO ABSEN',
    'NAMA SISWA',
    'JK',
    'TINGKAT',
    'NAMA KELAS',
    'WALI KELAS',
    'NILAI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
//if($this->session->userdata('ID_CAWU_ACTIVE') == 3) {
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
        
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('lpba/nilai_dauroh/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
    });
    
    function simpan_nilai(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var SISWA_LN = $(that).data('siswa');
        var NILAI_LN = $(that).val();
        var success = function(data){
            $(that).show();
            $(that).next().remove();
            
            if(data.status) {
                $(that).addClass('success');
            } else {
                $(that).addClass('error');
            }
        };
        
        if(NILAI_LN === '') {
            create_homer_error('Silahkan pilih nilai yang ada.');
            
            reload_datatables(table);
        } else {
            $(loading_bar).insertAfter(that);
            $(that).hide();
            create_ajax('<?php echo site_url('lpba/nilai_dauroh/simpan_nilai'); ?>', 'SISWA_LN=' + SISWA_LN + "&NILAI_LN=" + NILAI_LN, success);
        }
    }
</script>

<?php // } else { ?>
<!--<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel hbggreen">
                <div class="panel-body text-center">
                    <h1>MENU HANYA AKTIF HANYA PADA CAWU 3</h1>
                </div>
            </div>
        </div>
    </div>
</div>-->
<?php // } ?>