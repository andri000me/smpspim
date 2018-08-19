<?php
$title = 'Import dan Export data siswa';
$subtitle = "Form untuk import dan export data siswa";
$mode_edit = TRUE;
$id_form = 'form-siswa';
$name_function = 'siswa';

$this->generate->generate_panel_content($title, $subtitle);
?>

<div class="content animate-panel table-datatable1">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form Export
                </div>
                <div class="panel-body text-center">
                    <form action="#" method="post" class="form-horizontal" enctype="multipart/form-data" id="form-import">
                        <div class="form-group">
                            <label class="col-md-1 control-label">Pilih Filter</label>
                            <div class="col-md-2">
                                <select id="jenjang" class="form-control">
                                    <option value="">-- Pilih Jenjang --</option>
                                    <?php
                                    foreach ($JENJANG as $value) {
                                        echo '<option value="' . $value->ID_DEPT . '">' . $value->NAMA_DEPT . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select id="tingkat" class="form-control">
                                    <option value="">-- Pilih Tingkat --</option>
                                    <?php
                                    foreach ($TINGKAT as $value) {
                                        echo '<option value="' . $value->ID_TINGK . '">' . $value->KETERANGAN_TINGK . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select id="kelas" class="form-control">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php
                                    foreach ($KELAS as $value) {
                                        echo '<option value="' . $value->ID_KELAS . '">' . $value->NAMA_KELAS . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select id="jk" class="form-control">
                                    <option value="">-- Pilih JK --</option>
                                    <option value="L">Banin</option>
                                    <option value="P">Banat</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary" onclick="export_data();">EXPORT DATA SISWA</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Form Import
                </div>
                <div class="panel-body">
                    <h3>Peringatan!!!</h3>
                    <ul>
                        <li>File yang di import adalah file hasil export form diatas</li>
                        <li>Tidak boleh melakukan perubahan pada baris 1 (pertama) pada file export</li>
                        <li>Pastikan ID yang digunakan adalah ID yang telah terdaftar pada master data</li>
                        <li>Tidak diperbolehkan menghapus kolom</li>
                        <li>Tidak diperbolehkan merubah ID_SISWA</li>
                        <li>Diperbolehkan menghapus data siswa yang tidak dirubah</li>
                    </ul>
                    <form action="#" method="post" class="form-horizontal" enctype="multipart/form-data" id="form-import">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Pilih file</label>
                            <div class="col-md-5">
                                <input type="file" name="FILE_EXCEL" id="FILE_EXCEL" class="form-control"/>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary" onclick="import_data();">IMPORT DATA SISWA</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function export_data() {

        window.open('<?php echo site_url('akademik/siswa/export_data'); ?>?jenjang=' + $("#jenjang").val() + '&tingkat=' + $("#tingkat").val() + '&kelas=' + $("#kelas").val() + '&jk=' + $("#jk").val(), '_blank');
    }

    function import_data() {
        var data = {};
        var success = function (data, status) {
            if(data.status)
                create_homer_success(data.msg);
            else
                create_homer_error(data.msg);
        };

        create_ajax_file('<?php echo site_url('akademik/siswa/import_data'); ?>', 'FILE_EXCEL', data, success);

        return false;
    }
</script>