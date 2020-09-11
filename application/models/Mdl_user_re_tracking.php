<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_user_re_tracking extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->model('mdl_user_tracking');
        //$this->load->model('mdl_other_db');
    }


    function getBatchIdFromBatchName($batchName = ''){

        $batchId = 0;
        $batchCondition = array('batchName' => $batchName);
        $is_single = TRUE;
        $batchData = GetAllRecord(BATCH,$batchCondition,$is_single);
        if (counts($batchData) > 0) {
            $batchId = $batchData['batchId'];    
        }
        return $batchId;
    }

    function getGeneralBatchIdFromGeneralBatchName($generalBatchName=''){

        $generalBatchId = 0;
        $genBatchCondition = array('generalBatchName' => $generalBatchName);
        $is_single = TRUE;
        $generalBatchData = GetAllRecord(GENERAL_BATCH,$genBatchCondition,$is_single);
        if (counts($generalBatchData)>0) {
            $generalBatchId = $generalBatchData['generalBatchId'];
        }
        return $generalBatchId;
    }

    function getUserReTrackingDataCount($postData){

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
            $batchId = $this->getBatchIdFromBatchName($batchName);
            $condition['bu.batchId'] = $batchId;

        }else if($batchAndGeneralBatchNameExplode[0] == 'general_batch'){

            //get generalBatchId from general_batch
            $generalBatchName = $batchAndGeneralBatchNameExplode[1];
            $generalBatchId = $this->getGeneralBatchIdFromGeneralBatchName($generalBatchName);
            $condition['gbu.generalBatchId'] = $generalBatchId;
        }
        

        if($reTrackingCamapignFilter == 2){
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

        /* $sql = $this->db->last_query();
        echo $sql;
        die; */
        
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

            //get fileNameId from export_file_name table
            $postData['totalCount'] = $totalCount;
            $fileNameId = $this->mdl_user_tracking->getExportFileNameData($postData);
            $response['fileNameId'] = $fileNameId;

            //add campaign in batch campaign table.           
            $exportType = "csv";
            $response['batchCampaignId'] = $this->addNewBatchUserCampaign($campaignId,$country,$totalCount,$exportType,$csvType);
        }
        /*pre($response);
        die;*/
        return  $response;
    
    }

    function addNewBatchUserCampaign($campaignId,$country,$totalCount,$exportType,$csvType){
            $condition = array();
            $insertBatchCampaignData = array(
                'campaignId' => $campaignId,
                'country' => $country,
                'total' => $totalCount,
                'exportType' => $exportType,
                'csvType' => $csvType,
                'createdDate' => date('Y-m-d H:i:s')
            );
            $is_insert = TRUE;
            $batchCampaignId = ManageData(BATCH_CAMPAIGN,$condition,$insertBatchCampaignData,$is_insert);
            return $batchCampaignId;
    }

    function get_re_track_user_process_data($postData, $start, $perPage){


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
        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];

        $condition = array();

        //explode and check if 'batch_name' is there or 'general batch name' is there
        $batchAndGeneralBatchNameExplode = explode('_&_', $batchAndGeneralBatchName);

        if ($batchId > 0) {
            $condition['bu.batchId'] = $batchId;
        }else if($generalBatchId > 0){
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

        $this->db->order_by('`user`.`isUserActive`','desc');
        $this->db->order_by("r_id", "desc");
        /*$this->db->order_by('`user`.`userId`', 'desc');
        $this->db->order_by('`user`.`participated`', 'desc');*/

        $this->db->limit($perPage,$start);

        $userDataList = $this->db->get()->result_array();

        $response = array(
            'intervalCount' => count($userDataList),
            'userData' => $userDataList
        );
        /*pre($response);
        last_query();
        die;*/
        return $response;
        
    
    }

   


    public function getUniqueKey() {

        $uniqueKey      = generateRandomString(5);
        $condition      = array('uniqueKey' => $uniqueKey);
        $uniqueKeyCount = GetAllRecordCount(BATCH_USER, $condition);

        if ($uniqueKeyCount > 0) {
            return $this->getUniqueKey();
        }
        return $uniqueKey;
    }



    function addInReTrackBatchUserAndOtherTables($userData = array(),$postData = array()){

        $generalBatchId = $postData['generalBatchId'];
        $campaignId = $postData['campaignName'];
        $batchCampaignId = $postData['batchCampaignId'];
        $msg = $postData['msg'];
        $redirectUrl = $postData['redirectUrl'];
        $fileNameId = $postData['fileNameId'];
        $csvType = $postData['csvType'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $generalBatchDataCount = 0;
        $campaignDataCount = 0;

        foreach ($userData as $value) {

            $batchId = $value['batchId'];
            $userId = $value['userId'];
            $phone = $value['phone'];

            //generate unique key (5 char random string)
            $uniqueKey = $this->getUniqueKey();
            $condition = array();
            $is_insert = true;

            $insertData = array('batchId' => $batchId, 'userId' => $userId, 'campaignId' => $campaignId , 'batchCampaignId' => $batchCampaignId, 'uniqueKey' => $uniqueKey,'msg' => $msg, 'redirectUrl' => $redirectUrl, 'domain' => $domain, 'unsubscribeDomain' => $unsubscribeDomain);
            $batchUserId = ManageData(BATCH_USER, $condition, $insertData, $is_insert);

            if ($batchUserId > 0) {

                //add data in uniquekey_link (hoi3) table
                //$unique_key_link_data = array('uniqueKey' => $uniqueKey, 'redirectUrl' => $redirectUrl);
                //$this->mdl_other_db->add_data_in_unique_key_link_table($unique_key_link_data);

                //add data in with_merge_tag or without_merge_tag with use of csv type

                if ($csvType == 1) {

                    
                    /* $url = UNSUBSCRIBE_DOMAIN.'r/'.$uniqueKey;
                    $unsubscribe_url = UNSUBSCRIBE_DOMAIN.$uniqueKey; */
                    $url = $domain.'r/'.$uniqueKey;
                    if(!empty($unsubscribeDomain)){
                        $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
                    }else{
                        $unsubscribe_url = $domain.$uniqueKey;
                    }

                    $condition = array();
                    $is_insert = TRUE;
                    $insertedArr = array('fileNameId' => $fileNameId, 'phone' => $phone, 'url' => $url, 'unsubscribe_url' => $unsubscribe_url);
                    ManageData(WITH_MERGE_TAG,$condition,$insertedArr,$is_insert);
                    

                }else if($csvType == 2){

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

                    $condition = array();
                    $is_insert = TRUE;
                    $insertedArr = array('fileNameId' => $fileNameId, 'phone' => $phone, 'message' => $replacedMsg);
                    ManageData(WITHOUT_MERGE_TAG,$condition,$insertedArr,$is_insert);

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
                $this->addBatchSingleCount($batchId);    
            }
           
        }


        if ($campaignDataCount > 0) {
            $this->mdl_user_tracking->addCampaignCount($campaignId,$campaignDataCount);    
        }

        if ($generalBatchDataCount > 0) {
            $this->mdl_user_tracking->addGeneralBatchCount($generalBatchId,$generalBatchDataCount);    
        }
        
    }

   function addBatchSingleCount($batchId){
        $this->db->where('batchId', $batchId);
        $this->db->set('total', 'total+1', FALSE);
        $this->db->update(BATCH);
   }

}