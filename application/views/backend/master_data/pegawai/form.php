<?php
if ($data === NULL)
    $mode_edit = FALSE;
else
    $mode_edit = TRUE;

if ($data !== NULL and isset($mode_view))
    $mode_view = TRUE;
else
    $mode_view = FALSE;

$mode_edit ? $title = 'Perbaharui Data Pegawai' : $title = 'Tambah Pegawai';
$mode_view ? $title = 'Lihat Data Pegawai' : $title = $title;
$subtitle = "Form untuk pegawai";

$id_form = 'form-pegawai';
$name_function = 'pegawai';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel">
    <?php
    echo $this->generate->form_open($id_form, $name_function);
    if ($mode_edit)
        $this->generate->input_hidden('ID_PEG', $data->ID_PEG);
    ?>
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
                        <?php $this->generate->input_text('NIP', array('name' => 'NIP_PEG', 'maxlength' => 16, 'value' => $mode_edit ? $data->NIP_PEG : '', 'value' => $mode_edit ? $data->NIP_PEG : '', 'id' => 'NIP_PEG', 'onchange' => 'return check_data(\'NIP_PEG\');'), TRUE, 3); ?>
                        <?php $this->generate->input_text('NIK', array('name' => 'NIK_PEG', 'maxlength' => 16, 'value' => $mode_edit ? $data->NIK_PEG : '', 'value' => $mode_edit ? $data->NIK_PEG : '', 'id' => 'NIK_PEG', 'onchange' => 'return check_data(\'NIK_PEG\');'), TRUE, 4); ?>
                        <?php $this->generate->input_text('KK', array('name' => 'KK_PEG', 'maxlength' => 16, 'value' => $mode_edit ? $data->KK_PEG : '', 'value' => $mode_edit ? $data->KK_PEG : '', 'id' => 'KK_PEG', 'onchange' => 'return check_data(\'KK_PEG\');'), TRUE, 4); ?>
                        <?php $this->generate->input_text('Nama Lengkap', array('name' => 'NAMA_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->NAMA_PEG : ''), TRUE); ?>
                        <?php $this->generate->input_text('Gelar Awal', array('name' => 'GELAR_AWAL_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->GELAR_AWAL_PEG : ''), FALSE, 2); ?>
                        <?php $this->generate->input_text('Gelar Akhir', array('name' => 'GELAR_AKHIR_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->GELAR_AKHIR_PEG : ''), FALSE, 3); ?>
                        <?php $this->generate->input_text('Panggilan', array('name' => 'PANGGILAN_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->PANGGILAN_PEG : ''), FALSE, 4); ?>
                        <?php $this->generate->input_text('Ibu Kandung', array('name' => 'IBU_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->IBU_PEG : ''), FALSE, 4); ?>
                        <?php
                        $this->generate->input_dropdown('Jabatan', 'GURU_PEG', array(
                            array('id' => '1', 'text' => 'GURU', 'selected' => $mode_edit ? ($data->GURU_PEG == '1' ? TRUE : FALSE) : FALSE),
                            array('id' => '0', 'text' => 'NON-GURU', 'selected' => $mode_edit ? ($data->GURU_PEG == '0' ? TRUE : FALSE) : FALSE),
                                ), FALSE, 3);
                        ?>
                        <?php $this->generate->input_select2('Jenis Kelamin', array('name' => 'JK_PEG', 'url' => site_url('master_data/jk/auto_complete')), TRUE, 3, FALSE, $mode_edit ? array('id' => $data->JK_PEG, 'text' => $data->NAMA_JK) : NULL); ?>
                        <?php $this->generate->input_text('Tempat Lahir', array('name' => 'TEMPAT_LAHIR_PEG', 'maxlength' => 150, 'value' => $mode_edit ? $data->TEMPAT_LAHIR_PEG : ''), TRUE, 4); ?>
                        <?php $this->generate->input_date('Tanggal Lahir', array('name' => 'TANGGAL_LAHIR_PEG', 'value' => $mode_edit ? $this->date_format->to_view($data->TANGGAL_LAHIR_PEG) : ''), TRUE, 2); ?>
                        <?php $this->generate->input_select2('Suku', array('name' => 'SUKU_PEG', 'url' => site_url('master_data/suku/auto_complete')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->SUKU_PEG, 'text' => $data->NAMA_SUKU) : NULL); ?>
                        <?php $this->generate->input_select2('Agama', array('name' => 'AGAMA_PEG', 'url' => site_url('master_data/agama/auto_complete')), FALSE, 3, FALSE, $mode_edit ? array('id' => $data->AGAMA_PEG, 'text' => $data->NAMA_AGAMA) : NULL); ?>
                        <?php
                        $this->generate->input_dropdown('Status Menikah', 'MENIKAH_PEG', array(
                            array('id' => 'SUDAH', 'text' => 'BELUM MENIKAH', 'selected' => $mode_edit ? ($data->MENIKAH_PEG == 'SUDAH' ? TRUE : FALSE) : FALSE),
                            array('id' => 'BELUM', 'text' => 'SUDAH MENIKAH', 'selected' => $mode_edit ? ($data->MENIKAH_PEG == 'BELUM' ? TRUE : FALSE) : FALSE),
                                ), FALSE, 3);
                        ?>
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
                        FOTO
                    </div>
                    <div class="panel-body">
                        <?php $this->generate->input_photo('FOTO_PEG'); ?>
                        <?php $this->generate->content_webcam(); ?>
                        <?php
                        if ($mode_edit && ($data->FOTO_PEG != NULL)) {
                            echo '<hr><div class="row"><div class="col-md-12 text-center"><img src="' . base_url('files/pegawai/' . $data->FOTO_PEG) . '" width="400" /></div></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel hblue">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        KONTAK
                    </div>
                    <div class="panel-body">
                        <?php $this->generate->input_text('Alamat', array('name' => 'ALAMAT_PEG', 'maxlength' => 250, 'value' => $mode_edit ? $data->ALAMAT_PEG : ''), TRUE, 9); ?>
                        <?php $this->generate->input_select2('Kecamatan', array('name' => 'KECAMATAN_PEG', 'url' => site_url('master_data/kecamatan/auto_complete')), TRUE, 6, TRUE, $mode_edit ? array('id' => $data->KECAMATAN_PEG, 'text' => $data->NAMA_KEC . ', ' . $data->NAMA_KAB . ', ' . $data->NAMA_PROV) : NULL); ?>
                        <?php $this->generate->input_text('No. HP 1', array('name' => 'NOHP_PEG', 'maxlength' => 12, 'value' => $mode_edit ? $data->NOHP_PEG : ''), TRUE, 4); ?>
                        <?php $this->generate->input_text('No. HP 2', array('name' => 'NOHP1_PEG', 'maxlength' => 12, 'value' => $mode_edit ? $data->NOHP1_PEG : ''), TRUE, 4); ?>
                        <?php $this->generate->input_text('Email', array('name' => 'EMAIL_PEG', 'maxlength' => 100, 'value' => $mode_edit ? $data->EMAIL_PEG : ''), FALSE, 4); ?>
                    </div>
                </div>
            </div>
        </div>
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
        $("#UPLOAD_FOTO_PEG").change(function(){
            $('#from_upload').val(1);
        });
    });
            
    function action_save_<?php echo $name_function; ?>(id) {
        if (!$('#validasi').is(':checked')) {
            create_homer_error('Silahkan centang validasi data terlebih dahulu.');
        } else {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
                if (data.status) {
                    if ($("#from_upload").val() == 1) {
                        <?php if ($mode_edit) { ?> var ID_PEG = $('#ID_PEG').val();
                        <?php } else { ?> var ID_PEG = data.status;
                        <?php } ?>
                        simpan_foto_pegawai(ID_PEG);
                    } else {
                        remove_splash();
                        create_swal_success('', 'Data berhasil disimpan. Halaman ini akan dimuat ulang.');
                        reaload_page();
                    }
                } else {
                    <?php if ($mode_edit) { ?>
                    if ($("#from_upload").val() == 1) {
                        var ID_PEG = $('#ID_PEG').val();
                        simpan_foto_pegawai(ID_PEG);
                    } else {
                        <?php } ?>
                        remove_splash();
                        create_homer_error('Gagal menyimpan data. ' + data.msg);
                        <?php if ($mode_edit) { ?>
                    }
                    <?php } ?>
                }
            };
    <?php if ($mode_edit) { ?>create_form_ajax('<?php echo site_url('master_data/pegawai/ajax_update') ?>', id, success, message);
    <?php } else { ?>create_form_ajax('<?php echo site_url('master_data/pegawai/ajax_add') ?>', id, success, message);
    <?php } ?>
        }
        
        return false;
    }

    function simpan_foto_pegawai(ID_PEG) {
        var data = {
            'ID_PEG': ID_PEG
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
        
        create_ajax_file('<?php echo site_url('master_data/pegawai/save_photo'); ?>', 'UPLOAD_FOTO_PEG', data, success);
    }

    function reaload_page() {
        setTimeout(function () {
        window.location.reload();
<?php // if (!$mode_edit) {           ?>window.location.reload();<?php //}           ?>
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
                create_homer_error('Data tersebut sudah digunakan. Periksa kembali masukan Anda.');
            }
        };
        
        create_ajax('<?php echo site_url('master_data/pegawai/check_data'); ?>', 'name=' + name + '&value=' + val_data, success);
    }
    <?php } ?>
        </script>
</form>
</div>