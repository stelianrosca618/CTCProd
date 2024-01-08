<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_309 extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `tblproposals` ADD `notes` TEXT;");
        $this->db->query("ALTER TABLE `tblproposals` ADD `moq` INT NOT NULL DEFAULT '0';");
        $this->db->query("ALTER TABLE `tblproposals` ADD `quantity_offered` INT NOT NULL DEFAULT '0';");
    }
}
