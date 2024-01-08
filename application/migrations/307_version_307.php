<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_307 extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('fcl_20_container', db_prefix() . 'items')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `fcl_20_container` FLOAT(15,2) NULL DEFAULT "0" AFTER `unit`;');
        }

        if (!$this->db->field_exists('fcl_40_container', db_prefix() . 'items')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `fcl_40_container` FLOAT(15,2) NULL DEFAULT "0" AFTER `fcl_20_container`;');
        }

        if (!$this->db->field_exists('air_container', db_prefix() . 'items')) {
            $this->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `air_container` FLOAT(15,2) NULL DEFAULT "0" AFTER `fcl_40_container`;');
        }
    }
}
