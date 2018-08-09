<script type="text/javascript">

    function use_value(ID_TABUNGAN) {
        create_ajax('<?php echo site_url($url); ?>/use_value', 'ID_TABUNGAN=' + ID_TABUNGAN, function (data) {
            if (data.status) {
                create_homer_success(data.msg);
                reload_datatables(table);
            } else {
                create_homer_error(data.msg);
            }
        });
    }
</script>