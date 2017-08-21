<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Test_migration extends CI_Migration {

    public function up() {
        $this->db->qeury('ALTER TABLE `md_pegawai` ADD `NOHP1_PEG` INT(12) NULL DEFAULT NULL AFTER `NOHP_PEG`;
');
        
        return $this->db->get();
    }

    public function down() {
        
    }

}
