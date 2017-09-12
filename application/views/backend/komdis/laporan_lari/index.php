<?php
$title = 'Laporan Lari';
$subtitle = "Daftar semua lari siswa";
$id_datatables = 'datatable1';

$columns = array(
    'NIS',
    'NO ABSEN',
    'NAMA',
    'KELAS',
    'WALI KELAS',
    'JUMLAH POIN',
    'JUMLAH LARI',
    'AKSI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>
<script type="text/javascript">
    var status_check = false;
    var status_show = false;
    var checkbox_kelas = [];
    var ID_KELAS = 0;
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [];
    var orders = [];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {

    };

    $(document).ready(function () {
        get_data_kelas();
        $(".status-show").hide();

        $(".js-source-states-ID_KELAS").on("change", "", function () {
            var data = $(this).select2("data");
            
            $('.checkbox-kelas').removeAttr('checked');
            
            ID_KELAS = data.id;
        });

        table = initialize_datatables(id_table, '<?php echo site_url('komdis/laporan_lari/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $(".buttons-add, .buttons-print").remove();
        $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle">Cetak <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" data-toggle="modal" data-target="#cetak_modal_kelas">Cetak Perkelas</a></li><li><a href="#" onclick="cetak_siswa_multi()">Lari Persiswa</a></li></ul></div><button class="btn btn-sm btn-default" onclick="check_all()">Check All</button>').insertAfter('.buttons-reload');
        $('').insertAfter('.buttons-reload');
    });
    
    function check_all() {
        $(".checkbox").trigger('click');
    }

    function cetak_modal_kelas() {
        $("#cetak_modal_kelas").modal("hide");
        $(".js-source-states-ID_KELAS").select2('data', null);

        if (ID_KELAS > 0) {
            checkbox_kelas = [];
            checkbox_kelas.push(ID_KELAS);
        }

        window.open('<?php echo site_url('komdis/laporan_lari/cetak_perkelas'); ?>?KELAS=' + checkbox_kelas, '_blank');

        ID_KELAS = 0;
    }

    function check_cetak_siswa(that) {
        checkbox_siswa = [];
        $(".checkbox").each(function (index) {
            if ($(this).is(':checked')) {
                checkbox_siswa.push($(this).val());
            }
        });
    }

    function cetak_siswa_multi() {
        if (checkbox_siswa.length > 0)
            window.open('<?php echo site_url('komdis/laporan_poin/cetak_siswa_multi'); ?>?ID_KSH=' + checkbox_siswa, '_blank');
        else
            create_homer_error('Silahkan pilih siswa terlebih dahulu');

        checkbox_siswa = [];
        reload_datatables(table);
    }

    function cetak(ID_KSH) {
        window.open('<?php echo site_url('komdis/laporan_poin/cetak'); ?>/' + ID_KSH, '_blank');
    }

    function get_data_kelas() {
        var success = function (data) {
            console.log(data.length);
            var maks_perkolom = Math.round(data.length / 4) - 1;
            var x = 0;
            var posisi = 0;

            $.each(data, function (key, value) {
                if (x == 0)
                    posisi++;

                $("#checkbox-kelas-" + posisi).append('<label> <input type="checkbox" value="' + value.value + '" class="checkbox-kelas" onchange="checkbox_changed()">&nbsp;&nbsp;' + value.label + '</label>');

                if (x == maks_perkolom)
                    x = 0;
                else
                    x++;
            });
        };
        create_ajax('<?php echo site_url('akademik/kelas/get_all'); ?>', '', success);
    }

    function reset_select2() {
        $(".js-source-states-ID_KELAS").select2('data', null);
        ID_KELAS = 0;
    }

    function checkbox_changed() {
        checkbox_kelas = [];

        reset_select2();

        $(".checkbox-kelas").each(function (index) {
            if ($(this).is(':checked'))
                checkbox_kelas.push($(this).val());
        });
    }

    function toggle_click(that) {
        checkbox_kelas = [];

        reset_select2();

        if (status_check) {
            $(".checkbox-kelas").removeAttr('checked');
            status_check = false;
            $(that).html('Check All');
        } else {
            $(".checkbox-kelas").prop('checked', true);
            status_check = true;
            $(that).html('Uncheck All');

            $(".checkbox-kelas").each(function (index) {
                checkbox_kelas.push($(this).val());
            });
        }
    }
    
    function toggle_show(that) {
        $(".checkbox-kelas").removeAttr('checked');
        checkbox_kelas = [];
        reset_select2();
        
        if(status_show) {
            $(".status-show").slideUp();
            $(that).html('Tampilkan Semua Kelas');
        } else {
            $(".status-show").slideDown();
            $(that).html('Sembunyikan Semua Kelas');
        }
        
        status_show = !status_show;
    }

</script>

<div class="modal fade" id="cetak_modal_kelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Pelanggaran Perkelas</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
                <div class="row status-show">
                    <div class="col-md-12 text-center">
                        <hr>
                        <button type="button" class="btn btn-primary btn-sm" onclick="toggle_click(this)">Check All</button>
                    </div>
                </div>
                <div class="row status-show">
                    <div class="col-md-3"  id="checkbox-kelas-1"></div>
                    <div class="col-md-3"  id="checkbox-kelas-2"></div>
                    <div class="col-md-3"  id="checkbox-kelas-3"></div>
                    <div class="col-md-3"  id="checkbox-kelas-4"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-sm pull-left" onclick="toggle_show(this)">Tampilkan Semua Kelas</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_modal_kelas();" >Cetak</button>
            </div>
        </div>
    </div>
</div>