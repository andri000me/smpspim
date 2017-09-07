<?php
$title = 'Kelender Akademik';
$subtitle = "Kalender Akademik";
$id_datatables = 'datatable1';

$this->generate->generate_panel_content("Data " . $title, $subtitle);

$id_modal = "modal-data";
$title_form = "Tambah ". $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>

<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
        
    var url_delete = '<?php echo site_url('akademik/kalender_akademik/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('akademik/kalender_akademik/ajax_add'); ?>';
    var url_update = '<?php echo site_url('akademik/kalender_akademik/ajax_update'); ?>';
    var url_form = '<?php echo site_url('akademik/kalender_akademik/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var title = '<?php echo $title; ?>';

    $(function () {

        $('#calendar').fullCalendar({
            header: {
                left: 'prevYear,prev,next,nextYear today',
                center: 'title',
                right: 'month, listWeek, listDay' //agendaWeek, agendaDay, 
            },
            views: {
                listDay: { buttonText: 'per Hari' },
                listWeek: { buttonText: 'per Minggu' }
            },
            locale: 'id',
            timezone: 'Asia/Jakarta',
            editable: true,
            navLinks: true,
            eventLimit: true,
            weekNumbers: true,
            selectable: true,
            selectHelper: true,
            select: function(start, end) {
                add_event(start, end);
            },
            events: '<?php echo site_url('akademik/kalender_akademik/event_calendar'); ?>',
            eventDrop: function (event, dayDelta, minuteDelta) {
                update_event(event);
            },
            eventResize: function (event, dayDelta, minuteDelta) {
                update_event(event);
            },
            eventClick: function(event, jsEvent, view) {
                delete_event(event);
            }
        });


    });
    
    function add_event(start, end) {
        create_form_input(id_form, id_modal, url_form, title, null);
        $("#" + id_form).append('<input name="TGL_MULAI_AK" id="TGL_MULAI_AK" value="' + moment(start).format() + '" type="hidden">');
        $("#" + id_form).append('<input name="TGL_SELESAI_AK" id="TGL_SELESAI_AK" value="' + moment(end).format() + '" type="hidden">');
    }
    
    function saving_add() {
        var NAMA_AK = $("#NAMA_AK").val();
        var BACKGROUND_AK = $("#BACKGROUND_AK").val();
        var TGL_MULAI_AK = $("#TGL_MULAI_AK").val();
        var LIBUR_AK = $("#LIBUR_AK").val();
        var TGL_SELESAI_AK = $("#TGL_SELESAI_AK").val();
        
        var success = function(data) {
            $("#" + id_modal).modal('hide');
            remove_ladda();
            reload_calendar();
        };
        
        create_ajax(url_add, "TGL_MULAI_AK=" + TGL_MULAI_AK + "&TGL_SELESAI_AK=" + TGL_SELESAI_AK + "&NAMA_AK=" + NAMA_AK + "&BACKGROUND_AK=" + BACKGROUND_AK + "&LIBUR_AK=" + LIBUR_AK, success);
    }
    
    function update_event(event) {
        var id = event.id;
        var start = event.start;
        var end = event.end;
        
        var success = function(data) {
            reload_calendar();
        };
        
        create_ajax(url_update, 'ID_AK=' + id + "&TGL_MULAI_AK=" + moment(start).format() + "&TGL_SELESAI_AK=" + moment(end).format(), success);
    }
    
    function delete_event(event) {
        var id = event.id;
        var success = function(data) {
            reload_calendar();
        };
        var action = function(isConfirm) {
            if(isConfirm) create_ajax(url_delete, 'ID_AK=' + id, success);
        };
        
        create_swal_option("Apakah Anda yakin?", "Data yang telah dihapus tidak dapat dikembalikan.", action);
    }
    
    function action_save_<?php echo $id_datatables; ?>(id_form) {
        saving_add();
        
        return false;
    }
    
    function reload_calendar() {
        $('#calendar').fullCalendar('refetchEvents');
    }

</script>