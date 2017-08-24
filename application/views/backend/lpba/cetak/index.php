<?php
$this->generate->generate_panel_content("Cetak Syahadah", "Cetak syahadah dauroh");
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form Cetak Syahadah
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <?php $this->generate->input_select2('Tahun Ajaran', array('name' => 'TA_FILTER', 'url' => site_url('master_data/tahun_ajaran/auto_complete')), TRUE, 4, FALSE, NULL); ?>
                        <?php $this->generate->input_select2('Kelas', array('name' => 'KELAS_FILTER', 'url' => site_url('akademik/kelas/auto_complete')), TRUE, 7, FALSE, NULL); ?>
                        <?php $this->generate->input_text('Tanggal Hijriyah', array('name' => 'TANGGAL_HIJRIYAH', 'id' => 'TANGGAL_HIJRIYAH'), TRUE, 4); ?>
                        <?php $this->generate->input_text('Ketua Panitia', array('name' => 'KETUA_PANITIA', 'id' => 'KETUA_PANITIA'), TRUE, 5); ?>
                        <div class="form-group">
                            <div class="col-md-2 col-md-offset-2">
                                <button type="button" class="btn btn-save btn-primary btn-block" onclick="cetak();"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    var KELAS_FILTER = null;
    var ID_TA_FILTER = '<?php echo $this->session->userdata('ID_TA_ACTIVE'); ?>';
    var NAMA_TA_FILTER = '<?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?>';

    $(function () {
        $(".js-source-states-KELAS_FILTER").on("change", "", function () {
            var data_kelas = $(this).select2("data");

            KELAS_FILTER = data_kelas.id;
        });
        $(".js-source-states-TA_FILTER").select2("data", {id: ID_TA_FILTER, text: NAMA_TA_FILTER});
        $(".js-source-states-TA_FILTER").on("change", "", function () {
            var data_ta = $(this).select2("data");

            ID_TA_FILTER = data_ta.id;
            NAMA_TA_FILTER = data_ta.text;
        });
    });

    function cetak() {
        var TANGGAL_HIJRIYAH = $("#TANGGAL_HIJRIYAH").val();
        var KETUA_PANITIA = $("#KETUA_PANITIA").val();

        if (KELAS_FILTER === null || TANGGAL_HIJRIYAH === '')
            create_homer_error('Lengkapi terlebih dahulu form');
        else
            window.open('<?php echo site_url('lpba/cetak/cetak'); ?>?KELAS=' + KELAS_FILTER + '&TANGGAL=' + TANGGAL_HIJRIYAH + '&ID_TA_FILTER=' + ID_TA_FILTER + '&NAMA_TA_FILTER=' + NAMA_TA_FILTER + '&KETUA_PANITIA=' + KETUA_PANITIA, '_blank');
    }

</script>
