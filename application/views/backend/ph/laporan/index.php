<?php
$title = 'Cetak Laporan';
$subtitle = "Form cetak laporan";

$this->generate->generate_panel_content($title, $subtitle);
?>
<form class="form-horizontal">
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-6">
                <div class="hpanel">
                    <div class="panel-body no-padding">
                        <?php foreach ($data1 as $index => $detail) { ?>
                            <div class="list-group ">
                                <a class="list-group-item" href="#" onclick="cetak('data1_<?php echo $index; ?>');">
                                    <h4 class="list-group-item-heading"><?php echo $detail['title']; ?></h4>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="hpanel">
                    <div class="panel-body no-padding">
                        <?php foreach ($data2 as $index => $detail) { ?>
                            <div class="list-group ">
                                <a class="list-group-item" href="#" onclick="cetak('data2_<?php echo $index; ?>');">
                                    <h4 class="list-group-item-heading"><?php echo $detail['title']; ?></h4>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="list-group ">
                            <a class="list-group-item" href="#" onclick="$('#modal-pondok').modal('show');">
                                <h4 class="list-group-item-heading">Cetak Siswa Perpondok</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="modal-pondok" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih pondok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Nama Pondok', array('name' => 'ID_PONDOK', 'url' => site_url('master_data/pondok_siswa/auto_complete')), TRUE, 8, TRUE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="cetak_siswa_perpondok(this);">Cetak</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function cetak_siswa_perpondok(t) {
        var pondok = $(".js-source-states-ID_PONDOK").select2('data');
        window.open('<?php echo site_url('ph/laporan/cetak_siswa_perpondok'); ?>?id=' + pondok.id, '_blank');
    }

    function cetak(id) {
        var url = '';

<?php foreach ($data1 as $index => $detail) { ?>
            if (id === 'data1_<?php echo $index; ?>')
                url = '<?php echo $detail['url']; ?>';
<?php } ?>

<?php foreach ($data2 as $index => $detail) { ?>
            if (id === 'data2_<?php echo $index; ?>')
                url = '<?php echo $detail['url']; ?>';
<?php } ?>

        window.open(url);
    }
</script>