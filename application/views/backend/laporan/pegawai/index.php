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
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-primary" onclick="request_chart_<?php echo $id; ?>('bar');"><i class="fa fa-pie-chart"></i>&nbsp;&nbsp;Proses Grafik</button>
                                <button type="button" class="btn btn-info" onclick="download_csv();"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;Unduh Data Grafik</button>
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
    
    var data_response = null;
    var xls_content = "data:application/vnd.ms-excel;charset=utf-8,";

    $(document).ready(function () {
        
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
            link.download = "data_pegawai.xls";
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    function request_chart_<?php echo $id; ?>(type) {
        var kelompok = $("#kelompok").val();
        var keaktifan = $("#keaktifan").val();
        var pie_donut = 0;
        var success = function (data) {
            data_response = data;
            
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