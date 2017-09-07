<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Kedisiplinan', 'Laporan Kedisiplinan Siswa');

$options = array(
    'PELANGGARAN_KS#Kode Pelanggaran#Jumlah (siswa)#Kode Pelanggaran' => 'Kode Pelanggaran',
    'SUMBER_KS#Sumber Informasi#Jumlah (siswa)#Sumber Informasi' => 'Sumber Informasi',
    'LARI_KSH#Frekuensi Lari#Jumlah (siswa)#Frekuensi Lari' => 'Frekuensi Lari',
    'POIN_KSH#Jumlah Poin#Jumlah (siswa)#Poin' => 'Poin',
);
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
                            <label class="col-sm-3 control-label">Kelompokan berdasarkan</label>
                            <div class="col-sm-4">
                                <select id="kelompok" class="form-control">
                                    <option value="">-- Pilih Kelompok --</option>
                                    <?php 
                                    foreach ($options as $index => $value) {
                                        echo '<option value="'.$index.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success" onclick="filter_lanjutan();"><i class="fa fa-filter"></i>&nbsp;&nbsp;Filter Lanjutan</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-2">
                                <select id="ta" class="form-control" onchange="get_kelas();">
                                    <?php 
                                    foreach ($TA as $value) {
                                        echo '<option value="'.$value->ID_TA.'" '.($value->ID_TA == $this->session->userdata("ID_TA_ACTIVE") ? 'selected' : '').'>'.$value->NAMA_TA.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
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
                        <div class="form-group filter-lanjutan">
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
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Kelas</label>
                            <div class="col-sm-4">
                                <select id="kelas" class="form-control">
                                    <option value="">-- Pilih TA terlebih dahulu --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group filter-lanjutan">
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
                        <div class="form-group filter-lanjutan">
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
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="download_csv();"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;Unduh Data Grafik</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Kedisiplinan</button>
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
$this->generate->chart($id, 'Grafik Kedisiplinan', $single); 
?>

<script type="text/javascript">

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/komdis/get_data'); ?>';
    var toogle_filter = true;
    
    var data_response = null;
    var xls_content = "data:application/vnd.ms-excel;charset=utf-8,";

    $(document).ready(function () {
        $(".filter-lanjutan").hide();
        
        get_kelas();
    });

    function download_csv() {
        if (data_response === null) {
            create_homer_error('Silahkan tampilkan grafik terlebih dahulu sebelum mengunduh data');
        } else {
            xls_content += '<table>';
            xls_content += '<tr><td>Kelompok</td><td>Jumlah Siswa</td></tr>';
            $.each(data_response.data.x_label, function (key, value) {
                xls_content += '<tr><td>' + value + '</td><td>' + data_response.data.data1[key] + '<td></tr>';
            });
            xls_content += '</table>';

            var encoded_uri = encodeURI(xls_content);
//            window.open(encoded_uri, '_blank');

            var link = document.createElement("a");
            link.href = encoded_uri;
            
            link.style = "visibility:hidden";
            link.download = "data_komdis.xls";
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    function request_chart_<?php echo $id; ?>(type) {
        var kelompok = $("#kelompok").val();
        var ta = $("#ta").val();
        var cawu = $("#cawu").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var tahun = $("#tahun").val();
        var bulan = $("#bulan").val();
        var pie_donut = 0;
        var success = function (data) {
            data_response = data;
            
            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };
        
        if((type === 'pie') || (type === 'donut')) pie_donut = 1;
        
        if(kelompok !== '')
            create_ajax(url, "kelompok=" + kelompok + "&pie_donut=" + pie_donut + "&ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&cawu=" + cawu + "&bulan=" + bulan + "&tahun=" + tahun, success);
        else {
            $("#" + id).html(" ");
            
            create_homer_info("Pilih kelompok terlebih dahulu");
        }
    }
    
    function export_data() {
        var kelompok = $("#kelompok").val();
        var ta = $("#ta").val();
        var cawu = $("#cawu").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var bulan = $("#bulan").val();
        var tahun = $("#tahun").val();
        
        var req = "&ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&cawu=" + cawu + "&bulan=" + bulan + "&tahun=" + tahun;
        
        window.open("<?php echo site_url('laporan/komdis/export'); ?>?detail=detail" + req);
        window.open("<?php echo site_url('laporan/komdis/export'); ?>?detail=header" + req);
    }
    
    function get_kelas() {
        create_splash("Sistem sedang mengambil kelas");
        var ta = $('#ta').val();
        var success = function(data){
            var kelas = $("#kelas");
            
            kelas.html('<option value="">-- Pilih Kelas --</option>');
            
            $.each(data, function(index, item){
                kelas.append('<option value="' + item.ID_KELAS +'">' + item.NAMA_KELAS + '</option>');
            });
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('laporan/komdis/get_kelas'); ?>', 'ta=' + ta, success);
    }
    
    function filter_lanjutan() {
        toogle_filter = !toogle_filter;
        
        if(toogle_filter) $(".filter-lanjutan").slideUp();
        else $(".filter-lanjutan").slideDown();
    }

</script>