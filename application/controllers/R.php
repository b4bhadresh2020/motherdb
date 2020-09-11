<?php

/**
 * 
 */
class R extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        //header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
    }

    function redirect_click(){

        $uniqueKey = $this->input->post('uniqueKey');
        
        if($uniqueKey == "test0"){
            //redirected to redirect link
            $response['err'] = 0;
            $response['msg'] = "https://felinafinans.dk/"; 
        }else{
            //check if unique key is valid or not
            $condition = array('uniqueKey' => $uniqueKey);
            $this->db->limit(1);
            $getRedirectLinkData = GetAllRecordsTest(BATCH_USER,$condition,TRUE);

            $response = array();

            if (count($getRedirectLinkData) > 0) {

                $dataArr = array();
                    
                if ($getRedirectLinkData['isActive'] == 0) {
                    //update isActive to 1 in batch_user
                    $dataArr['isActive'] = 1;
                }

                if ($getRedirectLinkData['is_delivered'] == 0) {
                    //update is_delivered to 1 in batch_user
                    $dataArr['is_delivered'] = 1;
                }

                if (count($dataArr) > 0) {

                    $condition = array('uniqueKey' => $uniqueKey);
                    ManageData(BATCH_USER,$condition,$dataArr,FALSE);   
                }

                $redirectLink = $getRedirectLinkData['redirectUrl'];

                //add batch active count
                $this->addBatchActiveCount($getRedirectLinkData['batchId']);
                $this->addCampaignActiveCount($getRedirectLinkData['campaignId']);
                $this->addGeneralBatchActiveCount($getRedirectLinkData['batchUserId']);
                $this->addBatchCampaignActiveCount($getRedirectLinkData['batchCampaignId']);
                
                //add unique key in to redirect_link_clicks table with unix timestamp
                $condition = array();
                $dataArr = array('uniqueKey' => $uniqueKey,'clickDateTime' => time());
                $lastInsertId = ManageData(REDIRECT_LINK_CLICKS,$condition,$dataArr,TRUE);

                //update user active flag and total clicks
                $this->db->where('userId', $getRedirectLinkData['userId']);
                $this->db->set('totalClicks', 'totalClicks+1', FALSE);
                $this->db->set('isUserActive', 1);
                $this->db->update(USER);
                    
                //redirected to redirect link
                $response['err'] = 0;
                $response['msg'] = $redirectLink; 

            }else{
                //unique key does not available so redirected to page not found
                $response['err'] = 1;
                $response['msg'] = "page not found"; 

            }
        }
        echo json_encode($response);
    }


    function addBatchActiveCount($batchId = 0){

        $this->db->where('batchId', $batchId);
        $this->db->set('active', 'active+1', FALSE);
        $this->db->update(BATCH);
    }

    function addBatchCampaignActiveCount($batchCampaignId = 0){
        if($batchCampaignId !="" || $batchCampaignId != 0){
            $this->db->where('batchCampaignId', $batchCampaignId);
            $this->db->set('clicks', 'clicks+1', FALSE);
            $this->db->update(BATCH_CAMPAIGN);
        }
    }


    function addCampaignActiveCount($campaignId = 0){

        $this->db->where('campaignId', $campaignId);
        $this->db->set('active', 'active+1', FALSE);
        $this->db->update(CAMPAIGN);
    }
    

    function addGeneralBatchActiveCount($batchUserId = 0){

        //get general batch id
        $condition = array('batchUserId' => $batchUserId);
        // $is_single = TRUE;
        $this->db->limit(1);
        // $getGeneralBatchData = GetAllRecord(GENERAL_BATCH_USER,$condition,$is_single);
        $getGeneralBatchData = GetAllRecordsTest(GENERAL_BATCH_USER,$condition,TRUE);

        if (count($getGeneralBatchData) > 0) {

            $generalBatchId = $getGeneralBatchData['generalBatchId'];

            $this->db->where('generalBatchId', $generalBatchId);
            $this->db->set('active', 'active+1', FALSE);
            $this->db->update(GENERAL_BATCH);    
        }
        
    }   


    function get_only_link(){

        $uniqueKey = $this->input->post('uniqueKey');
        
        //check if unique key is valid or not
        $condition = array('uniqueKey' => $uniqueKey);
        // $is_single = TRUE;
        $this->db->limit(1);
        // $getRedirectLinkData = GetAllRecord(BATCH_USER,$condition,$is_single,array(),array(),array(),'redirectUrl');
        $getRedirectLinkData = GetAllRecordsTest(BATCH_USER,$condition,TRUE,'redirectUrl');

        $response = array();

        if (count($getRedirectLinkData) > 0) {

            $redirectLink = $getRedirectLinkData['redirectUrl'];

            //redirected to redirect link
            $response['err'] = 0;
            $response['msg'] = $redirectLink; 

        }else{
            //unique key does not available so redirected to page not found
            $response['err'] = 1;
            $response['msg'] = "page not found"; 

        }

        echo json_encode($response);
    }



}