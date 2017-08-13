<?php
$title = 'Cetak Kartu';
$subtitle = "Form pencetak kartu hafalan siswa";

$this->generate->generate_panel_content($title, $subtitle);

$id_form = 'form-hafalan';
$name_function = 'hafalan';
?>
<div class="content animate-panel">
    <?php 
    echo $this->generate->form_open($id_form, $name_function); 
    ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        Tahun Ajaran Aktif
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="#"  data-toggle="modal" data-target="#cetak_modal" onclick="set_type(1);"><button type="button" class="btn btn-primary btn-block"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Blanko Semua Siswa TA Aktif</button></a>
                            </div>
                            <div class="col-md-4">
                                <a href="#"  data-toggle="modal" data-target="#cetak_modal" onclick="set_type(0);"><button type="button" class="btn btn-info btn-block"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Bukti Semua Siswa TA Aktif</button></a>
                            </div>
                            <div class="col-md-4">
                                <a href="#"  data-toggle="modal" data-target="#cetak_modal" onclick="set_type(2);"><button type="button" class="btn btn-success btn-block"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak Monitoring Semua Siswa TA Aktif</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="fullscreen"><i class="fa fa-expand"></i></a>
                        </div>
                        Tahun Ajaran Pilihan
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php $this->generate->input_select2('Nama Siswa', array('name' => 'ID_SISWA', 'url' => site_url('ph/cetak_kartu/ac_siswa')), TRUE, 9, TRUE, NULL); ?>
                                <?php $this->generate->input_select2('Tahun Ajaran', array('name' => 'TA', 'url' => site_url('master_data/tahun_ajaran/auto_complete')), TRUE, 2, FALSE, array('id' => $this->session->userdata('ID_TA_ACTIVE'), 'text' => $this->session->userdata('NAMA_TA_ACTIVE'))); ?>
                                <?php $this->generate->input_select2('Tingkat', array('name' => 'TINGKAT', 'url' => site_url('master_data/tingkat/auto_complete')), TRUE, 3, FALSE, NULL); ?>
                                <?php
$this->generate->input_dropdown('Pilih Cetak', 'MODEL_CETAK', array(
    array('id' => 1, 'text' => "Blanko Pendaftaran", 'selected' => TRUE),
    array('id' => 0, 'text' => 'Bukti Penyemaan', 'selected' => FALSE)
        ), TRUE, 4);
?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-md-offset-5">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-print"></i>&nbsp;&nbsp;Cetak</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="cetak_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-modal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <p class="pull-left">Kosongi kelas untuk mencetak semua kelas.</p> -->
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak();" >Cetak</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ID_KELAS = 0;
    var ID_SISWA = null;
    var TA = <?php echo $this->session->userdata('ID_TA_ACTIVE'); ?>;
    var TINGKAT = null;
    var TYPE = null;
    var BULAN = <?php echo json_encode($this->pengaturan->getBulanHijriyah()); ?>;
    
    $(document).ready(function () {
        $(".js-source-states-ID_SISWA").on("change", "", function(){
            var data = $(this).select2("data");
            ID_SISWA = data.id;
        });
        $(".js-source-states-TA").on("change", "", function(){
            var data = $(this).select2("data");
            TA = data.id;
        });
        $(".js-source-states-TINGKAT").on("change", "", function(){
            var data = $(this).select2("data");
            TINGKAT = data.id;
        });
        $(".js-source-states-ID_KELAS").on("change", "", function(){
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
    });

    function set_type(TYPE_CETAK) {
        TYPE = TYPE_CETAK;
        $(".option-hijriyah").remove();
        var start = true;
        if(parseInt(TYPE_CETAK) === 2) {
            $.each(BULAN, function(index, item) {
                $(".form-modal").append('<div class="form-group option-hijriyah"><label class="col-sm-2 control-label">' + (start ? 'Pilih Bulan' : '') + '</label><div class="col-sm-9"><input type="checkbox" name="bulan" id="bulan" value="' + item + '">&nbsp;&nbsp;<label>' + item + '</label></div></div>'); 
                start = false;
            });
        }
    }

    function cetak() {
        var val_bulan = [];

        $("#cetak_modal").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        if(parseInt(TYPE) === 2) {
            $('input[id="bulan"]:checked').each(function() {
                val_bulan.push(this.value); 
            });
        }
        
        if(ID_KELAS == 0)
            create_homer_error('Silahkan pilih kelas terlebih dahulu.');
        else
            window.open('<?php echo site_url('ph/cetak_kartu/cetak_all'); ?>?blanko=' + TYPE + '&kelas=' + ID_KELAS + '&bulan=' + encodeURIComponent(JSON.stringify(val_bulan)), '_blank');

        ID_KELAS = 0;
    }

    function action_save_<?php echo $name_function; ?>(id) {
        var blanko = $("#MODEL_CETAK").val();
        
        if(ID_SISWA == null || TA == null || TINGKAT == null)
            create_homer_error('Silahkan lengkapi form terlebih dahulu.');
        else
            window.open('<?php echo site_url('ph/cetak_kartu/cetak_all'); ?>?blanko=' + blanko + '&ta=' + TA + '&id_siswa=' + ID_SISWA + '&tingkat=' + TINGKAT, '_blank');
        
        return false;
    }
</script>