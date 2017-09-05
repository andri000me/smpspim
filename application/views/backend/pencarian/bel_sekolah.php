
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
                            <div class="col-md-2">
                                <select class="form-control" id="jenjang_jk" onchange="get_alarm()">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <?php
                                    foreach ($dept as $detail_dept) {
                                        foreach ($jk as $detail_jk) {
                                            echo '<option value="'.$detail_dept->ID_DEPT.'_'.$detail_jk->ID_JK.'">'.$detail_dept->NAMA_DEPT.' - '.$detail_jk->NAMA_JK.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-10">
                                <h4 id="alarm" class="pull-right"></h4>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 50px;margin-bottom: 50px;height: 200px;">
                            <div class="col-md-12 text-center">
                                <i id="alarm_show" class="pe-7s-alarm" style="font-size: 150px"></i>
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
        var ALARM = new Array('11:18:00');
        var LOOP_ALARM = new Array('3');
        var LOOP = 1;
        
        $(document).ready(function () {
            create_homer_info('Pilih jenjang untuk mengambil alarm bel sekolah');
            $("#alarm_show").hide();
            
            setInterval(function () {
                create_ajax('<?php echo site_url('pencarian/get_tanggal_jam'); ?>', '', function (data) {
                    $("#jam").html(data.jam);
                    $("#tanggal").html(data.tanggal);
                    
                    if($.inArray(data.jam, ALARM) === 0) {
                        LOOP = LOOP_ALARM[ALARM.indexOf(data.jam)];
                        alarm_ringing();
                    }
                });
            }, 1000);
        });
        
        function alarm_ringing() {
            var audio = new Audio('../files/aplikasi/alarm_' + LOOP + '.mp3');
            var timer = 5000;
            var timeout = (LOOP * timer) + 1000;
            var RING = true;
            var interval = setInterval(function() {
                    if(RING) {
                        $("#alarm_show").show();
                        $("#jam").hide();
                    } else {
                        $("#alarm_show").hide();
                        $("#jam").show();
                    }
                    RING = !RING;
                }, 1000);
                
            audio.play();
            setTimeout(function(){
                clearInterval(interval);
            }, timeout);
            
            $("#alarm_show").hide();
            $("#jam").show();
            
            LOOP = 1;
        }
        
        function get_alarm() {
            var jenjang_jk = $('#jenjang_jk').val();
            var jenjang_jk_split = jenjang_jk.split("_");
            
            if(jenjang_jk_split !== '') {
                create_ajax('<?php echo site_url('pencarian/get_alarm'); ?>', 'DEPT_MJP=' + jenjang_jk_split[0] + '&JK_MJP=' + jenjang_jk_split[1], function (data) {
                    $("#alarm").html(' ');
                    $.each(data, function(key, value) {
                        var bel_mulai = parseInt(value.BEL_MULAI_MJP);
                        var bel_akhir = parseInt(value.BEL_AKHIR_MJP);
                        
                        if(bel_mulai > 0) {
                            $("#alarm").append('<span class="label label-success">' + value.MULAI_MJP + '</span>&nbsp;<span class="label label-info">' + value.BEL_MULAI_MJP + '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                            ALARM.push(value.MULAI_MJP);
                            LOOP_ALARM.push(bel_mulai);
                        } 
                        
                        if(bel_akhir > 0) {
                            $("#alarm").append('<span class="label label-success">' + value.AKHIR_MJP + '</span>&nbsp;<span class="label label-info">' + value.BEL_AKHIR_MJP + '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                            ALARM.push(value.AKHIR_MJP);
                            LOOP_ALARM.push(bel_akhir);
                        }
                    });
                });
            }
        }
    </script>

</body>
</html>