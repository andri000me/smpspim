<script type="text/javascript">
    var listData = [];
    var tempListData = [];
    var indexListData = 0;

    $(document).ready(function () {
        $('.buttons-add').remove();
        $("#datatable1_wrapper").prepend('<div class="row"><div class="col-md-10" id="list-data"><i>Silahkan pilih siswa untuk dicetak surat pemanggilannya</i></div><div class="col-md-2"><button class="btn btn-primary btn-block" onclick="cetak_surat();"><i class="fa fa-print"></i>&nbsp;&nbsp;CETAK</button></div></div><hr>');
    });

    function cetak_surat() {
        create_ajax('<?php echo site_url($url . '/simpan_surat'); ?>', 'tanggal=' + $('#tanggal_panggil').val() + '&data=' + JSON.stringify(listData), function (data) {
            window.open('<?php echo site_url($url . '/cetak_surat'); ?>/' + data.no_surat);
            window.location.reload();
        });
    }

    function create_list() {
        $("#list-data").html("Tanggal Panggil: <input class='form-control' type='text' id='tanggal_panggil' data-date-format='yyyy-mm-dd' />&nbsp;&nbsp;&nbsp;");
        $("#tanggal_panggil").datepicker("setDate", new Date());
        $("#tanggal_panggil").css('width', '100px');

        listData.forEach(function (item) {
            $("#list-data").append('<button class="btn btn-info btn-xs" onclick="hapus_siswa(this)" data-index="' + item.index + '" data-ksh="' + item.ksh + '">' + item.nis + ' - ' + item.nama + '&nbsp;&nbsp;<i class="fa fa-close"></i></button>&nbsp;&nbsp;');
        });
    }

    function hapus_siswa(t) {
        var index = $(t).data('index');
        var ksh = $(t).data('ksh');

        delete listData[parseInt(index)];
        delete tempListData[parseInt(ksh)];

        create_list();
    }

    function pilih_siswa(t) {
        var ksh = $(t).data('ksh');
        var nis = $(t).data('nis');
        var id = $(t).data('id');
        var nama = $(t).data('nama');
        var kelas = $(t).data('kelas');

        var data = {
            'index': indexListData,
            'ksh': ksh,
            'nis': nis,
            'id': id,
            'nama': nama,
            'kelas': kelas
        };

        if (typeof tempListData[parseInt(ksh)] === 'undefined') {
            tempListData[parseInt(ksh)] = indexListData;
            listData[indexListData] = data;
            indexListData++;

            create_list();
        }
    }

</script>