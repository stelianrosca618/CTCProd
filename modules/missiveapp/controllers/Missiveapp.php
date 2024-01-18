<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Missiveapp extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('cookie');
        $this->load->helper('missiveapp');

        $this->load->model('Missiveapp_model');
        $this->load->model('Misc_model');
    }

    public function index()
    {
        $data = array();

        // $headers = apache_request_headers();

        //print_r($headers);

        $this->load->view('missiveapp', $data);
    }

    public function login()
    {
        $data = array();
        $data['login_page'] = true;
        $this->load->view('missiveapp_login', $data);
    }

    public function get_relation_data()
    {
        if ($this->input->post()) {
            $type = $this->input->post('type');
            $data = get_relation_data($type, '', $this->input->post('extra'));
            if ($this->input->post('rel_id')) {
                $rel_id = $this->input->post('rel_id');
            } else {
                $rel_id = '';
            }

          //  $relOptions = init_relation_options($data, $type, $rel_id);
            echo json_encode($data);
            die;
        }
    }
    public function get_relation_data_values($rel_id, $rel_type)
    {
        echo json_encode($this->proposals_model->get_relation_data_values($rel_id, $rel_type));
    }
    public function auth()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $login_success = true;
        $login_message = '';
        if ((!empty($email)) and (!empty($password))) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
            $this->db->where('email', $email);
            $user = $this->db->get($table)->row();
            if ($user) {
                // Email is okey lets check the password now
                if (!app_hasher()->CheckPassword($password, $user->password)) {
                    $login_success = false;
                    $login_message = 'Failed Login Attempt';
                }
            } else {
                $login_success = false;
                $login_message = 'Non Existing User Tried to Login';
            }

            if ($login_success && $user && $user->active == 0) {
                $login_success = false;
                $login_message = 'Inactive User Tried to Login';
            }
        } else {
            $login_success = false;
        }

        if ($login_success) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user->$_id,
                    'name' => trim($user->firstname.' '.$user->lastname),
                    'email' => $email
                ]
            ]);
        } else {
            echo json_encode([
                'error' => true,
                'message' => _l('client_invalid_username_or_password'),
                'login_message' => $login_message
            ]);
        }
    }

    public function contact()
    {
        $emaillist = $this->input->get('email');

        $success = false;
        $status = '';
        $contact = array();
        $returnContact = array();
        $lead = null;
        $customFields = array();
        $products = '--';
        $consumption = '--';
        $notes = array();
        $fixed_notes = '--';
        $last_notes = [];

        $quotationHtml = '';
        $invoiceHtml = '';
        $consumptionHtml = '';

        $payments = array();

        foreach($emaillist as $email){
            $contact = $this->Missiveapp_model->search_contact($email);

            $this->db->select(db_prefix().'leads.*, '.db_prefix() . 'leads_sources.name as source_name');
            $this->db->where('email', $email);
            $this->db->from(db_prefix().'leads');
            //$this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'leads.country', 'left');
            $this->db->join(db_prefix() . 'leads_sources', '' . db_prefix() . 'leads_sources.id = ' . db_prefix() . 'leads.source', 'left');
            $leadData = $this->db->get()->row_array();
            
            if($contact){
                $success = true;
                $status = 'Client';
                $returnContact = $contact;
                $invoices_items = $this->Missiveapp_model->get_invoice_item($contact['userid']);
                
                $countryIds = explode(", ", $contact['country']);
                
                $this->db->where_in('country_id', $countryIds);
                $countylist = $this->db->get(db_prefix().'countries')->result_array();
                $countryNames = '';
                // print_r($countylist);
                // die;
                foreach($countylist as $key => $countyItem){
                    if($key > 0){
                        $countryNames .=", ". $countyItem['short_name'];
                    }else{
                        $countryNames .=$countyItem['short_name'];
                    }
                    
                }
                $contact['country'] = $countryIds;
                $returnContact = array_merge($contact, ['countryNames' => $countryNames]);
                // print_r($contact);
                // die;
                if ($invoices_items) {
                    $products = implode(",", array_values(array_unique(array_column($invoices_items, 'description'))));
                    $consumption = array_sum(array_values(array_column($invoices_items, 'qty')));
                }

                $notes = $this->Misc_model->get_notes($contact['userid'], 'customer');

                if($notes)
                {
                    $last_notes = $notes;
                    $fixed_notes = $notes[count($notes)-1]['description'];
                    $fixedNote_id = $notes[count($notes)-1]['id'];
                }
                
                // 
                $top_proposals = $this->Missiveapp_model->get_quotations($contact['userid']);
                $top_invoices = $this->Missiveapp_model->get_invoices($contact['userid']);
                $top_consumption = $this->Missiveapp_model->get_consumption($contact['userid']);
                $payments = $this->Missiveapp_model->get_payments($contact['userid']);
                
                if (is_numeric($contact['leadid'])) {
                    $lead = $this->Missiveapp_model->get_lead($contact['leadid']);
                    $customFields = $this->db->where('relid', $contact['leadid'])->get(db_prefix().'customfieldsvalues')->result_array();
                    $leadQuataintion = [
                        'isActive'=> false,
                        'Date'=> null
                    ];
                    if($top_proposals){
                        
                        $leadQuataintion = [
                            'isActive'=> true,
                            'Date'=> $top_proposals[0]['datecreated'],
                        ];
                    }
                }

                $quotationHtml = sales_tables_quotations($top_proposals);
                $invoiceHtml = sales_tables_invoices($top_invoices);
                $consumptionHtml = sales_tables_consumption($top_consumption);
                $paymentHtml = sales_payemnets($payments);
                break;
            }else if($leadData){
                $success = true;
                $status = 'lead';
                //$returnContact = $leadData;
                $this->db->where('country_id', $leadData['country']);
                $countylist = $this->db->get(db_prefix().'countries')->result_array();
                $countryNames = '';
                // print_r($countylist);
                // die;
                // $notes = $this->Misc_model->get_notes($leadData['id'], 'lead');
                // print_r($notes);
                $this->db->where('rel_id', $leadData['id']);
                $this->db->where('rel_type', 'lead');
                $this->db->order_by('dateadded', 'desc');
                $notes = $this->db->get(db_prefix() . 'notes')->result_array();
                if($notes)
                {
                    $last_notes = $notes;
                    $fixed_notes = $notes[count($notes)-1]['description'];
                    $fixedNote_id = $notes[count($notes)-1]['id'];
                }
                foreach($countylist as $key => $countyItem){
                    if($key > 0){
                        $countryNames .=", ". $countyItem['short_name'];
                    }else{
                        $countryNames .=$countyItem['short_name'];
                    }
                    
                }
                $returnContact = array_merge($leadData, ['countryNames' => $countryNames]);
                $customFields = $this->db->where('relid', $leadData['id'])->get(db_prefix().'customfieldsvalues')->result_array();
                $leadQuataintion = [
                    'isActive'=> false,
                    'Date'=> null
                ];
                $quotationHtml = "";
                $invoiceHtml = "";
                $consumptionHtml = "";
                $paymentHtml = "";
                
                break;
            }
        }
        
        $csrfName = $this->security->get_csrf_token_name();
        $csrfHash = $this->security->get_csrf_hash();

        echo json_encode(['success' => $success, 'status' => $status, 'contact' => $returnContact, 'lead' => $lead, 'leadQuataintion' => $leadQuataintion, 'leadFields' => $customFields, 'products' => $products, 'consumption' => $consumption, 'newConsumption' => $top_consumption, 'last_notes' => $last_notes, 'fixed_notes' => $fixed_notes, 'fixedNote_id' => $fixedNote_id, 'csrf_name' => $csrfName, 'csrf_hash' => $csrfHash, 'quotation_html' => $quotationHtml, 'invoice_html' => $invoiceHtml, 'consumption_html' => $consumptionHtml, 'payment_html' => $paymentHtml]);
    }

    public function loadNewProposalData() 
    {
        $data['subject'] = 'Proposal '.time();
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        // if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
        //     $data['items'] = $this->invoice_items_model->get_grouped();
        // } else {
        //     $data['items']     = [];
        //     $data['ajaxItems'] = true;
        // }
        $data['items'] = $this->db->get(db_prefix().'items')->result_array();
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses']      = $this->proposals_model->get_statuses();
        $data['staff']         = $this->staff_model->get('', ['active' => 1]);
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['countries'] = hooks()->apply_filters('all_countries', get_instance()->db->order_by('short_name', 'asc')->get(db_prefix().'countries')->result_array());
        $data['title'] = _l('add_new', _l('proposal_lowercase'));
        $data['sources'] = $this->db->get(db_prefix().'leads_sources')->result_array();
        // BOF VK, Hide subtotal element
        // VK Mod: Add
        $data['hide_sbutotal'] = true;
        // EOF VK, Hide subtotal element

        // BOF VK, Get freights list
        // VK Mod: Add
        $this->load->model('freights/freights_model');
        $data['freights'] = $this->freights_model->get();
        echo json_encode(['success'=>true, 'data' => $data]);
    }

    public function addLastNote(){
        $newNoteData = $this->input->post();
        if($newNoteData){
            $insertedID = $this->Misc_model->add_note($newNoteData, $newNoteData['rel_type'], $newNoteData['rel_id']);
            echo json_encode(['success'=>true, 'data' => $insertedID]);
        }else{
            echo json_encode(['success'=>false, 'data' => '']);
        }
    }

    public function editFixedNote(){
        $noteData = $this->input->post();
        if($noteData){
            $this->Misc_model->edit_note($noteData, $noteData['id']);
            
            $this->db->where('id', $noteData['id']);
            $data = $this->db->get(db_prefix() . 'notes')->result_array();
            echo json_encode(['success'=>'true', 'data' => $data[0]]);
        }else{
            echo json_encode(['success'=>false, 'data' => '']);
        }
    }
    public function getCountries(){
        $data = $this->db->get(db_prefix().'countries')->result_array();
        echo json_encode($data);
    }
    public function contact_update()
    {
        $data = $this->input->post();

        if ($contact_data = $this->Missiveapp_model->get_contact($data['contactid'])) {
            $email = $data['email'];
            $contactid = $data['contactid'];
            $csrfName = $this->security->get_csrf_token_name();
            unset($data[$csrfName]);
            unset($data['contactid']);
            unset($data['email']);
            $leadid = $data['leadid'];
            $tierlevel = $data['customerTierLevel'];
            $source = $data['source'];
            $country = implode(', ', $data['country']);
            unset($data['leadid']);
            unset($data['customerTierLevel']);
            unset($data['source']);
            unset($data['country']);
            // print_r($data);
            // die;
            /*update contact data*/
            $this->db->where('id', $contactid);
            $this->db->update(db_prefix() . 'contacts', $data);
            /*update client data*/
            $this->db->where('userid', $contact_data->userid);
            $this->db->update(db_prefix() . 'clients', [
                'country' => $country
            ]);

            $tierId = 4;
            switch($tierlevel){
                case 'Tier 1':
                    $tierId = 3;
                    break;
                case 'Tier 2':
                    $tierId = 4;
                    break;
                case 'Tier 3':
                    $tierId = 5;
                    break;
            }
            $this->db->where('customer_id', $contact_data->userid);
            $this->db->update(db_prefix().'customer_groups', [
                'groupid' => $tierId
            ]);
            /*update lead data*/
            $this->db->where('id', $leadid);
            $this->db->update(db_prefix() . 'leads', [
                'source' => $source,
                'tier' => $tierlevel,
            ]);
            //$this->Missiveapp_model->update_contact($data, $contact_data->id);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'error' => true,
                'message' => "Invalid contact ID."
            ]);
        }
    }

    public function getItems(){
        $items = $this->db->get(db_prefix().'items')->result_array();
        echo json_encode($items);
    }

    public function lead_Create(){
        $data = $this->input->post();
        if($data){
            $tierId = 3;
            switch($data['tier']){
                case 'Tier 1':
                    $tierId = 3;
                    break;
                case 'Tier 2':
                    $tierId = 4;
                    break;
                case 'Tier 3':
                    $tierId = 5;
                    break;
            }
            $this->db->insert(db_prefix().'leads', [
                'hash'=> app_generate_hash(),
                'name' => $data['fname'].' '.$data['lname'],
                'company' => $data['company'],
                'country' => $data['country'],
                'email' => $data['email'],
                'dateadded' => $data['created'],
                'source'=>$data['source'],
                'tier' => $data['tier'],
                'addedfrom' => 1,
                'status' => 2
            ]);
            $insertedLeadId = $this->db->insert_id();
            
            $customFields = $data['customFields'];
            foreach($customFields as $fItem){
                $this->db->insert(db_prefix().'customfieldsvalues', [
                    'relid' => $insertedLeadId,
                    'fieldid'=> $fItem['fieldid'],
                    'fieldto'=> $fItem['fieldto'],
                    'value' => $fItem['value'],
                    'dates' => $fItem['dates'],
                ]);
            }
            echo json_encode(['success' => true]);
        }else{
            echo json_encode(['success' => false]);
        }
    }

    public function lead_update(){
        $data = $this->input->post();
        if($data){
            $this->db->where('id', $data['contactid']);
            $this->db->update(db_prefix().'leads', [
                'country'=>$data['country'],
                'name'=> $data['firstname'].' '.$data['lastname'],
                'email' => $data['email'],
                'source' => $data['source'],
                'tier' => $data['customerTierLevel']
            ]);
            echo json_encode(['success' => true]);
        }else{
            echo json_encode(['success' => false]);
        }
    }

    public function leadFields_Update(){
        $data = $this->input->post();
        $wong = '>';
        if($data){
            foreach($data['customFields'] as $dItem) {
                if($dItem['id']){
                    if($dItem['id'] == 'null'){
                        $this->db->insert(db_prefix().'customfieldsvalues', [
                            'relid' => $data['leadId'],
                            'fieldid'=> $dItem['fieldto'],
                            'fieldto'=> "leads",
                            'value' => $dItem['value'],
                            'dates' => $dItem['dates'],
                        ]);
                    }else{
                        $this->db->where('id', $dItem['id'])->update(db_prefix().'customfieldsvalues', [
                            'value'=>$dItem['value'],
                            'dates'=>$dItem['dates']
                        ]);
                    }
                }
            }
        }
        echo json_encode(['success' => true, 'data'=>$wong]);
    }
}
