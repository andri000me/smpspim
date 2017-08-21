

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

        <!-- Vendor styles -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fontawesome/css/font-awesome.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/animate.css/animate.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/toastr/build/toastr.min.css" />

        <!-- App styles -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/helper.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.css">

    </head>
    <body class="blank">
    <!-- Simple splash screen-->
        <div class="splash-screen"></div>
        <!--[if lt IE 7]>
        <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="color-line"></div>

        <div class="login-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center m-b-md">
                        <h3>LOGIN ADMIN <?php echo $this->pengaturan->getNamaApp(); ?></h3>
                        <small><?php echo $this->pengaturan->getMotto(); ?></small>
                    </div>
                    <div class="hpanel">
                        <div class="panel-body">
                            <form action="#" id="loginForm" onsubmit="return login();">
                                    <div class="form-group">
                                        <label class="control-label" for="username">Username</label>
                                        <input type="text" title="Please enter you username" required="" value="" name="username" id="username" class="form-control required">
                                        <span class="help-block small">Ketikan username Anda</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="password">Password</label>
                                        <input type="password" title="Please enter your password" required="" value="" name="password" id="password" class="form-control required">
                                        <span class="help-block small">Ketikan password Anda</span>
                                    </div>
<!--                                    <div class="checkbox">
                                        <input type="checkbox" class="i-checks" checked>
                                             Remember login
                                        <p class="help-block small">(Centang jika Anda menggunakan komputer pribadi)</p>
                                    </div>-->
                                    <button class="btn btn-success btn-block">Masuk</button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    2017 Copyright <strong><?php echo $this->pengaturan->getNamaLembagaSingk(); ?> - <?php echo $this->pengaturan->getNamaLembaga(); ?></strong>
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
        <script src="<?php echo base_url(); ?>assets/vendor/sparkline/index.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendor/toastr/build/toastr.min.js"></script>

        <!-- App scripts -->
        <script src="<?php echo base_url(); ?>assets/scripts/homer.js"></script>
        <script src="<?php echo base_url(); ?>assets/scripts/apps.js" type="text/javascript"></script>
        
        <script type="text/javascript">
            function login() {
                var message = "Mohon tunggu sebentar, sistem sedang mengecek akun Anda.";
                var success = function(data) {
                    remove_splash();
                    
                    if(data.status) //if success close modal and reload ajax table
                    {
                        create_swal_success('', data.success_string);
                        
                        setTimeout(function(){
                            window.location = data.link;
                        }, 1500);
                    } else {
                        create_homer_error(data.error_string);
                    }
                };
                create_form_ajax('<?php echo site_url('login/ajax_login')?>', 'loginForm', success, message);
                
                return false;
            }
        </script>

    </body>
</html>