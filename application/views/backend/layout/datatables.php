<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Aplikasi Sistem Informasi Akademik (SIAKAD)
 * MTS TBS KUDUS
 * Dibuat oleh Rohmad Eko Wahyudi 
 * Website: www.kertaskuning.com Email: rohmad.ew@gmail.com
 * 
 */
$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>
<script type="text/javascript">
    var table;
    var url_class = '<?php echo site_url($url); ?>';
    var url_delete = '<?php echo site_url($url_action . '/delete'); ?>/';
    var url_add = '<?php echo site_url($url_action . '/add'); ?>';
    var url_update = '<?php echo site_url($url_action . '/edit'); ?>';
    var url_view = '<?php echo (isset($url_view) ? site_url($url_view) : site_url($url_action . '/view')); ?>';
    var url_form = '<?php echo site_url($url . '/request_form'); ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [<?php if (isset($columns['AKSI'])) echo '-1,'; ?> 0], "orderable": false}]; // {width: "30px", targets: 0}, {width: "50px", targets: -1}, 
    var orders = [[1, "DESC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_form_input(id_form, id_modal, url_form, title, null);
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url($url . '/get_datatables'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
<?php
if (isset($datatables['code_extra']))
    echo $datatables['code_extra'];
?>
<?php
if (isset($datatables['full'])) {
    ?>
            $("body").addClass('hide-sidebar');
    <?php
}
?>

<?php
if (isset($datatables['searching']['simple'])) {
    foreach ($datatables['searching']['simple'] as $id => $target) {
        echo 'dropdown_searching_yes_no("' . $id . '", ' . $target . ');';
    }
}
if (isset($datatables['searching']['multiple'])) {
    foreach ($datatables['searching']['multiple'] as $detail) {
        $options = "{";
        foreach ($detail['options'] as $option_item) {
            $options .= "'" . $option_item['id'] . "': '" . $option_item['text'] . "', ";
        }
        $options .= "}";
        echo "dropdown_searching('" . $detail['id'] . "', " . $detail['target'] . ", " . $options . ");";
    }
}
?>
    });


<?php
if (isset($datatables['function'])) {
    foreach ($datatables['function'] as $detail) {
        ?>
            function <?php echo $detail["name"]; ?>(<?php echo $detail["param"]; ?>) {
                var action = function (isConfirm) {
                    if (isConfirm) {
                        create_ajax('<?php echo site_url($url . '/' . $detail["name"]); ?>', <?php echo $detail["data"]; ?>, function (data) {
                            create_notify(data.notification);
                            reload_datatables(table);
                        });
                    }
                }
                create_notify_option('<?php echo $detail["title"]; ?>', '<?php echo $detail["message"]; ?>', action);
            }
        <?php
    }
}
?>

    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");

        if (status == 'add')
            url = url_add;
        else if (status == 'update')
            url = url_update;

        form_save(url, id_form, table, function(data){});

        return false;
    }

    var update_data_<?php echo $id_datatables; ?> = function (id) {
        create_form_input(id_form, id_modal, url_form, title, id);
    }

    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }

    function view_data_<?php echo $id_datatables; ?>(id) {
        create_window(url_view, 'ID=' + id);
    }
</script>