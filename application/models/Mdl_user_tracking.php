<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_user_tracking extends CI_Model {

	public function __construct() {
        parent::__construct();

        //$this->load->model('mdl_other_db');
    }


    public function getTrackingData($getData,$start,$perPage) {

        $campaignId   = @trim($getData['campaignName']);
        $generalBatchId   = @trim($getData['generalBatchName']);
        $batchId   = @trim($getData['batchName']);
        $phone     = @trim($getData['phone']);
        $uniqueKey = @trim($getData['uniqueKey']);

        $condition = array();

        if (@$campaignId > 0) {
            $condition['`batch_user`.`campaignId`'] = $campaignId;
        }

        if (@$generalBatchId > 0) {
            $condition['`gb`.`generalBatchId`'] = $generalBatchId;
        }

        if (@$batchId > 0) {
            $condition['`batch_user`.`batchId`'] = $batchId;
        }

        if(@$phone > 0){
            $condition['`user`.`phone`'] = $phone;   
        }

        if(@$uniqueKey != ''){
            $condition['`batch_user`.`uniqueKey`'] = $uniqueKey;   
        }

        

        $this->db->select('`campaign`.`campaignName`,`gb`.`generalBatchName`,`batch`.`batchName`,concat(`user`.`firstname`, " " , `user`.`lastname`) as `userName`, `user`.`phone`,`batch_user`.`uniqueKey`,`batch_user`.`isActive`,COUNT(`rlc`.`uniqueKey`) as `total_clicks`');

        $this->db->from('`batch_user`');
        $this->db->join('`redirect_link_clicks` as `rlc`', '`rlc`.`uniqueKey` = `batch_user`.`uniqueKey`');
        $this->db->join('`campaign`', '`campaign`.`campaignId` = `batch_user`.`campaignId`','left');
        $this->db->join('`batch`', '`batch`.`batchId` = `batch_user`.`batchId`');
        $this->db->join('`user`', '`user`.`userId`=`batch_user`.`userId`','left');
        $this->db->join('`general_batch_user` as `gbu`', '`gbu`.`batchUserId` = `batch_user`.`batchUserId`','left');
        $this->db->join('`general_batch` as `gb`', '`gb`.`generalBatchId`=`gbu`.`generalBatchId`','left');
        $this->db->where($condition);
        $this->db->group_by('`batch_user`.`uniqueKey`');
        $getBatchDataCount = $this->db->count_all_results();

        /* $sql = $this->db->last_query();
        echo $sql;
        die; */

        $getBatchData = array();
        if ($getBatchDataCount > 0) {

            $this->db->select('`campaign`.`campaignName`,`gb`.`generalBatchName`,`batch`.`batchName`,concat(`user`.`firstname`, " " , `user`.`lastname`) as `userName`, `user`.`phone`,`batch_user`.`uniqueKey`,`batch_user`.`isActive`,COUNT(`rlc`.`uniqueKey`) as `total_clicks`');

            $this->db->from('`batch_user`');
            $this->db->join('`redirect_link_clicks` as `rlc`', '`rlc`.`uniqueKey` = `batch_user`.`uniqueKey`');
            $this->db->join('`campaign`', '`campaign`.`campaignId` = `batch_user`.`campaignId`','left');
            $this->db->join('`batch`', '`batch`.`batchId` = `batch_user`.`batchId`');
            $this->db->join('`user`', '`user`.`userId`=`batch_user`.`userId`','left');
            $this->db->join('`general_batch_user` as `gbu`', '`gbu`.`batchUserId` = `batch_user`.`batchUserId`','left');
            $this->db->join('`general_batch` as `gb`', '`gb`.`generalBatchId`=`gbu`.`generalBatchId`','left');
            $this->db->where($condition);
            $this->db->group_by('`batch_user`.`uniqueKey`');
            $this->db->order_by('`total_clicks`', 'DESC');
            $this->db->limit($perPage,$start);
            $getBatchData = $this->db->get()->result_array();

        }


        $response = array(
            'batchDataCount' => $getBatchDataCount,
            'batchData' => $getBatchData
        );
        
        return $response;        
    }


    function getUserTrackingData($postData,$userIdsArr,$start,$perPage){
        
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
        $userIdArrLimit = 500;

        $condition = array();

        $condition['phone >'] = 0;

        if (@$country) {
            $condition['country'] = $country;
        }

        if (@$groupName) {
            $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";  
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
                $max = 10;
            }

            $condition['totalClicks >='] = $min;
            $condition['totalClicks <='] = $max;
        }
        
        if (@$keyword) {
            $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
        }

      
        //isset is for some purpose,dont just write @ here
        if (isset($phone) && $phone != '') {

            if ($start == 0) {    //array of userId should remain same on every pagination
                
                //get distict userIdsArr from user_participated_campaign table
                $campaignId = $postData['campaignName'];
                $this->db->select('userId');
                $this->db->distinct();
                $this->db->where('campaignId',$campaignId);
                $userIdArr = $this->db->get(USER_PARTICIPATED_CAMPAIGN)->result_array();

                $userIdsArr = array();
                foreach ($userIdArr as $value) {
                    $userIdsArr[] = $value['userId'];
                }
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

            /*last_query();
            pre($totalCount);*/
        /*
            For user count: Ends
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

            $this->db->order_by("isUserActive","desc");
            $this->db->order_by("participated", "desc");
            $this->db->order_by("r_id", "desc");
            $this->db->order_by("userId", "desc");


            if (@$numberOfSms > 0) {
                $this->db->limit($numberOfSms);
            }else{
                $this->db->limit($perPage,$start);
            }
            
            $userDataList = $this->db->get(USER)->result_array();
            /* $sql = $this->db->last_query();
            echo $sql;
            pre($totalCount);
            pre($userDataList);
            die; */
            return array(
                'totalCount' => $totalCount,
                'userData' => $userDataList,
                'userIdsArr' => $userIdsArr
            );

        /*
            get user data: starts
        */



        /*
            get user data: ends
        */

       /* if (count($userDataList) > 0) {
            
            $this->load->model('mdl_unsubscribed_fileter_data');
            $userDataList = $this->mdl_unsubscribed_fileter_data->getUserFilteredDataByPhone($userDataList);    
            
        }*/

        //return $userDataList;
        
    }



    function getUserTrackingDataCount($postData){
        
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
            $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";  
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
            $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
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
            /* $sql = $this->db->last_query();
            echo $sql;
            die; */

        /*
            For user count: ends
        */

        $response = array(
            'totalCount' => $totalCount
        );

        if ($totalCount > 0) {
            $response['batchId'] = $this->getBatchId($postData);
            $response['generalBatchId'] = $this->getGeneralBatchId($postData);
            $response['userIdsArr'] = json_encode($userIdsArr);

            //get fileNameId from export_file_name table
            $postData['totalCount'] = $totalCount;
            $fileNameId = $this->getExportFileNameData($postData);
            $response['fileNameId'] = $fileNameId;

            //add campaign in batch campaign table.           
            $exportType = "csv";
            $response['batchCampaignId'] = $this->addNewBatchUserCampaign($campaignId,$country,$totalCount,$exportType,$csvType);
        }

        return $response;

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


    function get_user_process_data($postData,$userIdsArr,$start,$perPage){
        
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
        $userIdArrLimit = 500;

        $condition = array();

        $condition['phone >'] = 0;

        if (@$country) {
            $condition['country'] = $country;
        }

        if (@$groupName) {
            $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";  
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
                $max = 10;
            }

            $condition['totalClicks >='] = $min;
            $condition['totalClicks <='] = $max;
        }
        
        if (@$keyword) {
            $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
        }


         /*
            get user data: starts
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

            $this->db->order_by("isUserActive","desc");
            $this->db->order_by("participated", "desc");
            $this->db->order_by("r_id", "desc");
            $this->db->order_by("userId", "desc");

            $this->db->limit($perPage,$start);
            
            $userDataList = $this->db->get(USER)->result_array();

            $response = array(
                'intervalCount' => count($userDataList),
                'userData' => $userDataList
            );
            
            return $response;



        /*
            get user data: ends
        */

        
    }

    function getBatchId($postData = array()){

        $batchName = $postData['batchName'];
        $condition = array('batchName' => $batchName);
        $is_single = TRUE;
        $this->db->limit(1);
        $getBatchData = GetAllRecord(BATCH,$condition,$is_single);

        if (counts($getBatchData) > 0) {
            $batchId = $getBatchData['batchId'];
        }else{  
            //insert batch name
            $condition = array();
            $insertBatchData = array('batchName' => $batchName);
            $is_insert = TRUE;
            $batchId = ManageData(BATCH,$condition,$insertBatchData,$is_insert);
        }

        return $batchId;
    }


    function getGeneralBatchId($postData = array()){

        $generalBatchId = 0;

        if (trim(@$postData['generalBatchName']) != '') {
            $generalBatchName = $postData['generalBatchName'];

            //check if general batch name is already in table or not, if not add
            $condition = array('generalBatchName' => $generalBatchName);
            $is_single = TRUE;
            $this->db->limit(1);
            $generalBatchData = GetAllRecord(GENERAL_BATCH,$condition,$is_single);

            if (counts($generalBatchData) > 0) {
                $generalBatchId = $generalBatchData['generalBatchId'];
            }else{
                $condition = array();
                $is_insert = TRUE;
                $insertedArr = array('generalBatchName' => $generalBatchName);
                $generalBatchId = ManageData(GENERAL_BATCH,$condition,$insertedArr,$is_insert);
            }

        }

        return $generalBatchId;
    }



    function getExportFileNameData($postData){

        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $msg = $postData['msg'];
        $redirectUrl = $postData['redirectUrl'];

        if ($csvType == 1) {
            $fileName = 'userdata_with_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';    
        }else if($csvType == 2){
            $fileName = 'userdata_with_out_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
        }

        if (@$postData['addCountryCodeToBatch'] != '') {
            //get country code
            $countryCode = getCountryCode($postData['country']);
            $fileName    = $countryCode . '_' . $fileName;
        }

        //now check if file name is exist in export_files table, if not then add else get id
        $condition = array('fileName' => $fileName);
        $is_single = TRUE;
        $this->db->limit(1);
        $export_file_data = GetAllRecord(EXPORT_FILES,$condition,$is_single,array(),array(),array(),'fileNameId');

        if (counts($export_file_data) > 0) {
            $fileNameId = $export_file_data['fileNameId'];
        }else{
            //add file name and csv type
            $condition = array();
            $is_insert = TRUE;
            $insertedArr = array('fileName' => $fileName, 'csvType' => $csvType,'total_count' => $totalCount,'msg' => $msg,'redirectUrl' => $redirectUrl);
            $fileNameId = ManageData(EXPORT_FILES,$condition,$insertedArr,$is_insert);
        }

        return $fileNameId;
    }


    function getExcelDataWithNormalTag($userdata){

        $reArrangeArray = array();
        $keyArr = array('firstName', 'lastName', 'emailId', 'address', 'postCode', 'city' ,'country', 'phone' , 'gender' , 'birthdateDay', 'birthdateMonth', 'birthdateYear', 'ip' , 'participated' , 'campaignSource' );

        for ($i=0; $i < count($userdata) ; $i++) { 

            foreach ($keyArr as $value) {
                $reArrangeArray[$i][$value] = $userdata[$i][$value];    
            }

        }

        return $reArrangeArray;
    }




    function getCountryWiseCampaign($country = '') {
        $condition = array();

        if ($country != '') {
            $condition = array('country' => $country);    
        }
        
        $is_single = FALSE;
        $getCampaign = GetAllRecord(CAMPAIGN,$condition,$is_single);
        return $getCampaign;
    }


    


    function addInSmsHistoryTable($postData = array()){
        $campaignId = $postData['campaignName'];
        $condition = array();
        $is_insert = TRUE;
        $insertedArr = array('campaignId' => $campaignId, 'smsHistoryData' => json_encode($postData));
        ManageData(SMS_HISTORY,$condition,$insertedArr,$is_insert);
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

    function addInBatchUserAndOtherTables($userData = array(),$postData = array()){
        
        $batchId = $postData['batchId'];
        $batchCampaignId = $postData['batchCampaignId'];
        $campaignId = $postData['campaignName'];
        $generalBatchId = $postData['generalBatchId'];
        $msg = $postData['msg'];
        $redirectUrl = $postData['redirectUrl'];
        $fileNameId = $postData['fileNameId'];
        $csvType = $postData['csvType'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $generalBatchDataCount = 0;
        $campaignDataCount = 0;
        $batchDataCount = 0;

        foreach ($userData as $value) {

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

                $batchDataCount++;
                $campaignDataCount++;
                

            }

            
        }

        if ($batchDataCount > 0) {
            $this->addBatchCount($batchId,$batchDataCount);    
        }

        if ($campaignDataCount > 0) {
            $this->addCampaignCount($campaignId,$campaignDataCount);    
        }

        if ($generalBatchDataCount > 0) {
            $this->addGeneralBatchCount($generalBatchId,$generalBatchDataCount);    
        }
        
    }



    function addBatchCount($batchId,$user_count){
        $this->db->where('batchId', $batchId);
        $this->db->set('total', 'total+'.$user_count, FALSE);
        $this->db->update(BATCH);
    }
    function addCampaignCount($campaignId,$user_count){
        $this->db->where('campaignId', $campaignId);
        $this->db->set('total', 'total+'.$user_count, FALSE);
        $this->db->update(CAMPAIGN);
    }

    function addGeneralBatchCount($generalBatchId,$user_count){
        $this->db->where('generalBatchId', $generalBatchId);
        $this->db->set('total', 'total+'.$user_count, FALSE);
        $this->db->update(GENERAL_BATCH);
    }


    function editIsNewInCampaignTable($campaignId){
        
        //update isNew = 0 in campaign table
        $condition = array('campaignId' => $campaignId);
        $updateArr = array('isNew' => 0);
        $is_insert = FALSE;
        ManageData(CAMPAIGN,$condition,$updateArr,$is_insert);
    }


    function getGroupClickersGeneralGroupClickers(){
        
        //batch data
        $condition = array();
        $is_single = FALSE;
        $getBatchData = GetAllRecord(BATCH,$condition,$is_single,array(),array(),array(),'batchName');

        //general batch data
        $condition = array();
        $is_single = FALSE;
        $getGeneralBatchData = GetAllRecord(GENERAL_BATCH,$condition,$is_single,array(),array(),array(),'generalBatchName');

        //we need to merge both tables (batch,general_batch) with identity (table name)
        $batchAndGeneralBatchArr = array();

        // add first table data
        foreach ($getBatchData as $value) {
            $batchAndGeneralBatchArr[] = array('batchAndGeneralBatchName' => $value['batchName'],'batchAndGeneralBatchIdentity' => 'batch');
        }

        // add second table data, resume with last element
        $batchAndGeneralBatchArrLength = count($batchAndGeneralBatchArr);

        $i = $batchAndGeneralBatchArrLength;
        foreach ($getGeneralBatchData as $value) {
            $batchAndGeneralBatchArr[$i] = array('batchAndGeneralBatchName' => $value['generalBatchName'],'batchAndGeneralBatchIdentity' => 'general_batch');
            $i++;
        }
        
        return $batchAndGeneralBatchArr;

    }
}