
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
                <h2>Pusat Pencarian Data Siswa</h2>
            </div>
            <div class="col-md-1">
                <a href="#" class="pull-right" title="Klik untuk kembali ke menu modul" onclick="window.close();">
                    <i class="pe-7s-close" style="font-size: 45px;"></i>
                </a>
            </div>
        </div>
        <?php 
        $id_form = 'form-pencarian';
        $name_function = 'pencarian';
        echo $this->generate->form_open($id_form, $name_function); 
        ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default btn-menu" onclick="kolom_pencarian(this);"><i class="fa fa-reorder"></i></button>
                    </div>
                    <input class="form-control" type="text" name="kata_kunci" placeholder="Ketikan kata kunci disini...">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <br><br>       
<?php 

function create_checkbox($label, $value, $checked = FALSE) {
    echo '<div class="checkbox checkbox-primary">
            <input id="checkbox5" name="filter[]" value="'.$value.'" type="checkbox" '.($checked ? 'checked=""' : '').'>
            <label for="checkbox5">
                <strong>'.$label.'</strong>
            </label>
        </div>';
}

?>
        <div class="row panel-menu">
            <div class="col-md-10 col-md-offset-1">
                <div class="hpanel hbggreen">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo create_checkbox('NAMA', 'NAMA_SISWA', TRUE); ?>
                                <?php echo create_checkbox('NIS', 'NIS_SISWA', TRUE); ?>
                                <?php echo create_checkbox('NIK', 'NIK_SISWA', TRUE); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo create_checkbox('TEMPAT LAHIR', 'TEMPAT_LAHIR_SISWA'); ?>
                                <?php echo create_checkbox('TANGGAL LAHIR', 'TANGGAL_LAHIR_SISWA'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo create_checkbox('TEMPAT LAHIR', 'TEMPAT_LAHIR_SISWA'); ?>
                                <?php echo create_checkbox('TANGGAL LAHIR', 'TANGGAL_LAHIR_SISWA'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo create_checkbox('TEMPAT LAHIR', 'TEMPAT_LAHIR_SISWA'); ?>
                                <?php echo create_checkbox('TANGGAL LAHIR', 'TANGGAL_LAHIR_SISWA'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div class="panel-result">
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
        $(document).ajaxStart(function() { Pace.restart(); });
    </script>
        
    <script type="text/javascript">
        var BAR_MENU_OPEN;
        
        $(document).ready(function(){
            BAR_MENU_OPEN = false;
            
            on_load();
        });
        
        function on_load() {
            $(".panel-menu").hide();
        }
        
        function kolom_pencarian(that) {
            if(BAR_MENU_OPEN) hide_kolom(that);
            else show_kolom(that);
        }
        
        function hide_kolom(that) {
            $(that).children().removeClass('fa-times');
            $(that).children().addClass('fa-reorder');

            $(".panel-menu").slideUp();

            BAR_MENU_OPEN = false;
        }
        
        function show_kolom(that) {
            $(that).children().removeClass('fa-reorder');
            $(that).children().addClass('fa-times');

            $(".panel-menu").slideDown();

            BAR_MENU_OPEN = true;
        }
        
        function action_save_<?php echo $name_function; ?>(id) {
            var message = "Sistem sedang mencari data...";
            var success = function(data) {
                var i = 0;
                var j = 0;
                $(".panel-result").append('<p>Kata kunci ditemukan dari data siswa sebanyak ' + data.JUMLAH + ' buah dari ' + data.TOTAL + ' buah.</p>');
                $(".panel-result").append('<div class="row detail_' + j + '">');
                $.each(data.DATA, function(index, detail){
                    if((i%4) == 0 && i > 0) {
                        j++;
                        $(".panel-result").append('</div><div class="row detail_' + j + '">');
                    }
                    
                    $(".detail_" + j).append(create_panel(detail));
                    i++;
                });
                $(".panel-result").append('</div>');
            };
            
            $(".panel-result").html(' ');
            hide_kolom('.btn-menu');
            create_form_ajax('<?php echo site_url('pencarian/cari'); ?>', id, success, message);
            
            return false;
        }
        
        function create_panel(data) {
            return '<div class="col-lg-3"><div class="hpanel hgreen contact-panel"><div class="panel-body"><!--<span class="label label-success pull-right">' + data.KOLOM + '</span>--><img alt="logo" class="img-circle m-b" src="' + (data.FOTO_SISWA === null ? 'files/no_image.jpg' : 'files/siswa/' + data.FOTO_SISWA) + '"><h3><a href="<?php echo site_url('pencarian/detail'); ?>/' + data.ID_SISWA + '" target="_blank">' + data.NAMA_SISWA + '</a></h3><div class="text-muted font-bold m-b-xs">' + (data.NIS_SISWA === null ? 'BELUM ADA NIS' : data.NIS_SISWA) + '</div><p>' + data.ALAMAT_SISWA + ', Kec. ' + data.NAMA_KEC + ', ' + data.NAMA_KAB + ', Prov. ' + data.NAMA_PROV + '</p></div><div class="panel-footer contact-footer"><div class="row"><div class="col-md-4 border-right"> <div class="contact-stat"><span>JENJANG: </span> <strong>' + (data.DEPT_TINGK === null ? '-' : data.DEPT_TINGK) + '</strong></div> </div><div class="col-md-4 border-right"> <div class="contact-stat"><span>TINGKAT: </span> <strong>' + (data.NAMA_TINGK === null ? '-' : data.NAMA_TINGK) + '</strong></div> </div><div class="col-md-4"> <div class="contact-stat"><span>KELAS: </span> <strong>' + (data.NAMA_KELAS === null ? '-' : data.NAMA_KELAS) + '</strong></div> </div></div></div></div></div>';
        }
    </script>

</body>
</html>