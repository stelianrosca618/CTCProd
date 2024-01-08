<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'freights')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "freights` (
  `id` int(11) NOT NULL,
  `port` varchar(255) NOT NULL,
  `fcl_20` float(15,2) NOT NULL,
  `fcl_40` float(15,2) NOT NULL,
  `air` float(15,2) NOT NULL,
  `validity` date NOT NULL,
  `dateadded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'freights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dateadded` (`dateadded`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'freights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
