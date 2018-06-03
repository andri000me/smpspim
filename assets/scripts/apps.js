// CREATED BY ROHMAD EKO WAHYUDI
// contact me at rohmad.ew@gmail.com

//
// --------------- START ---------------
//

var option_datatables_yes_no = {'': 'Pilih Opsi', '1': 'YA', '0': 'TIDAK'};

$(function () {
    $('#side-menu').click(function () {
        $("#menu").animate({
            scrollTop: $("#menu").height()
        }, 500);
    });
    
    $(":input").inputmask();
});

function start_generate() {
//    create_splash(" ");
    create_header_logo();
    create_header_content();
    sidebar_menu();
}

//
// --------------- NAVBAR ---------------
//

function create_header_logo() {
    var tag_html = "";

    tag_html += create_color_line();
    tag_html += create_nav_logo();

    $(tag_html).insertBefore("#navigation");
}

function create_header_content() {
    var tag_html = "";

    tag_html += create_nav_mobile_logo();
//    tag_html += create_nav_search();
    tag_html += create_nav_mobile_menu();
    tag_html += create_start_nav_right();
//    tag_html += create_nav_notication();
    tag_html += create_nav_change_hakakses();
    tag_html += create_nav_logout();
    tag_html += create_end_nav_right();

    $("#navigation").append(tag_html);
}

function create_color_line() {
    var tag_html = "";

    tag_html += '<div class="color-line"> </div>';

    return tag_html;
}

function create_nav_logo() {
    var tag_html = "";

    tag_html += '<div id="logo" class="light-version"><span>' + app_name + '</span></div>';

    return tag_html;
}

function create_nav_mobile_logo() {
    var tag_html = "";

    tag_html += '<div class="small-logo">' +
            '<span class="text-primary">' + app_name + '</span>' +
            '</div>';

    return tag_html;
}

function create_nav_search() {
    var tag_html = "";

    tag_html += '<form role="search" class="navbar-form-custom" method="post" action="#">' +
            '<div class="form-group">' +
            '<h3> TA ' + ta_active + '</h3>' +
            '</div>' +
            '</form>';

    return tag_html;
}

function create_nav_mobile_menu() {
    var tag_html = "";

    tag_html += '<div class="mobile-menu">' +
            '<button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">' +
            '<i class="fa fa-chevron-down"></i>' +
            '</button>' +
            '<div class="collapse mobile-navbar" id="mobile-collapse">' +
            '<ul class="nav navbar-nav">' +
            '<li>' +
            '<a href="' + base_url + 'login/option_hakakses">Menu Utama</a>' +
            '</li>' +
            '</ul>' +
            '</div>' +
            '</div>';

    return tag_html;
}

function create_start_nav_right() {
    var tag_html = "";

    tag_html += '<div class="navbar-right"><ul class="nav navbar-nav no-borders">';

    return tag_html;
}

function create_end_nav_right() {
    var tag_html = "";

    tag_html += '</div></ul>';

    return tag_html;
}

function create_nav_notication() {
    var tag_html = "";

    tag_html += '<li class="dropdown">' +
            '<a class="dropdown-toggle" href="#" data-toggle="dropdown">' +
            '<i class="pe-7s-speaker"></i>' +
            '</a>' +
            '<ul class="dropdown-menu hdropdown notification animated flipInX">' +
            '<li>' +
            '<a>' +
            '<span class="label label-success">NEW</span> It is a long established.' +
            '</a>' +
            '</li>' +
            '<li>' +
            '<a>' +
            '<span class="label label-warning">WAR</span> There are many variations.' +
            '</a>' +
            '</li>' +
            '<li>' +
            '<a>' +
            '<span class="label label-danger">ERR</span> Contrary to popular belief.' +
            '</a>' +
            '</li>' +
            '<li class="summary"><a href="#">See all notifications</a></li>' +
            '</ul>' +
            '</li>';

    return tag_html;

}

function change_hakakses_header(ID_HAKAKSES) {
    create_splash("Mohon tunggu sebentar, sistem sedang mengatur Hak Akses Anda.");
    var success = function (data) {
        remove_splash();

        if (data.status) //if success close modal and reload ajax table
        {
            create_swal_success('', data.msg);

            setTimeout(function () {
                window.location = data.link;
            }, 1500);
        } else {
            create_homer_error(data.msg);
        }
    };
    create_ajax(url_hakakses, 'ID_HAKAKSES=' + ID_HAKAKSES, success);

    return false;
}

function create_nav_change_hakakses() {
    var tag_html = "";
    $.each(list_hakakses, function (index, item) {
        tag_html += '<li style="padding: 5px">' +
                '<a href="#" onclick="change_hakakses_header(' + item.ID_HAKAKSES + ')">' +
                '<h5 class="font-extra-bold text-primary">' + item.NAME_HAKAKSES + '</h5>' +
                '</a>' +
                '</li>';
    });

    return '<li class="dropdown">' +
            '<a class="dropdown-toggle" href="#" data-toggle="dropdown">' +
            '<i class="pe-7s-keypad"></i>' +
            '</a>' +
            '<ul class="dropdown-menu hdropdown notification animated flipInX">' +
            tag_html +
            '</ul>' +
            '</li>';

}

