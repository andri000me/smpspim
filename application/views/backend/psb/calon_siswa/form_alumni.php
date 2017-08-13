<?php
$title = 'Alumni';
$subtitle = "Menambahkan calon siswa dari alumni.";

$this->generate->generate_panel_content($title, $subtitle);

$id_form = 'form-calon_siswa';
$name_function = 'calon_siswa';
?>
    <?php 
    echo $this->generate->form_open($id_form, $name_function); 
    $this->generate->input_hidden('ID_SISWA', "");
    ?>
    <div class="content animate-panel">
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        Form Tambah Calon Siswa dari Alumni
                    </div>
                    <div class="panel-body">
                        <div class="row form-input">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">NIK/NAMA</label>
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
                                        <small class="stats-label">NIK</small>
                                        <h4 id="data_nik"></h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="stats-label">NISN</small>
                                        <h4 id="data_nisn"></h4>
                                    </div>
                                    <div class="col-md-11">
                                        <small class="stats-label">Nama</small>
                                        <h4 id="data_nama"></h4>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <small class="stats-label">JK</small>
                                        <h4 id="data_jk"></h4>
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
                                    <div class="col-md-12 text-center">
                                        <h4>Siswa tersebut mutasi karena:</h4>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h3 id="nama_mutasi"></h3>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h4>pada tanggal:</h4>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h3 id="tanggal_mutasi"></h3>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h4>dengan nomor surat:</h4>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h3 id="no_surat_mutasi"></h3>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h4>pada jenjang:</h4>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h3 id="no_jenjang"></h3>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <h4>akan masuk ke:</h4>
                                    </div>
                                    <div class="col-md-12">
                                    <?php $this->generate->input_select2('jenjang', array('name' => 'MASUK_JENJANG_SISWA', 'url' => site_url('psb/calon_siswa/ac_jenjang_sekolah')), TRUE, 9, FALSE, NULL); ?>
                                    </div>
                                    <div class="col-md-12">
                                    <?php
                                    $this->generate->input_dropdown('tingkat', 'MASUK_TINGKAT_SISWA', array(
                                        array('id' => 0, 'text' => "Pilih jenjang terlebih dahulu", 'selected' => FALSE),
                                        array('id' => 1, 'text' => 1, 'selected' => FALSE),
                                        array('id' => 2, 'text' => 2, 'selected' => FALSE),
                                        array('id' => 3, 'text' => 3, 'selected' => FALSE),
                                        array('id' => 4, 'text' => 4, 'selected' => FALSE),
                                        array('id' => 5, 'text' => 5, 'selected' => FALSE),
                                        array('id' => 6, 'text' => 6, 'selected' => FALSE),
                                            ), TRUE, 9);
                                    ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary btn-block" type="submit" id="proses_siswa">PROSES SISWA KE PSB</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    var ID_SISWA = null;
    var JK_SISWA = null;

    $(document).ready(function () {
        $(".form-detail").hide();
        $("#MASUK_TINGKAT_SISWA").prop("disabled", true);

        $(".js-source-states-MASUK_JENJANG_SISWA").on("change", "", function () {
            var data_jenjang = $(this).select2("data");

            $("#MASUK_TINGKAT_SISWA").removeAttr("disabled");
            get_list_jenjang_siswa(data_jenjang.id);
        });
    });

    function get_list_jenjang_siswa(jenjang) {
        create_splash("Sedang mengambil data tingkat");
        var success = function (data) {
            $("#MASUK_TINGKAT_SISWA").html(" ");

            $.each(data, function (index, detail) {
                $("#MASUK_TINGKAT_SISWA").append("<option value='" + detail.NAMA_TINGK + "'>" + detail.KETERANGAN_TINGK + "</option>");
            });

            remove_splash();
        };

        create_ajax('<?php echo site_url('psb/calon_siswa/list_tingkat_jenjang'); ?>', 'jenjang=' + jenjang, success);
    }

    $(".js-source-states-input").select2({
        minimumInputLength: 1,
        escapeMarkup: function (markup) {
            return markup;
        },
        ajax: {
            url: '<?php echo site_url('master_data/alumni/auto_complete'); ?>',
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
        get_data_siswa(ID_SISWA);
    });

    function get_data_siswa(ID_SISWA) {
        create_splash('Sistem sedang mengambil data');

        var success_siswa = function (data) {
            if (data.siswa === null)
                create_homer_error("Gagal mendapatkan detail siswa. Ada kesalahan dalam database.");
            else {
                $(".form-detail").slideDown();
                show_data_siswa(data.siswa);
            }

            remove_splash();
        }

        create_ajax('<?php echo site_url('psb/calon_siswa/get_data_alumni'); ?>', 'ID_SISWA=' + ID_SISWA, success_siswa);
    }

    function show_data_siswa(data) {
        $("#ID_AS").val(data.ID_AS);
        JK_SISWA = data.JK_SISWA;

        if (data.FOTO_SISWA == null)
            $("#foto_siswa").html('<img src="<?php echo base_url('files/no_image.jpg'); ?>" class="img-rounded"  width="300"/>');
        else
            $("#foto_siswa").html('<img src="<?php echo base_url('files/siswa/'); ?>' + data.FOTO_SISWA + '" class="img-rounded"  width="300"/>');
        if (data.NISN_SISWA == null)
            $("#data_nisn").text("-");
        else
            $("#data_nisn").text(data.NIS_SISWA);
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

        $("#nama_mutasi").text(data.NAMA_MUTASI);
        $("#tanggal_mutasi").text(formattedDate(data.TANGGAL_MUTASI_SISWA));
        $("#no_surat_mutasi").text(data.NO_SURAT_MUTASI_SISWA);
        $("#no_jenjang").text(data.KETERANGAN_TINGK);

        if (data.ID_MUTASI == 98) {
            $("#proses_siswa").attr('disabled', true);
        } else {
            $("#proses_siswa").removeAttr('disabled');
        }
    }

    function action_save_<?php echo $name_function; ?>(id) {
        var success = function (data) {
            if (data.status) {
                create_homer_success('Berhasil memperoses data. Halaman akan dimuat ulang secara otomatis.');
                reload_window();
            } else
                create_homer_error('Gagal memperoses data. ' + data.msg);
            
            remove_splash();
        };
        var action = function (isConfirm) {
            if (isConfirm) {
                var message = 'Sistem sedang memproses data';
                create_form_ajax('<?php echo site_url('psb/calon_siswa/from_alumni'); ?>', id, success, message);
            }
        };

        create_swal_option('Apakah Anda yakin melanjutkan?', '', action);
        
        return false;
    }
</script>