<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MarketingPlatformQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_marketing_platform_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        // $providers = GetAllRecord(PROVIDERS,array("provider" => MARKETING_PLATFORM),false,array(),array(),array());

        //get file data
        $condition = array(
            'providers.provider' => MARKETING_PLATFORM,
            'marketing_platform_accounts.status' => 1
        );
        $is_single = FALSE;
        $providers = JoinData(PROVIDERS,$condition,MARKETING_PLATFORM_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"","");

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_marketing_platform_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('MarketingPlatformQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['marketingPlatformList'] = $providers;
        $data['headerTitle'] = "Marketing Platform Queue User Data";
        $data['load_page'] = 'marketingPlatformQueue';
        $data["curTemplateName"] = "marketingPlatformQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}