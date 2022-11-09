<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_broadcast extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_forty_two_sms_provider');
        $this->load->model('mdl_cp_sms_provider');
        $this->load->model('mdl_warriors_sms_provider');
        //$this->load->model('mdl_other_db');
    }




    function getUserBroadcastTrackClickerDataCount($postData){
        
        $groupName      = @$postData['groupName'];
        $minAge         = @$postData['minAge'];
        $maxAge         = @$postData['maxAge'];
        $gender         = @$postData['gender'];
        $type           = @$postData['type'];
        $exceptDays     = @$postData['exceptDays'];
        $keyword        = @$postData['keyword'];
        $phone          = @$postData['phone'];
        $numberOfSms    = @$postData['numberOfSms'];
        $superClickers  = @$postData['superClickers'];
        $country        = @$postData['country']; 
        $csvType        = @$postData['csvType']; 
        $campaignId     = @$postData['campaignName'];        

        $userIdArrLimit = 500;
        $userIdsArr = array();

        $condition = array();

        $condition['phone >'] = 0;

        if (@$country) {
            $condition['country'] = $country;
        }

        if (@$groupName) {
            $condition['groupName REGEXP'] = "\\b".trim($groupName)."\\b";  
        }

        if (@$minAge && @$maxAge) {

            $condition['age >='] = $minAge;
            $condition['age <='] = $maxAge;

        }else if(@$minAge || @$maxAge){

            if (@$minAge) {
                $age = $minAge;
            }else{
                $age = $maxAge;
            }
            $condition['age'] = $age;            
        }

        if (@$gender) {
            $condition['gender'] = strtolower($gender);
        }

        //isset is for some purpose,dont just write @ here
        if (isset($type) && $type != '') {   
            $condition['isUserActive'] = $type;
        }

        if (@$exceptDays) {
            $minusDay = '-'.$exceptDays.' days';
            $exceptedDate = date('Y-m-d', strtotime($minusDay));

            $condition['lastSmsDate <'] = $exceptedDate;
        }

        if (@$superClickers) {

            if ($superClickers == 1) {
                //1-3
                $min = 1;
                $max = 3;
            }elseif ($superClickers == 2) {
                //3-5
                $min = 3;
                $max = 5;
            }else{
                //5-10
                $min = 5;
                $max = 50;
            }

            $condition['totalClicks >='] = $min;
            $condition['totalClicks <='] = $max;
        }
        
        if (@$keyword) {
            $condition['keyword REGEXP'] = "\\b".trim($keyword)."\\b";   
        }

      
        //isset is for some purpose,dont just write @ here
        if (isset($phone) && $phone != '') {
                
            //get distict userIdsArr from user_participated_campaign table
            $this->db->select('userId');
            $this->db->distinct();
            $this->db->where('campaignId',$campaignId);
            $userIdArr = $this->db->get(USER_PARTICIPATED_CAMPAIGN)->result_array();

            $userIdsArr = array();
            foreach ($userIdArr as $value) {
                $userIdsArr[] = $value['userId'];
            }
            
        }


        /*
            For user count: starts
        */
        
            $this->db->where($condition);

            //isset is for some purpose,dont just write @ here
            if (isset($phone) && $phone != '') {
                
                if (count($userIdsArr) == 0) {
                    $userIdsArr = ['0'];
                }

                if ($phone == 1) {

                    if (count($userIdsArr) > $userIdArrLimit) {

                        $splitArrays = array_chunk($userIdsArr,$userIdArrLimit);

                        foreach ($splitArrays as $spa) {
                            $this->db->where_not_in('userId',$spa);        //because 'where_not_in' has specific space
                        }     

                    }else{
                        $this->db->where_not_in('userId',$userIdsArr);    
                    }
                    
                    
                }else if($phone == 0){

                    if (count($userIdsArr) > $userIdArrLimit) {
                        
                        $splitArrays = array_chunk($userIdsArr,$userIdArrLimit);

                        foreach ($splitArrays as $spa) {
                            $this->db->where_in('userId',$spa);             //because 'where_in' has specific space
                        }

                    }else{
                        $this->db->where_in('userId',$userIdsArr);
                    }
                    
                    
                }
            }

            if (@$numberOfSms > 0) {
                $this->db->limit($numberOfSms);
            }
            
            $totalCount = $this->db->count_all_results(USER);

        /*
            For user count: ends
        */

        $response = array(
            'totalCount' => $totalCount
        );

        if ($totalCount > 0) {
            $response['batchId'] = $this->mdl_user_tracking->getBatchId($postData);
            $response['generalBatchId'] = $this->mdl_user_tracking->getGeneralBatchId($postData);
            $response['userIdsArr'] = json_encode($userIdsArr); 
            $postData['totalCount'] = $totalCount;
        }

        return $response;

    }

    function addNewBatchUserCampaign($postData){
            
            if($postData['broadcastType'] == 1 || $postData['broadcastType'] == 2){
                $total = $postData['totalCount'];                
            }else if($postData['broadcastType'] == 3 ){
                $total = $postData['broadcast_split_part_process_per_page'];
            }

            $smsProviders[$postData['service_provider']] = array(
                'total'     => $total,
                'sent'      => 0,
                'delivered' => 0,
                'total_per' => 100,
                'sent_per' => 0,
                'delivered_per' => 0,
            );

            $condition = array();            
            $insertBatchCampaignData = array(
                'campaignId' => $postData['campaignName'],
                'country' => $postData['country'],
                'total' => $postData['totalCount'],
                'exportType' => "sms",
                'csvType' => $postData['csvType'], 
                'smsProvider' => json_encode($smsProviders),            
                'createdDate' => date('Y-m-d H:i:s')
            );

            $is_insert = TRUE;
            $batchCampaignId = ManageData(BATCH_CAMPAIGN,$condition,$insertBatchCampaignData,$is_insert);
            return $batchCampaignId;
    }

    function updateNewBatchUserCampaign($postData){

            $condition = array(
                'batchCampaignId' => $postData['batchCampaignId']
            );            
            $is_single = TRUE;
            $batchCampaignData = GetAllRecord(BATCH_CAMPAIGN,$condition,$is_single);
            $batchSmsProviders = json_decode($batchCampaignData['smsProvider'],true);

            if($postData['broadcastType'] == 1 || $postData['broadcastType'] == 2){
                $total = $postData['totalCount'];                
            }else if($postData['broadcastType'] == 3 ){
                $total = $postData['broadcast_split_part_process_per_page'];
            }

            if (array_key_exists($postData['service_provider'], $batchSmsProviders)) {
               $total = $total + $batchSmsProviders[$postData['service_provider']]['total'];              
               $sent =  $batchSmsProviders[$postData['service_provider']]['sent'];              
               $delivered =  $batchSmsProviders[$postData['service_provider']]['delivered'];              
               $total_per = $batchSmsProviders[$postData['service_provider']]['total_per'];              
               $sent_per =  $batchSmsProviders[$postData['service_provider']]['sent_per'];              
               $delivered_per =  $batchSmsProviders[$postData['service_provider']]['delivered_per'];              
            }else{
                $sent = 0;
                $delivered = 0;
                $total_per = 100;
                $sent_per = 0;
                $delivered_per = 0;
            }

            $batchSmsProviders[$postData['service_provider']] = array(
                'total'     => $total,
                'sent'      => $sent,
                'delivered' => $delivered,
                'total_per' => $total_per,
                'sent_per'  => $sent_per,
                'delivered_per'  => $delivered_per,
            );
            
            $updateBatchCampaignData = array(
                'smsProvider' => json_encode($batchSmsProviders)           
            );
            
            $is_insert = FALSE;
            $batchCampaignId = ManageData(BATCH_CAMPAIGN,$condition,$updateBatchCampaignData,$is_insert);
            return $batchCampaignId;
    }


    function get_user_process_data_from_broadcast($postData,$userIdsArr,$start,$perPage){

        $response = $this->mdl_user_tracking->get_user_process_data($postData,$userIdsArr,$start,$perPage);
        
        return $response;
        
    }

   
    function addInBatchUserAndOtherTablesFromBroadcast($userData = array(),$postData = array()){
        
        $batchId = $postData['batchId'];
        $batchCampaignId = $postData['batchCampaignId'];
        $campaignId = $postData['campaignName'];
        $generalBatchId = $postData['generalBatchId'];
        $msg = $postData['msg'];
        $redirectUrl = $postData['redirectUrl'];
        $csvType = $postData['csvType'];
        $sender_id = $postData['sender_id'];
        $service_provider = $postData['service_provider'];
        $previous_service_provider = $postData['previousProvider'];
        $specificTime = $postData['specificTime'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $totalCount = $postData['totalCount'];        
        $isThrottle = $postData['isThrottle'];
        $hours = $postData['sendOverHour'];

        $generalBatchDataCount = 0;
        $campaignDataCount = 0;
        $batchDataCount = 0;

        $recordPerMinute = ceil($totalCount / ($hours * 60));
        $minuteCounter   = $postData['minuteCounter'];
        $recordCounter   = 0;

        if($service_provider != '' && $service_provider != $previous_service_provider){
            $minuteCounter = 0;
        }

        //create log file.
        $responseFilePath = APPPATH."logs/extra/response_".date("Y-m-d").".txt";
        $writeFile = fopen($responseFilePath, 'a');
        fwrite($writeFile,"-----------".date("Y-m-d H:i")."-----------"."\n");

        foreach ($userData as $index => $value) {
            if($isThrottle){
                if($recordCounter == $recordPerMinute){
                    $recordCounter = 0;
                    $minuteCounter += 1;
                }
                $recordCounter += 1;

                if ($service_provider == 'forty_two') {
                    $specificTimeNew = $specificTime + ($minuteCounter * 60);
                }else if($service_provider == 'cp_sms'){
                    $specificTimeNew = strtotime('+'.$minuteCounter.' minutes',$specificTime);
                }else if($service_provider == 'warriors_sms'){
                    $specificTimeNew = date('Y-m-d H:i',strtotime('+'.$minuteCounter.' minutes',strtotime($specificTime)));
                }  
            }else{
                $specificTimeNew = $specificTime;
            }

            $userId = $value['userId'];
            $phone = $value['phone'];

            //generate unique key (5 char random string)
            $uniqueKey = $this->mdl_user_tracking->getUniqueKey();
            $condition = array();
            $is_insert = true;           

            $insertData = array('batchId' => $batchId, 'userId' => $userId, 'campaignId' => $campaignId , 'batchCampaignId' => $batchCampaignId, 'uniqueKey' => $uniqueKey,'msg' => $msg, 'redirectUrl' => $redirectUrl, 'domain' => $domain, 'unsubscribeDomain' => $unsubscribeDomain);
            $batchUserId = ManageData(BATCH_USER, $condition, $insertData, $is_insert);

            if ($batchUserId > 0) {

                //add data in uniquekey_link (hoi3) table
                //$unique_key_link_data = array('uniqueKey' => $uniqueKey, 'redirectUrl' => $redirectUrl);
                //$this->mdl_other_db->add_data_in_unique_key_link_table($unique_key_link_data);

                /*
                 * msg = "Hej, se hvad vi har til dig i dag, klik her: {url}"; 
                 * Replace {url} with url
                 */
                
                /* $replacedUrl = UNSUBSCRIBE_DOMAIN.'r/'.$uniqueKey;
                $unsubscribe_url = UNSUBSCRIBE_DOMAIN.$uniqueKey; */

                $replacedUrl = $domain.'r/'.$uniqueKey;
                if(!empty($unsubscribeDomain)){
                    $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
                }else{
                    $unsubscribe_url = $domain.$uniqueKey;
                }                

                $fixedString   = ["{url}","{unsubscribe_url}"];
                $replacedWith = [$replacedUrl,$unsubscribe_url];
                $replacedMsg = str_replace($fixedString,$replacedWith,$msg);
                
                fwrite($writeFile,$userId." - ".$phone." - ".$uniqueKey." - ".$service_provider." - ".$specificTimeNew." - ".$replacedMsg."\n");                

                //now send sms

                if ($service_provider == 'forty_two') {
                    
                    $smsData = array('mobile' => $phone, 'msg' => $replacedMsg, 'sender_id' => $sender_id, 'specificTime' => $specificTimeNew,'uniqueKey' => $uniqueKey );
                    $this->send_sms_to_forty_two($smsData,$batchCampaignId);

                }else if($service_provider == 'cp_sms'){

                    $smsData = array('to' => $phone, 'message' => $replacedMsg, 'from' => $sender_id, 'timestamp' => $specificTimeNew,'uniqueKey' => $uniqueKey );
                    $this->send_sms_to_cp_sms($smsData,$batchCampaignId);
                }else if($service_provider == 'warriors_sms'){

                    $smsData = array('to' => $phone, 'message' => $replacedMsg, 'from' => $sender_id, 'timestamp' => $specificTimeNew,'uniqueKey' => $uniqueKey );
                    $this->send_sms_to_warriors_sms($smsData,$batchCampaignId);
                }
                    
                /*
                 *  Add batchUserId and generalBatchId in GENERAL_BATCH_USER                     
                 */
                if($generalBatchId > 0){

                    $condition = array();
                    $is_insert = TRUE;
                    $insertedArr = array('batchUserId' => $batchUserId, 'generalBatchId' => $generalBatchId);
                    ManageData(GENERAL_BATCH_USER,$condition,$insertedArr,$is_insert);
                    $generalBatchDataCount++;
                    
                }

                /*
                Insert campaignId and userId in user_participated_campaign table
                first check if same record is already there or not
                if it is not there then insert otherwise dont
                 */

                if ($campaignId > 0) {

                    $condition           = array('campaignId' => $campaignId, 'userId' => $userId);
                    $getUserPartCampData = GetAllRecordCount(USER_PARTICIPATED_CAMPAIGN, $condition);

                    if ($getUserPartCampData == 0) {

                        $condition  = array();
                        $is_insert  = true;
                        $insertData = array('campaignId' => $campaignId, 'userId' => $userId);
                        ManageData(USER_PARTICIPATED_CAMPAIGN, $condition, $insertData, $is_insert);
                    }

                }

                /*
                update last sms date for user
                 */
                $condition = array('userId' => $userId);
                $is_insert = false;
                $updateArr = array('lastSmsDate' => date('Y-m-d'));
                ManageData(USER, $condition, $updateArr, $is_insert);

                $batchDataCount++;
                $campaignDataCount++; 
            }
        }

        fclose($writeFile);

        if ($batchDataCount > 0) {
            $this->mdl_user_tracking->addBatchCount($batchId,$batchDataCount);    
        }

        if ($campaignDataCount > 0) {
            $this->mdl_user_tracking->addCampaignCount($campaignId,$campaignDataCount);    
        }

        if ($generalBatchDataCount > 0) {
            $this->mdl_user_tracking->addGeneralBatchCount($generalBatchId,$generalBatchDataCount);    
        }

        return $minuteCounter + 1;        
    }



    function send_sms_to_forty_two($smsData,$batchCampaignId){

        $response = array();
        $mobile = $smsData['mobile'];
        $msg = $smsData['msg'];
        $sender_id = $smsData['sender_id'];
        $callback_url = base_url().'callback_url_forty_two/delivery_report';
        $job_id = $smsData['uniqueKey'];
        $specificTime = $smsData['specificTime'];
        $providerName = "forty_two";  

        $params = array(
            'destinations' => array(array('number' => $mobile)),
            'sms_content' => array(
                'message' => $msg,
                'sender_id' => $sender_id 
            ),
            'callback_url' => $callback_url,
            'job_id' => $job_id
        );

        if ($specificTime != '') {
            $params['ttl'] = $specificTime;
        }

        // comment below line on testing
        $model_response = $this->mdl_forty_two_sms_provider->send_sms_with_use_of_forty_two($params);

        //update sent status
        $updateArr = array('sms_provider' => 'forty_two');
        $condition = array('uniqueKey' => $job_id);

        if (@$model_response['result_info'] != '') {
                
            if($model_response['result_info']['status_code'] == 200){
                $updateArr['is_sent'] = 1;
                $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);
            }else{
                $updateArr['is_sent'] = 0;
            }
            $updateArr['sent_description'] = $model_response['result_info']['description'];
            
        }else{
            //error
            $updateArr['is_sent'] = 0;
            $updateArr['sent_description'] = "There is some problem occured. Please try again later.";
        }

        // uncomment below line on testing and comment above if condition.
        // $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);
        // $updateArr['is_sent'] = 0;
        // $updateArr['sent_description'] = "There is some problem occured. Please try again later.";

        ManageData(BATCH_USER,$condition,$updateArr,FALSE);
        
    }

    function send_sms_to_cp_sms($smsData,$batchCampaignId){

        $response = array();
        $to   = $smsData['to'];
        $message  = $smsData['message'];
        $from     = $smsData['from'];
        $callback_url = base_url().'callback_url_cp_sms/delivery_report?uniqueKey='.$smsData['uniqueKey'];
        $timestamp = $smsData['timestamp'];
        $providerName = "cp_sms";       
        
        $params = array(
            'to' => $to,
            'message' => $message,
            'from' => $from,
            'dlr_url' => $callback_url
        );

        if ($timestamp != '') {
            $params['timestamp'] = (int)$timestamp;
        }

        // comment below line on testing
        $model_response = $this->mdl_cp_sms_provider->send_sms_with_use_of_cp_sms($params);

        $updateArr = array('sms_provider' => 'cp_sms');
        $condition = array('uniqueKey' => $smsData['uniqueKey']);

        if (@$model_response['success']) {
            //error
            $updateArr['is_sent'] = 1;
            $updateArr['sent_description'] = 'SMS has been sent successfully';
            $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);
        }else{
            //ok
            $updateArr['is_sent'] = 0;
            if (@$model_response['error']['message'] != '') {
                $updateArr['sent_description'] = $model_response['error']['message'];    
            }else{
                $updateArr['sent_description'] = "SMS has not been sent due to some issue. Please try again later";
            }
        }

        // uncomment below line on testing and comment above if condition.
        // $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);
        // $updateArr['is_sent'] = 0;
        // $updateArr['sent_description'] = "There is some problem occured. Please try again later.";

        ManageData(BATCH_USER,$condition,$updateArr,FALSE);

    }

    function send_sms_to_warriors_sms($smsData,$batchCampaignId){

        $response = array();
        $to   = $smsData['to'];
        $message  = $smsData['message'];
        $from     = $smsData['from'];        
        $timestamp = $smsData['timestamp'];
        $providerName = "warriors_sms";  
        $uniqueKey = $smsData['uniqueKey'];     
        
        $params = array(
            'contacts' => $to,
            'msg' => urlencode($message),
            'senderid' => urlencode($from),
            'uniqueKey' => $uniqueKey
        );

        if ($timestamp != '') {
            $params['time'] = date("Y-m-d H:i",strtotime($timestamp));
        }

        // comment below line on testing
        $model_response = $this->mdl_warriors_sms_provider->send_sms_with_use_of_warriors_sms($params);

        $updateArr = array('sms_provider' => 'warriors_sms');
        $condition = array('uniqueKey' => $uniqueKey);

        if (@$model_response['status'] == "OK") {
            if (@$model_response['delivery'] == "sent") {
                $updateArr['is_sent'] = 1;
                $updateArr['sent_description'] = 'SMS has been sent successfully';
            }else{
                $updateArr['is_sent'] = 1;
                $updateArr['sent_description'] = 'SMS has been sent in queue successfully';
            }    
            $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);        
        }else{            
            $updateArr['is_sent'] = 0;
            if (@$model_response['error_msg'] != '') {
                $updateArr['sent_description'] = $model_response['error_msg'];    
            }else{
                $updateArr['sent_description'] = "SMS has not been sent due to some issue. Please try again later";
            }
        }

        // uncomment below line on testing and comment above if condition.
        // $this->updateBatchUserCampaignSentTotal($providerName,$batchCampaignId);
        // $updateArr['is_sent'] = 0;
        // $updateArr['sent_description'] = "There is some problem occured. Please try again later.";

        ManageData(BATCH_USER,$condition,$updateArr,FALSE);
    }

    function updateBatchUserCampaignSentTotal($providerName,$batchCampaignId){
        $condition = array(
            'batchCampaignId' => $batchCampaignId
        );            
        $is_single = TRUE;
        $this->db->limit(1);
        $batchCampaignData = GetAllRecord(BATCH_CAMPAIGN,$condition,$is_single,array(),array(),array(),'smsProvider');
        $batchSmsProviders = json_decode($batchCampaignData['smsProvider'],true);

        $total = $batchSmsProviders[$providerName]['total'];
        $sent = $batchSmsProviders[$providerName]['sent'] + 1;
        $sent_per = ($sent / $total) * 100;
        $batchSmsProviders[$providerName]['sent'] = $sent;
        $batchSmsProviders[$providerName]['sent_per'] = $sent_per;

        $updateBatchCampaignData = array(
            'smsProvider' => json_encode($batchSmsProviders)           
        );
            
        $is_insert = FALSE;
        ManageData(BATCH_CAMPAIGN,$condition,$updateBatchCampaignData,$is_insert);
    }

}