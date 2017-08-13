<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Absensi', 'Laporan absensi siswa');
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    Pengaturan Grafik
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-2">
                                <select id="ta" class="form-control" onchange="get_kelas(this);">
                                    <?php 
                                    foreach ($TA as $value) {
                                        echo '<option value="'.$value->ID_TA.'">'.$value->NAMA_TA.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Catur Wulan</label>
                            <div class="col-sm-2">
                                <select id="cawu" class="form-control">
                                    <option value="">-- Pilih Cawu --</option>
                                    <?php 
                                    foreach ($CAWU as $value) {
                                        echo '<option value="'.$value->ID_CAWU.'">'.$value->NAMA_CAWU.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jenjang Tingkat</label>
                            <div class="col-sm-4">
                                <select id="tingkat" class="form-control">
                                    <option value="">-- Pilih Jenjang Tingkat --</option>
                                    <?php 
                                    foreach ($TINGKAT as $value) {
                                        echo '<option value="'.$value->ID_TINGK.'">'.$value->KETERANGAN_TINGK.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jenis Kegiatan</label>
                            <div class="col-sm-4">
                                <select id="jenis_kegiatan" class="form-control">
                                    <option value="">-- Pilih Jenis Kegiatan --</option>
                                    <?php 
                                    foreach ($JENIS_ABSENSI as $value) {
                                        echo '<option value="'.$value->ID_MJK.'">'.$value->NAMA_MJK.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Kelas</label>
                            <div class="col-sm-4">
                                <select id="kelas" class="form-control">
                                    <option value="">-- Pilih TA terlebih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tahun</label>
                            <div class="col-sm-4">
                                <select id="tahun" class="form-control">
                                    <option value="">-- Pilih Tahun --</option>
                                    <?php 
                                    for ($tahun = 2016;$tahun <= date('Y');$tahun++) {
                                        echo '<option value="'.$tahun.'">'.$tahun.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Bulan</label>
                            <div class="col-sm-4">
                                <select id="bulan" class="form-control">
                                    <?php 
                                    foreach ($BULAN as $index => $value) {
                                        echo '<option value="'.$index.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Absensi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$single = TRUE;
$this->generate->chart($id, 'Grafik Absensi', $single); 
?>

<script type="text/javascript">

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/absensi/get_data'); ?>';
    var toogle_filter = true;

    $(document).ready(function () {
//        $(".filter-lanjutan").hide();
    });

    function request_chart_<?php echo $id; ?>(type) {
        var ta = $("#ta").val();
        var cawu = $("#cawu").val();
        var tingkat = $("#tingkat").val();
        var jenis_kegiatan = $("#jenis_kegiatan").val();
        var kelas = $("#kelas").val();
        var tahun = $("#tahun").val();
        var bulan = $("#bulan").val();
        var pie_donut = 0;
        var success = function (data) {
            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };
        
        if((type === 'pie') || (type === 'donut')) pie_donut = 1;
        
        create_ajax(url, "pie_donut=" + pie_donut + "&ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&cawu=" + cawu + "&jenis_kegiatan=" + jenis_kegiatan + "&bulan=" + bulan + "&tahun=" + tahun, success);
    }
    
    function export_data() {
        var ta = $("#ta").val();
        var cawu = $("#cawu").val();
        var tingkat = $("#tingkat").val();
        var jenis_kegiatan = $("#jenis_kegiatan").val();
        var kelas = $("#kelas").val();
        var bulan = $("#bulan").val();
        var tahun = $("#tahun").val();
        
        window.open("<?php echo site_url('laporan/absensi/export'); ?>?ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&cawu=" + cawu + "&jenis_kegiatan=" + jenis_kegiatan + "&bulan=" + bulan + "&tahun=" + tahun);
    }
    
    function get_kelas(that) {
        create_splash("Sistem sedang mengambil kelas");
        var ta = $(that).val();
        var success = function(data){
            var kelas = $("#kelas");
            
            kelas.html('<option value="">-- Pilih Kelas --</option>');
            
            $.each(data, function(index, item){
                kelas.append('<option value="' + item.ID_KELAS +'">' + item.NAMA_KELAS + '</option>');
            });
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('laporan/absensi/get_kelas'); ?>', 'ta=' + ta, success);
    }

</script>