function create_nav_logout() {
    var tag_html = "";

    tag_html += '<li class="dropdown">' +
            '<a href="' + base_url + 'pencarian" target="_blank">' +
            '<i class="pe-7s-search"></i>' +
            '</a>' +
            '</li>';
    tag_html += '<li class="dropdown">' +
            '<a href="' + base_url + 'login/option_hakakses">' +
            '<i class="pe-7s-upload pe-rotate-90"></i>' +
            '</a>' +
            '</li>';

    return tag_html;
}

//
// --------------- MENU ---------------
//

function sidebar_menu() {
    $(create_profile_picture()).insertBefore("#side-menu");
//    list_menu();
}

function create_profile_picture() {
    var tag_html = "";

    tag_html += '<div class="profile-picture">' +
            '<a href="index.html">' +
            '<img src="' + base_url + 'files/' + foto_user + '" class="img-rounded m-b" alt="logo" width="100" high="100">' +
            '</a>' +
            '<div class="stats-label text-color">' +
            '<span class="font-extra-bold font-uppercase">' + nama_user + '</span>' +
            '<div class="dropdown">' +
            '<small class="text-muted">LEVEL: ' + level_hakakses + ' </small><br>' +
            '<small class="text-muted font-extra-bold">' + cawu_active + ' </small><br>' +
            '<small class="text-muted font-extra-bold">TAHUN AJARAN: ' + ta_active + ' </small><br>' +
            '<small class="text-muted font-extra-bold">PSB: ' + psb_active + ' </small>' +
            '</div>' +
            '</div>' +
            '</div>';

    return tag_html;
}

function list_menu() {
    var tag_html = "";
    var url = base_url + 'menu/get_menu';
    var data = '';
    var open_parent = false;
    var open_child = false;
    var success = function (data) {
        $.each(data, function (index, item) {
            if (open_child && item.MARK_CHILD == 1) {
                open_child = false;
                tag_html += create_menu_child_close();
                tag_html += create_menu_parent_open();
            }
            if (open_parent) {
                open_parent = false;
                tag_html += create_menu_parent_close();
            }
            if (item.LEVEL_CHILD == 1) {
                open_parent = true;
                tag_html += create_menu_parent_open();
            }

            if (open_child)
                tag_html += create_menu_parent_open();

            tag_html += create_menu(item);

            if (open_child)
                tag_html += create_menu_parent_close();

            if (item.MARK_CHILD == 1) {
                open_child = true;
                tag_html += create_menu_child_open();
            }
        });

        $("#side-menu").html(tag_html);
    };

    create_splash("Sedang memuat menu");
    create_ajax(url, data, success);
}

function create_menu_parent_open() {
    return "<li>";
}

function create_menu_parent_close() {
    return "</li>";
}

function create_menu_child_open() {
    return '<ul class="nav nav-second-level">';
}

function create_menu_child_close() {
    return '</ul>';
}

function create_menu(item) {
    var tag_html = "";
    var label = item.NAME_MENU;
    var url_link = base_url + item.CONTROLLER_MENU + '/' + item.FUNCTION_MENU;
    ;

    if (item.LEVEL_CHILD == 1)
        label = '<span class="nav-label">' + item.NAME_MENU + '</span>';
    if (item.CONTROLLER_MENU === null)
        url_link = "#";
    if (item.LEVEL_CHILD == 1 && item.MARK_CHILD == 1)
        label += '<span class="fa arrow"></span>';

    tag_html += '<a href="' + url_link + '"> ' + label + ' </a>';

    return tag_html;
}

//
// --------------- NOTIFIKASI ---------------
//

// ================================================================================================================================

function create_splash(message) {
    var tag_html = '<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>' + app_name + '</h1><p>' + motto + '</p> ' + spinner_homer() + ' <p>' + message + '</p></div> </div>';

    remove_splash();
    $(".splash-screen").html(tag_html);
}

function remove_splash() {
    $(".splash-screen").html(" ");
}

function spinner_homer() {
    return '<div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div>';
}

// ================================================================================================================================

toastr.options = {
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-top-center",
    "closeButton": true,
    "toastClass": "animated fadeInDown",
};

function remove_homer() {
    $("#toast-container").remove();
}

function create_homer_info(message) {
    remove_homer();
    toastr.info('Info - ' + message);
}

function create_homer_success(message) {
    remove_homer();
    toastr.success('Success - ' + message);
}

function create_homer_warning(message) {
    remove_homer();
    toastr.warning('Warning - ' + message);
}

