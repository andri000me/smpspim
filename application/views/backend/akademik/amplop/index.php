<?php
$title = 'Cetak Amplop';
$subtitle = "Form cetak amplop";

$this->generate->generate_panel_content($title, $subtitle);
?>
<div class="content animate-panel">
    <form method="post" action="<?php echo site_url('akademik/amplop/cetak'); ?>" class="form-horizontal" target="_blank">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel collapsed">
                    <div class="panel-heading hbuilt">
                        Form Cetak Amplop Umum
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
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
    </form>
    <form method="post" action="<?php echo site_url('akademik/amplop/cetak_komdis'); ?>" class="form-horizontal" target="_blank">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel collapsed">
                    <div class="panel-heading hbuilt">
                        Form Cetak Surat Keluar dari Komdis
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php $this->generate->input_dropdown('Jenis Tindakan', 'JENIS_TINDAKAN', $jenis_tindakan, TRUE, 4); ?>
                        <?php $this->generate->input_text('Nomor Surat Mulai', array('name' => 'MULAI_NOMOR_SURAT', 'id' => 'MULAI_NOMOR_SURAT'), TRUE, 2); ?>
                        <?php $this->generate->input_text('Nomor Surat Komdis', array('name' => 'NOMOR_SURAT', 'id' => 'NOMOR_SURAT'), TRUE, 2); ?>
                        <?php $this->generate->input_date('Tanggal', array('name' => 'TANGGAL_SURAT', 'id' => 'TANGGAL_SURAT'), TRUE, 3); ?>
                        <?php $this->generate->input_time('Jam', array('name' => 'JAM_SURAT', 'id' => 'JAM_SURAT'), TRUE, 3); ?>
                        <?php $this->generate->input_text('Tempat', array('name' => 'TEMPAT_SURAT', 'id' => 'TEMPAT_SURAT', 'value' => 'Gedung Perguruan Islam Mathali\'ul Falah'), TRUE, 7); ?>
                        <?php $this->generate->input_dropdown('Nama TTD', 'TTD_SURAT', $pd, TRUE, 4); ?>
                        <div class="row">
                            <div class="col-md-3 col-md-offset-2">
                                <button type="submit" class="btn w-xs btn-primary" id="simpan"><i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Surat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>