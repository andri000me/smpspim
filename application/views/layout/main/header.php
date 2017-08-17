<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- Page title -->
        <title><?php echo $this->pengaturan->getNamaApp(); ?> | <?php echo $this->pengaturan->getNamaLembagaSingk(); ?></title>
        <link rel="shortcut icon" href="<?php echo base_url('files/aplikasi/'); ?>logo_14.png" />
        
        <script type="text/javascript">
            var app_name = "<?php echo $this->pengaturan->getNamaApp(); ?>";
            var motto = "<?php echo $this->pengaturan->getNamaLembagaSingk(); ?>";
            var base_url = "<?php echo base_url(); ?>";
            var nama_user = "<?php echo $this->session->userdata('FULLNAME_USER'); ?>";
            var foto_user = "<?php if($this->session->userdata('PHOTO_USER') == NULL) echo '/no_image.jpg'; else echo 'pegawai/'.$this->session->userdata('PHOTO_USER'); ?>";
            var level_hakakses = "<?php echo $this->session->userdata('NAME_HAKAKSES'); ?>";
            var cawu_active = "<?php echo $this->session->userdata('NAMA_CAWU_ACTIVE'); ?>";
            var ta_active = "<?php echo $this->session->userdata('NAMA_TA_ACTIVE'); ?>";
            var psb_active = "<?php echo $this->session->userdata('NAMA_PSB_ACTIVE'); ?>";
            var status_required = true;
            var ladda_clicked = null;
            var list_hakakses = <?php echo $this->session->userdata('LIST_HAKAKSES'); ?>;
            var url_hakakses = '<?php echo site_url('login/chooseHakAkses')?>';
        </script>