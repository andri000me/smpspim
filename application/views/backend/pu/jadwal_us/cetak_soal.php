<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                CETAK SOAL HARI <?php echo $this->date_format->get_day($TANGGAL) . ' TANGGAL ' . $this->date_format->to_print_text($TANGGAL); ?> JAM <?php echo $this->time_format->jam_menit($JAM_MULAI) . ' - ' . $this->time_format->jam_menit($JAM_SELESAI) . ' WIS'; ?>
            </h2>
            <small>Pencetakan soal ujian sekolah berdasarkan ruang</small>
        </div>
    </div>
</div>
<div class="content animate-panel">
    <div class="row">
        <div class="hpanel hgreen">
            <div class="panel-heading hbuilt">
                <div class="panel-tools">
                    <a class="fullscreen"><i class="fa fa-expand"></i></a>
                </div>
                PENGATURAN CETAK
            </div>
            <div class="panel-body ">
                <div class="row">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Lokasi program Adobe Reader</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="lokasiAdobeReader" placeholder="C:\program files\Adobe\reader\AcroRd32.exe">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Lokasi folder soal</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="lokasiFolderSoal" placeholder="D:\soal\cawu">
                            </div>
                            <button type="button" class="btn btn-info" onclick="cetakTest()"><span class="fa fa-print"></span>&nbsp;&nbsp;Coba Cetak</button>
                            <!--<button type="button" class="btn btn-warning" onclick="checkFiles()"><span class="fa fa-check"></span>&nbsp;&nbsp;Cek File PDF</button>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?php
        $cetak_test = 'TEST.pdf';
        $data_cetak = array();
        $nama_mapel = array();
        $nama_tingkat = array();
        $denah = json_decode($DENAH, TRUE);
        foreach ($denah as $jk => $data_denah) {
            if($jk != $JK_PUJ)
                continue;
            ?>
            <div class="col-md-12">
                <div class="hpanel hgreen">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        <?php echo ($jk == 'L' ? 'BANIN' : 'BANAT'); ?>
                    </div>
                    <div class="panel-body ">
                        <div class="row">
                            <?php
                            foreach ($data_denah['DENAH'] as $ruang => $value) {
                                ?>
                                <div class="col-md-4" style="cursor: pointer">
                                    <div class="hpanel <?php echo ($jk == 'L' ? 'hbgnavyblue' : 'hbgviolet'); ?>">
                                        <div class="panel-body text-center" onclick="cetak(this);"> 
                                            <?php
                                            echo '<h2>' . $data_denah['RUANG'][$ruang]['KODE_RUANG'], '</h2>';

                                            foreach ($data_denah["JENJANG"] as $index => $jenjang) {
                                                $tingkat = $data_denah["TINGKAT"][$index];
                                                $relasi = $this->jadwal->relasi_jenjang_departemen($ID, $jenjang, $tingkat);
                                                if($relasi != NULL) $data_relasi[$index] = $relasi;
                                            }

                                            foreach ($value as $tingkat) {
                                                $data_cetak[$jk . $ruang][] = $data_relasi[$tingkat]->ID_MAPEL . '.pdf';
                                                $nama_mapel[$jk . $ruang][] = $data_relasi[$tingkat]->NAMA_MAPEL;
                                                $nama_tingkat[$jk . $ruang][] = $data_relasi[$tingkat]->DEPT_MAPEL . '-' . $data_relasi[$tingkat]->NAMA_TINGK;
                                            }

                                            echo '<input type="hidden" class="data-cetak" value="', $jk . $ruang . '" />';
                                            echo '<input type="hidden" class="title" value="', $data_denah['RUANG'][$ruang]['KODE_RUANG'] . '_' . $jk . '" />';
                                            ?>
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
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    var dataCetak = <?php echo trim(json_encode($data_cetak)); ?>;
    var namaMapel = <?php echo trim(json_encode($nama_mapel)); ?>;
    var namaTingkat = <?php echo trim(json_encode($nama_tingkat)); ?>;

    $(document).ready(function () {
        create_swal_success('Sebelum menggunakan, pastikan pengaturan cetak halaman pada Adobe Reader sudah benar');
    });

    function checkOS() {
        var OSName = "Unknown OS";

        if (navigator.appVersion.indexOf("Win") != -1)
            OSName = "Windows";
        if (navigator.appVersion.indexOf("Mac") != -1)
            OSName = "MacOS";
        if (navigator.appVersion.indexOf("X11") != -1)
            OSName = "UNIX";
        if (navigator.appVersion.indexOf("Linux") != -1)
            OSName = "Linux";

        return OSName;
    }

    function cetakTest() {
        var item = <?php echo trim(json_encode($cetak_test)); ?>;
        var OSName = checkOS();

        if (OSName == "Windows") {
            var lokasiAdobeReader = $('#lokasiAdobeReader').val();
            var lokasiFolderSoal = $('#lokasiFolderSoal').val();

            window.open('<?php echo site_url('pu/jadwal_us/get_file_bat'); ?>?exe=' + lokasiAdobeReader + '&folder=' + lokasiFolderSoal + '&file=' + item + '&title=TEST');

            create_swal_success("Berhasil", "Silahkan running file yang telah didownload.");
        } else {
            create_homer_error("Fitur ini tidak dapat digunakan pada Sistem Operasi " + OSName + ". Silahkan menggunakan windows untuk menggunakan fitur ini.");
        }
    }

    function cetak(that) {
        var id = $(that).find('.data-cetak').val();
        var title = $(that).find('.title').val();
        var lokasiAdobeReader = $('#lokasiAdobeReader').val();
        var lokasiFolderSoal = $('#lokasiFolderSoal').val();
        var OSName = checkOS();
        //        console.log(namaTingkat[id]);
        //        console.log(dataCetak[id]);
        //        console.log(namaMapel[id]);
        //        console.log(lokasiAdobeReader);
        //        console.log(lokasiFolderSoal);

//        if (OSName == "Windows") {
            window.open('<?php echo site_url('pu/jadwal_us/get_file_bat'); ?>?exe=' + lokasiAdobeReader + '&folder=' + lokasiFolderSoal + '&file=' + dataCetak[id] + '&title=' + title);

            create_swal_success("Berhasil", "Silahkan running file yang telah didownload.");

            $(that).parent().removeClass('hbgnavyblue');
            $(that).parent().removeClass('hbgviolet');
            $(that).parent().addClass("hbggreen");
//        } else {
//            create_homer_error("Fitur ini tidak dapat digunakan pada Sistem Operasi " + OSName + ". Silahkan menggunakan windows untuk menggunakan fitur ini.");
//        }
    }

    function textHover(that, status) {
        if (status) {
            $(that).addClass('text-primary');
            $(that).addClass('font-extra-bold');
        } else {
            $(that).removeClass('text-primary');
            $(that).removeClass('font-extra-bold');
        }
    }
</script>
