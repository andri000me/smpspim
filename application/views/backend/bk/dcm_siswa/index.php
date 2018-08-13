<script type="text/javascript">

    $(document).ready(function () {

    });

    functionDrawCallback = function (settings, json) {
        $(".loading-soal").trigger('click');
    };

    functionInitComplete = function (settings, json) {
        $("#datatable1").find('th').each(function () {
            if ($(this).html() === 'TAMBAH') {
                $(this).css('width', '270px');
            }
            if ($(this).html() === 'SOAL') {
                $(this).css('min-width', '300px');
            }
        });
        $(".datatables-search-TAMBAH, .datatables-search-SOAL").remove();

        $(document).on('keypress', '.input-dcm', function (e) {
            if (e.which == 13) {
                $(this).next().trigger('click');
            }
        });
    };

    function simpan_dcm(t) {
        var nilai = $(t).prev().val();
        var siswa = $(t).prev().data('siswa');
        var kategori = $(t).prev().prev().val();

        create_ajax('<?php echo site_url($url . '/simpan'); ?>', 'siswa=' + siswa + '&nilai=' + nilai + '&kategori=' + kategori, function (data) {
            if (data.status) {
                create_homer_success(data.msg);
                $(t).prev().val("");
                $(t).parent().next().html('<img src="<?php echo base_url('assets/images/loading-bars.svg'); ?>" class="loading-soal" onload="get_dcm(this)" data-siswa="' + siswa + '"/>');
            } else {
                create_homer_error(data.msg);
            }
            $(t).prev().focus();
        });
    }

    function get_dcm(t) {
        var siswa = $(t).data('siswa');
        
        create_ajax('<?php echo site_url($url . '/get_dcm'); ?>', 'siswa=' + siswa, function (data) {
            $(t).parent().html('<div class="row detail-dcm-' + siswa + '"></div>');
            $.each(data, function (index, item) {
                $(".detail-dcm-" + siswa).append('<div class="col-lg-12">' + item + '</div>');
            });
        });
    }

</script>