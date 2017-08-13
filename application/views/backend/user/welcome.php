

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12 text-center m-t-md" style="">
            <h2>
                Selamat Datang <br>di Admin Dashboard <?php echo $this->session->userdata('NAME_HAKAKSES'); ?>
            </h2>
            <br>
            <img src="<?php echo base_url('files/aplikasi/logo_800.png'); ?>" alt="logo" width="200px" />
            <br>     
            <h1 class="text-uppercase">
                <?php echo $this->pengaturan->getNamaLembaga(); ?>
            </h1>
            <h3>
                TAHUN AJARAN: <?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?>
            </h3>
            <h3>
                TAHUN AJARAN PSB : <?php echo $this->session->userdata('NAMA_PSB_ACTIVE'); ?>
            </h3>
        </div>
    </div>

    <?php
    if (isset($STATUS_PSB)) {
        ?>
        <div class="hpanel stats">
            <div class="panel-body h-200">
                <div class="m-t-xl">
                    <div class="row">
                        <div class="col-md-12" >
                            <h1 class="text-success">STATUS PSB</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-md-offset-1" >
                            <div class="radio radio-info">
                                <input type="radio" id="STATUS_PSB_V_0" name="STATUS_PSB_V" value="1" onclick="change_status(0);" <?php echo $STATUS_PSB ? '' : 'checked'; ?> >
                                <label><h3> BUKA </h3></label>
                            </div>
                            <div class="radio radio-info">
                                <input type="radio" id="STATUS_PSB_V_1" name="STATUS_PSB_V" value="0" onclick="change_status(1);" <?php echo $STATUS_PSB ? 'checked' : ''; ?> >
                                <label><h3> TUTUP </h3></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            function change_status(status) {
                var msg = '';
                var action = function (isConfirm) {
                    if (isConfirm) {
                        var success = function (data) {
                            if (data.status) {
                                create_homer_success(data.msg);
                            } else {
                                create_homer_error(data.msg);
                                back_choose(status);
                            }
                        }

                        create_ajax('<?php echo site_url('psb/psb_validasi/change_status'); ?>', 'STATUS=' + status, success);
                    } else {
                        back_choose(status);
                    }
                };
                
                if(status) msg = 'Penutupan status PSB akan menutup penambahan data calon siswa dan memberikan akses kepada Panitia Ujian untuk membuat denah ujian.';
                else msg = 'Pembukaan status PSB akan menutup akses Panitia Ujian dalam mengelola denah Ujian Masuk';

                create_swal_option('Apakah Anda yakin merubah status PSB?', msg, action);
            }

            function back_choose(status) {
                if (status)
                    $("#STATUS_PSB_V_0").prop('checked', true);
                else
                    $("#STATUS_PSB_V_1").prop('checked', true);
            }

        </script>
    <?php } ?>

</div>
