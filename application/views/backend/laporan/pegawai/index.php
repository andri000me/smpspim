<?php
$id = 'grafik';
$this->generate->generate_panel_content('Laporan Pegawai', 'Laporan master data pegawai');

$options = array(
    'GELAR_AWAL_PEG#Nama Gelar#Jumlah (orang)#Gelar' => 'Gelar Awal',
    'GELAR_AKHIR_PEG#Nama Gelar#Jumlah(orang)#Gelar' => 'Gelar Akhir',
    'NAMA_SUKU#Nama Suku#Jumlah (orang)#Suku' => 'Suku',
    'NAMA_AGAMA#Nama Agama#Jumlah (orang)#Agama' => 'Agama',
    'NAMA_JK#Nama Jenis Kelamin#Jumlah (orang)#Jenis Kelamin' => 'Jenis Kelamin',
    'MENIKAH_PEG#Status Pernikahan#Jumlah (orang)#Pernikahan' => 'Status Menikah',
    'NAMA_KEC#Nama Kecamatan#Jumlah (orang)#Kecamatan' => 'Kecamatan',
    'NAMA_KAB#Nama Kabupaten#Jumlah (orang)#Kabupaten' => 'Kabupaten',
    'NAMA_PROV#Nama Provinsi#Jumlah (orang)#Provinsi' => 'Provinsi',
    'AKTIF_PEG#Keaktifan Pegawai#Jumlah (orang)#Keaktifan' => 'Status Aktif',
    'GURU_PEG#Status Guru#Jumlah (orang)#Guru' => 'Status Guru',
    'TANGGAL_LAHIR_PEG#Umur#Jumlah (orang)#Umur' => 'Umur',
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
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Status Keaktifan Pegawai</label>
                            <div class="col-sm-3">
                                <select id="keaktifan" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">&nbsp;</label>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="export_data();"><i class="fa fa-save"></i>&nbsp;&nbsp;Unduh Data Pegawai</button>
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
$this->generate->chart($id, 'Grafik Pegawai', $single); 
?>

<script type="text/javascript">

    var id = '<?php echo $id; ?>';
    var single = <?php echo ($single ? 'true' : 'false'); ?>;
    var chart_<?php echo $id; ?> = null;
    var panel = 'panel-' + id;
    var url = '<?php echo site_url('laporan/pegawai/get_data'); ?>';

    $(document).ready(function () {
        
    });

    function request_chart_<?php echo $id; ?>(type) {
        var kelompok = $("#kelompok").val();
        var keaktifan = $("#keaktifan").val();
        var pie_donut = 0;
        var success = function (data) {
            chart_<?php echo $id; ?> = create_chart(id, data, single, type, single);
        };
        
        if((type === 'pie') || (type === 'donut')) pie_donut = 1;
        
        if(kelompok !== '')
            create_ajax(url, "kelompok=" + kelompok + "&pie_donut=" + pie_donut + "&keaktifan=" + keaktifan, success);
        else {
            $("#" + id).html(" ");
            
            create_homer_info("Pilih kelompok terlebih dahulu");
        }
    }
    
    function export_data() {
        var keaktifan = $("#keaktifan").val();
        
        window.open('<?php echo site_url('laporan/pegawai/export'); ?>/' + keaktifan);
    }

</script>