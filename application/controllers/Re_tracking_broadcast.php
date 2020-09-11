<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Re_tracking_broadcast extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_re_tracking_broadcast');
        $this->load->model('mdl_history');
    }


    /*
        - ajax
        - get total data
    */
    
    public function getReTrackBroadcastClickerDataCount(){

        $postData = $_POST;
        $countArr = $this->mdl_re_tracking_broadcast->getUserReTrackingBroadcastDataCount($postData);
        echo json_encode($countArr);
    }

    /*
        ajax
        - process data for with merge tag and without merge tag
    */  

    function re_track_broadcast_send_now_process_user_data(){

        $postData = $this->input->post();
        $campaignId = $postData['campaignName'];
        $start = $postData['re_track_broadcast_send_now_process_start'];
        $perPage = $postData['re_track_broadcast_send_now_process_per_page'];
        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];
        $is_set_except_days = $postData['is_set_except_days'];
        $batchCampaignId = isset($postData['batchCampaignId'])?$postData['batchCampaignId']:0;

        // add sender data in postdata with updated keyname
        $postData['sender_id'] = $postData['send_now_sender_id']; 
        $postData['service_provider'] = $postData['send_now_service_provider'];
        $postData['specificTime'] = ''; //because no need in 'send now' option

        //get user data
        $modelResponse = $this->mdl_re_tracking_broadcast->get_re_track_broadcast_user_process_data($postData, $start, $perPage);

        $userData   = $modelResponse['userData'];
        $intervalCount = $modelResponse['intervalCount'];
        
        if ($start == 0 && $is_set_except_days == 0) {
            
            $this->mdl_user_tracking->addInSmsHistoryTable($postData);
            $this->mdl_user_tracking->editIsNewInCampaignTable($campaignId);
            $batchCampaignId = $this->mdl_re_tracking_broadcast->addNewBatchUserCampaign($postData);
            $postData['batchCampaignId'] = $batchCampaignId;

            //add data in history table
            if ($csvType == 1) {

                $fileName = 'sms_with_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'with_merge';

            }else if($csvType == 2){

                $fileName = 'sms_with_out_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'without_merge';
            }
            
            $jsonValue = array('campaignName' => $campaignId);
            if ($generalBatchId > 0) {
                $jsonValue['generalBatchName'] = $generalBatchId;
            }
            if($batchId > 0){
                $jsonValue['batchName'] = $batchId;
            }
            $jsonValue = json_encode($jsonValue);
            $isImported = 0;
            $this->mdl_history->addInHistoryTable($fileName, $fileModuleType, $isImported, $jsonValue, $totalCount);
        }else{
            $this->mdl_re_tracking_broadcast->updateNewBatchUserCampaign($postData);
        }

        //add data in batch user table and other table
        
        $this->mdl_re_tracking_broadcast->addInReTrackBatchUserAndOtherTablesFromBroadcast($userData,$postData);

        if (@$postData['exceptDays'] > 0) {
            $is_set_except_days = 1;
        }

        $response = array(
            'intervalCount' => $intervalCount,
            'is_set_except_days' => $is_set_except_days,
            'batchCampaignId' => $batchCampaignId
        );

        echo json_encode($response);

    }

    /*
        ajax
    */
    function re_track_broadcast_specific_time_process_user_data(){

        $postData = $this->input->post();
        $campaignId = $postData['campaignName'];
        $start = $postData['re_track_broadcast_specific_time_process_start'];
        $perPage = $postData['re_track_broadcast_specific_time_process_per_page'];
        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];
        $service_provider = $postData['specific_time_service_provider'];
        $is_set_except_days = $postData['is_set_except_days'];
        $batchCampaignId = isset($postData['batchCampaignId'])?$postData['batchCampaignId']:0;

        // add sender data in postdata with updated keyname
        $postData['sender_id'] = $postData['specific_time_sender_id']; 
        $postData['service_provider'] = $service_provider;

        //get user data
        $modelResponse = $this->mdl_re_tracking_broadcast->get_re_track_broadcast_user_process_data($postData, $start, $perPage);

        $userData   = $modelResponse['userData'];
        $intervalCount = $modelResponse['intervalCount'];
        
        if ($start == 0 && $is_set_except_days == 0) {
            
            $this->mdl_user_tracking->addInSmsHistoryTable($postData);
            $this->mdl_user_tracking->editIsNewInCampaignTable($campaignId);
            $batchCampaignId = $this->mdl_re_tracking_broadcast->addNewBatchUserCampaign($postData);
            $postData['batchCampaignId'] = $batchCampaignId;

            //add data in history table
            if ($csvType == 1) {

                $fileName = 'sms_with_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'with_merge';

            }else if($csvType == 2){

                $fileName = 'sms_with_out_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'without_merge';
            }
            
            $jsonValue = array('campaignName' => $campaignId);
            if ($generalBatchId > 0) {
                $jsonValue['generalBatchName'] = $generalBatchId;
            }
            if($batchId > 0){
                $jsonValue['batchName'] = $batchId;
            }
            $jsonValue = json_encode($jsonValue);
            $isImported = 0;
            $this->mdl_history->addInHistoryTable($fileName, $fileModuleType, $isImported, $jsonValue, $totalCount);
        }else{
            $this->mdl_re_tracking_broadcast->updateNewBatchUserCampaign($postData);
        }

        //add data in batch user table and other table        

        if ($service_provider == 'forty_two') {
            $postData['specificTime'] = $postData['diff_in_sec'];    
        }else if($service_provider == 'cp_sms'){
            $postData['specificTime'] = strtotime($postData['broadcast_specific_time']);
        }else if($service_provider == 'warriors_sms'){
            $postData['specificTime'] = date('Y-m-d H:i',strtotime($postData['broadcast_specific_time']));
        }

        
        $this->mdl_re_tracking_broadcast->addInReTrackBatchUserAndOtherTablesFromBroadcast($userData,$postData);

        if (@$postData['exceptDays'] > 0) {
            $is_set_except_days = 1;
        }

        $response = array(
            'intervalCount' => $intervalCount,
            'is_set_except_days' => $is_set_except_days,
            'batchCampaignId' => $batchCampaignId
        );

        echo json_encode($response);

    }

    
    /*
        ajax
    */
    function re_track_broadcast_process_split_part_user_data(){

        $postData = $this->input->post();
        $campaignId = $postData['campaignName'];
        $start = $postData['re_track_broadcast_split_part_process_start'];
        $perPage = $postData['re_track_broadcast_split_part_process_per_page'];
        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];
        $service_provider = $postData['split_part_service_provider'];
        $is_set_except_days = $postData['is_set_except_days'];
        $batchCampaignId = isset($postData['batchCampaignId'])?$postData['batchCampaignId']:0;

        // add sender data in postdata with updated keyname
        $postData['sender_id'] = $postData['split_part_sender_id']; 
        $postData['service_provider'] = $service_provider;

        //get user data
        $modelResponse = $this->mdl_re_tracking_broadcast->get_re_track_broadcast_user_process_data($postData, $start, $perPage);

        $userData   = $modelResponse['userData'];
        $intervalCount = $modelResponse['intervalCount'];
        
        if ($start == 0 && $is_set_except_days == 0) {
            
            $this->mdl_user_tracking->addInSmsHistoryTable($postData);
            $this->mdl_user_tracking->editIsNewInCampaignTable($campaignId);
            $batchCampaignId = $this->mdl_re_tracking_broadcast->addNewBatchUserCampaign($postData);
            $postData['batchCampaignId'] = $batchCampaignId;

            //add data in history table
            if ($csvType == 1) {

                $fileName = 'sms_with_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'with_merge';

            }else if($csvType == 2){

                $fileName = 'sms_with_out_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'without_merge';
            }
            
            $jsonValue = array('campaignName' => $campaignId);
            if ($generalBatchId > 0) {
                $jsonValue['generalBatchName'] = $generalBatchId;
            }
            if($batchId > 0){
                $jsonValue['batchName'] = $batchId;
            }
            $jsonValue = json_encode($jsonValue);
            $isImported = 0;
            $this->mdl_history->addInHistoryTable($fileName, $fileModuleType, $isImported, $jsonValue, $totalCount);
        }else{
            $this->mdl_re_tracking_broadcast->updateNewBatchUserCampaign($postData);
        }

        //add data in batch user table and other table        

        if ($service_provider == 'forty_two') {
            $postData['specificTime'] = $postData['split_part_diff_in_sec'];
        }else if($service_provider == 'cp_sms'){
            $postData['specificTime'] = strtotime($postData['split_part_specific_date']);
        }else if($service_provider == 'warriors_sms'){
            $postData['specificTime'] = date('Y-m-d H:i',strtotime($postData['split_part_specific_date']));
        }

        $minuteCounter = $this->mdl_re_tracking_broadcast->addInReTrackBatchUserAndOtherTablesFromBroadcast($userData,$postData);
        $previousProvider = $service_provider;

        if (@$postData['exceptDays'] > 0) {
            $is_set_except_days = 1;
        }

        $response = array(
            'intervalCount' => $intervalCount,
            'is_set_except_days' => $is_set_except_days,
            'batchCampaignId' => $batchCampaignId,
            'minuteCounter' => $minuteCounter,   
            'previousProvider' => $previousProvider
        );

        echo json_encode($response);
    }
}