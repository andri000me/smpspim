<?php
$title = 'Kwitansi';
$subtitle = "Form kwitansi";

$this->generate->generate_panel_content($title, $subtitle);
?>
<form method="post" action="<?php echo site_url('tuk/cetak_kwitansi/cetak'); ?>" class="form-horizontal">
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        Form Kwitansi
                    </div>
                    <div class="panel-body">
                        <?php $this->generate->input_hidden('NOMINAL', ''); ?>
                        <?php $this->generate->input_select2('Kelas', array('name' => 'KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 7, FALSE, NULL); ?>
                        <?php $this->generate->input_select2('Siswa', array('name' => 'SISWA', 'url' => site_url('akademik/siswa/auto_complete')), FALSE, 7, FALSE, NULL); ?>
                        <?php $this->generate->input_text('Nominal', array('name' => 'TEMP_NOMINAL', 'onblur' => 'display_nominal(this);', 'onclick' => 'show_nominal(this);'), TRUE, 3); ?>
                        <?php $this->generate->input_text('Untuk pembayaran', array('name' => 'KETERANGAN'), TRUE, 9); ?>
                        <?php $this->generate->input_select2('Penerima', array('name' => 'PENERIMA', 'url' => site_url('master_data/pegawai/auto_complete')), TRUE, 5, FALSE, NULL); ?>
                        <div class="row">
                            <div class="col-md-3 col-md-offset-2">
                                <button type="submit" class="btn w-xs btn-primary" id="simpan"><i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Kwitansi</button>
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
    
    function display_nominal(that) {
        nominal = $(that).val();
        
        $("#NOMINAL").val(nominal);
        $(that).val(formattedIDR(nominal));
    }
    
    function show_nominal(that) {
        $(that).val(nominal);
    }
</script>