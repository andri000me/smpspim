<?php
$title = 'Pembaharuan data aplikasi';
$subtitle = "Form untuk memperbaharui data aplikasi";

$id_form = 'form-aplikasi';
$name_function = 'aplikasi';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel">
    <?php
    echo $this->generate->form_open($id_form, $name_function);
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hblue">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    FORM PEMBAHARUAN
                </div>
                <div class="panel-body">
                    <?php
                    foreach ($data as $detail) {
                        if(
                                $detail->ID_PENGATURAN == 'ketua_p3h_banin' || 
                                $detail->ID_PENGATURAN == 'ketua_p3h_banat' || 
                                $detail->ID_PENGATURAN == 'ketua_pu' || 
                                $detail->ID_PENGATURAN == 'ketua_komdis'
                        ) 
                            $this->generate->input_select2(str_replace("_", " ", $detail->ID_PENGATURAN), array('name' => $detail->ID_PENGATURAN, 'url' => site_url('master_data/pegawai/auto_complete')), TRUE, 5, FALSE, $pegawai->get_name($detail->NAMA_PENGATURAN) == NULL ? NULL : array('id' => $detail->NAMA_PENGATURAN, 'text' => $pegawai->get_name($detail->NAMA_PENGATURAN)));
                        else
                            $this->generate->input_text(str_replace("_", " ", $detail->ID_PENGATURAN), array('name' => $detail->ID_PENGATURAN, 'id' => $detail->ID_PENGATURAN, 'value' => $detail->NAMA_PENGATURAN), TRUE, 8);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hbgblue">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2 col-md-offset-10">
                            <button type="submit" class="btn w-xs btn-primary" id="simpan">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        function action_save_<?php echo $name_function; ?>(id) {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
                remove_splash();
                
                create_swal_success('', 'Data berhasil disimpan.');

                reload_page();
            };
            var action = function(isConfirm) {
                if(isConfirm) 
                    create_form_ajax('<?php echo site_url('master_data/aplikasi/simpan') ?>', id, success, message);
            };

            create_swal_option("Apakah Anda yakin?", "Perubahan pada form ini akan mempengaruhi seluruh aplikasi. Pastikan perubahan adalah benar.", action);

            return false;
        }

        function reload_page() {
            setTimeout(function () {
                window.location.reload();
            }, 1500);
        }
        
    </script>
</form>
</div>