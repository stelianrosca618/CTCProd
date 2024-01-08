<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_308 extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists(db_prefix() . 'proposal_ports')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . "proposal_ports` (
              `proposal_id` int(11) NOT NULL,
              `fob_port` text DEFAULT NULL,
              `cfr_port` text DEFAULT NULL,
              `container_type` text DEFAULT NULL,
              `exchange_rate` float(15,2) NOT NULL DEFAULT '0'
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set . ';');

            $this->db->query('ALTER TABLE `' . db_prefix() . 'proposal_ports`
              ADD KEY `proposal_id` (`proposal_id`);');
        }

        if (!$this->db->table_exists(db_prefix() . 'itemable_incoterms')) {
            $this->db->query('CREATE TABLE `' . db_prefix() . "itemable_incoterms` (
              `proposal_id` int(11) NOT NULL,
              `item_id` int(11) NOT NULL,
              `freight_id` int(11) NOT NULL,
              `freight_port` varchar(255) NOT NULL,
              `rate_fob_fcl_20` float(15,2) NOT NULL DEFAULT '0',
              `rate_fob_fcl_40` float(15,2) NOT NULL DEFAULT '0',
              `rate_fob_air` float(15,2) NOT NULL DEFAULT '0',
              `rate_cfr_fcl_20` float(15,2) NOT NULL DEFAULT '0',
              `rate_cfr_fcl_40` float(15,2) NOT NULL DEFAULT '0',
              `rate_cfr_air` float(15,2) NOT NULL DEFAULT '0'
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set . ';');

            $this->db->query('ALTER TABLE `' . db_prefix() . 'itemable_incoterms`
              ADD KEY `proposal_id` (`proposal_id`),
              ADD KEY `item_id` (`item_id`),
              ADD KEY `freight_id` (`freight_id`);');
        }
    }
}
