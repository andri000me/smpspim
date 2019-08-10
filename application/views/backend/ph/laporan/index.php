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
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

    function cetak(id) {
        var url = '';
        
        <?php foreach ($data1 as $index => $detail) { ?>
        if(id === 'data1_<?php echo $index; ?>') url = '<?php echo $detail['url']; ?>';
        <?php } ?>

        <?php foreach ($data2 as $index => $detail) { ?>
        if(id === 'data2_<?php echo $index; ?>') url = '<?php echo $detail['url']; ?>';
        <?php } ?>
            
        window.open(url);
    }
</script>