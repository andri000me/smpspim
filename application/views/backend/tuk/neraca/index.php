<?php
$title = 'Neraca';
$subtitle = "Rekapitulasi Keuangan";

$this->generate->generate_panel_content($title, $subtitle);
?>
<div class="content animate-panel">
    <div class="row proses">
        <div class="col-md-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Proses Keuangan
                </div>
                <div class="panel-body text-center">
                    <h3>Untuk mendapatkan rekapitulasi keuangan, silahkan proses terlebih dahulu.</h3>
                    <h3><button type="button" class="btn btn-primary" onclick="proses_keuangan();">PROSES KEUANGAN&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-circle-right"></i></button></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row detail">
        <div class="col-md-12">
            <div class="hpanel">
                <div class="panel-heading hbuilt">
                    Neraca
                </div>
                <div class="panel-body">
                    <h2 class="font-extra-bold text-info">Pemasukan</h2>
                    <div class="neraca pemasukan"></div>
                    <hr>
                    <div class="neraca total_pemasukan"></div>
                    <h2 class="font-extra-bold text-info">Pengeluaran</h2>
                    <div class="neraca pengeluaran"></div>
                    <hr>
                    <div class="neraca total_pengeluaran"></div>
                    <h2 class="font-extra-bold text-info">Saldo</h2>
                    <div class="neraca laba_rugi"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script type="text/javascript">
    var nominal = null;

    $(function () {
        $(".detail").hide();
    });
    
    function proses_keuangan() {
        create_splash("Silahkan tunggu, sistem sedang menghitung rekapitulasi keuangan.");
        var success = function(data) {
            remove_splash();
            detail_neraca();
        };
        
        create_ajax('<?php echo site_url('tuk/laporan_neraca/proses_keuangan'); ?>', '', success);
    }
    
    function detail_neraca() {
        create_splash("Silahkan tunggu, sistem sedang memproses neraca.");
        var success = function(data) {
            create_homer_success('Proses telah selesai');
            
            var total_pemasukan = 0;
            var total_pengeluaran = 0;
            
            $(".neraca").html(" ");
            
            $.each(data, function(index, item){
                if(item.JENIS_TJK === 'PEMASUKAN') {
                    total_pemasukan += parseInt(item.NOMINAL_TJK);
                    $(".pemasukan").append(show_html(item.NAMA_TJK, item.NOMINAL_TJK, 8));
                } else if(item.JENIS_TJK === 'PENGELUARAN') {
                    total_pengeluaran += parseInt(item.NOMINAL_TJK);
                    $(".pengeluaran").append(show_html(item.NAMA_TJK, item.NOMINAL_TJK, 8));
                }
            });
            
            $(".total_pemasukan").append(show_html('TOTAL PEMASUKAN', total_pemasukan, 9));
            $(".total_pengeluaran").append(show_html('TOTAL PENGELUARAN', total_pengeluaran, 9));
            $(".laba_rugi").append(show_html('SALDO', (total_pemasukan - total_pengeluaran), 9));
            
            $(".detail").slideDown();
            $(".proses").slideUp();
            
            remove_splash();
        };
        
        create_ajax('<?php echo site_url('tuk/laporan_neraca/get_neraca'); ?>', '', success);
    }
    
    function show_html(label, value, length) {
        return '<div class="row detail_hover"><div class="col-md-' + length + '"><h4 class="font-extra-bold text-primary">' + label +'</h4></div><div class="col-md-3 text-right"><h4 class="font-extra-bold text-primary">' + formattedIDR(value) + '</h4></div></div>';
    }
</script>