function create_homer_error(message) {
    remove_homer();
    toastr.error('Error - ' + message);
}

// ================================================================================================================================

function create_swal_success(title, message) {
    if (title === "")
        title = "Success";

    swal({
        title: title,
        text: message,
        type: "success",
        html: true,
    });
}

function create_swal_error(title, message) {
    if (title === "")
        title = "Error";

    swal({
        title: title,
        text: message,
        type: "error",
        html: true,
    });
}

function create_swal_option(title, text, action) {
    swal({
        title: title,
        text: text,
        type: "warning",
        html: true,
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: true,
        closeOnCancel: true
    }, action);
}

function create_swal_delete(url_delete, id, success) {
    swal({
        title: "Apa kamu yakin?",
        text: "Data yang sudah dihapus tidak dapat dikembalikan!",
        type: "warning",
        html: true,
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        closeOnConfirm: true,
        closeOnCancel: true
    },
            function (isConfirm) {
                if (isConfirm) {
                    create_splash("Sedang menghapus data ...");
                    create_ajax(url_delete, id, success);
                }
            });
}

// ================================================================================================================================

function create_ladda(id_ladda) {
    ladda_clicked = $("." + id_ladda).ladda();

    ladda_clicked.ladda("start");
    $(".ladda-bind").attr("disabled", "true");
}

function remove_ladda() {
    if (ladda_clicked !== null)
        ladda_clicked.ladda("stop");
    $(".ladda-bind").removeAttr("disabled");
}

// ================================================================================================================================

//
// --------------- AJAX ---------------
//
// MENGEKSKUSI FORM DENGAN AJAX
function create_form_ajax(url, id_form, success, message) {
    $(".required").removeClass('error');
    $(".control-label").removeClass('text-danger');

    $(".required").each(function () {
        if (this.value == "") {
            status_required = false;

            $(this).addClass('error');
            $(this).parent().prev('.control-label').addClass('text-danger');
        }
    });

    if (status_required) {
        if (message !== null)
            create_splash(message);

        $.ajax({
            url: url,
            type: "POST",
            data: $('#' + id_form).serialize(),
            dataType: "JSON",
            success: success,
            error: function (jqXHR, textStatus, errorThrown) {
                remove_splash();
                remove_ladda();
                create_swal_error('Ajax tidak berjalan seharusnya. Silahkan muat ulang halaman ini.');
                show_error_ajax(jqXHR.responseText);
            }
        });
    } else {
        remove_ladda();
        create_homer_error("Silahkan lengkapi terlebih dahulu field yang wajib diisi.");
    }

    status_required = true;
}

// MENGEKSKUSI AJAX
function create_ajax(url, data, success) {
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: "JSON",
        success: success,
        error: function (jqXHR, textStatus, errorThrown) {
            remove_splash();
            remove_ladda();
            create_swal_error('Ajax tidak berjalan seharusnya. Silahkan muat ulang halaman ini.');
            show_error_ajax(jqXHR.responseText);
        }
    });
}

function show_error_ajax(text) {
    var tag_html = ' ' +
            '<div class="row error-response">' +
            '<div class="col-lg-12">' +
            '<div class="hpanel hred">' +
            '<div class="panel-heading hbuilt">' +
            '<div class="panel-tools">' +
            '<a class="closebox" onclick="remove_error_response();"><i class="fa fa-times"></i></a>' +
            '</div>' +
            'ERROR RESPONSE' +
            '</div>' +
            '<div class="alert alert-danger">' +
            text
    '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

    remove_error_response();

    $(".content").prepend(tag_html);
}

function remove_error_response() {
    $(".error-response").remove();
}
;

function create_ajax_file(url, id, data, success) {
    $.ajaxFileUpload({
        url: url,
        secureuri: false,
        fileElementId: id,
        dataType: 'json',
        data: data,
        success: success,
        error: function (jqXHR, textStatus, errorThrown) {
            remove_splash();
            remove_ladda();
            create_swal_error('Ajax tidak berjalan seharusnya. Silahkan muat ulang halaman ini.');
            show_error_ajax(jqXHR.responseText);
        }
//        data: {
//            'ID_SISWA': ID_SISWA
//        },
//        success: function (data, status) {
//            console.log(data);
//        }
    });
}

// 
// ------------- DATATABLES -----------------
// 

