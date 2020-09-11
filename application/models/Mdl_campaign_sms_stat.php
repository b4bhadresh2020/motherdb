<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class mdl_campaign_sms_stat extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getCampaignSmsStatData($getData,$start,$perpage) {

        $condition = array();
        $totalCount = GetAllRecordCount(CAMPAIGN,$condition);

        $campaignSmsStatData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $campaignSmsStatData = GetAllRecord(CAMPAIGN,$condition,FALSE,array(),array(),array(array('campaignId' => 'DESC')));
        }
        
        $response = array(
            'totalCount' => $totalCount,
            'resultData' => $campaignSmsStatData 
        );

        return $response;
        
    }


}