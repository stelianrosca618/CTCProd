<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Missiveapp_model extends App_Model
{

	public function search_contact($email)
    {   


        $this->db->select(db_prefix() . 'contacts.id AS contact_id, '. db_prefix() . 'contacts.firstname, '. db_prefix() . 'contacts.lastname, '. db_prefix() . 'contacts.email, '. db_prefix() . 'contacts.phonenumber, '. db_prefix() . 'contacts.title, ' . db_prefix() . 'clients.userid, ' .db_prefix() . 'clients.country, ' .db_prefix() . 'clients.company, ' .db_prefix() . 'clients.leadid, DATE_FORMAT(' .db_prefix() . 'clients.datecreated, "%d-%m-%Y") AS created_date, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'customer_groups JOIN ' . db_prefix() . 'customers_groups ON ' . db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id WHERE customer_id = ' . db_prefix() . 'clients.userid ORDER by name ASC) as customerGroups');
        $this->db->where(db_prefix() . 'contacts.active', 1);
        $this->db->like(db_prefix() . 'contacts.email', $email, 'none');
        
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.userid = ' . db_prefix() . 'contacts.userid', 'left');
       // $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');

        return $this->db->get(db_prefix() . 'contacts')->row_array();
    }

    public function get_lead($leadid)
    {
        $this->db->select('*,' . db_prefix() . 'leads.name, ' . db_prefix() . 'leads.id,' . db_prefix() . 'leads_status.name as status_name,' . db_prefix() . 'leads_sources.name as source_name');
        $this->db->join(db_prefix() . 'leads_status', db_prefix() . 'leads_status.id=' . db_prefix() . 'leads.status', 'left');
        $this->db->join(db_prefix() . 'leads_sources', db_prefix() . 'leads_sources.id=' . db_prefix() . 'leads.source', 'left');
        $this->db->where(db_prefix() . 'leads.id', $leadid);
        $lead = $this->db->get(db_prefix() . 'leads')->row_array();

        return $lead;
    }

    public function get_invoice_item($clientid)
    {
        $this->db->select(db_prefix() . 'itemable.*');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'itemable.rel_id', 'left');
        $this->db->where(db_prefix() . 'itemable.rel_type', 'invoice');
        $this->db->where(db_prefix() . 'invoices.clientid', $clientid);
        return $this->db->get(db_prefix() . 'itemable')->result_array();
    }

    public function get_quotations($clientid)
    {
        $this->db->select('id, subject, total, date, open_till, datecreated, status, currency, invoice_id, hash, DATE_FORMAT(' . db_prefix() . 'proposals.date, "%d-%m-%Y") AS formated_date, DATE_FORMAT(' . db_prefix() . 'proposals.open_till, "%d-%m-%Y") AS formated_open_till');
        $this->db->where('rel_type', 'customer');
        $this->db->where('rel_id', $clientid);
        $this->db->order_by('datecreated', 'desc');
        $this->db->limit(5);

        return $this->db->get(db_prefix() . 'proposals')->result_array();
    }

    public function get_invoices($clientid)
    {
        $this->db->select(db_prefix() . 'invoices.id, ' . db_prefix() . 'invoices.number, ' . db_prefix() . 'invoices.total, ' . db_prefix() . 'invoices.total_tax, ' . db_prefix() . 'invoices.date, ' . db_prefix() . 'invoices.duedate, ' . db_prefix() . 'invoices.status, ' . db_prefix() . 'currencies.name as currency_name, ' . db_prefix() . 'invoices.project_id, ' . db_prefix() . 'invoices.hash, ' . db_prefix() . 'invoices.recurring, ' . db_prefix() . 'invoices.deleted_customer_name, DATE_FORMAT(' . db_prefix() . 'invoices.date, "%d-%m-%Y") AS formated_date, DATE_FORMAT(' . db_prefix() . 'invoices.duedate, "%d-%m-%Y") AS formated_duedate');
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency', 'left');
        $this->db->where(db_prefix() . 'invoices.clientid', $clientid);
        $this->db->order_by(db_prefix() . 'invoices.date', 'desc');
        $this->db->limit(5);

        return $this->db->get(db_prefix() . 'invoices')->result_array();
    }

    public function get_consumption($clientid)
    {
        $this->db->select('YEAR(' . db_prefix() . 'invoices.date) AS date_year, SUM(' . db_prefix() . 'itemable.qty) AS total_qty');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'itemable.rel_id', 'left');
        $this->db->where(db_prefix() . 'itemable.rel_type', 'invoice');
        $this->db->where(db_prefix() . 'invoices.clientid', $clientid);
        $this->db->group_by('YEAR(' . db_prefix() . 'invoices.date)');
        $this->db->order_by('YEAR(' . db_prefix() . 'invoices.date)', 'desc');
        $consumptionByYear = $this->db->get(db_prefix() . 'itemable')->result_array();
        
        //select * from users
       //where date_joined> now() - INTERVAL 12 month;
       $lastYearDate = new DateTime();

       $dateMinus12 = $lastYearDate->modify("-12 months")->format('Y-m-d');
       $this->db->select(db_prefix() . 'itemable.*');
        $this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'itemable.rel_id', 'left');
        $this->db->where(db_prefix() . 'itemable.rel_type', 'invoice');
        $this->db->where(db_prefix() . 'invoices.clientid', $clientid);
        $this->db->where(db_prefix() . 'invoices.date >=', $dateMinus12);
        $lastMonths = $this->db->get(db_prefix() . 'itemable')->result_array();
        
        $lastMonthsQty = $lastMonths? array_sum(array_values(array_column($lastMonths, 'qty'))) : 0;
        $lastProducts = $lastMonths? getProductsMonths($lastMonths) : '';
        $qtyPerYear =  $consumptionByYear? array_sum(array_values(array_column($consumptionByYear, 'total_qty')))/count(array_values(array_column($consumptionByYear, 'total_qty'))) : 0;
        return ['consureData' => $consumptionByYear, 'lastQuantity' =>  $lastMonthsQty, "lastProducts" => $lastProducts, 'qtyPerYear'=>$qtyPerYear];
    }

    public function get_payments($clientid)
    {
        $this->load->model('currencies_model');

        $currencyid = $this->currencies_model->get_base_currency()->id;

        $result            = [];
        $result['pending'] = [];
        $result['overdue'] = [];
        $result['paid']    = [];

        for ($i = 1; $i <= 3; $i++) {
            $select = 'id, total';

            if ($i == 1 || $i == 2) {
                $select .= ', (SELECT total - (SELECT COALESCE(SUM(amount), 0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id)) as total_due';
            } elseif ($i == 3) {
                $select .= ', (SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid';
            }

            $this->db->select($select);
            $this->db->from(db_prefix() . 'invoices');
            $this->db->where('currency', $currencyid);
            $this->db->where('clientid', $clientid);
            // Exclude cancelled invoices
            $this->db->where('status !=', 5);
            // Exclude draft
            $this->db->where('status !=', 6);

            if ($i == 1 || $i == 2) {
                $this->db->where('status !=', 2);
            }

            if ($i == 1) {
                $this->db->where('DATE(duedate) >=', date("Y-m-d"));
            }

            if ($i == 2) {
                $this->db->where('DATE(duedate) <', date("Y-m-d"));
            }

            if ($i == 3) {
                $this->db->where_in('status', array(2, 3));
            }

            $this->db->where('YEAR(date)', date('Y'));

            $invoices = $this->db->get()->result_array();

            foreach ($invoices as $invoice) {
                if ($i == 1) {
                    $result['pending'][] = $invoice['total_due'];
                } elseif ($i == 2) {
                    $result['overdue'][] = $invoice['total_due'];
                } elseif ($i == 3) {
                    $result['paid'][] = $invoice['total_paid'];
                }
            }
        }

        $currency             = get_currency($currencyid);

        $result['pending']    = array_sum($result['pending']);
        $result['overdue']    = array_sum($result['overdue']);
        $result['due']        = $result['pending'] + $result['overdue'];
        $result['paid']       = array_sum($result['paid']);

        $result['paid_percentage'] = ($result['due'] > 0 && $result['paid'] > 0) ? (int)(($result['paid']*100)/$result['due']) : 0;

        $result['currency']   = $currency;
        $result['currencyid'] = $currencyid;

        return $result;
    }

    /**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'contacts')->row();
    }

    /**
     * Update contact data
     * @param  array  $data           $_POST data
     * @param  mixed  $id             contact id
     * @return mixed
     */
    public function update_contact($data, $id)
    {
        // print_r($data);
        // die;
        $this->db->where('id', $id);
        // print_r($this->db->get(db_prefix() . 'contacts')->result_array());
        // die;
        $this->db->update(db_prefix() . 'contacts', $data);
        //$contactData = $this->db->get(db_prefix() . 'contacts')->result_array();
        // $this->db->where('id', $data['leadid']);
        // $this->db->update(db_prefix().'leads', [
        //     'tier' => $data['customerTierLevel']
        // ]);
        //$contactData[0]['userid'];
        return true;
    }
}