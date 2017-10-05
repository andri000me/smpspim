<?php
$title = 'Jadwal Ujian Sekolah';
$subtitle = "Daftar semua Jadwal Ujian Sekolah";
$id_datatables = 'datatable1';

$columns = array(
    'ID',
    'TA',
    'CAWU',
    'JK',
    'TANGGAL',
    'JAM MULAI',
    'JAM SELESAI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);

$id_modal = "modal-data";
$title_form = "Tambah " . $title;
$id_form = "form-data";

$this->generate->form_modal($id_modal, $title_form, $id_form, $id_datatables);
?>

<div class="modal fade" id="cetak_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Form Cetak Kartu Ujian Per Kelas</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <?php $this->generate->input_select2('Kelas', array('name' => 'ID_KELAS', 'url' => site_url('akademik/kelas/auto_complete')), FALSE, 8, FALSE, NULL); ?>
                </form>
            </div>
            <div class="modal-footer">
                <p class="pull-left">Kosongi kelas untuk mencetak semua kelas.</p>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                <button type="button" class="btn btn-primary" onclick="cetak_kertu_siswa_<?php echo $id_datatables; ?>();" >Cetak</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ID_KELAS = null;
    var table;
    var url_delete = '<?php echo site_url('pu/jadwal_us/ajax_delete'); ?>/';
    var url_add = '<?php echo site_url('pu/jadwal_us/ajax_add'); ?>';
    var url_update = '<?php echo site_url('pu/jadwal_us/ajax_update'); ?>';
    var url_form = '<?php echo site_url('pu/jadwal_us/request_form'); ?>';
    var id_modal = '<?php echo $id_modal; ?>';
    var id_form = '<?php echo $id_form; ?>';
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = '';//[{ "width": "100px", "targets": 2 }, {"targets": [-1],"orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
<?php if ($validasi_denah) { ?>
            window.open('<?php echo site_url('pu/jadwal_us/form'); ?>', '_blank');
<?php } else { ?>
            create_homer_error('Denah pada tahun ajaran aktif belum divalidasi.');
<?php } ?>
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('pu/jadwal_us/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);

        $(".js-source-states-ID_KELAS").on("change", "", function () {
            var data = $(this).select2("data");

            ID_KELAS = data.id;
        });
    });

    function action_save_<?php echo $id_datatables; ?>(id_form) {
        var status = $("#" + id_form).data("status");

        if (status == 'add')
            url = url_add;
        else if (status == 'update')
            url = url_update;

        form_save(url, id_form, table);

        return false;
    }

    function cetak_blanko_nilai() {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_blanko_nilai'); ?>', '_blank');
    }

    function update_data_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/form'); ?>/' + id, '_blank');
    }

    function delete_data_<?php echo $id_datatables; ?>(id) {
        form_delete(url_delete, id, table);
    }

    function cetak_jadwal_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_jadwal'); ?>/' + id, '_blank');
    }

    function cetak_denah_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_denah'); ?>/' + id, '_blank');
    }

    function cetak_absen_pengawas_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_absen_pengawas'); ?>/' + id, '_blank');
    }

    function cetak_absen_peserta_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_absen_peserta'); ?>/' + id, '_blank');
    }

//    function cetak_kertu_meja_<?php echo $id_datatables; ?>(id) {
//        window.open('<?php echo site_url('pu/jadwal_us/cetak_kertu_meja'); ?>/' + id, '_blank');
//    }
//    
    function cetak_kertu_siswa_<?php echo $id_datatables; ?>(id) {
        if (ID_KELAS === null) {
            $("#cetak_modal").modal('show');
        } else {
            window.open('<?php echo site_url('pu/jadwal_us/cetak_kertu_siswa'); ?>/' + ID_KELAS, '_blank');
            $("#cetak_modal").modal('hide');
            $(".js-source-states-ID_KELAS").select2('data', null);
            ID_KELAS = null;
        }
    }

    function cetak_sampul_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_sampul'); ?>/' + id, '_blank');
    }

    function cetak_soal_<?php echo $id_datatables; ?>(id) {
        window.open('<?php echo site_url('pu/jadwal_us/cetak_soal'); ?>/' + id, '_blank');
    }


</script>