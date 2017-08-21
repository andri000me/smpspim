<?php 
$this->load->model('user_model', 'user');

$button = array(
    array(
        'class' => 'btn btn-success btn-flat btn-sm pull-right',
        'name' => 'Tambah User',
        'onclick' => 'add_data()',
        'icon' => 'plus'
        ),
    array(
        'class' => 'btn btn-default btn-flat btn-sm',
        'name' => 'Reload Tabel',
        'onclick' => 'reload_table()',
        'icon' => 'refresh'
        )
    );
$table = array(
    'Username',
    'Nama',
    'Status',
    'Hak Akses',
    'Alamat',
    'Login Terahir'
    ); 

$form = array(
        array(
            'type' => 'hidden',
            'name' => 'ID_USER',
            'label' => 'ID User'
            ),
        array(
            'type' => 'text',
            'name' => 'NAME_USER',
            'label' => 'Username',
            'edit' => '"readonly", "true"',
            ),
        array(
            'type' => 'text',
            'name' => 'PASSWORD_USER',
            'label' => 'Password',
            ),
        array(
            'type' => 'text',
            'name' => 'REPASSWORD_USER',
            'label' => 'Password Ulang',
            ),
        array(
            'type' => 'text',
            'name' => 'FULLNAME_USER',
            'label' => 'Nama Lengkap'
            ),
        array(
            'type' => 'autocomplete',
            'name' => 'KECAMATAN_USER',
            'id' => 'NAMA_KECAMATAN',
            'label' => 'Alamat',
            'site' => site_url('pesantren/ajax_kecamatan')
            )
        );

$this->template_handler->create_content(
    'User',
    'Data User',
    $button,
    $table
    );

$this->template_handler->create_form(
    'Tambah User',
    $form
); ?>

<script type="text/javascript">
    var save_method; //for save method string
    var table;
     
    $(document).ready(function() {
        
        <?php
        if(isset($EDIT_USER)) {
            echo "edit_data(".$ID_USER.")";
        }
        ?>
        
        $("[name='NAME_USER']").change(function(){
            <?php $this->template_handler->show_notify('', '', 'Sistem sedang mengecek ketersediaan username. Silahkan tunggu beberapa saat.',TRUE) ?>
            $("#btnSave").attr('disabled', 'true');
            var val = $(this).val();
            $.ajax({
                url: '<?php echo site_url('user/ajax_check_username') ?>/' + val,
                type: 'POST',
                dataType: 'JSON',
                success: function (data, textStatus, jqXHR) {
                    if(data.status) {
                        <?php $this->template_handler->show_notify('success', '', 'Username dapat digunakan.',TRUE) ?>
                        $("#btnSave").removeAttr('disabled');
                    } else {
                        <?php $this->template_handler->show_notify('error', '', 'Username tidak dapat digunakan.',TRUE) ?>
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    <?php $this->template_handler->show_notify('error', '', 'Username tidak dapat digunakan.',TRUE) ?>
                }
            });
        });
    
        <?php $this->template_handler->initialize_datatables(site_url('user/ajax_list')); ?>

        
        
    });

    function change_status(ID_USER) {
        if(confirm('Apakah Anda yakin merubah status User?')) {
            <?php $this->template_handler->show_notify('', '', 'Sistem sedang merubah status user. Silahkan tunggu beberapa saat.',TRUE) ?>
            var STATUS_USER = $('#STATUS_USER'+ID_USER+' option:selected').val();
            $.ajax({
                url: '<?php echo site_url('user/ajax_change_status') ?>/'+ID_USER+'/'+STATUS_USER,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    <?php echo $this->template_handler->show_notify('success', '', 'Status User berhasil dirubah.',TRUE) ?>
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    <?php echo $this->template_handler->show_notify('error', '', 'Status user gagal dirubah.',TRUE) ?>
                }

            });
        }
    }

    function change_hakakses(ID_USER) {
        if(confirm('Apakah Anda yakin merubah hakakses User?')) {
            <?php $this->template_handler->show_notify('', '', 'Sistem sedang merubah hakakses user. Silahkan tunggu beberapa saat.',TRUE) ?>
            var HAKAKSES = $('#HAKAKSES'+ID_USER+' option:selected').val();
            $.ajax({
                url: '<?php echo site_url('user/ajax_change_hakakses') ?>/'+ID_USER+'/'+HAKAKSES,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    <?php echo $this->template_handler->show_notify('success', '', 'Hakakses User berhasil dirubah.',TRUE) ?>
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    <?php echo $this->template_handler->show_notify('error', '', 'Hakakses user gagal dirubah',TRUE) ?>
                    reload_table();
                }

            });
        }
    }

    <?php 
    $this->template_handler->js_reload();
    $this->template_handler->js_adder('Tambah User', $this->user->primary_key, $form);
    $this->template_handler->js_updater('Edit User', $this->user->primary_key, $form, site_url('user/ajax_edit'));
    $this->template_handler->js_reader('Lihat User', $this->user->primary_key, $form, site_url('user/ajax_edit'));
    $this->template_handler->js_save(site_url('user/ajax_add'), site_url('user/ajax_update')); 
    $this->template_handler->js_remover(site_url('user/ajax_delete'), $this->user->primary_key); 
    ?>
</script>