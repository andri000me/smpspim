
<!-- Vendor styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fontawesome/css/font-awesome.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/animate.css/animate.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/css/bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.css" />
<link href="<?php echo base_url(); ?>assets/vendor/ladda/dist/ladda-themeless.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/toastr/build/toastr.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/pace/pace.min.css" />

<!-- App styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/helper.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/custom.css">

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
            <div class="col-md-10 col-md-offset-1 text-center">
                <h2>PANEL DETAIL SISWA</h2>
            </div>
            <div class="col-md-1">
                <a href="#" class="pull-right" title="Klik untuk kembali ke menu modul" onclick="window.close();">
                    <i class="pe-7s-close" style="font-size: 45px;"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default btn-menu"><i class="fa fa-barcode"></i></button>
                    </div>
                    <input class="form-control" type="text" id="nis" placeholder="SCAN BARCODE ANDA" onchange="nis_changed(this);">
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="keterangan" style="margin-top: 60px">
        <div class="col-md-12 text-center">
            <h1 class="font-light">SCAN KARTU PELAJAR UNTUK MELIHAT DETAIL</h1>
            <h1 class="font-light">SCAN KEMBALI KARTU PELAJAR UNTUK MENGHAPUS DETAIL</h1>
            <h2 class="font-extra-bold">PASTIKAN DATA TELAH DIHAPUS SEBELUM MENINGGALKAN LAYAR</h2>
            <h2 class="font-light">GUNAKAN SCROLL UNTUK MELIHAT SELURUH DETAIL</h2>
        </div>
    </div>
    <iframe id="detail-nis" frameborder="1"></iframe>

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
    <script src="<?php echo base_url(); ?>assets/vendor/pace/pace.min.js"></script>

    <!-- App scripts -->
    <script src="<?php echo base_url(); ?>assets/scripts/homer.js"></script>
    <script src="<?php echo base_url(); ?>assets/scripts/apps.js"></script>

    <script type="text/javascript">
                        $(document).ajaxStart(function () {
                            Pace.restart();
                        });
    </script>

    <script type="text/javascript">
        var nis = null;

        $(document).ready(function (event) {
            $("#nis").focus();

            // KEEP SESSION IS STILL ALIVE ON SERVER
            setInterval(function () {
                create_ajax('<?php echo site_url('pencarian/get_tanggal_jam'); ?>', '', function (data) {});
            }, 60000);

            set_frame();
            $("#detail-nis").hide();
        });

        function set_frame() {
            var height_color = $(".color-line").outerHeight();
            var height_content = $(".content").outerHeight();
            var height_page = $(window).height();
            var width_page = $(window).width();

            $("#detail-nis").prop('style', 'width: ' + width_page + 'px;height: ' + (height_page - height_color - height_content + 20) + 'px;margin-top: -25px;border-width: 1px;border-color: #ffffff;');
        }

        function nis_filled() {
            $("#detail-nis").fadeIn();
            $("#keterangan").fadeOut();
        }

        function nis_remove() {
            $("#detail-nis").fadeOut();
            $("#keterangan").fadeIn();
        }

        function nis_changed(that) {
            var nis_input = $(that).val();
            nis_remove();

            if (nis === nis_input) {
                $("#detail-nis").removeAttr('src');
                nis = null;
            } else {
                var success = function (data) {
                    if (data.url === null) {
                        create_homer_error('NIS tidak ditemukan');
                        nis = null;
                    } else {
                        $("#detail-nis").attr('src', data.url);
                        nis = nis_input;
                        nis_filled();
                    }
                };

                create_ajax('<?php echo site_url('pencarian/get_data_panel'); ?>', 'NIS=' + nis_input, success);
            }

            $(that).val('');
            $(that).focus();
        }
    </script>

</body>
</html>