// MENGATUR AWAL DATATABLES
function initialize_datatables_pipeline(id_table, site, columns, orders, functionInitComplete, functionDrawCallback, functionAddData) {
    //
    // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
    //
    $.fn.dataTable.pipeline = function (opts) {
        // Configuration options
        var conf = $.extend({
            pages: 5, // number of pages to cache
            url: site, // script url
            data: null, // function or object with parameters to send to the server
            // matching how `ajax.data` works in DataTables
            method: 'POST' // Ajax HTTP method
        }, opts);

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function (request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                // outside cached data - need to make a request
                ajax = true;
            } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                    JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                    JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
                    ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (conf.pages - 1));

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if ($.isFunction(conf.data)) {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                settings.jqXHR = $.ajax({
                    "type": conf.method,
                    "url": conf.url,
                    "data": request,
                    "dataType": "json",
                    "cache": false,
                    "success": function (json) {
                        cacheLastJson = $.extend(true, {}, json);

                        if (cacheLower != drawStart) {
                            json.data.splice(0, drawStart - cacheLower);
                        }
                        json.data.splice(requestLength, json.data.length);

                        drawCallback(json);
                    }
                });
            } else {
                json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);

                drawCallback(json);
            }
        }
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register('clearPipeline()', function () {
        return this.iterator('table', function (settings) {
            settings.clearCache = true;
        });
    });

    $('#' + id_table + ' tfoot th').each(function () {
        var title = $(this).text();
        if (title !== 'AKSI')
            $(this).html('<input type="text" placeholder="Search ' + title + '" class="form-control input-sm datatables-search" style="width:100%">');
    });
    var table = $('#' + id_table).DataTable({
        "processing": true,
        "serverSide": true,
        "order": orders,
        "columnDefs": columns,
        "initComplete": functionInitComplete,
        "drawCallback": functionDrawCallback,
        "ajax": {
            "url": site,
            "type": "POST",
            "pages": 5
        },
        "search": {
            "regex": true
        },
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4 text-right'f>>t<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        buttons: [
            {extend: 'copy', className: 'btn-sm'},
            {extend: 'csv', title: 'DownloadDataCSV', className: 'btn-sm'},
            {extend: 'pdf', title: 'DownloadDataPDF', className: 'btn-sm'},
            {extend: 'print', className: 'btn-sm'},
            {
                text: 'Reload',
                className: 'btn-sm',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            },
            {
                text: 'Add',
                className: 'btn-sm',
                action: functionAddData
            }
        ],
        language: {
            processing: create_splash("Sedang memuat data ...")
        },
        processing: true
    });
    table.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            that.search(this.value).draw();
        });
    });

    return table;
}

function initialize_datatables(id_table, site, columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport, reqScroll) {
    var buttonsExport = "";

//    jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
//        if (this.context.length) {
//            var jsonResult = $.ajax({
//                url: site,
////                data: {search: $(#search).val()},
//                success: function (result) {
//                    //Do nothing
//                },
//                async: false
//            });
//
//            return {body: jsonResult.responseJSON.data, header: $("#myTable thead tr th").map(function () {
//                    return this.innerHTML;
//                }).get()};
//        }
//    });

    if (requestExport) {
        buttonsExport = [
//            {extend: 'copy', className: 'btn-sm'},
//            {extend: 'excel', title: 'DownloadDataXLSX', className: 'btn-sm'},
//            {extend: 'csv', title: 'DownloadDataCSV', className: 'btn-sm'},
//            {extend: 'pdf', title: 'DownloadDataPDF', className: 'btn-sm'},
//            {extend: 'print', className: 'btn-sm'},
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-sm btn-default',
                title: 'DownloadDataXLSX'
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-sm btn-default',
                title: 'DownloadDataCSV'
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-sm btn-default',
                title: 'DownloadDataPDF'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-sm btn-default',
            },
            {
                text: 'Reload',
                className: 'btn btn-sm btn-default buttons-reload',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            },
            {
                text: 'Add',
                className: 'btn btn-sm btn-default buttons-add',
                action: functionAddData
            }
        ];
    } else {
        buttonsExport = [
            {
                text: 'Reload',
                className: 'btn btn-default btn-sm buttons-reload',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            },
            {
                text: 'Add',
                className: 'btn btn-default btn-sm buttons-add',
                action: functionAddData
            }
        ];
    }

    $('#' + id_table + ' tfoot th').each(function () {
        var title = $(this).text();
        if (title !== 'AKSI')
            $(this).html('<input type="text" placeholder="Search ' + title + '" class="form-control input-sm datatables-search datatables-search-' + title.replace(" ", "-") + '" style="width:100%">');
    });

    var table = null;
    if (typeof reqScroll === "undefined") {
        table = $('#' + id_table).DataTable({
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "order": orders,
            //        "pagingType": "input",
            //        "sPaginationType": "extStyle",
            //        "sPaginationType": "listbox",
            "columnDefs": columns,
            "initComplete": functionInitComplete,
            "drawCallback": functionDrawCallback,
            "ajax": {
                "url": site,
                "type": "POST",
                "pages": 5
            },
            "search": {
                "regex": true
            },
            dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3 text-right'f>>t<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            buttons: buttonsExport,
            language: {
                processing: create_splash("Sedang memuat data ...")
            },
            processing: true
        });
    } else {
        table = $('#' + id_table).DataTable({
            "scrollY": window.innerHeight,
            "scrollCollapse": true,
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "order": orders,
//        "pagingType": "input",
//        "sPaginationType": "extStyle",
//        "sPaginationType": "listbox",
            "columnDefs": columns,
            "initComplete": functionInitComplete,
            "drawCallback": functionDrawCallback,
            "ajax": {
                "url": site,
                "type": "POST",
                "pages": 5
            },
            "search": {
                "regex": true
            },
            dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3 text-right'f>>t<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            buttons: buttonsExport,
            language: {
                processing: create_splash("Sedang memuat data ...")
            },
            processing: true
        });
    }

    $(".dt-buttons").addClass('btn-group');
    $(".dataTables_filter").children().children().attr('style', 'width: 150px;');

    table.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            that.search(this.value).draw();
        });
    });

    return table;
}

