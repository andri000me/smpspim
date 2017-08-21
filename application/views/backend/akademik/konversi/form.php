<?php
$title = 'Proses Konversi Siswa';
$subtitle = "Proses ini meliputi perpindahan siswa ke jenjang atau kelas yang lain.";

$this->generate->generate_panel_content($title, $subtitle);

$id_form = 'form-konversi';
$name_function = 'konversi';
?>
<div class="content animate-panel">
    <?php 
    echo $this->generate->form_open($id_form, $name_function); 
    $this->generate->input_hidden('ID_SISWA', "");
    $this->generate->input_hidden('ID_AS', "");
    ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        FORM KONVERSI SISWA
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
                                        <small class="stats-label">Proses Konversi Siswa</small>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        Siswa ini sekarang berada pada jenjang: <br>
                                        <h3 class="text-big" id="data_jenjang"></h3><br>
                                        tingkat: <br>
                                        <h3 class="text-big" id="data_tingkat"></h3><br>
                                        akan dipindah ke:<br><br>
                                        <select class="form-control" name="ID_TINGKAT" id="pilihan_tingkat" onchange="change_tingkat(this);"></select><br>
                                        kelas:<br><br>
                                        <select class="form-control" name="ID_KELAS" id="pilihan_kelas"></select><br><br><br>
                                        <button class="btn btn-success btn-block" type="submit">PROSES SISWA</button>
                                    </div>
                                </div>
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
    var JK_SISWA = null;
    
    $(document).ready(function () {
        $(".form-detail").hide();
    });

    $(".js-source-states-input").select2({
        minimumInputLength: 1,
        escapeMarkup: function (markup) {
            return markup;
        },
        ajax: {
            url: '<?php echo site_url('akademik/konversi/ac_siswa'); ?>',
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
            $(".form-detail").slideDown();
            show_data_siswa(data.siswa);
            show_tingkat(data.tingkat);
            
            remove_splash();
        }

        create_ajax('<?php echo site_url('akademik/konversi/get_data_siswa'); ?>', 'ID_SISWA=' + ID_SISWA, success_siswa);
    }

    function show_data_siswa(data) {
        $("#ID_AS").val(data.ID_AS);
        JK_SISWA = data.JK_SISWA;
        
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
    
    function show_tingkat(data) {
        $("#pilihan_tingkat").html('<option value="">-- Pilih Tingkat --</option>');
        $.each(data, function(index, item){
            $("#pilihan_tingkat").append('<option value="' + item.ID_TINGK + '">' + item.KETERANGAN_TINGK + '</option>');
        });
    }

    function action_save_<?php echo $name_function; ?>(id) {
        var status_tingkat = $("#pilihan_tingkat").val();
        var status_kelas = $("#pilihan_kelas").val();
        var message = 'Sistem sedang menyimpan pembayaran';
        var success = function (data) {
            remove_splash();
            
            if (data.status) create_homer_success(data.msg);
            else create_homer_error(data.msg);
            
            setTimeout(function(){
                reload_window();
            }, 3500);
        };
        var action = function(isConfirm) {
            if(isConfirm) create_form_ajax('<?php echo site_url('akademik/konversi/proses'); ?>', id, success, message);
        };
        
        remove_homer();

        if((status_tingkat === "") || (status_tingkat === null)) 
            create_homer_error("Silahkan pilih tingkat tujuan terlebih dahulu.");
        else if((status_kelas === "") || (status_kelas === null)) 
            create_homer_error("Silahkan pilih kelas tujuan terlebih dahulu.");
        else 
            create_swal_option("Apakah Anda yakin?", "Proses konversi akan memindahkan siswa menuju jenjang dan tingkat tujuan. Semua data siswa yang berhubungan dengan jenjang dan tingkat lama akan dihapus dan data tidak dapat dikembalikan lagi. Proses ini tidak dapat diulang.", action);
        
        return false;
    }
    
    function change_tingkat(t) {
        create_splash("Sistem sedang mengambil data kelas");
        var ID_TINGK = $(t).val();
        var tag_kelas = $("#pilihan_kelas");
        var success = function(data) {
            tag_kelas.html("<option value='' >-- Pilih Kelas --</option>");
            
            $.each(data.kelas, function(index, detail){
                tag_kelas.append("<option value='" + detail.ID_KELAS + "' >" + detail.NAMA_KELAS + "</option>");
            });
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('akademik/konversi/change_tingkat'); ?>','ID_TINGK=' + ID_TINGK + "&JK_SISWA=" + JK_SISWA, success);
    }
</script>