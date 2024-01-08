<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stocks extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
       // $this->load->model('invoice_items_model');
        //$data['items_groups'] = $this->invoice_items_model->get_groups();
        if($this->db->table_exists('stocks')){
            $this->db->select(db_prefix().'items_groups.*, (SELECT SUM('.db_prefix().'stocks.quantity) FROM '.db_prefix().'stocks WHERE '.db_prefix().'stocks.itemGroup_id ='. db_prefix().'items_groups.id) as totalQuantity');
        }else{
            $this->db->select(db_prefix().'items_groups.*, 0 as totalQuantity');
        }
        
        $data['items_groups'] = $this->db->get(db_prefix().'items_groups')->result_array();
        $this->load->view('admin/stocks/manage', $data);
    }

    public function addStock(){
        if($this->input->post()){
            $itemId = $this->input->post('item_id');
            $quantity           = $this->input->post('quantity');
            $stockDate           = $this->input->post('date');
            $status           = $this->input->post('status');
            $Notes           = $this->input->post('Notes');
            $itemGroupId           = $this->input->post('itemGroup_id');
            if($itemId){
                $this->db->where('id', $itemId);
                $this->db->update(db_prefix() . 'stocks', [
                    'quantity'    => $quantity,
                    'date' => date("Y-m-d", strtotime($stockDate) ),
                    'status' => $status,
                    'Notes' => $Notes,
                    'itemGroup_id' => $itemGroupId,
                ]);
            }else{
                $this->db->insert(db_prefix() . 'stocks', [
                    'quantity'    => $quantity,
                    'date' => date("Y-m-d", strtotime($stockDate) ),
                    'status' => $status,
                    'Notes' => $Notes,
                    'itemGroup_id' => $itemGroupId,
                ]);
            }

            echo json_encode([
            'success' => true,
            'message' => "Success",
        ]);
          //  redirect(admin_url('stocks' . $id));
        }

    }

    public function quantiylimit(){
        if($this->input->post()){
            $groupdId = $this->input->post('id');
            $limitVal = $this->input->post('limitval');
            $this->db->where('id', $groupdId);
            $this->db->update(db_prefix().'items_groups', [
                'limitQty' => $limitVal,
            ]);
            echo json_encode([
                'success' => true,
                'message' => "Success",
            ]);
        }
    }

    public function stock($id=''){
        
        $this->db->where('id', $id);
        $groupData = $this->db->get(db_prefix().'items_groups')->result_object();
        $data['group'] = $groupData[0];
        $this->db->where('itemGroup_id', $id);
        $this->db->order_by('id', 'DESC');
        $data['grouped'] = $this->db->get(db_prefix().'stocks')->result_array();
        $this->load->view('admin/stocks/stock', $data);
    }
    public function makeStock(){

        // $this->db->select('*')->from(db_prefix().'items');
        // $itemArr = $this->db->get()->result_array();
        // foreach($itemArr as $itemD){
        //     $this->db->where('description', $itemD['description']);
        //     $this->db->update(db_prefix().'itemable', [
        //         'reference_item_id'=>$itemD['id']
        //     ]);
        // }
        $this->db->select(db_prefix().'stocks.*, '.db_prefix().'clients.company');
        $this->db->from(db_prefix().'stocks');
        $this->db->join(db_prefix().'invoices', db_prefix().'invoices.id='.db_prefix().'stocks.invoice_id', 'left');
        $this->db->join(db_prefix().'clients', db_prefix().'clients.userid='.db_prefix().'invoices.clientid', 'left');
        $this->db->where(db_prefix().'stocks.status', 'invoiced');
        $stocklist = $this->db->get()->result_array();

        foreach($stocklist as $stockItem){
            $words = explode(' ', $stockItem['Notes']);
            $this->db->where('id', $stockItem['id']);
            $this->db->update(db_prefix().'stocks', [
                'Notes' => $words[0].' '.$stockItem['company'],
            ]);
        }

        // $this->db->select(db_prefix().'itemable.*, '.db_prefix().'proposals.id as ProsID, '.db_prefix().'proposals.invoice_id, '.db_prefix().'items.production_ratio, '.db_prefix().'items.group_id, '.db_prefix().'invoices.date as invoiceDate');
        // $this->db->from(db_prefix() . 'itemable');
        // $this->db->join(db_prefix().'proposals', db_prefix().'proposals.invoice_id='.db_prefix().'itemable.rel_id', 'left')->where_not_in(db_prefix().'proposals.invoice_id', 'NULL');
        // $this->db->join(db_prefix().'invoices', db_prefix().'invoices.id = '.db_prefix().'proposals.invoice_id', 'left');
        // $this->db->join(db_prefix().'items', db_prefix().'items.id='.db_prefix().'itemable.reference_item_id', 'left');
        // $this->db->where(db_prefix().'itemable.rel_type', 'invoice');
        // //$this->db->where_not_in(db_prefix().'proposals.invoice_id', 'NULL');
        
        // $grouped = $this->db->get()->result_array();
        // // print_r($grouped);
        // // die;
        // foreach($grouped as $groupItem){
        //     // print_r($groupItem);
        //     // die;
        //   //  if($groupItem['production_ratio'] && $groupItem['group_id']){
        //     $prodRatio = $groupItem['production_ratio']? $groupItem['production_ratio'] : 100;
        //         $this->db->insert(db_prefix().'stocks', [
        //             'quantity'    => $groupItem['qty'] * ($prodRatio / 100) * -1,
        //             'date' => $groupItem['invoiceDate'],
        //             'status' => 'invoiced',
        //             'Notes' => get_option('proposal_number_prefix') . str_pad($groupItem['ProsID'], get_option('number_padding_prefixes'), '0', STR_PAD_LEFT).' CTC',
        //             'itemGroup_id' => $groupItem['group_id'],
        //             'invoice_id'=>$groupItem['invoice_id'],
        //             'proposal_id'=>$groupItem['ProsID'],
        //             'prod_ratio'=>$prodRatio,
        //             'item_id'=>$groupItem['reference_item_id'],
        //         ]);
        //         // $this->db->where('invoice_id', $groupItem['invoice_id']);
        //         // $this->db->update(db_prefix().'stocks', [
        //         //     'prod_ratio'=>$prodRatio
        //         // ]);
        //     //}
            
           
        // //    echo json_encode(get_option('proposal_number_prefix') . str_pad($groupItem['ProsID'], get_option('number_padding_prefixes'), '0', STR_PAD_LEFT),);
        //    // echo ', ';
        // }
         echo json_encode($stocklist);
        //redirect(admin_url('stocks'));
       // $this->load->view('admin/stocks/stock', $data);
    }
    public function updateProposalMoq(){
        $proposalArr = $this->db->get(db_prefix().'proposals')->result_array();
        //print_r($proposalArr);
        foreach($proposalArr as $propItem){
            $this->db->select(db_prefix().'itemable.reference_item_id,'.db_prefix().'items.fcl_20_container');
            $this->db->from(db_prefix().'itemable');
            $this->db->join(db_prefix().'items', db_prefix().'items.id='.db_prefix().'itemable.reference_item_id', 'left');
            $this->db->where(db_prefix().'itemable.rel_id', $propItem['id']);
            $this->db->order_by(db_prefix().'itemable.item_order', 'ASC');
            $gettingData = $this->db->get()->result_array();
          //  print_r($gettingData);
          if($gettingData[0]['reference_item_id'] != 0){
            //echo json_encode($gettingData[0]['fcl_20_container']);
            $this->db->where('id', $propItem['id']);
            $this->db->update(db_prefix().'proposals', [
                'moq' => $gettingData[0]['fcl_20_container']
            ]);
          }
          
        }
        redirect(admin_url('proposals'));
        
    }
    public function stockData($id){
        //$this->load->model('invoice_items_model');
        //$grouped = $this->invoice_items_model->get_grouped_itemable($id);
        $this->db->where('itemGroup_id', $id);
        $this->db->order_by('id', 'DESC');
        $grouped = $this->db->get(db_prefix().'stocks')->result_array();
        echo json_encode($grouped);
    }

    public function item($id){
        $this->db->where('id', $id);
        $stockItem = $this->db->get(db_prefix().'stocks')->result_array();
        echo json_encode($stockItem[0]);
    }
}
?>