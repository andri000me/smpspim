<?php
$title = 'Cetak Absensi';
$subtitle = "Form cetak absensi";

$this->generate->generate_panel_content($title, $subtitle);
?>
<form class="form-horizontal">
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        Form Cetak Absensi
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), TRUE, 8, FALSE, NULL); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    var ID_KELAS = 0;

    $(document).ready(function () {
        $(".js-source-states-ID_KELAS").on("change", "", function () {
            var data = $(this).select2("data");
            console.log('DATA', data);
            ID_KELAS = data.id;
        });
    });

    function cetak(id) {
        var url = '';
        
        <?php foreach ($data1 as $index => $detail) { ?>
        if(id === 'data1_<?php echo $index; ?>') url = '<?php echo $detail['url']; ?>';
        <?php } ?>

        <?php foreach ($data2 as $index => $detail) { ?>
        if(id === 'data2_<?php echo $index; ?>') url = '<?php echo $detail['url']; ?>';
        <?php } ?>

        if(ID_KELAS === 0)
            create_homer_error('Silahkan pilih kelas terlebih dahulu.');
        else
            window.open(url, '_blank');

//        $(".js-source-states-ID_KELAS").select2('data', null);
//        ID_KELAS = 0;
    }
</script>