function dropdown_searching_yes_no(id, target) {
    var html_option = '';
    html_option += '<option value="">Pilih Opsi</option>';
    html_option += '<option value="1">YA</option>';
    html_option += '<option value="0">TIDAK</option>';

    $(".datatables-search-" + id).replaceWith('<select class="form-control input-sm datatables-search datatables-search-' + id + '" style="width:100%" onchange="refresh_datatables(this, ' + target + ');">' + html_option + '</select>');
}

function dropdown_searching(id, target, data) {
    var html_option = '';
    $.each(data, function (value, text) {
        html_option += '<option value="' + value + '">' + text + '</option>';
    });
    console.log(data);
    console.log(html_option);
    $(".datatables-search-" + id).replaceWith('<select class="form-control input-sm datatables-search datatables-search-' + id + '" style="width:100%" onchange="refresh_datatables(this, ' + target + ');">' + html_option + '</select>');
}

function refresh_datatables(that, target) {
    var val = $(that).val();
    table.columns(target).search(val).draw();
}

// MEMUAT ULANG TABEL DATATABLES

function reload_datatables(table) {
    table.ajax.reload(null, false);
//    create_notify('', 'Tabel sedang dimuat ulang.');
}

// 
// ------------ FORM -------------
// 


function create_form_input(id_form, id_modal, url_form, title, id) {
    var data_id = "ID=" + id;
    var status = "update";

    $("#" + id_modal).modal();

    if (id === null) {
        data_id = "";
        status = 'add';
        $(".modal-title").text('Tambah ' + title);
    } else {
        $(".modal-title").text('Ubah ' + title);
    }

    $("#" + id_form).data("status", status).html("");

    create_ajax(url_form, data_id, function (data) {
        if (data.STATUS) {
            $.each(data.DATA, function (index, item) {
                $(".modal-body").removeClass('text-center');
                form_input(id_form, item);
            });
        } else {
            $("#" + id_modal).modal('hide');
            create_swal_error(data.MESSAGE);
        }

        $(".ladda-button-save, .info-field").show();
    });
}

function create_form_view(id_form, id_modal, url_view, title, id) {
    var tag_html = "";
    var data_id = "ID=" + id;

    $("#" + id_modal).modal();
    $(".modal-title").text('Lihat ' + title);

    create_ajax(url_view, data_id, function (data) {
        $(".modal-body").removeClass('text-center');

        tag_html += '<div class="table-responsive"><table cellpadding="1" cellspacing="1" class="table"><tbody>';
        $.each(data, function (key, value) {
            tag_html += '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
        });
        tag_html += '</tbody></table></div>';

        $("#" + id_form).html(tag_html);
        $(".ladda-button-save, .info-field").hide();
    });
}

