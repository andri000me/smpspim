<script type="text/javascript">

    function detail_penanganan(t) {
        $('.detail-penanganan').slideUp();
        create_ajax('<?php echo site_url('bk/laporan_penanganan/get_data'); ?>', 'id=' + $(t).data('id'), function (data) {
            $(".detail-penanganan").remove();
            $('<tr class="detail-penanganan" style="display: none;"><td colspan="10"><div class="row"><div class="col-lg-12"><strong>Detail Penanganan:</strong></div></div><div class="row"><div class="col-lg-1 col-lg-offset-1"><strong>Penyebab</strong></div><div class="col-lg-10">: ' + data.PENYEBAB_PENANGANAN + '</div></div><div class="row"><div class="col-lg-1 col-lg-offset-1"><strong>Solusi</strong></div><div class="col-lg-10">: ' + data.SOLUSI_PENANGANAN + '</div></div></td></tr>').insertAfter($(t).parent().parent());
            $('.detail-penanganan').slideDown();
        });
    }

</script>