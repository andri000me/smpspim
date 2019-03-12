<?php
if ($data === NULL)
    $mode_edit = FALSE;
else
    $mode_edit = TRUE;

if ($data !== NULL and isset($mode_view))
    $mode_view = TRUE;
else
    $mode_view = FALSE;

$mode_edit ? $title = 'Perbaharui Data Calon Siswa' : $title = 'Tambah Calon Siswa';
$mode_view ? $title = 'Lihat Data Calon Siswa' : $title = $title;
$subtitle = "Form untuk calon siswa";

$id_form = 'form-siswa';
$name_function = 'siswa';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel">
    <?php
    echo $this->generate->form_open($id_form, $name_function);
    if ($mode_edit)
        $this->generate->input_hidden('ID_SISWA', $data->ID_SISWA);
    ?>
    <?php //if (!$mode_edit && !$mode_view && $STATUS_PSB) { ?>
    <!--        <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hbggreen">
                        <div class="panel-body">
                            <h1 class="text-center">PSB TELAH DITUTUP. ANDA TIDAK DIPERBOLEHKAN MENAMBAH DATA BARU.</h1>
                        </div>
                    </div>
                </div>
            </div>-->
    <?php //} else { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hblue">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    INFORMASI PRIBADI
                </div>
                <div class="panel-body">
                    <?php $this->generate->input_text('NIK', array('name' => 'NIK_SISWA', 'data-inputmask' => "'mask': '9999 9999 9999 9999'", 'maxlength' => 20, 'value' => $mode_edit ? $data->NIK_SISWA : '', 'value' => $mode_edit ? $data->NIK_SISWA : '', 'id' => 'NIK_SISWA', 'onchange' => 'return check_data(\'NIK_SISWA\');'), TRUE, 4); ?>
                    <?php $this->generate->input_text('NISN', array('name' => 'NISN_SISWA', 'data-inputmask' => "'mask': '999 999 999 9'", 'maxlength' => 13, 'value' => $mode_edit ? $data->NISN_SISWA : '', 'value' => $mode_edit ? $data->NISN_SISWA : '', 'id' => 'NISN_SISWA', 'onchange' => 'return check_data(\'NISN_SISWA\');'), FALSE, 4); ?>
                    <?php $this->generate->input_text('Nama Lengkap', array('name' => 'NAMA_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->NAMA_SISWA : ''), TRUE); ?>
                    <?php // $this->generate->input_text('Nama Panggilan', array('name' => 'PANGGILAN_SISWA', 'maxlength' => 50, 'value' => $mode_edit ? $data->PANGGILAN_SISWA : ''), FALSE, 4); ?>
                    <?php //$this->generate->input_select2('Jenis Kelamin', array('name' => 'JK_SISWA', 'url' => site_url('psb/calon_siswa/ac_jk')), TRUE, 3, FALSE, $mode_edit ? array('id' => $data->JK_SISWA, 'text' => $data->NAMA_JK) : NULL); ?>
                    <?php $this->generate->input_radio('Jenis Kelamin', array('name' => 'JK_SISWA', 'checked' => $mode_edit ? $data->JK_SISWA : '', 'value' => $mode_edit ? [['value' => 'L', 'label' => 'Laki-laki'], ['value' => 'P', 'label' => 'Perempuan']] : [['value' => 'L', 'label' => 'Laki-laki'], ['value' => 'P', 'label' => 'Perempuan']]), TRUE, 9); ?>
                    <?php $this->generate->input_text('Tempat Lahir', array('name' => 'TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->TEMPAT_LAHIR_SISWA : ''), TRUE, 4); ?>
                    <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'TANGGAL_LAHIR_SISWA', 'data-inputmask' => "'mask': '9999-99-99'", 'value' => $mode_edit ? $this->date_format->to_view($data->TANGGAL_LAHIR_SISWA) : ''), TRUE, 2); ?>
                    <?php // $this->generate->input_select2('Suku', array('name' => 'SUKU_SISWA', 'url' => site_url('psb/calon_siswa/ac_suku')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->SUKU_SISWA, 'text' => $data->NAMA_SUKU) : NULL); ?>
                    <?php // $this->generate->input_select2('Agama', array('name' => 'AGAMA_SISWA', 'url' => site_url('psb/calon_siswa/ac_agama')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AGAMA_SISWA, 'text' => $data->NAMA_AGAMA) : NULL); ?>
                    <?php // $this->generate->input_select2('Kondisi', array('name' => 'KONDISI_SISWA', 'url' => site_url('psb/calon_siswa/ac_kondisi')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->KONDISI_SISWA, 'text' => $data->NAMA_KONDISI) : NULL); ?>
                    <?php // $this->generate->input_select2('Kewarganegaraan', array('name' => 'WARGA_SISWA', 'url' => site_url('psb/calon_siswa/ac_warga')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WARGA_SISWA, 'text' => $data->NAMA_WARGA) : NULL); ?>
                    <?php // $this->generate->input_select2('Tempat Tinggal', array('name' => 'TEMPAT_TINGGAL_SISWA', 'url' => site_url('psb/calon_siswa/ac_tinggal')), TRUE, 3, FALSE, $mode_edit ? array('id' => $data->TEMPAT_TINGGAL_SISWA, 'text' => $data->NAMA_TEMTING) : NULL); ?>
                    <?php // $this->generate->input_text('Berat Badan', array('name' => 'BERAT_SISWA', 'placeholder' => 'kg', 'maxlength' => 3, 'value' => $mode_edit ? $data->BERAT_SISWA : ''), FALSE, 2); ?>
                    <?php // $this->generate->input_text('Tinggi Badan', array('name' => 'TINGGI_SISWA', 'placeholder' => 'cm', 'maxlength' => 3, 'value' => $mode_edit ? $data->TINGGI_SISWA : ''), FALSE, 2); ?>
                    <?php // $this->generate->input_select2('Golongan Darah', array('name' => 'GOL_DARAH_SISWA', 'url' => site_url('psb/calon_siswa/ac_darah')), FALSE, 2, FALSE, $mode_edit ? array('id' => $data->GOL_DARAH_SISWA, 'text' => $data->NAMA_DARAH) : NULL); ?>
                    <?php // $this->generate->input_text('Riwayat Kesehatan', array('name' => 'RIWAYAT_KESEHATAN_SISWA', 'maxlength' => 500, 'value' => $mode_edit ? $data->RIWAYAT_KESEHATAN_SISWA : ''), FALSE, 9); ?>
                    <?php // $this->generate->input_text('Anak ke-', array('name' => 'ANAK_KE_SISWA', 'maxlength' => 1, 'value' => $mode_edit ? $data->ANAK_KE_SISWA : ''), FALSE, 1); ?>
                    <?php // $this->generate->input_text('Jumlah Saudara', array('name' => 'JUMLAH_SDR_SISWA', 'maxlength' => 1, 'value' => $mode_edit ? $data->JUMLAH_SDR_SISWA : ''), FALSE, 1); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    ASAL SEKOLAH
                </div>
                <div class="panel-body">
                    <?php
                    if ($mode_edit) {
                        $this->generate->input_hidden('TEMP_MASUK_JENJANG_SISWA', $data->MASUK_JENJANG_SISWA);
                        $this->generate->input_hidden('TEMP_MASUK_TINGKAT_SISWA', $data->MASUK_TINGKAT_SISWA);
                        $this->generate->input_hidden('TEMP_NO_UM_SISWA', $data->NO_UM_SISWA);
                    }
                    ?>
                    <?php // $this->generate->input_select2('Asal Sekolah', array('name' => 'ASAL_SEKOLAH_SISWA', 'url' => site_url('psb/calon_siswa/ac_asal_sekolah')), TRUE, 8, TRUE, $mode_edit ? array('id' => $data->ASAL_SEKOLAH_SISWA, 'text' => $data->NAMA_AS) : array('id' => '1', 'text' => 'Belum sekolah'), '<div class="col-sm-1"><a href="' . site_url('master_data/asal_sekolah') . '" class="btn btn-primary" target="_blank"><i class="fa fa-plus"></i></a></div>'); ?>
                    <?php $this->generate->input_select2('Masuk kejenjang', array('name' => 'MASUK_JENJANG_SISWA', 'url' => site_url('psb/calon_siswa/ac_jenjang_sekolah')), TRUE, 2, FALSE, $mode_edit ? array('id' => $data->MASUK_JENJANG_SISWA, 'text' => $data->NAMA_JS) : NULL); ?>
                    <?php
                    $this->generate->input_dropdown('Masuk ketingkat', 'MASUK_TINGKAT_SISWA', array(
                        array('id' => 0, 'text' => "Pilih jenjang terlebih dahulu", 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 1 ? TRUE : FALSE) : FALSE),
                        array('id' => 1, 'text' => 1, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 1 ? TRUE : FALSE) : FALSE),
                        array('id' => 2, 'text' => 2, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 2 ? TRUE : FALSE) : FALSE),
                        array('id' => 3, 'text' => 3, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 3 ? TRUE : FALSE) : FALSE),
                        array('id' => 4, 'text' => 4, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 4 ? TRUE : FALSE) : FALSE),
                        array('id' => 5, 'text' => 5, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 5 ? TRUE : FALSE) : FALSE),
                        array('id' => 6, 'text' => 6, 'selected' => $mode_edit ? ($data->MASUK_TINGKAT_SISWA == 6 ? TRUE : FALSE) : FALSE),
                            ), TRUE, 4);
                    ?>
                    <?php // $this->generate->input_text('No. Ijasah', array('name' => 'NO_IJASAH_SISWA', 'maxlength' => 40, 'value' => $mode_edit ? $data->NO_IJASAH_SISWA : ''), FALSE, 3); ?>
                    <?php // $this->generate->input_date('Tanggal Ijasah', array('name' => 'TANGGAL_IJASAH_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->TANGGAL_IJASAH_SISWA) : ''), FALSE, 2); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hgreen">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    KONTAK
                </div>
                <div class="panel-body">
                    <?php $this->generate->input_text('Alamat', array('name' => 'ALAMAT_SISWA', 'maxlength' => 250, 'value' => $mode_edit ? $data->ALAMAT_SISWA : ''), TRUE, 9); ?>
                    <?php $this->generate->input_select2('Kecamatan', array('name' => 'KECAMATAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_kecamatan')), TRUE, 6, TRUE, $mode_edit ? array('id' => $data->KECAMATAN_SISWA, 'text' => $data->NAMA_KEC . ', ' . $data->NAMA_KAB . ', ' . $data->NAMA_PROV) : NULL); ?>
                    <?php // $this->generate->input_text('Kode Pos', array('name' => 'KODE_POS_SISWA', 'maxlength' => 5, 'value' => $mode_edit ? $data->KODE_POS_SISWA : ''), FALSE, 2); ?>
                    <?php $this->generate->input_text('No. HP', array('name' => 'NOHP_SISWA', 'data-inputmask' => "'mask': '9999 9999 9999'", 'maxlength' => 14, 'value' => $mode_edit ? $data->NOHP_SISWA : ''), FALSE, 4); ?>
                    <?php // $this->generate->input_text('Email', array('name' => 'EMAIL_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->EMAIL_SISWA : ''), FALSE, 4); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hviolet">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    DATA AYAH
                </div>
                <div class="panel-body">
                    <?php // $this->generate->input_text('NIK', array('name' => 'AYAH_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->AYAH_NIK_SISWA : '', 'id' => 'AYAH_NIK_SISWA', 'onchange' => 'return check_data(\'AYAH_NIK_SISWA\');'), FALSE, 4); ?>
                    <?php $this->generate->input_select2('Status Hidup', array('name' => 'AYAH_HIDUP_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_hidup')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AYAH_HIDUP_SISWA, 'text' => $data->NAMA_SO_AYAH) : NULL); ?>
                    <?php $this->generate->input_text('Nama', array('name' => 'AYAH_NAMA_SISWA', 'id' => 'AYAH_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->AYAH_NAMA_SISWA : ''), TRUE); ?>
                    <?php $this->generate->input_text('Tempat Lahir', array('name' => 'AYAH_TEMPAT_LAHIR_SISWA', 'id' => 'AYAH_TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->AYAH_TEMPAT_LAHIR_SISWA : ''), TRUE, 4); ?>
                    <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'AYAH_TANGGAL_LAHIR_SISWA', 'id' => 'AYAH_TANGGAL_LAHIR_SISWA', 'data-inputmask' => "'mask': '9999-99-99'", 'value' => $mode_edit ? $this->date_format->to_view($data->AYAH_TANGGAL_LAHIR_SISWA) : ''), TRUE, 2); ?>
                    <?php // $this->generate->input_select2('Pendidikan', array('name' => 'AYAH_PENDIDIKAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AYAH_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_AYAH) : NULL); ?>
                    <?php // $this->generate->input_select2('Pekerjaan', array('name' => 'AYAH_PEKERJAAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->AYAH_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_AYAH) : NULL); ?>
                </div>
            </div>
        </div>
    </div>
    <!--        <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hyellow">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                <a class="closebox"><i class="fa fa-times"></i></a>
                            </div>
                            DATA IBU
                        </div>
                        <div class="panel-body">
    <?php $this->generate->input_text('NIK', array('name' => 'IBU_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->IBU_NIK_SISWA : '', 'id' => 'IBU_NIK_SISWA', 'onchange' => 'return check_data(\'IBU_NIK_SISWA\');'), FALSE, 4); ?>
    <?php $this->generate->input_text('Nama', array('name' => 'IBU_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->IBU_NAMA_SISWA : ''), TRUE); ?>
    <?php $this->generate->input_select2('Status Hidup', array('name' => 'IBU_HIDUP_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_hidup')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->IBU_HIDUP_SISWA, 'text' => $data->NAMA_SO_IBU) : NULL); ?>
    <?php $this->generate->input_text('Tempat Lahir', array('name' => 'IBU_TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->IBU_TEMPAT_LAHIR_SISWA : ''), FALSE, 4); ?>
    <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'IBU_TANGGAL_LAHIR_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->IBU_TANGGAL_LAHIR_SISWA) : ''), FALSE, 2); ?>
    <?php $this->generate->input_select2('Pendidikan', array('name' => 'IBU_PENDIDIKAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->IBU_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_IBU) : NULL); ?>
    <?php $this->generate->input_select2('Pekerjaan', array('name' => 'IBU_PEKERJAAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->IBU_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_IBU) : NULL); ?>
                        </div>
                    </div>
                </div>
            </div>-->
    <!--        <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hred">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                <a class="closebox"><i class="fa fa-times"></i></a>
                            </div>
                            DATA WALI
                        </div>
                        <div class="panel-body">
    <?php $this->generate->input_text('NIK', array('name' => 'WALI_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->WALI_NIK_SISWA : '', 'id' => 'WALI_NIK_SISWA', 'onchange' => 'return check_data(\'WALI_NIK_SISWA\');'), FALSE, 4); ?>
    <?php $this->generate->input_text('Nama', array('name' => 'WALI_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->WALI_NAMA_SISWA : ''), FALSE); ?>
    <?php $this->generate->input_select2('Hubungan', array('name' => 'WALI_HUBUNGAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_wali_hubungan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WALI_HUBUNGAN_SISWA, 'text' => $data->NAMA_HUB) : NULL); ?>
    <?php $this->generate->input_select2('Pendidikan', array('name' => 'WALI_PENDIDIKAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WALI_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_WALI) : NULL); ?>
    <?php $this->generate->input_select2('Pekerjaan', array('name' => 'WALI_PEKERJAAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->WALI_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_WALI) : NULL); ?>
                        </div>
                    </div>
                </div>
            </div>-->
    <!--        <div class="row">
                <div class="col-md-12">
                    <div class="hpanel hreddeep">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                <a class="closebox"><i class="fa fa-times"></i></a>
                            </div>
                            DATA ORANG TUA
                        </div>
                        <div class="panel-body">
    <?php // $this->generate->input_text('Alamat', array('name' => 'ORTU_ALAMAT_SISWA', 'maxlength' => 250, 'value' => $mode_edit ? $data->ORTU_ALAMAT_SISWA : ''), TRUE, 9); ?>
    <?php // $this->generate->input_select2('Kecamatan', array('name' => 'ORTU_KECAMATAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_kecamatan')), TRUE, 6, TRUE, $mode_edit ? array('id' => $data->ORTU_KECAMATAN_SISWA, 'text' => $data->NAMA_KEC_ORTU . ', ' . $data->NAMA_KAB_ORTU . ', ' . $data->NAMA_PROV_ORTU) : NULL); ?>
    <?php // $this->generate->input_select2('Penghasilan', array('name' => 'ORTU_PENGHASILAN_SISWA', 'url' => site_url('psb/calon_siswa/ac_ortu_penghasilan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->ORTU_PENGHASILAN_SISWA, 'text' => $data->NAMA_HASIL) : NULL); ?>
    <?php // $this->generate->input_text('No. HP (1)', array('name' => 'ORTU_NOHP1_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP1_SISWA : ''), FALSE, 4); ?>
    <?php // $this->generate->input_text('No. HP (2)', array('name' => 'ORTU_NOHP2_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP2_SISWA : ''), FALSE, 4); ?>
    <?php // $this->generate->input_text('No. HP (3)', array('name' => 'ORTU_NOHP3_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP3_SISWA : ''), FALSE, 4); ?>
    <?php // $this->generate->input_text('Email', array('name' => 'ORTU_EMAIL_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->ORTU_EMAIL_SISWA : ''), FALSE, 4); ?>
                        </div>
                    </div>
                </div>
            </div>-->
    <?php if (!$mode_view) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hbgblue">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="checkbox checkbox-success">
                                    <input type="checkbox" name="validasi" id="validasi">
                                    <label> Saya menyetujui bahwa data yang saya masukan adalah benar. Jika tidak, saya bersedia menerima sanki sesuai dengan perundang-undangan yang berlaku</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="reset" class="btn w-xs btn-danger" id="reset">Reset</button>
                                <button type="submit" class="btn w-xs btn-primary" id="simpan">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>



    <script type="text/javascript">
<?php
if ($mode_view) {
    ?>
            $(function () {
            $(".form-control").prop('disabled', true);
            $(".js-source-multi").select2("enable", false);
            $("#from_upload").change(function(){
            $(this).val(1);
            });
            });
    <?php
} else {
    ?>
            $(function () {
            $("#UPLOAD_FOTO_SISWA").change(function(){
            $('#from_upload').val(1);
            });
    <?php if ($mode_edit) { ?>
                $(".js-source-states-MASUK_JENJANG_SISWA").select2("enable", false);
                $("#MASUK_TINGKAT_SISWA").prop('disabled', true);
        //        $(".js-source-states-MASUK_JENJANG_SISWA, #MASUK_TINGKAT_SISWA").click(function(){
        //            create_homer_error('PSB telah ditutup. Anda tidak diperbolehkan merubah data Masuk kejenjang dan Masuk ketingkat.');
        //        });

        //        create_homer_error('PSB telah ditutup. Anda tidak diperbolehkan merubah data Masuk kejenjang dan Masuk ketingkat.');
    <?php } ?>

            $("#MASUK_TINGKAT_SISWA").prop("disabled", true);
            $(".js-source-states-AYAH_HIDUP_SISWA").on("change", "", function(){
            var data_hidup = $(this).select2("data");
            var statusReq = true;
            if (parseInt(data_hidup.id) > 1) {
            statusReq = false
                    $("#AYAH_TEMPAT_LAHIR_SISWA, #AYAH_TANGGAL_LAHIR_SISWA").removeClass('required');
            } else {
            $("#AYAH_TEMPAT_LAHIR_SISWA, #AYAH_TANGGAL_LAHIR_SISWA").addClass('required');
            }
            resetLabel("AYAH_TEMPAT_LAHIR_SISWA", "Tempat Lahir", statusReq);
            resetLabel("AYAH_TANGGAL_LAHIR_SISWA", "Tanggal Lahir", statusReq);
            });
            $(".js-source-states-MASUK_JENJANG_SISWA").on("change", "", function(){
            var data_jenjang = $(this).select2("data");
            $("#MASUK_TINGKAT_SISWA").removeAttr("disabled");
            get_list_jenjang_siswa(data_jenjang.id);
            });
            });
            function resetLabel(id, label, statusReq) {
            $("#" + id).parent().parent().find(".control-label").html("<strong>" + label + (statusReq ? " *" : "") + "</strong>");
            }

            function get_list_jenjang_siswa(jenjang) {
            create_splash("Sedang mengambil data tingkat");
            var success = function (data) {
            $("#MASUK_TINGKAT_SISWA").html(" ");
            $.each(data, function(index, detail) {
            $("#MASUK_TINGKAT_SISWA").append("<option value='" + detail.NAMA_TINGK + "'>" + detail.KETERANGAN_TINGK + "</option>");
            });
            remove_splash();
            };
            create_ajax('<?php echo site_url('psb/calon_siswa/list_tingkat_jenjang'); ?>', 'jenjang=' + jenjang, success);
            }

            function action_save_<?php echo $name_function; ?>(id) {
            if (!$('#validasi').is(':checked')) {
            create_homer_error('Silahkan centang validasi data terlebih dahulu.');
            } else {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
            if (data.status) {
            if ($("#from_upload").val() == 1) {
    <?php if ($mode_edit) { ?> var ID_SISWA = $('#ID_SISWA').val();
    <?php } else { ?> var ID_SISWA = data.status;
    <?php } ?>
            simpan_foto_siswa(ID_SISWA);
            } else {
            remove_splash();
            create_swal_success('', 'Data berhasil disimpan. Halaman ini akan dimuat ulang.');
            reaload_page();
            }
            } else {
    <?php if ($mode_edit) { ?>
                if ($("#from_upload").val() == 1) {
                var ID_SISWA = $('#ID_SISWA').val();
                simpan_foto_siswa(ID_SISWA);
                } else {
    <?php } ?>
            remove_splash();
            create_homer_error('Gagal menyimpan data. ' + data.msg);
    <?php if ($mode_edit) { ?>
                }
    <?php } ?>
            }
            };
    <?php if ($mode_edit) { ?>create_form_ajax('<?php echo site_url('psb/calon_siswa/ajax_update') ?>', id, success, message);
    <?php } else { ?>create_form_ajax('<?php echo site_url('psb/calon_siswa/ajax_add') ?>', id, success, message);
    <?php } ?>
            }

            return false;
            }

            function simpan_foto_siswa(ID_SISWA) {
            var data = {
            'ID_SISWA': ID_SISWA
            };
            var success = function (data, status) {
            remove_splash();
            if (data.status) {
            create_swal_success('', 'Data berhasil disimpan dan foto ' + data.msg + '. Halaman ini akan dimuat ulang.');
            } else {
            create_swal_error('', 'Data berhasil disimpan dan foto ' + data.msg + '.');
            }
            reaload_page();
            };
            create_ajax_file('<?php echo site_url('psb/calon_siswa/save_photo'); ?>', 'UPLOAD_FOTO_SISWA', data, success);
            }

            function reaload_page() {
            setTimeout(function () {
            window.location.reload();
            }, 1500);
            }

            function check_data(name) {
            var tag = $('#' + name);
            var val_data = tag.val();
            var success = function (data) {
            if (data.status) {
            tag.removeClass('error');
            tag.parent().prev('.control-label').removeClass('text-danger');
            } else {
            tag.addClass('error');
            tag.parent().prev('control-label').addClass('text-danger');
            create_homer_error('NIK sudah digunakan. Periksa kembali masukan Anda.');
            }
            };
            create_ajax('<?php echo site_url('psb/calon_siswa/check_data'); ?>', 'name=' + name + '&value=' + val_data, success);
            }
<?php } ?>
    </script>

<?php //}  ?>
</form>
</div>