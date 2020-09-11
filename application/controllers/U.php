<?php

/**
 * This conroller for http://hoi3.com/ in live and http://localhost/motherdb_unsubscribe/ in local
 */
class U extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();
        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }

    function unsubscribe(){
        
        $uniqueKey = $this->input->post('uniqueKey');

        if($uniqueKey != "test0"){
            //check if unique key is valid or not
            $condition = array('uniqueKey' => $uniqueKey);
            $is_single = TRUE;
            $this->db->limit(1);
            $getRedirectLinkData = GetAllRecord(BATCH_USER,$condition,$is_single,array(),array(),array());

            if (count($getRedirectLinkData) > 0) {

                if ($getRedirectLinkData['isUnsubscribed'] == 0) {
                    
                    //update isUnsubscribed = 1 in batch_user
                    $condition = array('batchUserId' => $getRedirectLinkData['batchUserId']);
                    $is_insert = FALSE;
                    $updateArr = array('isUnsubscribed' => 1);
                    ManageData(BATCH_USER,$condition,$updateArr,$is_insert);
                }
                
                //add batch unsubscribed count
                $this->addBatchUnsubscribedCount($getRedirectLinkData['batchId']);
                $this->addCampaignUnsubscribedCount($getRedirectLinkData['campaignId']);
                $this->addGeneralBatchUnsubscribedCount($getRedirectLinkData['batchUserId']);
                $this->addBatchCampaignUnsubscribedCount($getRedirectLinkData['batchCampaignId']);


                //get user data
                $condition = array('userId' => $getRedirectLinkData['userId']);            
                $is_single = TRUE;
                $this->db->limit(1);
                $getUserData = GetAllRecord(USER,$condition,$is_single,array(),array(),array(),'firstName,lastName,emailId,phone,country,gender');

                if (count($getUserData) > 0) {
            
                    //check email or phone is already in unsubscribe or not
                    $emailId = $getUserData['emailId'];
                    $phone = $getUserData['phone'];

                    if ($emailId != '' && $phone != '') {
                        $this->db->where('emailId', $emailId);
                        $this->db->or_where('phone',$phone);  
                    }else if($emailId != ''){
                        $this->db->where('emailId', $emailId);
                    }else if($phone != ''){
                        $this->db->where('phone',$phone); 
                    }
                    
                    $unsubscriberCount = $this->db->count_all_results(UNSUBSCRIBER);

                    if ($unsubscriberCount == 0) {
                        
                        $fieldArr = array('firstName','lastName','emailId','phone','country','gender');

                        $dataArr = array();
                        foreach ($fieldArr as $value) {
                            $dataArr[$value] = $getUserData[$value];
                        }

                        $lastInsertedId = ManageData(UNSUBSCRIBER, array(), $dataArr, TRUE);    



                        //delete from user table and manage masters tables.

                        if ($getUserData['emailId'] != '') {

                            $condition     = array('emailId' => $getUserData['emailId']);
                            $userEmailData = GetAllRecord(USER, $condition, $is_single = false);

                            foreach ($userEmailData as $key => $value) {

                                $con_country   = $value['country'];
                                $con_keyword   = $value['keyword'];
                                $con_groupName = $value['groupName'];
                            
                                if (@$value['gender'] == 'male') {
                                    
                                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET male = male - 1 WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male - 1 WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male - 1 WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);


                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }

                                if (@$value['gender'] == 'female') {
                                
                                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET female = female - 1 WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female - 1 WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female - 1 WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);


                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }

                                if(@$value['gender'] != 'male' && @$value['gender'] != 'female'){

                                    $sql_country = "UPDATE ".COUNTRY_MASTER." SET other = other - 1  WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET other = other - 1  WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE ".GROUP_MASTER." SET other = other - 1  WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);

                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }
                            }

                            $this->db->where('emailId', $getUserData['emailId']);
                            $this->db->delete(USER);

                        }

                        if ($getUserData['phone'] != '') {

                            $condition     = array('phone' => $getUserData['phone']);
                            $userPhoneData = GetAllRecord(USER, $condition, $is_single = false);

                            foreach ($userPhoneData as $key => $value) {

                                $con_country   = $value['country'];
                                $con_keyword   = $value['keyword'];
                                $con_groupName = $value['groupName'];
                            
                                if (@$value['gender'] == 'male') {
                                    
                                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET male = male - 1 WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male - 1 WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male - 1 WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);


                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }

                                if (@$value['gender'] == 'female') {
                                
                                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET female = female - 1 WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female - 1 WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female - 1 WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);


                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }

                                if(@$value['gender'] != 'male' && @$value['gender'] != 'female'){

                                    $sql_country = "UPDATE ".COUNTRY_MASTER." SET other = other - 1  WHERE country = '$con_country'";
                                    $this->db->query($sql_country);

                                    $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET other = other - 1  WHERE keyword = '$con_keyword'";
                                    $this->db->query($sql_keyword);

                                    $sql_groupName = "UPDATE ".GROUP_MASTER." SET other = other - 1  WHERE groupName = '$con_groupName'";
                                    $this->db->query($sql_groupName);

                                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                    $this->db->query($sql_keywordCountryCount);

                                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                                    $this->db->query($sql_keywordCountryCount_zero);

                                }
                            }

                            $this->db->where('phone', $getUserData['phone']);
                            $this->db->delete(USER);
                        }
                        //delete from batch_user
                        /*$this->db->where('uniqueKey', $uniqueKey);
                        $this->db->delete(BATCH_USER);*/    
                    }
                }
            }
        }
    }



    function addBatchUnsubscribedCount($batchId = 0){

        $this->db->where('batchId', $batchId);
        $this->db->set('unsubscribed', 'unsubscribed+1', FALSE);
        $this->db->update(BATCH);
    }

    function addBatchCampaignUnsubscribedCount($batchCampaignId = 0){
        if($batchCampaignId !="" || $batchCampaignId != 0){
            $this->db->where('batchCampaignId', $batchCampaignId);
            $this->db->set('unsubscribed', 'unsubscribed+1', FALSE);
            $this->db->update(BATCH_CAMPAIGN);
        }
    }


    function addCampaignUnsubscribedCount($campaignId = 0){

        $this->db->where('campaignId', $campaignId);
        $this->db->set('unsubscribed', 'unsubscribed+1', FALSE);
        $this->db->update(CAMPAIGN);
    }
    

    function addGeneralBatchUnsubscribedCount($batchUserId = 0){

        //get general batch id
        $condition = array('batchUserId' => $batchUserId);
        $is_single = TRUE;
        $this->db->limit(1);
        $getGeneralBatchData = GetAllRecord(GENERAL_BATCH_USER,$condition,$is_single);

        if (count($getGeneralBatchData) > 0) {

            $generalBatchId = $getGeneralBatchData['generalBatchId'];

            $this->db->where('generalBatchId', $generalBatchId);
            $this->db->set('unsubscribed', 'unsubscribed+1', FALSE);
            $this->db->update(GENERAL_BATCH);    
        }
    }   


    
}