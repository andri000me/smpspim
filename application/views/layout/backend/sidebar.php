
<!-- Navigation -->
<aside id="menu">
    <div id="navigation">
        <ul class="nav" id="side-menu">
            <?php 
            echo $this->menu_handler->generate($this->session->userdata("MENU_USER"));
            ?>
        </ul>
    </div>
</aside>
<div id="wrapper">