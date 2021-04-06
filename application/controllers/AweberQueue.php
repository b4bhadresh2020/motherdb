<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AweberQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }    

        $this->load->model('mdl_aweber_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => AWEBER),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_aweber_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('AweberQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['aweberList'] = $providers;
        $data['headerTitle'] = "Aweber Queue User Data";
        $data['load_page'] = 'aweberQueue';
        $data["curTemplateName"] = "aweberQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}