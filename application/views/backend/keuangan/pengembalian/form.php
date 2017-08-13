<?php
$id_form = 'form-tagihan';
$name_function = 'tagihan';
?>
<div class="small-header transition animated fadeIn">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>
            <h2 class="font-light m-b-xs">
                PENGEMBALIAN PEMBAYARAN SISWA
            </h2>
            <small>Form pengembalian pengembalian siswa</small>
        </div>
    </div>
</div>
<div class="content animate-panel">
    <?php echo $this->generate->form_open($id_form, $name_function); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        FORM PENGEMBALIAN PEMBAYARAN
                    </div>
                    <div class="panel-body">
                        <div class="row form-input">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">NIS/NAMA</label>
                                    <div class="col-md-7">
                                        <input class="form-control js-source-states-input" type="text" multiple="multiple">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed form-detail"></div>
                        <div class="row form-detail">
                            <div class="col-md-6 border-right" style="">
                                <div class="row">
                                    <div class="col-md-12 text-center" id="foto_siswa">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="stats-label">NIS</small>
                                        <h4 id="data_nis"></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="stats-label">NIK</small>
                                        <h4 id="data_nik"></h4>
                                    </div>
                                    <div class="col-md-11">
                                        <small class="stats-label">Nama</small>
                                        <h4 id="data_nama"></h4>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <small class="stats-label">JK</small>
                                        <h4 id="data_jk"></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="stats-label">Angkatan</small>
                                        <h4 id="data_angkatan"></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="stats-label">Kelas</small>
                                        <h4 id="data_kelas"></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="stats-label">Jenjang</small>
                                        <h4 id="data_jenjang"></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="stats-label">Tingkat</small>
                                        <h4 id="data_tingkat"></h4>
                                    </div>
                                    <div class="col-md-8">
                                        <small class="stats-label">Tempat Lahir</small>
                                        <h4 id="data_tempat_lahir"></h4>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <small class="stats-label">Tanggal Lahir</small>
                                        <h4 id="data_tanggal_lahir"></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="stats-label">Nama Ayah</small>
                                        <h4 id="data_nama_ayah"></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="stats-label">Nama Ibu</small>
                                        <h4 id="data_nama_ibu"></h4>
                                    </div>
                                    <div class="col-md-12">
                                        <small class="stats-label">Alamat</small>
                                        <h4 id="data_alamat"></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <small class="stats-label">Tagihan</small>
                                    </div>
                                </div>
                                <div id="data_tagihan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h4>PEMBAYARAN</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4 text-center">
                                <input class="form-control" type="text" id="nominal" onkeydown="temp_nominal(this);" onkeyup="input_format(this);">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4 text-center">
                                <h3 id="input_nominal"></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                <input type="hidden" name="ID_SISWA" id="ID_SISWA">
                                <input class="form-control" type="text" name="KETERANGAN" id="keterangan" placeholder="Keterangan pengembalian">
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-2 col-md-offset-5 text-center">
                                <button class="btn btn-success btn-block" type="button" id="btn_bayar" onclick="simpan_pengembalian();">BAYAR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    var ID_SISWA = null;
    var TOTAL_TAGIHAN = 0;
    var TEMP_NOMINAL = 0;
    var NOMINAL = 0;

    $(document).ready(function () {
        $(".panel-footer, .form-detail, #nominal").hide();
        reset_nominal();
    });

    $(".js-source-states-input").select2({
        minimumInputLength: 1,
        escapeMarkup: function (markup) {
            return markup;
        },
        ajax: {
            url: '<?php echo site_url('keuangan/pengembalian/ac_siswa'); ?>',
            dataType: "json",
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
        },
        formatSelection: function (element) {
            return element.text;
        },
    }).on("change", function (e) {
        var data = $(".js-source-states-input").select2('data');
        ID_SISWA = data.id;
        
        $("#ID_SISWA").val(ID_SISWA);

        create_splash('Sistem sedang mengambil data');

        reset_nominal();
        get_data_siswa();
        get_data_tagihan();
    });

    function temp_nominal(t) {
        TEMP_NOMINAL = $(t).val();
    }

    function input_format(t) {
        NOMINAL = $(t).val();

        if (nominal === "" || nominal == 0)
            $('#btn_bayar').prop('disabled', true);
        else
            $('#btn_bayar').removeAttr('disabled');

        if (NOMINAL <= TOTAL_TAGIHAN) {
            $("#input_nominal").html(formattedIDR(NOMINAL));
        } else {
            NOMINAL = TEMP_NOMINAL;
            create_homer_error("Nominal pengembalian tidak boleh lebih dari total tagihan");
            $(t).val(NOMINAL);
        }
    }

    function reset_nominal() {
        TEMP_NOMINAL = 0;
        TOTAL_TAGIHAN = 0;
        NOMINAL = 0;
        $('#btn_bayar').prop('disabled', true);
        $("#nominal").val('0');
        $("#keterangan").val('');
        $("#input_nominal").html(formattedIDR(0));
    }

    function get_data_siswa() {
        var success_siswa = function (data) {
            $(".panel-footer, .form-detail").slideDown();
            show_data_siswa(data);
        }

        create_ajax('<?php echo site_url('keuangan/pengembalian/get_data_siswa'); ?>', 'ID_SISWA=' + ID_SISWA, success_siswa);
    }

    function get_data_tagihan() {
        var success_tag = function (data) {
            $(".panel-footer, .form-detail").slideDown();
            show_data_tagihan(data);
        }

        create_ajax('<?php echo site_url('keuangan/pengembalian/get_data_tagihan'); ?>', 'ID_SISWA=' + ID_SISWA, success_tag);
    }

    function show_data_siswa(data) {
        if (data.FOTO_SISWA == null)
            $("#foto_siswa").html('<img src="<?php echo base_url('files/no_image.jpg'); ?>" class="img-rounded"  width="300"/>');
        else
            $("#foto_siswa").html('<img src="<?php echo base_url('files/siswa/'); ?>' + data.FOTO_SISWA + '" class="img-rounded"  width="300"/>');
        if (data.NIS_SISWA == null)
            $("#data_nis").text("-");
        else
            $("#data_nis").text(data.NIS_SISWA);
        if (data.NIK_SISWA == null)
            $("#data_nik").text("-");
        else
            $("#data_nik").text(data.NIK_SISWA);
        $("#data_nama").text(data.NAMA_SISWA);
        $("#data_jk").text(data.JK_SISWA);
        $("#data_angkatan").text(data.ANGKATAN_SISWA);
        if (data.NAMA_KELAS == null)
            $("#data_kelas").text("-");
        else
            $("#data_kelas").text(data.NAMA_KELAS);
        $("#data_tempat_lahir").text(data.TEMPAT_LAHIR_SISWA);
        $("#data_tanggal_lahir").text(formattedDate(data.TANGGAL_LAHIR_SISWA));
        $("#data_nama_ayah").text(data.AYAH_NAMA_SISWA);
        $("#data_nama_ibu").text(data.IBU_NAMA_SISWA);
        $("#data_alamat").text(data.ALAMAT_SISWA + ', ' + data.NAMA_KEC_SISWA + ', ' + data.NAMA_KAB_SISWA + ', ' + data.NAMA_PROV_SISWA);
        if (data.DEPT_TINGK_NOW == null)
            $("#data_jenjang").text("-");
        else
            $("#data_jenjang").text(data.DEPT_TINGK_NOW);
        if (data.NAMA_TINGK_NOW == null)
            $("#data_tingkat").text("-");
        else
            $("#data_tingkat").text(data.NAMA_TINGK_NOW);
    }

    function show_data_tagihan(data) {
        var tag_html = "";
        var ta = null;
        $.each(data, function (item, detail) {
            if (ta !== detail.ID_TA) {
                ta = detail.ID_TA;
                tag_html += '<div class="row"><div class="col-md-12"><h3># TA: ' + detail.NAMA_TA + '</h3></div></div>';
            }
            var sisa_tagihan = parseFloat(detail.NOMINAL_DT);
            TOTAL_TAGIHAN += sisa_tagihan;
            tag_html += '<div class="row"><div class="col-md-8"><h4><input type="checkbox" onclick="check_tagihan();" class="status_tagihan" data-nominal="' + sisa_tagihan + '" name="PEMBAYARAN[]" value="' + detail.ID_SETUP + '">&nbsp;&nbsp;' + ((parseInt(detail.PSB_TAG) === 1) ? 'PSB&nbsp;-&nbsp;' : '') + detail.NAMA_DT + '</h4></div><div class="col-md-4 text-right"><h4>' + formattedIDR(sisa_tagihan) + '</h4></div></div>';
        });
        tag_html += '<hr><div class="row"><div class="col-md-6"><h3>Total tagihan: </h3></div><div class="col-md-6 text-right"><h3>' + formattedIDR(TOTAL_TAGIHAN) + '</h3></div></div>';
        $("#data_tagihan").html(tag_html);

        remove_splash();
    }
    
    function check_tagihan() {
        var nominal = 0;
        
        $(".status_tagihan").each(function( index ) {
            if($(this).is(':checked')) {
                nominal += parseInt($(this).data("nominal"));
            }
        });
        
        NOMINAL = nominal;
        $("#nominal").val(nominal);
        $("#input_nominal").html(formattedIDR(nominal));
        
        if (nominal == 0)
            $('#btn_bayar').prop('disabled', true);
        else
            $('#btn_bayar').removeAttr('disabled');
    }

    function simpan_pengembalian() {
        var action = function (isConfirm) {
            if (isConfirm) {
                action_save_<?php echo $name_function; ?>('<?php echo $id_form; ?>');
            }
        };
        create_swal_option('Apakah Anda yakin?', 'Siswa membayar tagihan dengan nominal ' + formattedIDR(NOMINAL), action);
    }

    function action_save_<?php echo $name_function; ?>(id) {
        var message = 'Sistem sedang menyimpan pengembalian';

        var success = function (data) {
            if (data.status) {
                cetak_pengembalian(data.nota);

                create_homer_success(data.msg);
            } else {
                create_homer_error(data.msg);
            }

            remove_splash();

            reload_window();
        }

        create_form_ajax('<?php echo site_url('keuangan/pengembalian/ajax_add'); ?>', id, success, message);
    }

    function cetak_pengembalian(id) {
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

        newwin = window.open("<?php echo site_url('keuangan/pengembalian/ajax_cetak'); ?>/" + id, "windowname5", params);
        if (window.focus) {
            newwin.focus()
        }
        return false;
    }
</script>