<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ConvertkitQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_convertkit_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => CONVERTKIT),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_convertkit_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('ConvertkitQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['convertkitList'] = $providers;
        $data['headerTitle'] = "Convertkit Queue User Data";
        $data['load_page'] = 'convertkitQueue';
        $data["curTemplateName"] = "convertkitQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}