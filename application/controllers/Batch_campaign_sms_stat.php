<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_campaign_sms_stat extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }     

        $this->load->model('mdl_batch_campaign_sms_stat');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_batch_campaign_sms_stat->getCampaignSmsStatData($_GET,$start,$perPage);

        $dataCount = $responseData['totalCount'];
        $resultData = $responseData['resultData']; 

        $data = pagination_data('batch_campaign_sms_stat/manage/', $dataCount, $start, 3, $perPage, $resultData);

        $data['load_page'] = 'batch_campaign_sms_stat';
        $data["curTemplateName"] = "batch_campaign_sms_stat/list";
        $data['headerTitle'] = "Batch campaign sms stat";
        $data['pageTitle'] = "Batch campaign sms stat";

        $this->load->view('commonTemplates/templateLayout', $data);
    }   

    public function getProviderMoreData()
    {
        $batchCampaignId = trim($this->input->post('batchCampaignId'));

        //get from redirect_link_clicks
        $condition                 = array('batchCampaignId' => $batchCampaignId);
        $is_single                 = true;        
        $providerJsonData          = GetAllRecord(BATCH_CAMPAIGN, $condition, $is_single);

        $data                      = array();
        $data['providerData']      = json_decode($providerJsonData['smsProvider'],true);

        foreach($data['providerData'] as $providerName => $providerDetails){
            $condition             = array(
                                        'batchCampaignId' => $batchCampaignId,
                                        'sms_provider'    => $providerName,
                                        'isActive'        => 1,
                                    );
            $is_single             = false;        
            $providerClickData    = JoinData(BATCH_USER, $condition,REDIRECT_LINK_CLICKS,'uniqueKey','uniqueKey','', $is_single,array(),'reditectLinkClickId');
            $providerClickCount = count($providerClickData);
            $providerDetails['click'] = $providerClickCount;
            $providerDetails['click_per'] = ($providerDetails['delivered'] != 0)?($providerClickCount / $providerDetails['delivered']) * 100:0;
            $data['providerData'][$providerName] = $providerDetails;

        }
        $this->load->view('batch_campaign_sms_stat/provider_detail_table_view', $data);

    }

    
    
}