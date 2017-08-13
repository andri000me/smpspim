<?php
$title = 'Cetak Amplop';
$subtitle = "Form cetak amplop";

$this->generate->generate_panel_content($title, $subtitle);
?>
<form method="post" action="<?php echo site_url('akademik/amplop/cetak'); ?>" class="form-horizontal">
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        Form Cetak Amplop
                    </div>
                    <div class="panel-body">
                        <?php // $this->generate->input_text('Nomor Awal', array('name' => 'NOMOR_AWAL'), TRUE, 2); ?>
                        <?php // $this->generate->input_text('Nomor Akhir', array('name' => 'NOMOR_AKHIR'), TRUE, 2); ?>
                        <?php $this->generate->input_text('Hal', array('name' => 'HAL'), TRUE, 9); ?>
                        <?php $this->generate->input_select2('Kelas', array('name' => 'KELAS', 'url' => site_url('akademik/kelas/auto_complete')), TRUE, 7, FALSE, NULL); ?>
                        <div class="row">
                            <div class="col-md-3 col-md-offset-2">
                                <button type="submit" class="btn w-xs btn-primary" id="simpan"><i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Surat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>