function form_input(id_form, json_content) {
    var tag_html = "";
    var tag_value = null;
    var tag_hidden = false;
    var tag_name = null;
    var tag_class = null;
    var tag_label = null;
    var tag_minimum = 1;
    var label = json_content.label;
    var type = null;
    var start_tag = true;
    var value_blank = null;
    var required = false;
    var length = 5;
    var url_ajax = null;
    var request_multiple = false;
    var request_inline = false;

    if (typeof json_content.required !== "undefined")
        required = json_content.required;
    if (typeof json_content.length !== "undefined")
        length = json_content.length;

    if (required)
        label += "&nbsp;*";

    if (typeof json_content.label !== "undefined")
        tag_html += '<div class="form-group"><label class="col-sm-2 control-label">' + label + '</label>';

    if (json_content.data !== null) {
        if (typeof json_content.label !== "undefined")
            tag_html += '<div class="col-sm-' + length + '">';

        if (typeof json_content.hidden !== "undefined")
            tag_hidden = json_content.hidden;

        $.each(json_content.data, function (key, value) {
            if (key == '')
                return;
            if (key == 'type')
                type = value;
            if (key == 'value')
                tag_value = value;
            if (key == 'name')
                tag_name = value;
            if (key == 'label')
                tag_label = value;
            if (key == 'minimum')
                tag_minimum = value;
            // ================================================================================== HIDDEN =====================================================
            if (tag_hidden) {
                if (start_tag)
                    tag_html += '<input type="hidden" ';

                tag_html += key + '="' + value + '" ';
                // ================================================================================== DROPDOWN =====================================================
            } else if (type === 'dropdown') {
                if (start_tag)
                    tag_html += '<select ';

                if (key === 'value_blank')
                    value_blank = value;

                if (key === "data") {
                    tag_html += form_check_required(required) + ">";

                    if (value_blank != null)
                        tag_html += '<option value="">' + value_blank + '</option>';

                    $.each(value, function (key, value) {
                        tag_html += '<option value="' + value.id + '"';
                        if (tag_value === value.id)
                            tag_html += ' selected';
                        tag_html += '>' + value.text + '</option>';
                    });
                } else if (key !== 'value_blank') {
                    tag_html += key + '="' + value + '" ';
                }
                // ================================================================================== AUTOCOMPLETE =====================================================
            } else if (type === 'autocomplete') {
                if (start_tag && key == 'multiple') {
                    request_multiple = value;

                    if (request_multiple)
                        tag_class = "js-source-states-multiple";
                    else
                        tag_class = "js-source-states";

                    tag_class += '-' + tag_name;

                    start_tag = false;
                }

                if (key === "data" && value !== null) {
                    tag_html += '<select class="form-control ' + tag_class + '" name="' + tag_name + '" style="width: 100%" multiple="multiple" >';

                    if (value_blank != null)
                        tag_html += '<option value="">' + value_blank + '</option>';

                    $.each(value, function (key, value) {
                        tag_html += '<option value="' + value.value + '"';
                        if (tag_value === value.value)
                            tag_html += ' selected';
                        tag_html += '>' + value.label + '</option>';
                    });
                } else if (key === 'url' && value !== null) {
                    tag_html += '<input class="form-control ' + tag_class + '" name="' + tag_name + '" style="width: 100%" multiple="multiple" >';

                    url_ajax = value;
                }
                // ================================================================================== CHECKBOX AND RADIO =============================================
            } else if (type === 'checkbox' || type === 'radio' || type === 'checkbox_simple') {
                if (key === 'inline')
                    request_inline = value;

                if (key === "data") {
                    $.each(value, function (key, value) {
                        if (request_inline)
                            tag_class = type + '-inline';
                        else
                            tag_class = "";

                        var req_value = null;
                        var req_label = null;

                        if (value.value !== "undefined")
                            req_value = value.value;
                        else if (value.id !== "undefined")
                            req_value = value.id;

                        if (value.label !== "undefined")
                            req_label = value.label;
                        else if (value.text !== "undefined")
                            req_label = value.text;

                        if (type === 'checkbox_simple') {
                            tag_html += '<label><input type="checkbox" name="' + tag_name + '" value="' + req_value + '" id="' + type + req_value + tag_name + '" class="checkbox_simple" ';
                            if (tag_value === req_value)
                                tag_html += ' checked';
                            tag_html += '>&nbsp;&nbsp;' + req_label + '</label><br>';
                        } else {
                            tag_html += '<div class="' + type + ' ' + tag_class + ' ' + type + '-primary"><input type="' + type + '" name="' + tag_name + '" value="' + req_value + '" id="' + type + req_value + tag_name + '" ';
                            if (tag_value === req_value)
                                tag_html += ' checked';
                            tag_html += '>&nbsp;&nbsp;<label for="' + type + req_value + '">' + req_label + '</label></div>';
                        }

                    });
                }
                // ================================================================================== TEXT AREA =====================================================
            } else if (type === 'textarea') {
                if (start_tag)
                    tag_html += '<textarea class="form-control" ';
                if (key === 'value')
                    tag_html += '>' + value;
                else if (key !== 'type')
                    tag_html += key + '="' + value + '" ';
                // ================================================================================== TEXT =====================================================
            } else if (type === 'clockpicker' || type === 'datepicker' || type === 'datetimepicker') {
                if (start_tag)
                    tag_html += '<input type="text" class="form-control ' + type + '" ';
                if (type === 'datepicker')
                    tag_html += ' data-date-format="yyyy-mm-dd" ';
                if (key !== 'type')
                    tag_html += key + '="' + value + '" ';
                // ================================================================================== PASSWORD =====================================================
            } else if (type == 'password') {
                if (start_tag)
                    tag_html += '<input type="password" ';

                tag_html += key + '="' + value + '" ';
                // ================================================================================== TEXT =====================================================
            } else if (type == 'finance') {
                if (start_tag)
                    tag_html += '<input type="text" onkeydown="return haltnondigit(event)" onkeypress="validate(event)" ';

                tag_html += key + '="' + value + '" ';
                // ================================================================================== TEXT =====================================================
            } else {
                if (start_tag)
                    tag_html += '<input type="text" ';

                tag_html += key + '="' + value + '" ';
            }

            if (type !== 'autocomplete')
                start_tag = false;
        });

        if (tag_hidden)
            tag_html += '>';
        else if (type == 'dropdown' || type === 'autocomplete')
            tag_html += '</select>';
        else if (type == 'checkbox' || type == 'checkbox_simple' || type === 'radio')
            tag_html += ' ';
        else if (type == 'textarea')
            tag_html += '</textarea>';
        else
            tag_html += form_check_required(required) + " >";

        if (required && typeof json_content.keterangan !== "undefined" && json_content.keterangan !== null)
            tag_html += '<span class="help-block m-b-none text-left">' + json_content.keterangan + '</span>';

        if (typeof json_content.label !== "undefined")
            tag_html += '</div>';
    }

    if (typeof json_content.label !== "undefined")
        tag_html += '</div>';

    $("#" + id_form).append(tag_html);

    if (type === 'autocomplete') {
        if (!request_multiple)
            $("." + tag_class).removeAttr('multiple');
        if (url_ajax === null) {
            $("." + tag_class).select2({
                minimumInputLength: tag_minimum,
                escapeMarkup: function (markup) {
                    return markup;
                },
                formatResult: function (element) {
                    return element.id + ' - ' + element.text;
                },
                formatSelection: function (element) {
                    return element.id + ' - ' + element.text;
                },
            });

            if (tag_value !== "")
                $("." + tag_class).select2().val(tag_value).trigger("change");
        } else {
            $("." + tag_class).select2({
                minimumInputLength: tag_minimum,
                escapeMarkup: function (markup) {
                    return markup;
                },
                ajax: {
                    url: url_ajax,
                    dataType: 'json',
                    type: "POST",
                    delay: 100,
                    cache: true,
                    data: function (term, page) {
                        return {
                            q: term
                        }
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    }
                },
                formatResult: function (element) {
                    return element.text;
//                    return element.id + ' - ' + element.text;
                },
                formatSelection: function (element) {
                    return element.text;
//                    return element.id + ' - ' + element.text;
                },
            });

            if (tag_value !== "" && tag_label !== "")
                $("." + tag_class).select2('data', {id: tag_value, text: tag_label});
        }
    } else if (type === 'clockpicker' || type === 'datepicker' || type === 'datetimepicker') {
        $('.clockpicker').clockpicker({autoclose: true});

        if (tag_value === null)
            $('.datepicker').datepicker().datepicker("setDate", new Date());
        else
            $('.datepicker').datepicker();

        $('.datetimepicker').datetimepicker();
    }

}

