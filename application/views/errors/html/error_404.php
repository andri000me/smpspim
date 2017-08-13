<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI =& get_instance();

$CI->load->view('layout/main/header.php');
$CI->load->view('layout/backend/header.php');
$CI->load->view('layout/backend/sidebar.php');
?>
<div class="error-container">
    <i class="pe-7s-way text-success big-icon"></i>
    <h1><?php echo $heading; ?></h1>
    <h4><?php echo $message; ?></h4>
</div>
<?php
$CI->load->view('layout/backend/footer.php');
?>