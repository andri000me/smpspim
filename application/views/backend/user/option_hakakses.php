
<!-- Vendor styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fontawesome/css/font-awesome.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/animate.css/animate.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/css/bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.css" />
<link href="<?php echo base_url(); ?>assets/vendor/ladda/dist/ladda-themeless.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/toastr/build/toastr.min.css" />

<!-- App styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/helper.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.css">

</head>
<body class="fixed-navbar fixed-sidebar">

    <!-- Simple splash screen-->
    <div class="splash"> <div class="color-line"></div><div class="splash-title"><h1><?php echo $this->pengaturan->getNamaApp(); ?></h1><p><?php echo $this->pengaturan->getNamaLembagaSingk(); ?></p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
    <!--[if lt IE 7]>
    <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div class="color-line"></div>

    <!-- Main Wrapper -->
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center">
                <h2>Selamat Datang</h2>
                <h3><?php echo $this->session->userdata('FULLNAME_USER'); ?></h3>
                <h4>TAHUN AJARAN <?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?> | PSB <?php echo $this->session->userdata('NAMA_PSB_ACTIVE'); ?></h4>
                <div class="row" style="height: 40px;">
                    <div class="col-md-2 col-md-offset-5" onmouseover="mouse_position(true)" onmouseout="mouse_position(false)">
                        <h4 class="text-center" id="text-cawu"><?php echo $this->session->userdata('NAMA_CAWU_ACTIVE'); ?></h4>
                        <select class="form-control" id="id-cawu" style="width: 100px;" onchange="change_cawu(this)"><option value="1" <?php echo $this->session->userdata('ID_CAWU_ACTIVE') == 1 ? 'selected' : ''; ?>>CAWU 1</option><option value="2" <?php echo $this->session->userdata('ID_CAWU_ACTIVE') == 2 ? 'selected' : ''; ?>>CAWU 2</option><option value="3" <?php echo $this->session->userdata('ID_CAWU_ACTIVE') == 3 ? 'selected' : ''; ?>>CAWU 3</option></select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <a href="#" class="pull-right" title="Klik untuk keluar dari Aplikasi" onclick="log_out();">
                    <i class="pe-7s-power" style="font-size: 25px;"></i>
                </a>
                <a href="#" class="pull-right" title="Klik untuk mengubah password" onclick="change_password();">
                    <i class="pe-7s-key" style="font-size: 25px; margin-right: 20px"></i>
                </a>
                <a href="<?php echo site_url('pencarian'); ?>" class="pull-right" title="Klik untuk mencari data siswa" target="_blank">
                    <i class="pe-7s-search" style="font-size: 25px; margin-right: 20px"></i>
                </a>
            </div>
        </div>
        <hr>
        <?php
        $i = 0;
        foreach ($data as $detail) {
            if ($i == 0)
                echo '<div class="row">';

            echo '<div class="col-md-3"><div class="hpanel hbg' . $detail->COLOR_HAKAKSES . '" onclick="chooseHakAkses(\'' . $detail->ID_HAKAKSES . '\');" style="cursor: pointer">
                    <div class="panel-body">
                        <div class="text-center">
                            <h3>MODUL</h3>
                            <h1>' . $detail->NAME_HAKAKSES . '</h1>
                            <small>
                                Klik untuk masuk
                            </small>
                        </div>
                    </div>
                </div></div>';

            if ($i == 3) {
                $i = 0;
                echo '</div>';
            } else {
                $i++;
            }
        }
        if ($i != 3)
            echo '</div>';
        ?>
    </div>

    <?php
    $id_modal = "modal-data";
    $title_form = "Ubah Password";
    $id_form = "form-data";

    $this->generate->form_modal($id_modal, $title_form, $id_form, '');
    ?>

    <!-- Vendor scripts -->
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/iCheck/icheck.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/sparkline/index.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/ladda/dist/spin.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/ladda/dist/ladda.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/ladda/dist/ladda.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/toastr/build/toastr.min.js"></script>

    <!-- App scripts -->
    <script src="<?php echo base_url(); ?>assets/scripts/homer.js"></script>
    <script src="<?php echo base_url(); ?>assets/scripts/apps.js"></script>

    <script type="text/javascript">
                    var id_modal = '<?php echo $id_modal; ?>';
                    var id_form = '<?php echo $id_form; ?>';

                    $(document).ready(function () {
                        $("#id-cawu").hide();
                    });

                    function mouse_position(ontop) {
                        if (ontop) {
                            $("#text-cawu").hide();
                            $("#id-cawu").show();
                        } else {
                            $("#text-cawu").show();
                            $("#id-cawu").hide();
                        }
                    }

                    function change_cawu(that) {
                        create_splash("Mohon tunggu sebentar, sistem sedang merubah cawu.");
                        var success = function (data) {
                            if (data.status) {
                                create_swal_success('Berhasil merubah cawu. Halaman akan dimuat ulang');
                                reload_window();
                            }
                        };

                        create_ajax('<?php echo site_url('login/change_cawu'); ?>', 'ID_CAWU=' + $(that).val(), success);

                    }

                    function log_out() {
                        create_splash("Mohon tunggu sebentar, sistem sedang memproses permintaan.");

                        var action = function (isConfirm) {
                            if (isConfirm) {
                                var success = function (data) {
                                    if (data.status) {
                                        window.location = data.link;
                                    }
                                };

                                create_ajax('<?php echo site_url('login/log_out'); ?>', '', success);
                            }
                        };

                        create_swal_option('Apakah Anda yakin untuk keluar?', '', action);
                    }

                    function change_password() {
                        create_form_input(id_form, id_modal, '<?php echo site_url('login/change_password'); ?>', '', null);
                        $(".modal-title").html('Update Password');
                    }

                    function save_form(id_form) {
                        create_ladda("ladda-button-save");

                        var success = function (data) {
                            if (data.status) {
                                create_homer_success(data.msg);

                                $("#" + id_modal).modal('hide');
                            } else {
                                create_homer_error(data.msg);
                            }

                            remove_ladda();
                        };

                        create_ajax('<?php echo site_url('login/proccess_change_password'); ?>', $('#' + id_form).serialize(), success);

                        return false;
                    }

                    function chooseHakAkses(ID_HAKAKSES) {
                        create_splash("Mohon tunggu sebentar, sistem sedang mengatur Hak Akses Anda.");
                        var success = function (data) {
                            remove_splash();

                            if (data.status) //if success close modal and reload ajax table
                            {
                                create_swal_success('', data.msg);

                                setTimeout(function () {
                                    window.location = data.link;
                                }, 1500);
                            } else {
                                create_homer_error(data.msg);
                            }
                        };
                        create_ajax('<?php echo site_url('login/chooseHakAkses') ?>', 'ID_HAKAKSES=' + ID_HAKAKSES, success);

                        return false;
                    }

    </script>

</body>
</html>