function form_check_required(required) {
    var tag_html = "";

    if (required)
        tag_html += 'required=""  class="form-control required" ';
    else
        tag_html += 'class="form-control" ';

    return tag_html;
}

function form_save(url, id_form, table) {
    var message = null;
    var success = function (data) {
        remove_ladda();

        if (data.status > 0) {
            create_homer_success("Data berhasil disimpan");
            $("#" + id_modal).modal('hide');
            reload_datatables(table);

            if (typeof data.url !== "undefined")
                window.open(data.url, '_blank');
        } else {
            if (typeof data.STATUS !== "undefined" && !data.STATUS)
                create_homer_error(data.MESSAGE);
            else
                create_homer_error("Gagal menyimpan data");
        }
    };

    create_form_ajax(url, id_form, success, message);
}

function form_delete(url, id, table) {
    var success = function (data) {
        if (data.status > 0) {
            create_homer_success("Data berhasil dihapus");
            reload_datatables(table);
        } else {
            if (typeof data.STATUS !== "undefined" && !data.STATUS)
                create_homer_error(data.MESSAGE);
            else
                create_homer_error("Gagal menghapus data");
        }

        remove_splash();
    };

    create_swal_delete(url, "ID=" + id, success);
}

function create_view_modal(id, datatables, title) {
    $("#" + id).html('<div class="modal fade" id="view_data_' + datatables + '" tabindex="-1" role="dialog"  aria-hidden="true"> ' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="color-line"></div>' +
            '<div class="modal-header">' +
            '<h4 class="modal-title">' + title + '</h4>' +
            '</div>' +
            '<div class="modal-body detail_' + datatables + '">' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');
}

function list_group(datatables, title) {
    $('.detail_' + datatables).append('<h3 class="font-extra-bold no-margins text-success">' + title + '</h3><br>');
}

function list_detail(datatables, length1, label1, data1, length2, label2, data2) {
    $('.detail_' + datatables).append('<div class="row"><div class="col-md-' + length1 + '"><small class="stat-label">' + label1 + '</small><h4>' + (data1 == null ? '-' : data1) + '</h4></div><div class="col-md-' + length2 + ' text-right"><small class="stat-label">' + label2 + '</small><h4>' + (data2 == null ? '-' : data2) + '</h4></div></div>');
}

// =================================================================================================================
function formattedDate(date) {
    var d = new Date(date || Date.now()),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [day, month, year].join('/');
}

function formattedIDR(angka) {
    var thoudelim = ".";
    var decdelim = ",";
    var curr = "Rp. ";
    var r = 2;

    angka = Math.round(angka * Math.pow(10, r)) / Math.pow(10, r);
    angka = String(angka);
    angka = angka.split(".");
    var l = angka[0].length;
    var t = "";
    var c = 0;
    while (l > 0) {
        t = angka[0][l - 1] + (c % 3 == 0 && c != 0 ? thoudelim : "") + t;
        l--;
        c++;
    }
    angka[1] = angka[1] == undefined ? "0" : angka[1];
    for (i = angka[1].length; i < r; i++) {
        angka[1] += "0";
    }
    return curr + t + (r == 0 ? "" : decdelim + angka[1]);
}

function haltnondigit(e) {
    var allowkey = Array(48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 8, 9, 188, 190, 45, 46, 13, 33, 34, 35, 36, 37, 38, 39, 40, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 123);

    if (allowkey.indexOf(e.keyCode) === -1)
        return false;
    else
        return true;
}

function validate(evt) {
    var theEvent = evt || window.event;
    var regex = /[0-9]|\./;
    var key = theEvent.keyCode || theEvent.which;

    key = String.fromCharCode(key);

    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault)
            theEvent.preventDefault();
    }
}

