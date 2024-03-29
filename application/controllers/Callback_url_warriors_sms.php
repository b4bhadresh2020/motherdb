<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Callback_url_warriors_sms extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();
        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }

    public function index() {
       
        $postData = $_POST;
        
        // test response get from warriors API
        $responseFilePath = APPPATH."logs/sms_warriors/response_".date("Y-m-d").".txt";
        $writeFile = fopen($responseFilePath, 'a');
        fwrite($writeFile,json_encode($postData)."\n");
        fclose($writeFile);

        if (@$postData['uniqueKey'] != '') {

            //update delivery report
            $uniqueKey = $postData['uniqueKey'];
            $status = $postData['status'];            
            $condition = array('uniqueKey' => $uniqueKey);
            $updateArr = array('status' => $status);
            if ($status == "delivered") {
                $updateArr['is_delivered'] = 1;
            }
            $is_insert = FALSE;
            ManageData(BATCH_USER,$condition,$updateArr,$is_insert);

            // update a delivered count in batch campaign
            $this->updateBatchUserCampaignDeliveredTotal("warriors_sms",$uniqueKey);
        }

    }

    function updateBatchUserCampaignDeliveredTotal($providerName,$uniqueKey){

        $batchCampaignId = $this->getBatchCampaignId($uniqueKey);

        if($batchCampaignId != 0){
            $condition = array(
                'batchCampaignId' => $batchCampaignId
            );            
            $is_single = TRUE;
            $this->db->limit(1);
            $batchCampaignData = GetAllRecord(BATCH_CAMPAIGN,$condition,$is_single,array(),array(),array(),'smsProvider');
            $batchSmsProviders = json_decode($batchCampaignData['smsProvider'],true);

            $sent = $batchSmsProviders[$providerName]['sent'];
            $delivered = $batchSmsProviders[$providerName]['delivered'] + 1;
            $delivered_per = ($delivered / $sent) * 100;
            $batchSmsProviders[$providerName]['delivered'] = $delivered;
            $batchSmsProviders[$providerName]['delivered_per'] = $delivered_per;

            $updateBatchCampaignData = array(
                'smsProvider' => json_encode($batchSmsProviders)           
            );
                
            $is_insert = FALSE;
            ManageData(BATCH_CAMPAIGN,$condition,$updateBatchCampaignData,$is_insert);
        }
    }

    function getBatchCampaignId($uniqueKey){
        $condition = array('uniqueKey' => $uniqueKey);
        $is_single = TRUE;
        $this->db->limit(1);
        $batchCampaignData = GetAllRecord(BATCH_USER,$condition,$is_single,array(),array(),array(),'batchCampaignId');
        if(isset($batchCampaignData['batchCampaignId'])){
            return $batchCampaignData['batchCampaignId'];
        }else{
            return 0;
        }
    }
    
}