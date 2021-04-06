<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_sms_stat extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_campaign_sms_stat');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_campaign_sms_stat->getCampaignSmsStatData($_GET,$start,$perPage);

        $dataCount = $responseData['totalCount'];
        $resultData = $responseData['resultData'];

        $data = pagination_data('campaign_sms_stat/manage/', $dataCount, $start, 3, $perPage, $resultData);

        $data['load_page'] = 'campaign_sms_stat';
        $data["curTemplateName"] = "campaign_sms_stat/list";
        $data['headerTitle'] = "Campaign sms stat";
        $data['pageTitle'] = "Campaign sms stat";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

   
    
}