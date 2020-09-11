<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class mdl_re_tracking_broadcast extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_user_re_tracking');
        $this->load->model('mdl_broadcast');
        //$this->load->model('mdl_other_db');

    }


    function getUserReTrackingBroadcastDataCount($postData){

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
        $batchAndGeneralBatchName = $postData['batchAndGeneralBatchName'];
        $reTrackingCamapignFilter = $postData['reTrackingCamapignFilter'];

        $condition = array();

        $batchId = 0;
        $generalBatchId = 0;

        //explode and check if 'batch_name' is there or 'general batch name' is there
        $batchAndGeneralBatchNameExplode = explode('_&_', $batchAndGeneralBatchName);

        if ($batchAndGeneralBatchNameExplode[0] == 'batch') {
            
            //get batchId from batch name
            $batchName = $batchAndGeneralBatchNameExplode[1];
            $batchId = $this->mdl_user_re_tracking->getBatchIdFromBatchName($batchName);
            $condition['bu.batchId'] = $batchId;

        }else if($batchAndGeneralBatchNameExplode[0] == 'general_batch'){

            //get generalBatchId from general_batch
            $generalBatchName = $batchAndGeneralBatchNameExplode[1];
            $generalBatchId = $this->mdl_user_re_tracking->getGeneralBatchIdFromGeneralBatchName($generalBatchName);
            $condition['gbu.generalBatchId'] = $generalBatchId;
        }
        

        if($reTrackingCamapignFilter == 2){
            $campaignId = $postData['campaignName'];
            $condition['bu.campaignId'] = $campaignId;
        }


        if (@$country) {
            $condition['user.country'] = $country;
        }

        if (@$groupName) {
            $condition['user.groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";  
        }

        if (@$minAge && @$maxAge) {

            $condition['user.age >='] = $minAge;
            $condition['user.age <='] = $maxAge;

        }else if(@$minAge || @$maxAge){

            if (@$minAge) {
                $age = $minAge;
            }else{
                $age = $maxAge;
            }
            $condition['user.age'] = $age;            
        }

        if (@$gender) {
            $condition['user.gender'] = strtolower($gender);
        }

        //isset is for some purpose,dont just write @ here
        if (isset($type) && $type != '') {   
            $condition['user.isUserActive'] = $type;
        }

        if (@$exceptDays) {
            $minusDay = '-'.$exceptDays.' days';
            $exceptedDate = date('Y-m-d', strtotime($minusDay));

            $condition['user.lastSmsDate <'] = $exceptedDate;
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
                $max = 10;
            }

            $condition['user.totalClicks >='] = $min;
            $condition['user.totalClicks <='] = $max;
        }
        
        if (@$keyword) {
            $condition['user.keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
        }

        $this->db->select('*');
        $this->db->from('`batch_user` as `bu`');
        $this->db->join('`user`', '`user`.`userId` = `bu`.`userId`');
        $this->db->join('`general_batch_user` as `gbu`', '`gbu`.`batchUserId` = `bu`.`batchUserId`','left');
        $this->db->where($condition);
        $totalCount = $this->db->count_all_results();
        
        if (@$numberOfSms > 0) {
            if ($numberOfSms < $totalCount) {
                $totalCount = $numberOfSms;
            }
        }

        $response = array(
            'totalCount' => $totalCount
        );

        if ($totalCount > 0) {
            $response['batchId'] = $batchId;
            $response['generalBatchId'] = $generalBatchId; 
        }
        return  $response;
    
    }

    function addNewBatchUserCampaign($postData){
            
            if($postData['broadcastType'] == 1 || $postData['broadcastType'] == 2){
                $total = $postData['totalCount'];                
            }else if($postData['broadcastType'] == 3 ){
                $total = $postData['re_track_broadcast_split_part_process_per_page'];
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
                $total = $postData['re_track_broadcast_split_part_process_per_page'];
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


    function get_re_track_broadcast_user_process_data($postData, $start, $perPage){

        $response = $this->mdl_user_re_tracking->get_re_track_user_process_data($postData, $start, $perPage);

        return $response;
    }


    function addInReTrackBatchUserAndOtherTablesFromBroadcast($userData = array(),$postData = array()){

        $generalBatchId = $postData['generalBatchId'];
        $batchCampaignId = $postData['batchCampaignId'];
        $campaignId = $postData['campaignName'];
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

        foreach ($userData as $value) {

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

            $batchId = $value['batchId'];
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
                    $this->mdl_broadcast->send_sms_to_forty_two($smsData,$batchCampaignId);
                }else if($service_provider == 'cp_sms'){

                    $smsData = array('to' => $phone, 'message' => $replacedMsg, 'from' => $sender_id, 'timestamp' => $specificTimeNew,'uniqueKey' => $uniqueKey );
                   $this->mdl_broadcast->send_sms_to_cp_sms($smsData,$batchCampaignId);
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
                
                $campaignDataCount++;
                $this->mdl_user_re_tracking->addBatchSingleCount($batchId);    
            }
           
        }

        fclose($writeFile);

        if ($campaignDataCount > 0) {
            $this->mdl_user_tracking->addCampaignCount($campaignId,$campaignDataCount);    
        }

        if ($generalBatchDataCount > 0) {
            $this->mdl_user_tracking->addGeneralBatchCount($generalBatchId,$generalBatchDataCount);    
        }
        return $minuteCounter + 1;   
    }

}