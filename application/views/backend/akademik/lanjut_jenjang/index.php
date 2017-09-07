<?php
$title = 'Lanjut Jenjang';
$subtitle = "Daftar semua siswa lulus yang akan melanjutkan jenjang diatasnya";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NO ABSEN',
    'NAMA SISWA',
    'JK',
    'TINGKAT',
    'NAMA KELAS',
    
    'WALI KELAS',
    
    'JENJANG LANJUT',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
if($this->session->userdata('ID_CAWU_ACTIVE') == 3) {
$this->generate->datatables($id_datatables, $title, $columns);
?>
<!--<i class="fa fa-check-circle-o"></i>
<i class="fa fa-warning"></i>-->
<script type="text/javascript">
    var table;
    var TINGKAT = {};
    <?php 
    foreach ($DEPT as $detail) {
        echo 'TINGKAT['.$detail->ID_TINGK.'] = "'.$detail->NAMA_DEPT.'";'; 
    }
    ?>;
    var ID_TA = '';
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
        
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/lanjut_jenjang/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
        
        $('<div class="row"><div class="col-md-4 col-md-offset-4 text-center"><select class="form-control" onchange="rubah_ta(this);"><option value="">-- Pilih TA berikutnya terlebih dahulu --</option><?php 
    foreach ($TA as $detail) {
        if($detail->ID_TA == $this->session->userdata('ID_TA_ACTIVE')) continue;
        
        echo '<option value="'.$detail->ID_TA.'">'.$detail->NAMA_TA.'</option>';
    }
        ?></select></div></div><hr>').insertBefore("#datatable1_wrapper");
    });
    
    function rubah_ta(that) {
        ID_TA = $(that).val();
    }
    
    function reload_table() {
        reload_datatables(table);
    }
    
    function proses_siswa(that) {
        var loading_bar = '<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" width="31px" class="loading_bar"/>';
        var ID_TINGK = $(that).parent().prev().children().val();
        var ID_AS = $(that).data('siswa');
        var ID_SISWA = $(that).data('id');
        var NAMA_SISWA = $(that).data('nama');
        var success = function(data){
            $(that).show();
            $(that).next().remove();
            
            $(that).removeClass('btn-primary');
            $(that).attr('onclick', 'reload_table();');
            $(that).children().removeClass('fa-arrow-circle-right');
            
            if(data.status) {
                $(that).addClass('btn-success');
                $(that).children().addClass('fa-check');
                $(that).attr('disabled', 'true');
            } else {
                $(that).addClass('btn-danger');
                $(that).children().addClass('fa-refresh');
                $(that).attr('onclick', 'reload_table();');
                
                create_homer_error("Siswa dengan nama " + NAMA_SISWA + " gagal diproses." + data.msg);
            }
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                $(loading_bar).insertAfter(that);
                $(that).hide();
                create_ajax('<?php echo site_url('akademik/lanjut_jenjang/proses_siswa'); ?>', 'ID_AS=' + ID_AS + "&ID_SISWA=" + ID_SISWA + "&ID_TA=" + ID_TA + "&ID_TINGK=" + ID_TINGK, success);
            }
        };
        
        if(ID_TA === '') {
            create_homer_error('Silahkan pilih tahun ajaran berikutnya terlebih dahulu.');
            
            reload_datatables(table);
        } else {
            create_swal_option('Apakah Anda yakin melanjutkan?', 'Anda akan memproses dengan nama ' + NAMA_SISWA + ' lanjut ke jenjang ' + TINGKAT[ID_TINGK] + '. Proses ini tidak dapat diulang.', action);
        }
    }
</script>

<?php } else { ?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel hbggreen">
                <div class="panel-body text-center">
                    <h1>MENU HANYA AKTIF HANYA PADA CAWU 3</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>