function create_window(url, data) {
    var width = 1150;
    var height = 550;
    var left = (screen.width - width) / 2;
    var top = (screen.height - height) / 2;
    var params = 'width=' + width + ', height=' + height;
    params += ', top=' + top + ', left=' + left;
    params += ', directories=no';
    params += ', location=no';
    params += ', menubar=no';
    params += ', resizable=no';
    params += ', scrollbars=no';
    params += ', status=no';
    params += ', toolbar=no';
    params += ', titlebar=no';

    newwin = window.open(url + data, "windowname5", params);
    if (window.focus) {
        newwin.focus();
    }

    return false;
}

function reload_window() {
    setTimeout(function () {
        window.location.reload();
    }, 1500);
}

// ============================================================ CHART ==========================================

function create_chart(id, data_json, single, type, single) {
    var chart = null;
    if (((type === 'pie') || (type === 'donut')) && single) {
        chart = c3.generate({
            bindto: '#' + id,
            data: {
                json: data_json.data,
                colors: data_json.colors,
                names: data_json.names,
                type: type,
//            }
            }
        });
    } else {
        chart = c3.generate({
            bindto: '#' + id,
            data: {
                x: 'x_label',
                json: data_json.data,
                colors: data_json.colors,
                names: data_json.names,
                type: type,
//            onclick: function (d, i) {
//                console.log("onclick", d, i);
//            },
//            onmouseover: function (d, i) {
//                console.log("onmouseover", d, i);
//            },
//            onmouseout: function (d, i) {
//                console.log("onmouseout", d, i);
//            }
            },
            axis: {
                x: {
                    type: 'category', // timeseries, category, indexed
                    tick: {
                        rotate: 75,
                        multiline: false,
                        fit: true,
                    },
                    height: 100,
                    label: {
                        text: data_json.label_x,
                        position: 'outer-center'
                                // inner-right : default
                                // inner-center
                                // inner-left
                                // outer-right
                                // outer-center
                                // outer-left
                    }
                },
                y: {
                    label: {
                        text: data_json.label_y,
                        position: 'outer-middle'
                                // inner-top : default
                                // inner-middle
                                // inner-bottom
                                // outer-top
                                // outer-middle
                                // outer-bottom
                    }
                }
            },
            grid: {
                x: {
                    show: false
                },
                y: {
                    show: true
                }
            },
            tooltip: {
                format: {
                    title: function (d) {
                        return data_json.data.x_label[d];
                    },
                    name: function (name, ratio, id, index) {
                        if (single)
                            name = data_json.data.x_label[index];

                        return name;
                    },
                    value: function (value, ratio, id) {
                        if (typeof data_json.type !== "undefined") {
                            if (data_json.type === "money")
                                value = formattedIDR(value);
                        }
//                    var format = id === 'data1' ? d3.format(',') : d3.format('$');
//                    return format(value);
                        return value;
                    }
//            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });
    }

    return chart;
}

function reload_chart(chart, data_json) {
    chart.load({
        json: data_json.data,
        colors: data_json.colors,
        names: data_json.names,
        unload: data_json.unload
    });
}

function chart_transform_spline(chart) {
    chart.transform('spline');
}

function chart_transform_line(chart) {
    chart.transform('line');
}

function chart_transform_area(chart) {
    chart.transform('area');
}

function chart_transform_area_spline(chart) {
    chart.transform('area-spline');
}

function chart_transform_bar(chart) {
    chart.transform('bar');
}

function chart_transform_pie(chart) {
    chart.transform('pie');
}

function chart_transform_donut(chart) {
    chart.transform('donut');
}