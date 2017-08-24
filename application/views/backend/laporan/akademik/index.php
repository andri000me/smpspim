<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Akademik', 'Laporan master data akademik');

$options = array(
    'NAMA_TA#Nama Tahun Ajaran#Jumlah (siswa)#Tahun Ajaran' => 'Tahun Ajaran',
    'mt.KETERANGAN_TINGK#Nama Jenjang Tingkat#Jumlah (siswa)#Jenjang Tingkat' => 'Jenjang Tingkat',
    'NAMA_KELAS#Nama Kelas#Jumlah (siswa)#Kelas' => 'Kelas',
    'KONVERSI_AS#Status Konversi Siswa#Jumlah (siswa)#Konversi' => 'Status Konversi',
    'AKTIF_AS#Status Keaktifan Siswa#Jumlah (siswa)#Keaktifan' => 'Status Keaktifan',
    'ANGKATAN_SISWA#Tahun Masuk Siswa#Jumlah (siswa)#Tahun Masuk' => 'Tahun Masuk',
    'TANGGAL_LAHIR_SISWA#Umur#Jumlah (siswa)#Umur' => 'Umur',
    'NAMA_SUKU#Nama Suku#Jumlah (siswa)#Suku' => 'Suku',
    'NAMA_AGAMA#Nama Agama#Jumlah (siswa)#Agama' => 'Agama',
    'NAMA_KONDISI#Nama Kondisi#Jumlah (siswa)#Kondisi' => 'Kondisi',
    'NAMA_JK#Nama Jenis Kelamin#Jumlah (siswa)#Jenis Kelamin' => 'Jenis Kelamin',
    'NAMA_WARGA#Status Kewarganegaraan#Jumlah (siswa)#Kewarganegaraan' => 'Kewarganegaraan',
    'NAMA_DARAH#Golongan Darah#Jumlah (siswa)#Golongan Darah' => 'Golongan Darah',
    'NAMA_KEC#Nama Kecamatan#Jumlah (siswa)#Kecamatan' => 'Kecamatan',
    'NAMA_KAB#Nama Kabupaten#Jumlah (siswa)#Kecamatan' => 'Kabupaten',
    'NAMA_PROV#Nama Provinsi#Jumlah (siswa)#Provinsi' => 'Provinsi',
    'NAMA_TEMTING#Tempat Tinggal#Jumlah (siswa)#Tempat Tinggal' => 'Tempat Tinggal',
    'NAMA_PONDOK_MPS#Nama Pondok Siswa#Jumlah (siswa)#Pondok Siswa' => 'Pondok Siswa',
    'mdt.KETERANGAN_TINGK#Jenjang Masuk Siswa#Jumlah (siswa)#Jenjang Masuk Siswa' => 'Jenjang Masuk Siswa',
    'moha.NAMA_SO#Status Hidup Ayah#Jumlah (siswa)#Status Hidup Ayah' => 'Status Hidup Siswa',
    'mjpa.NAMA_JP#Jenjang Pendidikan Ayah#Jumlah (siswa)#Jenjang Pendidikan Ayah' => 'Jenjang Pendidikan Ayah',
    'mpka.NAMA_JENPEK#Jenis Pekerjaan Ayah#Jumlah (siswa)#Jenis Pekerjaan Ayah' => 'Jenis Pekerjaan Ayah',
    'mohi.NAMA_SO#Status Hidup Ibu#Jumlah (siswa)#Status Hidup Ibu' => 'Status Hidup Siswa',
    'mjpi.NAMA_JP#Jenjang Pendidikan Ibu#Jumlah (siswa)#Jenjang Pendidikan Ibu' => 'Jenjang Pendidikan Ibu',
    'mpki.NAMA_JENPEK#Jenis Pekerjaan Ibu#Jumlah (siswa)#Jenis Pekerjaan Ibu' => 'Jenis Pekerjaan Ibu',
    'NAMA_HASIL#Penghasilan Orang Tua#Jumlah (siswa)#Penghasilan Orang Tua' => 'Penghasilan Orang Tua',
    'NAMA_MUTASI#Jenis Mutasi Siswa#Jumlah (siswa)#Mutasi Siswa' => 'Status Mutasi Siswa',
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
                        <div class="form-group filter-lanjutan">
                            <label class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-2">
                                <select id="ta" class="form-control" onchange="get_kelas(this);">
                                    <option value="">-- Pilih TA --</option>
                                    <?php 
                                    foreach ($TA as $value) {
                                        echo '<option value="'.$value->ID_TA.'">'.$value->NAMA_TA.'</option>';
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
                            <label class="col-sm-3 control-label">Jenis Kelamin</label>
                            <div class="col-sm-4">
                                <select id="jk" class="form-control">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Akademik</button>
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
$this->generate->chart($id, 'Grafik Akademik', $single); 
?>

<script type="text/javascript">

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/akademik/get_data'); ?>';
    var toogle_filter = true;

    $(document).ready(function () {
        $(".filter-lanjutan").hide();
    });

    function request_chart_<?php echo $id; ?>(type) {
        var kelompok = $("#kelompok").val();
        var ta = $("#ta").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var jk = $("#jk").val();
        var pie_donut = 0;
        var success = function (data) {
            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };
        
        if((type === 'pie') || (type === 'donut')) pie_donut = 1;
        
        if(kelompok !== '')
            create_ajax(url, "kelompok=" + kelompok + "&pie_donut=" + pie_donut + "&ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + '&jk=' + jk, success);
        else {
            $("#" + id).html(" ");
            
            create_homer_info("Pilih kelompok terlebih dahulu");
        }
    }
    
    function export_data() {
        var ta = $("#ta").val();
        var tingkat = $("#tingkat").val();
        var kelas = $("#kelas").val();
        var jk = $("#jk").val();
        
        window.open("<?php echo site_url('laporan/akademik/export'); ?>?ta=" + ta + "&tingkat=" + tingkat + "&kelas=" + kelas + "&jk=" + jk);
    }
    
    function filter_lanjutan() {
        toogle_filter = !toogle_filter;
        
        if(toogle_filter) $(".filter-lanjutan").slideUp();
        else $(".filter-lanjutan").slideDown();
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
        
        create_ajax('<?php echo site_url('laporan/akademik/get_kelas'); ?>', 'ta=' + ta, success);
    }

</script>