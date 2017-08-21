
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
                <h2>DATA SISWA</h2>
            </div>
            <div class="col-md-1">
                <a href="#" class="pull-right" title="Klik untuk menutup halaman" onclick="window.close();">
                    <i class="pe-7s-close" style="font-size: 40px;"></i>
                </a>
            </div>
        </div>
        <br>
<?php
function to_print_text($data) {
    $bulan = array(
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'Nopember',
        'Desember',
    );

    return date("j", strtotime($data)) . ' ' . $bulan[date("n", strtotime($data))] . ' ' . date("Y", strtotime($data));
}
function list_detail($label, $value) {
    echo '<div class="list-item">
            <small>'.$label.'</small>
            <h3 class="font-extra-bold text-primary">'.($value == NULL ? '-' : $value).'</h3>
        </div>';
}
function list_detail_kehadiran($label, $data, $position) {
    $value = count($data);
    $title = '';
    foreach ($data as $detail) {
        $title .= to_print_text($detail->TANGGAL_AKH).' | ';
    }
    if($title == '') $title = 'Tidak ada data.';
    
    echo '<div class="list-item">
            <small>'.$label.'</small>
            <h3 class="font-extra-bold text-primary" data-toggle="tooltip" data-placement="'.$position.'" title="'.$title.'">'.($value == NULL ? '-' : $value).'</h3>
        </div>';
}
?>
        <div class="row">
            <div class="col-md-8">
                <div class="hpanel hgreen">
                    <div class="panel-heading">
                        Informasi Dasar
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('NIK', $SISWA->NIK_SISWA); ?>
                                <?php list_detail('NISN', $SISWA->NISN_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('NIS', $SISWA->NIS_SISWA); ?>
                                <?php list_detail('NO UN', $SISWA->NO_UN_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <?php list_detail('NAMA', $SISWA->NAMA_SISWA); ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php list_detail('PANGGILAN', $SISWA->PANGGILAN_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('JENIS KELAMIN', $SISWA->NAMA_JK); ?>
                                <?php list_detail('TEMPAT LAHIR', $SISWA->TEMPAT_LAHIR_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('STATUS', ($SISWA->AKTIF_SISWA == 1) ? 'AKTIF' : 'TIDAK AKTIF'); ?>
                                <?php list_detail('TANGGAL LAHIR', $this->date_format->to_print($SISWA->TANGGAL_LAHIR_SISWA)); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php list_detail('ALAMAT', $SISWA->ALAMAT_SISWA.', Kec. '.$SISWA->NAMA_KEC_SISWA.', '.$SISWA->NAMA_KAB_SISWA.', Prov. '.$SISWA->NAMA_PROV_SISWA); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="hpanel hblue">
                    <div class="panel-heading">
                        Foto
                    </div>
                    <div class="panel-body text-center">
                        <img src="<?php 
                                if (file_exists('files/siswa/' . $SISWA->NIS_SISWA . '.jpg')) {
                                    echo base_url('files/siswa/'. $SISWA->NIS_SISWA . '.jpg');
                                } elseif (file_exists('files/siswa/' . $SISWA->ID_SISWA . '.png') || $SISWA->FOTO_SISWA != NULL) {
                                    echo base_url('files/siswa/'. $SISWA->ID_SISWA . '.png');
                                } else {
                                    echo base_url('files/no_image.jpg');
                                }
                                ?>" class="img-responsive" style="height: 410px" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel horange">
                    <div class="panel-heading">
                        Biodata Siswa
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php list_detail('SUKU', $SISWA->NAMA_SUKU); ?>
                                        <?php list_detail('ANAK KE-', $SISWA->ANAK_KE_SISWA); ?>
                                        <?php list_detail('GOLONGAN DARAH', $SISWA->GOL_DARAH_SISWA); ?>
                                        <?php list_detail('HOBI', $SISWA->NAMA_HOBI); ?>
                                        <?php if($SISWA->NAMA_PONDOK_MPS != NULL) list_detail('ALAMAT PONDOK', $SISWA->ALAMAT_MPS); ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php list_detail('AGAMA', $SISWA->NAMA_AGAMA); ?>
                                        <?php list_detail('JUMLAH SAUDARA', $SISWA->JUMLAH_SDR_SISWA); ?>
                                        <?php list_detail('RIWAYAT KESEHATAN', $SISWA->RIWAYAT_KESEHATAN_SISWA); ?>
                                        <?php list_detail('ASAL SISWA', $SISWA->NAMA_ASSAN); ?>
                                        <?php if($SISWA->NAMA_PONDOK_MPS != NULL) list_detail('TELP. PONDOK', $SISWA->TELP_MPS); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php list_detail('KONDISI', $SISWA->NAMA_KONDISI); ?>
                                        <?php list_detail('BERAT BADAN (KG)', $SISWA->BERAT_SISWA); ?>
                                        <?php list_detail('NO HP', $SISWA->NOHP_SISWA); ?>
                                        <?php list_detail('DOMISI', $SISWA->NAMA_PONDOK_MPS); ?>
                                        <?php if($SISWA->NAMA_PONDOK_MPS != NULL) list_detail('EMAIL PONDOK', $SISWA->EMAIL_MPS); ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php list_detail('KEWARGANEGARAAN', $SISWA->NAMA_WARGA); ?>
                                        <?php list_detail('TINGGI BADAN (CM)', $SISWA->TINGGI_SISWA); ?>
                                        <?php list_detail('EMAIL', $SISWA->EMAIL_SISWA); ?>
                                        <?php if($SISWA->NAMA_PONDOK_MPS != NULL) list_detail('PENGASUH PONDOK', $SISWA->PENGASUH_MPS); ?>
                                        <?php if($SISWA->NAMA_PONDOK_MPS != NULL) list_detail('JARAK PONDOK (M)', $SISWA->JARAK_MPS); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="hpanel hyellow">
                    <div class="panel-heading">
                        Data Ayah
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('NIK', $SISWA->AYAH_NIK_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('STATUS', $SISWA->NAMA_SO_AYAH); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php list_detail('NAMA', $SISWA->AYAH_NAMA_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('TEMPAT LAHIR', $SISWA->AYAH_TEMPAT_LAHIR_SISWA); ?>
                                <?php list_detail('PENDIDIKAN', $SISWA->NAMA_JP_AYAH); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('TANGGAL LAHIR', $this->date_format->to_print($SISWA->AYAH_TANGGAL_LAHIR_SISWA)); ?>
                                <?php list_detail('PEKERJAAN', $SISWA->NAMA_JENPEK_AYAH); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="hpanel hyellow">
                    <div class="panel-heading">
                        Data Ibu
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('NIK', $SISWA->IBU_NIK_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('STATUS', $SISWA->NAMA_SO_IBU); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php list_detail('NAMA', $SISWA->IBU_NAMA_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('TEMPAT LAHIR', $SISWA->IBU_TEMPAT_LAHIR_SISWA); ?>
                                <?php list_detail('PENDIDIKAN', $SISWA->NAMA_JP_IBU); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('TANGGAL LAHIR', $this->date_format->to_print($SISWA->IBU_TANGGAL_LAHIR_SISWA)); ?>
                                <?php list_detail('PEKERJAAN', $SISWA->NAMA_JENPEK_IBU); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="hpanel hyellow">
                    <div class="panel-heading">
                        Data Wali
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('NIK', $SISWA->WALI_NIK_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('HUBUNGAN', $SISWA->NAMA_HUB); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php list_detail('NAMA', $SISWA->WALI_NAMA_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('PENDIDIKAN', $SISWA->NAMA_JP_WALI); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('PEKERJAAN', $SISWA->NAMA_JENPEK_WALI); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="hpanel hyellow">
                    <div class="panel-heading">
                        Kontak Orang Tua
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php list_detail('PENGHASILAN', $SISWA->NAMA_HASIL); ?>
                                <?php list_detail('NO HP', $SISWA->ORTU_NOHP1_SISWA); ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php list_detail('EMAIL', $SISWA->ORTU_EMAIL_SISWA); ?>
                                <?php list_detail('NO HP', $SISWA->ORTU_NOHP2_SISWA); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php list_detail('ALAMAT', $SISWA->ORTU_ALAMAT_SISWA.', Kec. '.$SISWA->NAMA_KEC_ORTU.', '.$SISWA->NAMA_KAB_ORTU.', Prov. '.$SISWA->NAMA_PROV_ORTU); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hviolet">
                    <div class="panel-heading">
                        Data Pendaftaran Siswa Baru
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php list_detail('NO UM', $this->pengaturan->getKodeUM($SISWA)); ?>
                                        <?php list_detail('JENJANG MASUK', $SISWA->NAMA_JS_SISWA); ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php list_detail('ANGKATAN', $SISWA->ANGKATAN_SISWA); ?>
                                        <?php list_detail('TINGKAT MASUK', $SISWA->MASUK_TINGKAT_SISWA); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php list_detail('JENJANG ASAL', $SISWA->NAMA_JS_AS); ?>
                                    </div>
                                    <div class="col-md-9 text-right">
                                        <?php list_detail('ASAL SEKOLAH', $SISWA->NAMA_AS); ?>                                 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php list_detail('ALAMAT ASAL SEKOLAH', 'Kec. '.$SISWA->NAMA_KEC_AS.', '.$SISWA->NAMA_KAB_AS.', Prov. '.$SISWA->NAMA_PROV_AS); ?>                                 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php list_detail('NO. IJASAH', $SISWA->NO_IJASAH_SISWA); ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                         <?php list_detail('TANGGAL IJASAH', $this->date_format->to_print($SISWA->TANGGAL_IJASAH_SISWA)); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>NILAI UJIAN MASUK</h3>
                                    </div>
                                </div>
                                <?php if($SISWA->NO_UM_SISWA != NULL && $NILAI_PSB != NULL) { ?>
                                <div class="row">
                                <?php 
                                $x = 1;
                                foreach ($NILAI_PSB as $NILAI) { 
                                    $x++;
                                ?>
                                    <?php if(($x%2) == 0) { ?>
                                    <div class="col-md-6">
                                        <?php list_detail($NILAI->NAMA_MAPEL, $NILAI->NILAI_PNU); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="col-md-6 text-right">
                                        <?php list_detail($NILAI->NAMA_MAPEL, $NILAI->NILAI_PNU); ?>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php foreach ($AKADEMIK as $DETAIL) { ?>
            <?php 
            $TA = $DETAIL['TA']; 
            $NIS = $DETAIL['NIS']; 
            $AKADEMIK = $DETAIL['AKADEMIK']; 
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hviolet">
                        <div class="panel-heading">
                            Data Akademik TA: <?php echo $TA->NAMA_TA; ?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 border-right">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php list_detail('NIS', $NIS); ?>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <?php list_detail('JENJANG TINGKAT', $AKADEMIK->KETERANGAN_TINGK); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php list_detail('KELAS', $AKADEMIK->NAMA_KELAS); ?>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <?php list_detail('WALI KELAS', $AKADEMIK->NAMA_PEG); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php foreach ($DETAIL['CAWU'] as $DETAIL_CAWU) { ?>
                            <?php if(isset($DETAIL['NILAI'][$DETAIL_CAWU->ID_CAWU])) { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="hpanel hgreen">
                                        <div class="panel-heading hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            CAWU: <?php echo $DETAIL_CAWU->ID_CAWU; ?>
                                        </div>
                                        <div class="panel-body">
                                            <?php if(count($DETAIL['NILAI'][$DETAIL_CAWU->ID_CAWU]) > 0) { ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="list-item">
                                                        <small>NAMA MATA PELAJARAN</small>
                                                    </div>  
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <div class="list-item">
                                                        <small>NILAI</small>
                                                    </div>  
                                                </div>
                                            </div>
                                            <?php 
                                            $jumlah_nilai = 0;
                                            $count = 0;
                                            foreach ($DETAIL['NILAI'][$DETAIL_CAWU->ID_CAWU] as $NILAI) { 
                                                $jumlah_nilai += $NILAI->NILAI_SISWA;
                                                $count++;
                                                ?>
                                            <div class="row" onmouseover="highlight_row(this);" onmouseout="remove_highlight(this);">
                                                <div class="col-md-10">
                                                    <div class="list-item">
                                                        <h3 class="font-extra-bold text-primary"><?php echo $NILAI->NAMA_MAPEL; ?></h3>
                                                    </div>  
                                                </div>
                                                <div class="col-md-2 text-right">
                                                    <div class="list-item">
                                                        <h3 class="font-extra-bold text-primary"><?php echo $NILAI->NILAI_SISWA; ?></h3>
                                                    </div>  
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <?php list_detail('POIN', $DETAIL['POIN'][$DETAIL_CAWU->ID_CAWU]); ?>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <?php list_detail('JUMLAH NILAI', $jumlah_nilai); ?>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <?php list_detail('RATA-RATA NILAI', number_format(($jumlah_nilai/$count), 2, ",", ".")); ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="row">
                                                <?php 
                                                $x = 1;
                                                foreach ($DETAIL['KEHADIRAN'][$DETAIL_CAWU->ID_CAWU] as $DETAIL_KEHADIRAN) {
                                                    $x++;
                                                    ?>
                                                <div class="col-md-6 <?php echo (($x%2) == 0) ? 'border-right' : ''; ?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h3><?php echo $DETAIL_KEHADIRAN['DATA']->NAMA_MJK; ?></h3>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <?php list_detail_kehadiran('SAKIT', $DETAIL_KEHADIRAN['SAKIT'], 'right'); ?>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <?php list_detail_kehadiran('IZIN', $DETAIL_KEHADIRAN['IZIN'], 'bottom'); ?>
                                                        </div>
                                                        <div class="col-md-4 text-right">
                                                            <?php list_detail_kehadiran('LARI', $DETAIL_KEHADIRAN['ALPHA'], 'left'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="hpanel hviolet">
                                        <div class="panel-heading hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            KEUANGAN
                                        </div>
                                        <div class="panel-body">
                                            <?php 
                                            $jumlah_tunggakan = 0;
                                            $jumlah_pembayaran = 0;
                                            if(count($DETAIL['KEUANGAN']) > 0) { ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="list-item">
                                                        <small>NAMA TAGIHAN</small>
                                                    </div>  
                                                </div>
                                                <div class="col-md-3 text-right border-right">
                                                    <div class="list-item">
                                                        <small>NOMINAL TAGIHAN</small>
                                                    </div>  
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="list-item">
                                                        <small>STATUS</small>
                                                    </div> 
                                                </div>
                                            </div>
                                            <?php foreach ($DETAIL['KEUANGAN'] as $DETAIL_KEUANGAN) { 
                                                if($DETAIL_KEUANGAN->STATUS_SETUP)
                                                    $jumlah_pembayaran += $DETAIL_KEUANGAN->NOMINAL_DT;
                                                else
                                                    $jumlah_tunggakan += $DETAIL_KEUANGAN->NOMINAL_DT;
                                                ?>
                                            <div class="row" onmouseover="highlight_row(this);" onmouseout="remove_highlight(this);">
                                                <div class="col-md-6">
                                                    <div class="list-item">
                                                        <h3 class="font-extra-bold text-primary"><?php echo $DETAIL_KEUANGAN->NAMA_DT; ?></h3>
                                                    </div> 
                                                </div>
                                                <div class="col-md-3 text-right border-right">
                                                    <div class="list-item">
                                                        <h3 class="font-extra-bold text-primary"><?php echo $this->money->format($DETAIL_KEUANGAN->NOMINAL_DT); ?></h3>
                                                    </div> 
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="list-item">
                                                        <h3 class="font-extra-bold text-primary"><?php echo $DETAIL_KEUANGAN->STATUS_SETUP ? 'LUNAS' : 'BELUM LUNAS'; ?></h3>
                                                    </div> 
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?php list_detail('JUMLAH PEMBAYARAN', $this->money->format($jumlah_pembayaran)); ?>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <?php list_detail('JUMLAH TUNGGAKAN', $this->money->format($jumlah_tunggakan)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <?php if($SISWA->STATUS_MUTASI_SISWA != NULL) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel horange">
                    <div class="panel-heading">
                        Mutasi Siswa
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php list_detail('NOMOR SURAT', $SISWA->NO_SURAT_MUTASI_SISWA); ?>
                                        <?php list_detail('TANGGAL MUTASI', $this->date_format->to_print($SISWA->TANGGAL_MUTASI_SISWA)); ?>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <?php list_detail('STATUS MUTASI', $SISWA->NAMA_MUTASI); ?>
                                        <?php list_detail('YANG MENGELUARKAN', $SISWA->NAMA_PEG_MUTASI); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
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
        $(document).ajaxStart(function () {
            Pace.restart();
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            
        });
        
        function highlight_row(that) {
            console.log("ok");
            $(that).find('h3').removeClass('text-primary').addClass('text-success');
        }
        
        function remove_highlight(that) {
            console.log("remove");
            $(that).find('h3').removeClass('text-success').addClass('text-primary');
        }
    </script>

</body>
</html>