<?php
$title = 'Pembaharuan data siswa';
$subtitle = "Form untuk memperbaharui data siswa";
$mode_edit = TRUE;
$id_form = 'form-siswa';
$name_function = 'siswa';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel">
    <?php
    echo $this->generate->form_open($id_form, $name_function);
    $this->generate->input_hidden('ID_SISWA', $data->ID_SISWA);
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1"> Data Pribadi</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-11"> Data Tambahan</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> Asal Sekolah</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3"> Kontak</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-4"> Data Ayah</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-5"> Data Ibu</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-6"> Data Wali</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-7"> Data Orangtua</a></li>
                    <li class=""><a href="#" style="padding: unset;padding-top: 5px"> <button type="submit" class="btn w-xs btn-primary btn-sm" id="simpan">Simpan</button></a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                            <?php $this->generate->input_text('Nomor KK', array('name' => 'KK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->KK_SISWA : '', 'value' => $mode_edit ? $data->KK_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_text('NIK', array('name' => 'NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->NIK_SISWA : '', 'value' => $mode_edit ? $data->NIK_SISWA : '', 'id' => 'NIK_SISWA', 'onchange' => 'return check_data(\'NIK_SISWA\');'), TRUE, 4); ?>
                            <?php $this->generate->input_text('NISN', array('name' => 'NISN_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->NISN_SISWA : '', 'value' => $mode_edit ? $data->NISN_SISWA : '', 'id' => 'NISN_SISWA', 'onchange' => 'return check_data(\'NISN_SISWA\');'), FALSE, 4); ?>
                            <?php $this->generate->input_text('Nama Lengkap', array('name' => 'NAMA_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->NAMA_SISWA : ''), TRUE); ?>
                            <?php $this->generate->input_select2('Jenis Kelamin', array('name' => 'JK_SISWA', 'url' => site_url('akademik/siswa/ac_jk')), TRUE, 3, FALSE, $mode_edit ? array('id' => $data->JK_SISWA, 'text' => $data->NAMA_JK) : NULL); ?>
                            <?php $this->generate->input_text('Tempat Lahir', array('name' => 'TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->TEMPAT_LAHIR_SISWA : ''), TRUE, 4); ?>
                            <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'TANGGAL_LAHIR_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->TANGGAL_LAHIR_SISWA) : ''), TRUE, 2); ?>
                            <?php $this->generate->input_select2('Pondok Siswa', array('name' => 'PONDOK_SISWA', 'url' => site_url('master_data/pondok_siswa/auto_complete')), FALSE, 8, FALSE, $mode_edit ? array('id' => $data->PONDOK_SISWA, 'text' => $data->NAMA_PONDOK_MPS) : '', '<div class="col-sm-1"><a href="' . site_url('master_data/pondok_siswa') . '" class="btn btn-primary" target="_blank"><i class="fa fa-plus"></i></a></div>'); ?>
                        </div>
                    </div>
                    <div id="tab-11" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('Nama Panggilan', array('name' => 'PANGGILAN_SISWA', 'maxlength' => 50, 'value' => $mode_edit ? $data->PANGGILAN_SISWA : ''), FALSE, 4); ?>  
                            <?php $this->generate->input_select2('Suku', array('name' => 'SUKU_SISWA', 'url' => site_url('akademik/siswa/ac_suku')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->SUKU_SISWA, 'text' => $data->NAMA_SUKU) : NULL); ?>
                            <?php $this->generate->input_select2('Agama', array('name' => 'AGAMA_SISWA', 'url' => site_url('akademik/siswa/ac_agama')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AGAMA_SISWA, 'text' => $data->NAMA_AGAMA) : NULL); ?>
                            <?php $this->generate->input_select2('Kondisi', array('name' => 'KONDISI_SISWA', 'url' => site_url('akademik/siswa/ac_kondisi')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->KONDISI_SISWA, 'text' => $data->NAMA_KONDISI) : NULL); ?>
                            <?php $this->generate->input_select2('Kewarganegaraan', array('name' => 'WARGA_SISWA', 'url' => site_url('akademik/siswa/ac_warga')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WARGA_SISWA, 'text' => $data->NAMA_WARGA) : NULL); ?>
                            <?php $this->generate->input_select2('Tempat Tinggal', array('name' => 'TEMPAT_TINGGAL_SISWA', 'url' => site_url('akademik/siswa/ac_tinggal')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->TEMPAT_TINGGAL_SISWA, 'text' => $data->NAMA_TEMTING) : NULL); ?>
                            <?php $this->generate->input_text('Berat Badan', array('name' => 'BERAT_SISWA', 'placeholder' => 'kg', 'maxlength' => 3, 'value' => $mode_edit ? $data->BERAT_SISWA : ''), FALSE, 2); ?>
                            <?php $this->generate->input_text('Tinggi Badan', array('name' => 'TINGGI_SISWA', 'placeholder' => 'cm', 'maxlength' => 3, 'value' => $mode_edit ? $data->TINGGI_SISWA : ''), FALSE, 2); ?>
                            <?php $this->generate->input_select2('Golongan Darah', array('name' => 'GOL_DARAH_SISWA', 'url' => site_url('akademik/siswa/ac_darah')), FALSE, 2, FALSE, $mode_edit ? array('id' => $data->GOL_DARAH_SISWA, 'text' => $data->NAMA_DARAH) : NULL); ?>
                            <?php $this->generate->input_text('Riwayat Kesehatan', array('name' => 'RIWAYAT_KESEHATAN_SISWA', 'maxlength' => 500, 'value' => $mode_edit ? $data->RIWAYAT_KESEHATAN_SISWA : ''), FALSE, 9); ?>
                            <?php $this->generate->input_text('Anak ke-', array('name' => 'ANAK_KE_SISWA', 'maxlength' => 1, 'value' => $mode_edit ? $data->ANAK_KE_SISWA : ''), FALSE, 1); ?>
                            <?php $this->generate->input_text('Jumlah Saudara', array('name' => 'JUMLAH_SDR_SISWA', 'maxlength' => 1, 'value' => $mode_edit ? $data->JUMLAH_SDR_SISWA : ''), FALSE, 1); ?>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_select2('Asal Sekolah', array('name' => 'ASAL_SEKOLAH_SISWA', 'url' => site_url('akademik/siswa/ac_asal_sekolah')), FALSE, 8, FALSE, $mode_edit ? array('id' => $data->ASAL_SEKOLAH_SISWA, 'text' => $data->NAMA_AS) : array('id' => '1', 'text' => 'Belum sekolah'), '<div class="col-sm-1"><a href="' . site_url('master_data/asal_sekolah') . '" class="btn btn-primary" target="_blank"><i class="fa fa-plus"></i></a></div>'); ?>
                            <?php $this->generate->input_text('No. Ijasah', array('name' => 'NO_IJASAH_SISWA', 'maxlength' => 40, 'value' => $mode_edit ? $data->NO_IJASAH_SISWA : ''), FALSE, 3); ?>
                            <?php $this->generate->input_date('Tanggal Ijasah', array('name' => 'TANGGAL_IJASAH_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->TANGGAL_IJASAH_SISWA) : ''), FALSE, 2); ?>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('Alamat', array('name' => 'ALAMAT_SISWA', 'maxlength' => 250, 'value' => $mode_edit ? $data->ALAMAT_SISWA : ''), TRUE, 9); ?>
                            <?php $this->generate->input_select2('Kecamatan', array('name' => 'KECAMATAN_SISWA', 'url' => site_url('akademik/siswa/ac_kecamatan')), TRUE, 6, TRUE, $mode_edit ? array('id' => $data->KECAMATAN_SISWA, 'text' => $data->NAMA_KEC . ', ' . $data->NAMA_KAB . ', ' . $data->NAMA_PROV) : NULL); ?>
                            <?php $this->generate->input_text('Kode Pos', array('name' => 'KODE_POS_SISWA', 'maxlength' => 5, 'value' => $mode_edit ? $data->KODE_POS_SISWA : ''), FALSE, 2); ?>
                            <?php $this->generate->input_text('No. HP', array('name' => 'NOHP_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->NOHP_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_text('Email', array('name' => 'EMAIL_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->EMAIL_SISWA : ''), FALSE, 4); ?>
                        </div>
                    </div>
                    <div id="tab-4" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('NIK', array('name' => 'AYAH_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->AYAH_NIK_SISWA : '', 'id' => 'AYAH_NIK_SISWA'), FALSE, 4); ?>
                            <?php $this->generate->input_text('Nama', array('name' => 'AYAH_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->AYAH_NAMA_SISWA : ''), TRUE); ?>
                            <?php $this->generate->input_select2('Status Hidup', array('name' => 'AYAH_HIDUP_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_hidup')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AYAH_HIDUP_SISWA, 'text' => $data->NAMA_SO_AYAH) : NULL); ?>
                            <?php $this->generate->input_text('Tempat Lahir', array('name' => 'AYAH_TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->AYAH_TEMPAT_LAHIR_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'AYAH_TANGGAL_LAHIR_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->AYAH_TANGGAL_LAHIR_SISWA) : ''), FALSE, 2); ?>
                            <?php $this->generate->input_select2('Pendidikan', array('name' => 'AYAH_PENDIDIKAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AYAH_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_AYAH) : NULL); ?>
                            <?php $this->generate->input_select2('Pekerjaan', array('name' => 'AYAH_PEKERJAAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->AYAH_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_AYAH) : NULL); ?>
                        </div>  
                    </div>
                    <div id="tab-5" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('NIK', array('name' => 'IBU_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->IBU_NIK_SISWA : '', 'id' => 'IBU_NIK_SISWA'), FALSE, 4); ?>
                            <?php $this->generate->input_text('Nama', array('name' => 'IBU_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->IBU_NAMA_SISWA : ''), TRUE); ?>
                            <?php $this->generate->input_select2('Status Hidup', array('name' => 'IBU_HIDUP_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_hidup')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->IBU_HIDUP_SISWA, 'text' => $data->NAMA_SO_IBU) : NULL); ?>
                            <?php $this->generate->input_text('Tempat Lahir', array('name' => 'IBU_TEMPAT_LAHIR_SISWA', 'maxlength' => 150, 'value' => $mode_edit ? $data->IBU_TEMPAT_LAHIR_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'IBU_TANGGAL_LAHIR_SISWA', 'value' => $mode_edit ? $this->date_format->to_view($data->IBU_TANGGAL_LAHIR_SISWA) : ''), FALSE, 2); ?>
                            <?php $this->generate->input_select2('Pendidikan', array('name' => 'IBU_PENDIDIKAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->IBU_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_IBU) : NULL); ?>
                            <?php $this->generate->input_select2('Pekerjaan', array('name' => 'IBU_PEKERJAAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->IBU_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_IBU) : NULL); ?>
                        </div>
                    </div>
                    <div id="tab-6" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('NIK', array('name' => 'WALI_NIK_SISWA', 'maxlength' => 16, 'value' => $mode_edit ? $data->WALI_NIK_SISWA : '', 'id' => 'WALI_NIK_SISWA', 'onchange' => 'return check_data(\'WALI_NIK_SISWA\');'), FALSE, 4); ?>
                            <?php $this->generate->input_text('Nama', array('name' => 'WALI_NAMA_SISWA', 'maxlength' => 200, 'value' => $mode_edit ? $data->WALI_NAMA_SISWA : ''), FALSE); ?>
                            <?php $this->generate->input_select2('Hubungan', array('name' => 'WALI_HUBUNGAN_SISWA', 'url' => site_url('akademik/siswa/ac_wali_hubungan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WALI_HUBUNGAN_SISWA, 'text' => $data->NAMA_HUB) : NULL); ?>
                            <?php $this->generate->input_select2('Pendidikan', array('name' => 'WALI_PENDIDIKAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pendidikan')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->WALI_PENDIDIKAN_SISWA, 'text' => $data->NAMA_JP_WALI) : NULL); ?>
                            <?php $this->generate->input_select2('Pekerjaan', array('name' => 'WALI_PEKERJAAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_pekerjaan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->WALI_PEKERJAAN_SISWA, 'text' => $data->NAMA_JENPEK_WALI) : NULL); ?>
                        </div>
                    </div>
                    <div id="tab-7" class="tab-pane ">
                        <div class="panel-body">
                            <?php $this->generate->input_text('Alamat', array('name' => 'ORTU_ALAMAT_SISWA', 'maxlength' => 250, 'value' => $mode_edit ? $data->ORTU_ALAMAT_SISWA : ''), TRUE, 9); ?>
                            <?php $this->generate->input_select2('Kecamatan', array('name' => 'ORTU_KECAMATAN_SISWA', 'url' => site_url('akademik/siswa/ac_kecamatan')), TRUE, 6, TRUE, $mode_edit ? array('id' => $data->ORTU_KECAMATAN_SISWA, 'text' => $data->NAMA_KEC_ORTU . ', ' . $data->NAMA_KAB_ORTU . ', ' . $data->NAMA_PROV_ORTU) : NULL); ?>
                            <?php $this->generate->input_select2('Penghasilan', array('name' => 'ORTU_PENGHASILAN_SISWA', 'url' => site_url('akademik/siswa/ac_ortu_penghasilan')), FALSE, 4, FALSE, $mode_edit ? array('id' => $data->ORTU_PENGHASILAN_SISWA, 'text' => $data->NAMA_HASIL) : NULL); ?>
                            <?php $this->generate->input_text('No. HP (1)', array('name' => 'ORTU_NOHP1_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP1_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_text('No. HP (2)', array('name' => 'ORTU_NOHP2_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP2_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_text('No. HP (3)', array('name' => 'ORTU_NOHP3_SISWA', 'maxlength' => 12, 'value' => $mode_edit ? $data->ORTU_NOHP3_SISWA : ''), FALSE, 4); ?>
                            <?php $this->generate->input_text('Email', array('name' => 'ORTU_EMAIL_SISWA', 'maxlength' => 100, 'value' => $mode_edit ? $data->ORTU_EMAIL_SISWA : ''), FALSE, 4); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--    <div class="row">
            <div class="col-md-12">
                <div class="hpanel hbgblue">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="checkbox checkbox-success">
                                    <input type="checkbox" name="validasi" id="validasi" <?php if ($pop_up) { ?>checked="true"<?php } ?>>
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
        </div>-->

    <script type="text/javascript">

        $(function () {
<?php if ($pop_up) { ?>
                $('body').addClass('hide-sidebar');
                $("#header").hide();
                $("#wrapper").attr('style', 'top:unset');
<?php } ?>

            $("#UPLOAD_FOTO_SISWA").change(function () {
                $('#from_upload').val(1);
            });
        });

        function action_save_<?php echo $name_function; ?>(id) {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
                if (data.status) {
                    create_swal_success('', 'Data berhasil disimpan. Halaman ini akan dimuat ulang.');
                    reaload_page();
                } else {
                    create_homer_error('Gagal menyimpan data. ' + data.msg);
                }

                remove_splash();
            };

            create_form_ajax('<?php echo site_url('akademik/siswa/ajax_update') ?>', id, success, message);

            return false;
        }

        function reaload_page() {
            setTimeout(function () {
<?php if ($pop_up) { ?>
                    window.close();
<?php } else { ?>
                    window.location.reload();
<?php } ?>
            }, <?php if ($pop_up) { ?>500<?php } else { ?>1500<?php } ?>);
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
                            create_homer_error('Data sudah digunakan. Periksa kembali masukan Anda.');
                        }
                    };

                    create_ajax('<?php echo site_url('akademik/siswa/check_data'); ?>', 'name=' + name + '&value=' + val_data, success);
                }
    </script>

</form>
</div>