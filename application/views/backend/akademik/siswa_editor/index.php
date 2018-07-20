<?php
$title = 'Siswa Editor';
$subtitle = "Daftar semua siswa yang dapat diubah";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NAMA',
    'JK',
    'KELAS',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>

<!--<div id="modal_view"></div>
<div id="view_photo"></div>-->

<script type="text/javascript">
    var ID_KELAS = 0;
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1], "orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = false;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        // window.open('<?php echo site_url('akademik/siswa/cetak_kartu'); ?>', '_blank');
    };

    $(document).ready(function () {
        $("body").addClass('hide-sidebar');
//        $("body").removeClass('fixed-navbar');

        $('#' + id_table + ' tfoot th').each(function () {
            var title = $(this).text();
            if (title !== 'AKSI')
                $(this).html('<input type="text" placeholder="Search ' + title + '" class="form-control input-sm datatables-search datatables-search-' + title.replace(" ", "-") + '" style="width:100%">');
        });

        table = $('#' + id_table).DataTable({
            "bDestroy": true,
            "processing": true,
            "serverSide": true,
            "order": orders,
            "sPaginationType": "listbox",
            "columnDefs": columns,
            "initComplete": functionInitComplete,
            "drawCallback": functionDrawCallback,
            "ajax": {
                "url": '<?php echo site_url('akademik/siswa_editor/ajax_list'); ?>',
                "type": "POST",
                "pages": 5
            },
            "search": {
                "regex": true
            },
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4 text-right'f>>t<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            buttons: [
                {
                    text: 'Reload',
                    className: 'btn btn-default btn-sm buttons-reload',
                    action: function (e, dt, node, config) {
                        dt.ajax.reload();
                    }
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

        $("#datatable1_wrapper").parent().parent().parent().addClass('col-lg-5').removeClass('col-lg-12').attr("id", "datatable_siswa");
        $("#datatable1_paginate").parent().addClass('col-sm-12').removeClass('col-sm-6');
        $("#datatable1_paginate").parent().prev().remove();

        $(".buttons-add, .buttons-csv, .buttons-pdf, .buttons-copy, .buttons-print, .dataTables_filter, .dataTables_length, .dataTables_info").remove();

        create_form();

        get_jk();
        get_wilayah();
        get_pondok();
        get_asal_sekolah();
    });

    function open_editor(ID_SISWA) {
        var success = function (data) {
            var id_wilayah = null;
            var nama_kec_wilayah = null;
            var nama_kab_wilayah = null;
            var nama_prov_wilayah = null;
            var id_pondok = null;
            var nama_pondok = null;
            var id_asal_sekolah = null;
            var nama_asal_sekolah = null;
            $.each(data, function (index, item) {
                if (index === 'KECAMATAN_SISWA') {
                    id_wilayah = item;
                } else if (index === 'NAMA_KEC') {
                    nama_kec_wilayah = item;
                } else if (index === 'NAMA_KAB') {
                    nama_kab_wilayah = item;
                } else if (index === 'NAMA_PROV') {
                    nama_prov_wilayah = item;
                } else if ((index === 'PONDOK_SISWA') && (index !== null)) {
                    id_pondok = item;
                } else if ((index === 'NAMA_PONDOK_MPS') && (index !== null)) {
                    nama_pondok = item;
                } else if ((index === 'ASAL_SEKOLAH_SISWA') && (index !== null)) {
                    id_asal_sekolah = item;
                } else if ((index === 'NAMA_AS') && (index !== null)) {
                    nama_asal_sekolah = item;
                } else {
                    $("#" + index).val(item);
                }
            });

            $("#KECAMATAN_SISWA").select2("data", {id: id_wilayah, text: nama_kec_wilayah + ' - ' + nama_kab_wilayah + ' - ' + nama_prov_wilayah});
            
            if(id_pondok !== null) $("#PONDOK_SISWA").select2("data", {id: id_pondok, text: nama_pondok});
            else $("#PONDOK_SISWA").select2("data", null);
            
            if(id_asal_sekolah !== null) $("#ASAL_SEKOLAH_SISWA").select2("data", {id: id_asal_sekolah, text: nama_asal_sekolah});
            else $("#ASAL_SEKOLAH_SISWA").select2("data", null);

            create_editor();
        };

        destroy_editor();
        create_ajax('<?php echo site_url('akademik/siswa_editor/view_data'); ?>', 'ID_SISWA=' + ID_SISWA, success);
    }

    function get_jk() {
        var success = function (data) {
            $.each(data, function (index, item) {
                $("#JK_SISWA").append('<option value="' + item.ID_JK + '">' + item.NAMA_JK + '</option>');
            });
        };

        create_ajax('<?php echo site_url('akademik/siswa_editor/get_jk'); ?>', '', success);
    }

    function get_wilayah() {
        var success = function (data) {
            $("#KECAMATAN_SISWA").select2({
                data: data,
                minimumInputLength: 1
            });
        };

        create_ajax('<?php echo site_url('akademik/siswa_editor/get_wilayah'); ?>', '', success);
    }

    function get_pondok() {
        var success = function (data) {
            $("#PONDOK_SISWA").select2({
                data: data,
                minimumInputLength: 2
            });
        };

        create_ajax('<?php echo site_url('master_data/pondok_siswa/auto_complete'); ?>', 'q=' + null, success);
    }

    function get_asal_sekolah() {
        var success = function (data) {
            $("#ASAL_SEKOLAH_SISWA").select2({
                data: data,
                minimumInputLength: 2
            });
        };

        create_ajax('<?php echo site_url('master_data/asal_sekolah/auto_complete'); ?>', 'q=' + null, success);
    }

    function create_form() {
        var tag_html = '\n\
                <div class="col-lg-7" id="datatable_editor">\n\
                    <div class="hpanel">\n\
                            <div class="panel-heading hbuilt">\n\
                                <div class="panel-tools">\n\
                                    <a class="closebox" onclick="destroy_editor()"><i class="fa fa-times"></i></a>\n\
                                </div>\n\
                                Form Siswa Editor\n\
                            </div>\n\
                            <div class="panel-body">\n\
                                <form action="#" method="post" class="form-horizontal" onsubmit="return save_data();" id="detail_siswa">\n\
                                        \n\
                                </form>\n\
                            </div>\n\
                    </div>\n\
                </div>\n\
            ';

        $(tag_html).insertAfter("#datatable_siswa");
        $("#datatable_editor").hide();
        $("#detail_siswa").append('<?php $this->generate->input_hidden('ID_SISWA', NULL); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('NIK', array('name' => 'NIK_SISWA', 'id' => 'NIK_SISWA', 'maxlength' => 19, 'value' => ''), TRUE, 4); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Nama Lengkap', array('name' => 'NAMA_SISWA', 'id' => 'NAMA_SISWA', 'maxlength' => 200, 'value' => ''), TRUE, 8); ?>');
        $("#detail_siswa").append('<?php
$this->generate->input_dropdown('JK', 'JK_SISWA', NULL, TRUE, 4);
?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Tempat Lahir', array('name' => 'TEMPAT_LAHIR_SISWA', 'id' => 'TEMPAT_LAHIR_SISWA', 'maxlength' => 200, 'value' => ''), TRUE, 6); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Tanggal Lahir (YYYY-MM-DD)', array('name' => 'TANGGAL_LAHIR_SISWA', 'id' => 'TANGGAL_LAHIR_SISWA', 'maxlength' => 400, 'value' => ''), TRUE, 3); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Alamat', array('name' => 'ALAMAT_SISWA', 'id' => 'ALAMAT_SISWA', 'maxlength' => 400, 'value' => ''), TRUE, 8); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Kecamatan', array('name' => 'KECAMATAN_SISWA', 'id' => 'KECAMATAN_SISWA', 'maxlength' => 100, 'value' => ''), TRUE, 8); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Nama Ayah', array('name' => 'AYAH_NAMA_SISWA', 'id' => 'AYAH_NAMA_SISWA', 'maxlength' => 200, 'value' => ''), TRUE, 5); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Nama Ibu', array('name' => 'IBU_NAMA_SISWA', 'id' => 'IBU_NAMA_SISWA', 'maxlength' => 200, 'value' => ''), TRUE, 5); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Pondok', array('name' => 'PONDOK_SISWA', 'id' => 'PONDOK_SISWA', 'maxlength' => 100, 'value' => ''), TRUE, 8); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('Asal Sekolah', array('name' => 'ASAL_SEKOLAH_SISWA', 'id' => 'ASAL_SEKOLAH_SISWA', 'maxlength' => 100, 'value' => ''), FALSE, 8); ?>');
        $("#detail_siswa").append('<?php $this->generate->input_text('No HP', array('name' => 'ORTU_NOHP1_SISWA', 'id' => 'ORTU_NOHP1_SISWA', 'maxlength' => 15, 'value' => ''), FALSE, 5); ?>');
        $("#detail_siswa").append('<button class="btn btn-info pull-right" type="submit"><i class="fa fa-save"></i>&nbsp;&nbsp;Simpan</button>');
        $(".control-label").addClass('col-sm-3').removeClass('col-sm-2');
        $(".help-block").remove();
        $("#NIK_SISWA").inputmask({"mask": "9999 9999 9999 9999"});
        $("#TANGGAL_LAHIR_SISWA").inputmask({"mask": "9999-99-99"});
        $("#ORTU_NOHP1_SISWA").inputmask({"mask": "999 999 999 999"});
    }

    function save_data() {
        var id = "detail_siswa";
        var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
        var success = function (data) {
            remove_splash();

            if (data.status) {
                create_homer_success('', 'Data berhasil disimpan. Halaman ini akan dimuat ulang.');
                reload_datatables(table);
                destroy_editor();
            } else {
                create_homer_error('Gagal menyimpan data. ' + data.msg);
            }

        };

        create_form_ajax('<?php echo site_url('akademik/siswa/ajax_update') ?>', id, success, message);

        return false;
    }

    function destroy_editor() {
        $("#datatable_editor").slideUp();
    }

    function create_editor() {
        $("#datatable_editor").slideDown();
    }

    function kartu_pelajar(ID_SISWA) {
        window.open('<?php echo site_url('akademik/siswa/kartu'); ?>/' + ID_SISWA, '_blank');
    }
</script>