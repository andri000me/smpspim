<?php
$title = ($ADD ? 'Tambah ' : 'Ubah ').'Nilai Hafalan';
$subtitle = "Form nilai hafalan";

$this->generate->generate_panel_content($title, $subtitle);

$id_form = 'form-hafalan';
$name_function = 'hafalan';
?>
<div class="content animate-panel">
    <?php 
    echo $this->generate->form_open($id_form, $name_function); 
    if(!$ADD) $this->generate->input_hidden('ID_SISWA', $DATA->ID_SISWA);
    ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        Form Nilai
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 form-nilai">
                                <?php $this->generate->input_select2('Nama Siswa', array('name' => 'ID_SISWA', 'url' => site_url('ph/cetak_kartu/ac_siswa')), TRUE, 8, TRUE, $ADD ? NULL : array('id' => $DATA->SISWA_PHN, 'text' => $DATA->NAMA_SISWA)); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-md-offset-2">
                                <button type="submit" class="btn btn-primary btn-block" id="btn_simpan" disabled="true"><i class="fa fa-print"></i>&nbsp;&nbsp;Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    
    $(document).ready(function () {
        <?php
        if(!$ADD) { ?>
        $(".js-source-states-ID_SISWA").select2("enable", false);
//        $(".js-source-states-PENYEMAK1").select2("enable", false);
//        $(".js-source-states-PENYEMAK2").select2("enable", false);
//        $(".js-source-states-PENYEMAK3").select2("enable", false);

        get_batasan(<?php echo $DATA->ID_SISWA; ?>);
        <?php } else { ?>
        $(".js-source-states-ID_SISWA").on("change", "", function(){
            var data = $(this).select2("data");
            get_batasan(data.id);
        });
        <?php } ?>
    });
    
    function get_batasan(ID_SISWA) {
        create_splash('Sedang mengambil data batasan');
        var success = function(data) {
            $('.form-batasan').remove();
            
            $("#btn_simpan").attr('disabled', 'true');
            $.each(data.data, function(index, item){
                $(".form-nilai").append(create_form_batasan(item));
                $("#btn_simpan").removeAttr('disabled');
            });
            
            remove_splash();
            
            get_pegawai();
            
        };
        
        create_ajax('<?php echo site_url('ph/nilai/get_batasan'); ?>', 'ID_SISWA=' + ID_SISWA, success);
    }
    
    function create_form_batasan(data) {
        return '<div class="form-group form-batasan"><label class="col-sm-2 control-label">Kitab *</label><div class="col-sm-2"><input type="hidden" name="ID_KITAB[]" value="' +data.ID_KITAB + '" /><input type="text" class="form-control" value="' +data.NAMA_KITAB + '" readonly="true" /><span class="help-block m-b-none text-left">Wajib diisi</span></div><label class="col-sm-1 control-label">Batasan</label><div class="col-sm-3"><input type="hidden" name="BATASAN_PHN[]" value="' + data.ID_BATASAN + '"/><input type="text" class="form-control" value="' + data.AKHIR_BATASAN + ' - ' + data.AWAL_BATASAN + '" readonly="true" /></div><label class="col-sm-1 control-label">Nilai Maks</label><div class="col-sm-1"><input type="text" class="form-control" name="NILAI_MAKS_BATASAN[]" value="' + data.NILAI_MAKS_BATASAN + '" readonly="true" /></div><label class="col-sm-1 control-label">Nilai</label><div class="col-sm-1"><input type="text" class="form-control required" name="NILAI_PHN[]" value="' + (data.NILAI_PHN === null ? '' : data.NILAI_PHN) + '" /></div></div><div class="form-group form-batasan"><label class="col-sm-2 control-label option-penyemak">Penyemak *</label><div class="col-sm-6"><select name="PENYEMAK[]" class="form-control penyemak" data-id="' + (data.PENYEMAK_PHN === null ? '' : data.PENYEMAK_PHN) + '"><option value="">-- Pilih Penyemak --</option></select><span class="help-block m-b-none text-left">Wajib diisi</span></div>';
    }
    
    function get_pegawai() {
        create_splash('Sedang mengambil data penyemak');
        var success = function(data) {
            $.each(data.data, function(index, item){
                $(".penyemak").append('<option value="' + item.ID_PEG + '">' + item.NAMA_PEG + '</option>');
            });
            
            $('.penyemak').each(function(){
                var id = $(this).data('id');
                $(this).val(id);
            });
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('ph/nilai/get_penyemak'); ?>', '', success);
    }
  
    function action_save_<?php echo $name_function; ?>(id) {
        var status_required = true;
        var message = "Mohon tunggu sebentar, sistem sedang menyimpan data...";
        var success = function (data) {
            create_homer_success('Data berhasil disimpan. Halaman akan dimuat ulang.');
            remove_splash();
            reload_window();
        };
        
        $(".required").removeClass('error');
        $(".control-label").removeClass('text-danger');
        
        $(".required").each(function () {
            if (this.value == "") {
                status_required = false;

                $(this).addClass('error');
                $(this).parent().prev('.control-label').addClass('text-danger');
            }
        });
        
        if(status_required)
            create_form_ajax('<?php echo site_url('ph/nilai/'.($ADD ? 'ajax_add' : 'ajax_update')) ?>', id, success, message);
        else
            create_homer_error("Silahkan lengkapi terlebih dahulu field yang wajib diisi.");
        
        return false;
    }
</script>