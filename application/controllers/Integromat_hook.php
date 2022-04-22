<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Integromat_hook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        
    }
        
    function index(){

        $data = $this->input->post();

        $integromatLead = array(
            "name" => $data['name'],
            "emailId" => $data['email'],
            "scenario" => $data['scenario'],
            "country" => $data['country']
        );
        ManageData(INTEGROMAT_LEAD_DATA,[],$integromatLead,TRUE);
    }      
    
}
