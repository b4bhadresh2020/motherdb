<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class mdl_batch_campaign_sms_stat extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getCampaignSmsStatData($getData,$start,$perpage) {

        $condition = array();
        $totalCount = GetAllRecordCount(BATCH_CAMPAIGN,$condition);

        $campaignSmsStatData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $campaignSmsStatData = JoinData(BATCH_CAMPAIGN,$condition,CAMPAIGN,"campaignId","campaignId","left",FALSE,array(array(BATCH_CAMPAIGN.'.createdDate' => 'DESC')),BATCH_CAMPAIGN.'.*,campaignName');
        }
        
        $response = array(
            'totalCount' => $totalCount,
            'resultData' => $campaignSmsStatData 
        );

        return $response;
        
    }


}