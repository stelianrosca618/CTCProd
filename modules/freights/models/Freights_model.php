<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Freights_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get freight by id
     * @param  mixed $id freight id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $this->db->from(db_prefix() . 'freights');

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'freights.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Update freight
     * @param  array $data     freight $_POST data
     * @param  mixed $freightid freight id
     * @return boolean
     */
    public function update($data, $freight)
    {        
        $this->db->where('id', $freight);
        $this->db->update(db_prefix().'freights', [
            'port'            => $data['port'],
            'fcl_20'          => $data['fcl_20'],
            'fcl_40'          => $data['fcl_40'],
            'air'             => $data['air'],
            'validity'        => $data['validity'],
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('Survey Updated [ID: ' . $freight . ', Subject: ' . $data['port'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Add new freight
     * @param array $data freight $_POST data
     * @return mixed
     */
    public function add($data)
    {        
        $data['validity']              = to_sql_date($data['validity']);
        $data['dateadded']             = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix().'freights', [
            'port'            => $data['port'],
            'fcl_20'          => $data['fcl_20'],
            'fcl_40'          => $data['fcl_40'],
            'air'             => $data['air'],
            'validity'        => $data['validity'],
            'dateadded'       => $data['dateadded'],
        ]);
        $freightid = $this->db->insert_id();
        if (!$freightid) {
            // return false;
        }
        log_activity('New Freight Added [ID: ' . $freightid . ', Port: ' . $data['port'] . ']');

        return $freightid;
    }

    /**
     * Delete freight and all connections
     * @param  mixed $freightid freight id
     * @return boolean
     */
    public function delete($freightid)
    {
        $affectedRows = 0;
        $this->db->where('id', $freightid);
        $this->db->delete(db_prefix().'freights');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            log_activity('Freight Deleted [ID: ' . $freightid . ']');

            return true;
        }

        return false;
    }
}
