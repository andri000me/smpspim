<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                Kartu Pelajar
            </h2>
            <small>Data dan foto kartu pelajar</small>
        </div>
    </div>
</div>
<div class="content animate-panel table-datatable1">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    Kartu Pelajar: <?php echo $NIS_SISWA.' - '.$NAMA_SISWA; ?>
                </div>
                <div class="panel-body">
                <form class="form-horizontal" id="foto" onsubmit="return simpan();">
                <input type="hidden" name="ID_SISWA" value="<?php echo $ID_SISWA; ?>" />
                        <div class="row">
                            <div class="col-md-12">
                                <?php $this->generate->input_photo('FOTO_SISWA'); ?>
                                <?php $this->generate->content_webcam(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>
                                <button type="button" class="btn btn-primary" onclick="cetak();"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="<?php 
                                if (file_exists('files/siswa/' . $NIS_SISWA . '.jpg')) {
                                    echo base_url('files/siswa/'. $NIS_SISWA . '.jpg');
                                } elseif (file_exists('files/siswa/' . $ID_SISWA . '.png') || $FOTO_SISWA != NULL) {
                                    echo base_url('files/siswa/'. $ID_SISWA . '.png');
                                } else {
                                    echo base_url('files/no_image.jpg');
                                }
                                    ?>" alt="Foto siswa" width="300px"/>
                            </div>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ID_SISWA = <?php echo $ID_SISWA; ?>;
    
    function simpan() {
        create_splash("Sedang menyimpan foto siswa.");
        
        if ($("#from_upload").val() == 0) {
            simpan_photobooth();
        } else {
            simpan_file();
        }
        
        return false;
    }
    
    function simpan_photobooth() {
        var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
        var success = function(data) {
            response_simpan(data.status, data.msg);
            
            reaload_page();
        };
        
        create_form_ajax('<?php echo site_url('akademik/siswa/save_take_photo'); ?>', 'foto', success, message);
    }
    
    function simpan_file() {
        var data = {
            'ID_SISWA': ID_SISWA
        };
        var success = function (data, status) {
            response_simpan(data.status, data.msg);
            
            reaload_page();
        };
        
        create_ajax_file('<?php echo site_url('akademik/siswa/save_photo'); ?>', 'UPLOAD_FOTO_SISWA', data, success);
    }
    
    function response_simpan(status, msg) {       
        if (status) {
            create_homer_success('Foto siswa berhasil disimpan. Halaman akan dimuat ulang.');
        } else {
            create_homer_error('Foto siswa gagal disimpan. Halaman akan dimuat ulang. #ERROR MSG: ' + msg);
        }
    }

    function reaload_page() {
        setTimeout(function () {
            window.location.reload();
        }, 1500);
    }
    
    function cetak() {
        window.open('<?php echo site_url('akademik/siswa/cetak_kartu'); ?>/' + ID_SISWA);
    }
    
</script>