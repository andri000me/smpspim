<?php
$title = 'Alumni';
$subtitle = "Daftar semua alumni";
$id_datatables = 'datatable1';

$columns = array(
//    'NIS',
    'NAMA',
    'ANGKATAN',
    'JK',
    'ORANGTUA',
//    'TEMPAT LAHIR',
//    'TANGGAL LAHIR',
    'ALAMAT',
    'KECAMATAN',
    'KABUPATEN',
    'PROVINSI',
    'STATUS MUTASI',
    'AKSI',
);

$this->generate->generate_panel_content("Data " . $title, $subtitle);
$this->generate->datatables($id_datatables, $title, $columns);
?>

<div id="modal_view"></div>
<div id="view_photo"></div>

<script type="text/javascript">
    var table;
    var id_table = '<?php echo $id_datatables; ?>';
    var title = '<?php echo $title; ?>';
    var columns = [{"targets": [-1], "orderable": false}];
    var orders = [[0, "ASC"]];
    var requestExport = true;
    var functionInitComplete = function (settings, json) {

    };
    var functionDrawCallback = function (settings, json) {

    };
    var functionAddData = function (e, dt, node, config) {
        create_homer_error("Anda tidak memiliki hak akses untuk menambah alumni.");
    };

    $(document).ready(function () {
        table = initialize_datatables(id_table, '<?php echo site_url('master_data/alumni/ajax_list'); ?>', columns, orders, functionInitComplete, functionDrawCallback, functionAddData, requestExport);
        
        $(".buttons-add").remove();
        
        create_view_modal("modal_view", id_table, "Detail Siswa");
        
        $("#view_photo").hide();
        $("#view_photo").click(function(){
            $(".splash-photo").remove();
            $("#view_photo").hide();
        });
    });

    function view_data(ID_SISWA) {
//        create_splash("Sistem  sedang mangambil data.");
        var success = function(data) {
            $('.detail_' + id_table).html(" ");
            
            list_group(id_table, "Informasi Pribadi");
            list_detail(id_table, 6, 'NIS', data.NIS_SISWA, 6, 'NISN', data.NISN_SISWA);
            list_detail(id_table, 6, 'NIK', data.NIK_SISWA, 6, 'NO. UN', data.NO_UN_SISWA);
            list_detail(id_table, 9, 'NAMA', data.NAMA_SISWA, 3, 'PANGGILAN', data.PANGGILAN_SISWA);
            list_detail(id_table, 6, 'TANGGAL MASUK', formattedDate(data.TANGGAL_MASUK_SISWA), 6, 'ANGKATAN', data.ANGKATAN_SISWA);
            list_detail(id_table, 6, 'TEMPAT LAHIR', data.TEMPAT_LAHIR_SISWA, 6, 'TANGGAL LAHIR', data.TANGGAL_LAHIR_SISWA);
            list_detail(id_table, 6, 'BERAT BADAN', data.BERAT_SISWA, 6, 'TINGGI BADAN', data.TINGGI_SISWA);
            list_detail(id_table, 6, 'JUMLAH SAUDARA', data.JUMLAH_SDR_SISWA, 6, 'ANAK KE-', data.ANAK_KE_SISWA);
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Kontak");
            list_detail(id_table, 6, 'ALAMAT', data.ALAMAT_SISWA, 6, 'KECAMATAN', data.NAMA_KEC_SISWA);
            list_detail(id_table, 6, 'KABUPATEN', data.NAMA_KAB_SISWA, 6, 'PROVINSI', data.NAMA_PROV_SISWA);
            list_detail(id_table, 6, 'KODE POS', data.KODE_POS_SISWA, 6, 'GOLONGAN DARAH', data.NAMA_DARAH);
            list_detail(id_table, 6, 'NO. HP', data.NOHP_SISWA, 6, 'EMAIL', data.EMAIL_SISWA);
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Asal Sekolah");
            list_detail(id_table, 6, 'JENJANG SEKOLAH ASAL', data.NAMA_JS, 6, 'SEKOLAH ASAL', data.NAMA_AS);
            list_detail(id_table, 6, 'MASUK JENJANG', data.NAMA_JS, 6, 'MASUK TINGKAT', data.MASUK_TINGKAT_SISWA);
            list_detail(id_table, 6, 'NO. IJASAH', data.NO_IJASAH_SISWA, 6, 'TANGGAL IJASAH', formattedDate(data.TANGGAL_IJASAH_SISWA));
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Ayah");
            list_detail(id_table, 6, 'NIK AYAH', data.AYAH_NIK_SISWA, 6, 'STATUS HIDUP', data.NAMA_SO_AYAH);
            list_detail(id_table, 6, 'NAMA AYAH', data.AYAH_NAMA_SISWA, 6, 'PENDIDIKAN TERAKHIR', data.NAMA_JP_AYAH);
            list_detail(id_table, 6, 'TEMPAT LAHIR', data.AYAH_TEMPAT_LAHIR_SISWA, 6, 'TANGGAL LAHIR', formattedDate(data.AYAH_TANGGAL_LAHIR_SISWA));
            list_detail(id_table, 6, 'PEKERJAAN', data.NAMA_JENPEK_AYAH, 6, '', '');
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Ibu");
            list_detail(id_table, 6, 'NIK IBU', data.IBU_NIK_SISWA, 6, 'STATUS HIDUP', data.NAMA_SO_IBU);
            list_detail(id_table, 6, 'NAMA IBU', data.IBU_NAMA_SISWA, 6, 'PENDIDIKAN TERAKHIR', data.NAMA_JP_IBU);
            list_detail(id_table, 6, 'TEMPAT LAHIR', data.IBU_TEMPAT_LAHIR_SISWA, 6, 'TANGGAL LAHIR', formattedDate(data.IBU_TANGGAL_LAHIR_SISWA));
            list_detail(id_table, 6, 'PEKERJAAN', data.NAMA_JENPEK_IBU, 6, '', '');
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Wali");
            list_detail(id_table, 6, 'NIK WALI', data.WALI_NIK_SISWA, 6, 'HUBUNGAN', data.NAMA_HUB);
            list_detail(id_table, 6, 'NAMA WALI', data.IBU_NAMA_SISWA, 6, 'PENDIDIKAN TERAKHIR', data.NAMA_JP_WALI);
            list_detail(id_table, 6, 'PEKERJAAN', data.NAMA_JENPEK_IBU, 6, '', '');
            
            $('.detail_' + id_table).append('<hr>');
            list_group(id_table, "Orang Tua");
            list_detail(id_table, 6, 'ALAMAT', data.ORTU_ALAMAT_SISWA, 6, 'KECAMATAN', data.NAMA_KEC_ORTU);
            list_detail(id_table, 6, 'KABUPATEN', data.NAMA_KAB_ORTU, 6, 'PROVINSI', data.NAMA_PROV_ORTU);
            list_detail(id_table, 6, 'PENGHASILAN', data.NAMA_HASIL, 6, 'NO. HP', data.ORTU_NOHP1_SISWA);
            list_detail(id_table, 6, 'NO. HP', data.ORTU_NOHP2_SISWA, 6, 'NO. HP', data.ORTU_NOHP3_SISWA);
            list_detail(id_table, 6, 'EMAIL', data.ORTU_EMAIL_SISWA, 6, '', '');
            
            $('#view_data_' + id_table).modal('show');
            remove_splash();
        };
        
//        create_ajax('<?php echo site_url('master_data/alumni/view_data'); ?>', 'ID_SISWA=' + ID_SISWA, success);
        window.open('<?php echo site_url('pencarian/detail'); ?>/' + ID_SISWA, '_blank');
    }
    
    
    function view_photo(ID_SISWA) {
        create_splash("Sistem  sedang mangambil data.");
        var success = function(data) {
            if(data.status) {
                $('#view_photo').html('<div class="splash-photo text-center"><img src="<?php echo base_url('files/alumni'); ?>/' + data.data.FOTO_SISWA + '" class="img-rounded m-b" alt="Foto ' + data.data.NAMA_SISWA + '"><h1>Foto ' + data.data.NAMA_SISWA + '</h1></div>');
                $("#view_photo").slideDown();
            } else {
                create_homer_error('Foto tidak ditemukan');
            }
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('master_data/alumni/view_photo'); ?>', 'ID_SISWA=' + ID_SISWA, success);
    }
</script>