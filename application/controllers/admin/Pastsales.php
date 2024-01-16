<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pastsales extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
       
    //    $data['pastsales'] = [];
        $this->db->select(db_prefix().'pastsales.*, '.db_prefix().'items.description as ProdName');
        $this->db->from(db_prefix().'pastsales');
        $this->db->join(db_prefix().'items', db_prefix().'items.id='.db_prefix().'pastsales.Product', 'left');
        $data['pastsales'] = $this->db->get()->result_array();
        if(!$data['pastsales']){
            $data['pastsales'] = [];
        }
    //     print_r($data['pastsales']);
    //    die;
       //$data['pastsales'] = 
       $data['items'] = $this->db->get(db_prefix().'items')->result_array();
       $data['companies'] = $this->db->select('company')->get(db_prefix().'clients')->result_array();

       $this->load->view('admin/pastsales/manage', $data);
    }

    public function initPastSales(){
        $this->db->select(db_prefix().'itemable.*, '.db_prefix().'invoices.date as addedDate, '.db_prefix().'clients.company');
        $this->db->where('rel_type', 'invoice');
        $this->db->from(db_prefix().'itemable');
        $this->db->join(db_prefix().'invoices', db_prefix().'invoices.id='.db_prefix().'itemable.rel_id', 'left');
        $this->db->join(db_prefix().'clients', db_prefix().'clients.userid='.db_prefix().'invoices.clientid', 'left');
        $addDatas = $this->db->get()->result_array();
        
        foreach($addDatas as $addItem){
            $this->db->insert(db_prefix().'pastsales', [
                'company' => $addItem['company'],
                'date' => date("Y-m-d", strtotime($addItem['addedDate'])),
                'Product' => $addItem['reference_item_id'],
                'Quantity' => $addItem['qty'],
                "Price" => $addItem['rate'] * $addItem['qty'],
            ]);
        }
        print_r($addDatas);
    }

    public function addSales(){
        $data = $this->input->post();
        if($data){
            if($data['id']){
                $this->db->where('id', $data['id']);
                $this->db->update(db_prefix().'pastsales', [
                    'company' => $data['company'],
                    'date' => date("Y-m-d", strtotime($data['date'])),
                    'Product' => $data['Product'],
                    'Quantity' => $data['Quantity'],
                    "Price" => $data['Price'],
                ]);
            }else{
                $this->db->insert(db_prefix().'pastsales', [
                    'company' => $data['company'],
                    'date' => date("Y-m-d", strtotime($data['date'])),
                    'Product' => $data['Product'],
                    'Quantity' => $data['Quantity'],
                    "Price" => $data['Price'],
                ]);
            }
            
            //echo json_encode(['success' => false]);
          
        }
        //echo json_encode(['success' => false]);
        redirect(admin_url('pastsales'));
    }
    
    public function uploadPastsales(){
        $data = $this->input->post();
        //foreach($data )
        if($data){
            $pastSales = $data['pastSales'];
            foreach($pastSales as $saleItem){
                $itemProd = $this->db->where('description', $saleItem['PRODUCT'])->get(db_prefix().'items')->row();
                $this->db->insert(db_prefix().'pastsales', [
                    'company' => $saleItem['COMPANY'],
                    'date' => date("Y-m-d", strtotime($saleItem['DATE'])),
                    'Product' => $itemProd->id,
                    'Quantity' => $saleItem['QUANTITY'],
                    "Price" => $saleItem['PRICE/UNIT'],
                ]);
            }
            echo json_encode(['success' => true]);
        }else{
            echo json_encode(['success' => false]);
        }
    }

    public function getSales($id){
        $this->db->where('id', $id);
        $saleData = $this->db->get(db_prefix().'pastsales')->result_array();
        echo json_encode($saleData);
    }

    public function removeSales($id){
        $this->db->where('id', $id)->delete(db_prefix().'pastsales');
        echo json_encode(['success' => true]);
    }
}
?>