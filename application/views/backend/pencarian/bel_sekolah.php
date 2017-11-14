
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
<body class="fixed-navbar fixed-sidebar"><!-- Main Wrapper -->

    <div class="color-line"></div>
    <!-- Main Wrapper -->

    <div class="normalheader transition animated fadeIn" style="margin-top: -30px;">
        <div class="hpanel">
            <div class="panel-body text-center">
                <h2 class="font-light m-b-xs">
                    BEL SEKOLAH
                </h2>
            </div>
        </div>
    </div>
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-control" id="jenjang_jk" onchange="get_alarm()">
                                            <option value="">-- Pilih Jenis --</option>
                                            <?php
                                            foreach ($jk as $detail_jk) {
                                                echo '<option value="KBM_' . $detail_jk->ID_JK . '">KBM - ' . $detail_jk->NAMA_JK . '</option>';
                                                echo '<option value="UJIAN_' . $detail_jk->ID_JK . '">UJIAN - ' . $detail_jk->NAMA_JK . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row" id="alarm">
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-6 text-center">
                                <h1 id="jam" class="font-light text-primary" style="font-size: 150px;"></h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9" style="padding-top: 20px;">
                                <p><em>Jam dan tanggal diambil dari server. Pastikan pengaturan jam dan tanggal diserver benar.</em></p>
                            </div>
                            <div class="col-md-3">
                                <h4 id="tanggal" class="pull-right" style="margin-left: 20px;"></h4>
                                <i onclick="alarm_ringing()" class="pe-7s-alarm pull-right" style="font-size: 25px;margin-top: 7px;cursor: pointer;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    var ALARM = new Array();
                                    var FILE_ALARM = new Array();
                                    var FILE_RINGING = 'alarm_1.mp3';

                                    var DATE_SERVER = null;
                                    var TAFAWUT = null;

                                    $(document).ready(function () {
                                        create_homer_info('Pilih jenis untuk mengambil alarm bel sekolah');

                                        setInterval(function () {
                                            create_ajax('<?php echo site_url('pencarian/get_tanggal_jam'); ?>', 'date=' + DATE_SERVER + '&tafawut=' + TAFAWUT, function (data) {
                                                $("#jam").html(data.jam);
                                                $("#tanggal").html(data.tanggal);
                                                DATE_SERVER = data.date;
                                                TAFAWUT = data.tafawut;
                                                
                                                jam_server = data.jam.toString();
                                                
                                                if ($.inArray(jam_server, ALARM) !== -1) {
                                                    FILE_RINGING = FILE_ALARM[ALARM.indexOf(jam_server)];
                                                    alarm_ringing();
                                                }
                                                
                                                $(".error-response").remove();
                                            });
                                        }, 1000);
                                    });

                                    function alarm_ringing() {
                                        var audio = new Audio('../files/aplikasi/' + FILE_RINGING);
                                        
                                        console.log('ALARM IS RINGING NOW');
                                        flag_alarm(true);
                                        audio.play();
                                        flag_alarm(false);
                                    }

                                    function flag_alarm(seeFlag) {
                                        if (seeFlag) {
                                            $("#jam").removeClass('text-primary');
                                            $("#jam").addClass('text-success');
                                        } else {
                                            setTimeout(function () {
                                                $("#jam").removeClass('text-success');
                                                $("#jam").addClass('text-primary');
                                            }, 15000);
                                        }
                                    }

                                    function get_alarm() {
                                        var jenjang_jk = $('#jenjang_jk').val();
                                        var jenjang_jk_split = jenjang_jk.split("_");

                                        if (jenjang_jk_split !== '') {
                                            create_ajax('<?php echo site_url('pencarian/get_alarm'); ?>', 'JENIS_MA=' + jenjang_jk_split[0] + '&JK_MA=' + jenjang_jk_split[1], function (data) {
                                                $("#alarm").html(' ');
                                                $.each(data, function (key, value) {
                                                    $("#alarm").append('<div class="col-md-6"><h4><span class="label label-success">' + value.JAM_MA + '</span>&nbsp;<span class="label label-info">' + value.FILE_MA + '</span></h4></div>');
                                                    ALARM.push(value.JAM_MA);
                                                    FILE_ALARM.push(value.FILE_MA);
                                                });
                                            });
                                        }
                                    }
    </script>

</body>
</html>