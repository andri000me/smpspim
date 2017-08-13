<?php
$title = 'Database';
$subtitle = "Proses backup database";

$this->generate->generate_panel_content($title, $subtitle);
?>
<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    <div class="panel-tools">
                        <a class="fullscreen"><i class="fa fa-expand"></i></a>
                    </div>
                    Backup Database
                </div>
                <div class="panel-body">
                    <div class="row form-input">
                        <div class="col-md-3 col-md-offset-4">
                            <a href="<?php echo site_url('master_data/database/backup_db'); ?>" target="_blank"><button type="button" class="btn btn-success btn-block"><i class="fa fa-download"></i>&nbsp;&nbsp;&nbsp;Backup Database</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        
    });
</script>