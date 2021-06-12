<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ExpertSenderQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_expert_sender_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => EXPERT_SENDER),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_expert_sender_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('ExpertSenderQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['expertSenderList'] = $providers;
        $data['headerTitle'] = "Expert Sender Queue User Data";
        $data['load_page'] = 'expertSenderQueue';
        $data["curTemplateName"] = "expertSenderQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}