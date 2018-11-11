<?php
$title = $JENIS;
$subtitle = "Form ".$JENIS;

$this->generate->generate_panel_content("Data " . $title, $subtitle);

$id_form = 'form-transaksi';
$name_function = 'transaksi';
echo $this->generate->form_open($id_form, $name_function);
?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form <?php echo $JENIS; ?>
                </div>
                <div class="panel-body">
                    <?php $this->generate->input_text('Diterima dari', array('name' => 'NAMA_TJ'), TRUE, 6); ?>
                    <?php $this->generate->input_hidden('NOMINAL_TJ', ''); ?>
                    <?php $this->generate->input_select2('Jenis Kelompok', array('name' => 'KELOMPOK_TJ', 'url' => site_url('tuk/jenis_kelompok/auto_complete_'.$JENIS)), TRUE, 4, FALSE, NULL); ?>
                    <?php $this->generate->input_text('Nominal', array('name' => 'TEMP_NOMINAL_TJ', 'onblur' => 'display_nominal(this);', 'onclick' => 'show_nominal(this);'), TRUE, 3); ?>
                    <?php $this->generate->input_text('Keterangan', array('name' => 'KETERANGAN_TJ'), TRUE, 8); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel hbgblue">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="checkbox checkbox-success">
                                <input type="checkbox" name="validasi" id="validasi">
                                <label> Saya menyetujui bahwa data yang saya masukan adalah benar</label>
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
</div>
</form>

<script type="text/javascript">
    var nominal = null;

    $(function () {

    });

    function action_save_<?php echo $name_function; ?>(id) {
        if (!$('#validasi').is(':checked')) {
            create_homer_error('Silahkan centang validasi data terlebih dahulu.');
        } else {
            var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
            var success = function (data) {
                if (data.status) {
                    create_homer_success('Berhasil menyimpan data. Halaman ini akan dimuat ulang.');
                    
                    window.open('<?php echo site_url('tuk/cetak_kwitansi/cetak_individu'); ?>/' + data.status, '_blank');
                    
                    reload_window();
                } else {
                    create_homer_error('Gagal menyimpan data');
                }
                
                remove_splash();
            };
            create_form_ajax('<?php echo site_url('tuk/pemasukan/ajax_add') ?>', id, success, message);
        }

        return false;
    }
    
    function display_nominal(that) {
        nominal = $(that).val();
        
        $("#NOMINAL_TJ").val(nominal);
        $(that).val(formattedIDR(nominal));
    }
    
    function show_nominal(that) {
        $(that).val(nominal);
    }
</script>