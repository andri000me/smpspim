<?php
$this->generate->generate_panel_content("Jadwal", "Daftar semua jadwal");
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-6">
            <div class="hpanel collapsed">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
                    Unduh XML Timetables
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Jenjang</label>
                                <div class="col-md-8">
                                    <select id="JENJANG_DOWNLOAD" class="form-control">
                                        <option value="ALL">SEMUA JENJANG</option>
                                        <?php
                                        foreach ($JENJANG as $detail) {
                                            echo '<option value="' . $detail->ID_DEPT . '">' . $detail->NAMA_DEPT . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Jenis Kelamin</label>
                                <div class="col-md-6">
                                    <select id="JK_DOWNLOAD" class="form-control">
                                        <?php
                                        foreach ($JK as $detail) {
                                            echo '<option value="' . $detail->ID_JK . '">' . $detail->NAMA_JK . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">&nbsp;</label>
                                <div class="col-md-4">
                                    <div class="col-md-3 text-center">
                                        <button type="button" class="btn btn-info" onclick="unduh_xml();"><i class="fa fa-upload"></i>&nbsp;&nbsp;Unduh XML Timetables</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="hpanel collapsed">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
                    Unggah XML Timetables
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" method="post" enctype="multipart/form-data" id="form-xml" onsubmit="return unggah_xml();">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Pilih File XML</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="file" name="file_xml" id="file_xml">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Jenjang</label>
                                    <div class="col-md-9">
                                        <select id="JENJANG_UPLOAD" class="form-control">
                                            <option value="ALL">SEMUA JENJANG</option>
                                            <?php
                                            foreach ($JENJANG as $detail) {
                                                echo '<option value="' . $detail->ID_DEPT . '">' . $detail->NAMA_DEPT . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
<!--                                <div class="form-group">
                                    <label class="col-md-3 control-label">Jenis Kelamin</label>
                                    <div class="col-md-3">
                                        <select id="JK_UPLOAD" class="form-control">
                                            <?php
                                            foreach ($JK as $detail) {
                                                echo '<option value="' . $detail->ID_JK . '">' . $detail->NAMA_JK . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>-->
                                <div class="form-group">
                                    <label class="col-md-3 control-label">&nbsp;</label>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-download"></i>&nbsp;&nbsp;Unggah XML Timetables</button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$title = 'Tabel Jadwal';
$id_datatables = 'datatable1';

$columns = array(
    'KELAS',
    'MAPEL',
    'NIP',
    
    'NAMA GURU',
    
    'HARI',
    'JAM',
);

$this->generate->datatables($id_datatables, $title, $columns);
?>

<script type="text/javascript">
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1],"orderable": false}];
    var orders = [[ 0, "ASC" ]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_homer_error("Anda tidak memiliki hak akses untuk menambah guru.");
    };

    $(document).ready(function () {
        $(".table-datatable1").attr('style', 'margin-top: -60px;');
        
        table = initialize_datatables(id_table, '<?php echo site_url('akademik/jadwal/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-copy, .buttons-pdf, .buttons-add").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" onclick="cetak_kehadiran_guru();">Cetak Absensi</a></li><li><a href="#" onclick="cetak_jadwal_kelas();">Jadwal Kelas</a></li><li><a href="#" onclick="cetak_jurnal_kelas();">Jurnal Kelas</a></li><li><a href="#" onclick="cetak_jadwal_guru();">Jadwal Guru</a></li><li><a href="#" onclick="cetak_kelas_guru();">Jumlah Kelas Guru</a></li><li><a href="#" onclick="cetak_mapel_guru();">Jumlah Mapel Guru</a></li></ul></div>').insertAfter('.buttons-reload');
        
        create_homer_info("Untuk dapat mencetak, silahkan matikan download manager terlebih dahulu.");
    });
    
    function cetak_absensi() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_absensi'); ?>', '_blank');
    }
    
    function cetak_jurnal_kelas() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_jurnal_kelas'); ?>', '_blank');
    }
    
    function cetak_kehadiran_guru() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_kehadiran_guru'); ?>', '_blank');
    }
    
    function cetak_jadwal_kelas() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_jadwal_kelas'); ?>', '_blank');
    }
    
    function cetak_jadwal_guru() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_jadwal_guru'); ?>', '_blank');
    }
    
    function cetak_kelas_guru() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_kelas_guru'); ?>', '_blank');
    }
    
    function cetak_mapel_guru() {
        window.open('<?php echo site_url('akademik/jadwal/cetak_mapel_guru'); ?>', '_blank');
    }
    
    function unduh_xml() {
        var jenjang = $('#JENJANG_DOWNLOAD').val();
        var jk = $('#JK_DOWNLOAD').val();

        window.open('<?php echo site_url('akademik/jadwal/unduh_xml'); ?>/' + jenjang + '/' + jk);
    }

    function unggah_xml() {
        var data = {
            'JENJANG_UPLOAD': $("#JENJANG_UPLOAD").val(),
//            'JK_UPLOAD': $("#JK_UPLOAD").val(),
        };
        var success = function (data, status) {
            remove_splash();
            
            if (data.status) {
                create_swal_success('', 'Data berhasil disimpan.');
            } else {
                create_swal_error('', 'Data ' + data.msg + '.');
            }
            
            reload_window();
        };
        var action = function(isConfirm) {
            if(isConfirm) {
                create_splash("Sistem sedang menyimpan data.");
                
                create_ajax_file('<?php echo site_url('akademik/jadwal/unggah_xml'); ?>', 'file_xml', data, success);
            }
        };

        create_swal_option('Apakah Anda yakin?', "Menggunggah file akan menyebabkan data diganti dengan data terbaru.", action);

        return false;
    }
</script>