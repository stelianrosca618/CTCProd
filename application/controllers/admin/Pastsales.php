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
    
    public function getSales($id){
        $this->db->where('id', $id);
        $saleData = $this->db->get(db_prefix().'pastsales')->result_array();
        echo json_encode($saleData);
    }
}
?>