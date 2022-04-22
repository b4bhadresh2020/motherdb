<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Live_delivery_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $this->load->model('mdl_csv');
    }

    public function rest()
    {   
        
        $response = array();

        if (isset($_GET['apikey'])) {

            if ($_GET['apikey'] != '') {

                //check api key is valid or not
                $condition            = array('apikey' => $_GET['apikey']);
                $getLiveDeliveryCount = GetAllRecordCount(LIVE_DELIVERY, $condition);
                
                if ($getLiveDeliveryCount > 0) {

                    $condition           = array('apikey' => $_GET['apikey']);
                    $is_single           = true;
                    $getLiveDeliveryData = GetAllRecord(LIVE_DELIVERY, $condition, $is_single);

                    //check link is active or not
                    if ($getLiveDeliveryData['isInActive'] == 0) {
                        
                        if($getLiveDeliveryData['dataSourceType'] == 1 || $getLiveDeliveryData['dataSourceType'] == 2) {
                            $identifier = $getLiveDeliveryData['identifier'];
                            if (isset($_GET[$identifier])) {
                                if($_GET[$identifier] != '' ) {
                                    if ($identifier == 'emailId') {
                                        $emailId = $_GET['emailId']; 
                                        $notToCheckFurther = 0;
                                        $isEmailChecked = 0;
                                        

                                        if(@$_GET['emailId'] != '' && isValidEmail($emailId) == 0) {
                                            $notToCheckFurther = 1;
                                        }
                                        if($notToCheckFurther == 0) {
                                            // check live email check flag is on
                                            if($getLiveDeliveryData['checkEmail'] == 1) {
                                                $checkEmailResponse = isValidDeliverableEmail($emailId);
                                                if($checkEmailResponse == 0){
                                                    $notToCheckFurther = 2;
                                                }

                                                // check successfully get valid response from checker api
                                                if($checkEmailResponse != -1){
                                                    $isEmailChecked = 1;
                                                }
                                            }
                                            
                                            // check email in inboxgame facebook lead for rejection
                                            
                                            if($notToCheckFurther == 0) {
                                                $condition = array('emailId' => $emailId);
                                                $getUserDataCount = GetAllRecordCount(USER, $condition);
                                                $getInboxUserDataCount = GetAllRecordCount(INBOXGAME_FACEBOOKLEAD_DATA, $condition);

                                                //die($getUserDataCount ." ". $getInboxUserDataCount);

                                                if ($getUserDataCount == 0 && $getInboxUserDataCount == 0) {
                                                    //data save to live_delivery_data table
                                                    $isFail          = 0;
                                                    $sucFailMsgIndex = 0; //success
                                                    // $response['success'] = 'Data Added Successfully';
                                                } else if ($getInboxUserDataCount != 0){
                                                    //data save to live_delivery_data table
                                                    $isFail            = 1;
                                                    $sucFailMsgIndex   = 22; //duplicate
                                                    // $response['error'] = 'Duplicate record found.';
                                                } else if ($getUserDataCount != 0){
                                                    //data save to live_delivery_data table
                                                    $isFail            = 1;
                                                    $sucFailMsgIndex   = 1; //duplicate
                                                    // $response['error'] = 'Duplicate record found.';
                                                } 
                                            }                                           
                                            
                                        } 
                                        
                                        if($notToCheckFurther == 1) {
                                            //data save to live_delivery_data table
                                            $isFail            = 1;
                                            $sucFailMsgIndex   = 9; //Invalid email format
                                            // $response['error'] = 'Invalid email format';

                                        } else if($notToCheckFurther == 2) {
                                            // data save to live_delivery_data table
                                            $isFail            = 1;
                                            $sucFailMsgIndex   = 9; //Invalid email
                                            // $response['error'] = 'Invalid email';
                                        }
                                    }
                                } else {
                                    if ($identifier == 'emailId') {
                                        $sucFailMsgIndex = 7; //emailId is blank
                                        $isFail          = 1;
                                        $response['error'] = 'emailId is required';
                                    }
                                }
                            } else {
                                if ($identifier == 'emailId') {
                                    $sucFailMsgIndex = 5; //emailId is required
                                    $isFail            = 1;
                                    $response['error'] = 'emailId is required';
                                }
                            }
                        } else {
                            // LIVE DELIVERY
                            //get identifier and check if it is blank or not
                            $identifier = $getLiveDeliveryData['identifier'];

                            if (isset($_GET[$identifier])) {

                                if ($_GET[$identifier] != '') {

                                    $notToCheckFuther = 0;
                                    $isEmailChecked = 0;  
                                    
                                    // get current ip
                                    $ipValue = getIPAddress();
                                        
                                    // get all blacklist ip
                                    $condition           = array();
                                    $is_single           = false;
                                    $getIPBlacklistData = GetAllRecord(IP_BLACKLIST, $condition, $is_single, array(), array(), array(), 'ip');

                                    $allBlackListIP = array_column($getIPBlacklistData, 'ip');
                                    // check whether IP is in blacklist
                                    if(!empty($ipValue) && in_array($ipValue, $allBlackListIP)) {
                                        $notToCheckFuther = 11;
                                    } 
                                   
                                    if (@$_GET['emailId'] != '' && isValidEmail($_GET['emailId']) == 0) {
                                        $notToCheckFuther = 1;
                                    }

                                    if($notToCheckFuther == 0){
                                        // check email id host in blocklist array.
                                        $emailAddressChunk = explode("@",$_GET['emailId']);
                                        $country = $getLiveDeliveryData['country'];

                                        // dublicate old (birth year older than 1957)
                                        $birthdateYear = $_GET['birthdateYear'];
                                        
                                        if($emailAddressChunk[1] == TELIA_DOMAIN){
                                            $notToCheckFuther = 4; // Telia MX Block	
                                        } else if($emailAddressChunk[1] == LUUKKU_DOMAIN) {
                                            $notToCheckFuther = 5; // Luukku MX Block	
                                        } else if(startsWith($emailAddressChunk[1],PP_DOMAIN_START) && endsWith($emailAddressChunk[1],PP_DOMAIN_END)) {
                                            $notToCheckFuther = 6; // PP MX Block	
                                        } else if (strpos($emailAddressChunk[1], YAHOO_DOMAIN) !== false && $country == 'SE') {
                                            $notToCheckFuther = 7; // Yahoo MX Block (SE only)
                                        } else if (strpos($emailAddressChunk[1], ICLOUD_DOMAIN) !== false) {
                                            $notToCheckFuther = 8; // Icloud MX Block
                                        } else if (strpos($emailAddressChunk[1], GMX_DOMAIN) !== false) {
                                            $notToCheckFuther = 9; // GMX MX Block
                                        } else if($birthdateYear < 1957) {
                                            $notToCheckFuther = 10; // dublicate old                                           
                                        } else if($emailAddressChunk[1] == PROTONMAIL_DOMAIN) {
                                            $notToCheckFuther = 12; // Protonmail MX Block	
                                        }
                                        
                                        // check live email check flag is on
                                        if ($notToCheckFuther == 0 && $getLiveDeliveryData['checkEmail'] == 1) {
                                            $checkEmailResponse = isValidDeliverableEmail($_GET['emailId']);
                                            if($checkEmailResponse == 0){
                                                $notToCheckFuther = 3;
                                            }

                                            // check successfully get valid response from checker api
                                            if($checkEmailResponse != -1){
                                                $isEmailChecked = 1;
                                            }
                                        }
                                    }
                                    // check phone number if check phone status enable from live delivery
                                    if ($getLiveDeliveryData['checkPhone'] == 1) {
                                        if (@$_GET['phone'] != '' && !is_numeric($_GET['phone'])) {
                                            $notToCheckFuther = 2;
                                        }
                                    }                              


                                    if ($notToCheckFuther == 0) {

                                        //check if phone or email is blacklisted or not
                                        if (@$_GET['emailId'] != '' && @$_GET['phone'] != '') {
                                            $this->db->where('emailId', @$_GET['emailId']);
                                            $this->db->or_where('phone', @$_GET['phone']);
                                        } else if (@$_GET['emailId'] != '') {
                                            $this->db->where('emailId', @$_GET['emailId']);
                                        } else if (@$_GET['phone'] != '') {
                                            $this->db->where('phone', @$_GET['phone']);
                                        }

                                        $blackListCount = $this->db->count_all_results(BLACKLIST);

                                        if ($blackListCount == 0) {

                                            $this->db->where('email',@$_GET['emailId']);
                                            $this->db->where('status', 1);
                                            $alreadyUnsubscribeCount = $this->db->count_all_results(PROVIDER_UNSUBSCRIBER);

                                            if($alreadyUnsubscribeCount == 0){
                                                // check email in inboxgame facebook lead for rejection
                                                $condition        = array($identifier => $_GET[$identifier]);
                                                $getInboxUserDataCount = GetAllRecordCount(INBOXGAME_FACEBOOKLEAD_DATA, $condition);

                                                if($getInboxUserDataCount == 0){

                                                    // check email exist from integromat data
                                                    $getIntegromatUserDataCount = GetAllRecordCount(INTEGROMAT_LEAD_DATA, $condition);
                                                    if($getIntegromatUserDataCount == 0){
                                                        //check if data is in user table or not                                                    
                                                        $getUserDataCount = GetAllRecordCount(USER, $condition);
                                                        
                                                        if ($getUserDataCount == 0) {

                                                            $genArr = array('male', 'female', 'other');

                                                            $isGenderNotValid = 0;
                                                            if (isset($_GET['gender']) && (!in_array(strtolower($_GET['gender']), $genArr))) {
                                                                $isGenderNotValid = 1;
                                                            }

                                                            if ($isGenderNotValid == 0) {

                                                                //add in user database
                                                                $condition          = array();
                                                                $is_insert          = true;
                                                                $dataArr            = array();
                                                                $allDataValInString = '';

                                                                foreach ($_GET as $key => $value) {

                                                                    if ($key == 'birthdateYear') {
                                                                        if ($value != '') {
                                                                            $dataArr['age'] = date('Y') - date('Y', strtotime(date($value . '-m-d')));
                                                                        }
                                                                    }
                                                                    
                                                                    // store tag value in custom fields
                                                                    if ($key == 'tag') {
                                                                        if ($value != '' || $value != 'tag') {
                                                                            $dataArr['otherLable'] = json_encode(["Tag"]);
                                                                            $dataArr['other'] = json_encode([$value]);
                                                                        }
                                                                    }else{
                                                                        $dataArr[$key] = $value;
                                                                    }

                                                                    //make all value in string for global search purpose
                                                                    if ($allDataValInString == '') {
                                                                        $allDataValInString = $value;
                                                                    } else {
                                                                        $allDataValInString .= '+' . $value;
                                                                    }

                                                                }

                                                                $dataArr['allDataInString'] = $allDataValInString;
                                                                $dataArr['country']         = $getLiveDeliveryData['country'];
                                                                $dataArr['groupName']       = $getLiveDeliveryData['groupName'];
                                                                $dataArr['keyword']         = $getLiveDeliveryData['keyword'];
                                                                $dataArr['campaignSource']  = $dataArr['optinurl'];
                                                                $dataArr['participated']    = $dataArr['optindate'];

                                                                //unset below element we dont want to in $dataArr
                                                                unset($dataArr['apikey']);
                                                                unset($dataArr['financingNeed']);
                                                                unset($dataArr['timestamp']);
                                                                unset($dataArr['tag']);

                                                                $dataArr['r_id'] = rand(1,10000); //make random number between 1 to 10000

                                                                $insertedId = ManageData(USER, $condition, $dataArr, $is_insert);

                                                                if ($insertedId > 0) {

                                                                    //insert group name and keyword name
                                                                    $this->mdl_csv->insertGroupName($getLiveDeliveryData['groupName']);
                                                                    $this->mdl_csv->insertKeyword($getLiveDeliveryData['keyword']);

                                                                    // new edited start

                                                                    $con_country   = $getLiveDeliveryData['country'];
                                                                    $con_keyword   = $getLiveDeliveryData['keyword'];
                                                                    $con_groupName = $getLiveDeliveryData['groupName'];

                                                                    // manage country wise keyword
                                                                    $keywordCountryCount = GetAllRecordCount(KEYWORD_COUNTRY_COUNT, $condition = array('keyword' => $con_keyword, 'country' => $con_country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'keywordCountryId');

                                                                    if ($keywordCountryCount == 0) {

                                                                        $sql_keywordCountryCount = "INSERT INTO " . KEYWORD_COUNTRY_COUNT . " (keyword, country, total)  VALUES ('$con_keyword', '$con_country', 1)";

                                                                        $this->db->query($sql_keywordCountryCount);
                                                                    }

                                                                    if ($keywordCountryCount > 0) {

                                                                        $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total + 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                                                                        $this->db->query($sql_keywordCountryCount);
                                                                    }

                                                                    // manage country wise group
                                                                    $groupCountryCount = GetAllRecordCount(GROUP_COUNTRY_COUNT, $condition = array('groupName' => $con_groupName, 'country' => $con_country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupCountryId');

                                                                    if ($groupCountryCount == 0) {

                                                                        $sql_groupCountryCount = "INSERT INTO " . GROUP_COUNTRY_COUNT . " (groupName, country)  VALUES ('$con_groupName', '$con_country')";

                                                                        $this->db->query($sql_groupCountryCount);
                                                                    }

                                                                    if (@$_GET['gender'] == 'male') {

                                                                        $sql_country = "UPDATE " . COUNTRY_MASTER . " SET male = male + 1  WHERE country = '$con_country'";
                                                                        $this->db->query($sql_country);

                                                                        $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male + 1  WHERE keyword = '$con_keyword'";
                                                                        $this->db->query($sql_keyword);

                                                                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male + 1  WHERE groupName = '$con_groupName'";
                                                                        $this->db->query($sql_groupName);

                                                                    }

                                                                    if (@$_GET['gender'] == 'female') {

                                                                        $sql_country = "UPDATE " . COUNTRY_MASTER . " SET female = female + 1  WHERE country = '$con_country'";
                                                                        $this->db->query($sql_country);

                                                                        $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female + 1  WHERE keyword = '$con_keyword'";
                                                                        $this->db->query($sql_keyword);

                                                                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female + 1  WHERE groupName = '$con_groupName'";
                                                                        $this->db->query($sql_groupName);
                                                                    }

                                                                    if (@$_GET['gender'] != 'male' && @$_GET['gender'] != 'female') {

                                                                        $sql_country = "UPDATE " . COUNTRY_MASTER . " SET other = other + 1  WHERE country = '$con_country'";
                                                                        $this->db->query($sql_country);

                                                                        $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other + 1  WHERE keyword = '$con_keyword'";
                                                                        $this->db->query($sql_keyword);

                                                                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other + 1  WHERE groupName = '$con_groupName'";
                                                                        $this->db->query($sql_groupName);

                                                                    }

                                                                    // new edited end

                                                                    //Now get user data and give it to response with success
                                                                    $condition = array('userId' => $insertedId);
                                                                    $is_single = true;
                                                                    $this->db->limit(1);
                                                                    $userData = GetAllRecord(USER, $condition, $is_single, array(), array(), array(), 'firstName,lastName,emailId,phone');

                                                                    $response['success'] = 'Data Added Successfully';
                                                                    $response['data']    = $userData;
                                                                    //$response['egoi'] = $egoiResponse;

                                                                    //data save to live_delivery_data table
                                                                    $isFail          = 0;
                                                                    $sucFailMsgIndex = 0; //success

                                                                } else {
                                                                    //data save to live_delivery_data table
                                                                    $isFail            = 1;
                                                                    $sucFailMsgIndex   = 3; //server issue
                                                                    $response['error'] = 'Something went wrong. Please try again later.';
                                                                }

                                                            } else {

                                                                //data save to live_delivery_data table
                                                                $isFail            = 1;
                                                                $sucFailMsgIndex   = 11; //invalid gender
                                                                $response['error'] = 'Gender should be "male" or "female" or other';

                                                            }

                                                        } else {

                                                            //Update user data in user table . 

                                                            $genArr = array('male', 'female', 'other');
                                                            $isGenderNotValid = 0;

                                                            if (isset($_GET['gender']) && (!in_array(strtolower($_GET['gender']), $genArr))) {
                                                                $isGenderNotValid = 1;
                                                            }

                                                            if ($isGenderNotValid == 0) {

                                                                //add in user database
                                                                $condition        = array($identifier => $_GET[$identifier]);
                                                                $is_insert          = false;
                                                                $dataArr            = array();
                                                                $allDataValInString = '';

                                                                foreach ($_GET as $key => $value) {

                                                                    if ($key == 'birthdateYear') {
                                                                        if ($value != '') {
                                                                            $dataArr['age'] = date('Y') - date('Y', strtotime(date($value . '-m-d')));
                                                                        }
                                                                    }

                                                                    $dataArr[$key] = $value;

                                                                    //make all value in string for global search purpose
                                                                    if ($allDataValInString == '') {
                                                                        $allDataValInString = $value;
                                                                    } else {
                                                                        $allDataValInString .= '+' . $value;
                                                                    }

                                                                }

                                                                $dataArr['allDataInString'] = $allDataValInString;
                                                                $dataArr['country']         = $getLiveDeliveryData['country'];
                                                                $dataArr['campaignSource']  = $dataArr['optinurl'];
                                                                $dataArr['participated']    = $dataArr['optindate'];
                                                                
                                                                //$dataArr['groupName']       = $getLiveDeliveryData['groupName'];
                                                                //$dataArr['keyword']         = $getLiveDeliveryData['keyword'];
                                                                
                                                                //unset below element we dont want to in $dataArr
                                                                unset($dataArr['apikey']);
                                                                unset($dataArr['financingNeed']);
                                                                unset($dataArr['timestamp']);
                                                                unset($dataArr['tag']);

                                                                ManageData(USER, $condition, $dataArr, $is_insert);

                                                                // Get userdata 
                                                                $this->db->limit(1);
                                                                $is_single   = true;
                                                                $getUserData = GetAllRecord(USER, $condition, $is_single); 
                                                            
                                                                $this->updateUserGroupName($getUserData,$getLiveDeliveryData);
                                                                $this->updateUserKeyword($getUserData,$getLiveDeliveryData);

                                                            }    

                                                            //data save to live_delivery_data table
                                                            $isFail            = 1;
                                                            $sucFailMsgIndex   = 1; //duplicate
                                                            $response['error'] = 'Duplicate record found.';

                                                            /* $duplicateCondition    = array($identifier => $_GET[$identifier]);
                                                            $userDuplicatedData    = GetAllRecord(USER, $duplicateCondition, $is_single, array(), array(), array(), 'firstName,lastName,emailId,phone');
                                                            $response['success']   = 'Data Added Successfully';
                                                            $response['data']      = $userDuplicatedData; */

                                                            //do things of add group feature ("if user in these groups" then "add the user in this group")
                                                            //get user data

                                                            if ($getLiveDeliveryData['ifUserInThisGroups'] != '' && $getLiveDeliveryData['addTheUserInThisGroup'] != '') {

                                                                $condition = array($identifier => $_GET[$identifier]);
                                                                $this->db->limit(1);
                                                                $is_single   = true;
                                                                $getUserData = GetAllRecord(USER, $condition, $is_single);

                                                                $this->ifUserInThisGroupsThenAddUserInThisGroup($getUserData, $getLiveDeliveryData);

                                                            }

                                                        }
                                                    }else{
                                                        $isFail            = 1;
                                                        $sucFailMsgIndex   = 23; //rejected by integromat lead
                                                        $response['error'] = 'Duplicate record found.';
                                                    }                                                    
                                                }else{
                                                    $isFail            = 1;
                                                    $sucFailMsgIndex   = 22; //rejected by inboxgame lead
                                                    $response['error'] = 'Duplicate record found.';
                                                }
                                                
                                            } else {
                                                //data save to live_delivery_data table
                                                $isFail            = 1;
                                                $sucFailMsgIndex   = 15; //user already unsubscribed before
                                                $response['error'] = 'Duplicate record found.';
                                            }

                                        } else {
                                            //data save to live_delivery_data table
                                            $isFail            = 1;
                                            $sucFailMsgIndex   = 2; //black listed
                                            $response['error'] = 'User is blacklisted';
                                        }
                                    } else if ($notToCheckFuther == 1) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 9; //Invalid email format
                                        $response['error'] = 'Invalid email format';
                                    } else if ($notToCheckFuther == 2) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 10; //Invalid Phone
                                        $response['error'] = 'Invalid Phone';
                                    } else if ($notToCheckFuther == 3) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 9; //Invalid email
                                        $response['error'] = 'Invalid email';
                                    } else if ($notToCheckFuther == 4) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 12; //Telia MX Block
                                        $response['error'] = 'Duplicate record found.';
                                    } else if ($notToCheckFuther == 5) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 13; //Luukku MX Block
                                        $response['error'] = 'Duplicate record found.';
                                    } else if ($notToCheckFuther == 6) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 14; //PP MX Block
                                        $response['error'] = 'Duplicate record found.';
                                    } else if ($notToCheckFuther == 7) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 16; //Yahoo MX Block (SE only)
                                        $response['error'] = 'Duplicate record found.';
                                    }  else if ($notToCheckFuther == 8) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 17; //Icloud MX Block
                                        $response['error'] = 'Duplicate record found.';
                                    } else if ($notToCheckFuther == 9) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 18; //GMX MX Block
                                        $response['error'] = 'Duplicate record found.';
                                    } else if ($notToCheckFuther == 10) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 19; //dublicate old
                                        $response['error'] = 'Duplicate Old.';
                                    } else if ($notToCheckFuther == 11) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 20; // Blacklisted IP
                                        $response['error'] = 'Blacklisted IP.';
                                    } else if ($notToCheckFuther == 12) {
                                        //data save to live_delivery_data table
                                        $isFail            = 1;
                                        $sucFailMsgIndex   = 21; // Protonmail MX Block
                                        $response['error'] = 'Protonmail MX Block.';
                                    }
                                    
                                } else {

                                    //identifier will be emailId or phone

                                    if ($identifier == 'emailId') {
                                        $sucFailMsgIndex = 7; //emailId is blank
                                    } else {
                                        $sucFailMsgIndex = 8; //phone is blank
                                    }
                                    //data save to live_delivery_data table
                                    $isFail            = 1;
                                    $response['error'] = $identifier . ' is blank';
                                }
                            } else {

                                //identifier will be emailId or phone

                                if ($identifier == 'emailId') {
                                    $sucFailMsgIndex = 5; //emailId is required
                                } else {
                                    $sucFailMsgIndex = 6; //phone is required
                                }
                                //data save to live_delivery_data table
                                $isFail            = 1;
                                $response['error'] = $identifier . ' is required';
                            }
                        }

                    } else {
                        //data save to live_delivery_data table
                        $isEmailChecked    = 0;
                        $isFail            = 1;
                        $sucFailMsgIndex   = 4; //api key is not active
                        $response['error'] = 'Api key is not active. Please contact to admin';
                    }

                    if($getLiveDeliveryData['dataSourceType'] == 1 || $getLiveDeliveryData['dataSourceType'] == 2) {
                        $response['inbox_fblead'] = $this->addToInboxgameFacebookleadData($_GET, $getLiveDeliveryData, $sucFailMsgIndex, $isFail, $isEmailChecked);
                    } else {
                        //add data to live delivery table
                        $this->addToLiveDeliveryDataTable($_GET, $getLiveDeliveryData, $sucFailMsgIndex, $isFail, $isEmailChecked);
                    }
                } else {
                    $response['error'] = 'Invalid apikey';
                    $sucFailMsgIndex   = 3;
                    $this->addToLiveDeliveryUndefinedDataTable($_GET, $sucFailMsgIndex);
                }

            } else {
                $response['error'] = 'Api key is blank';
                $sucFailMsgIndex   = 2;
                $this->addToLiveDeliveryUndefinedDataTable($_GET, $sucFailMsgIndex);
            }

        } else {
            $response['error'] = 'Undefine Api key';
            $sucFailMsgIndex   = 1;
            $this->addToLiveDeliveryUndefinedDataTable($_GET, $sucFailMsgIndex);
        }
        header("Content-type:application/json");
        echo json_encode($response);
    }

    public function addToLiveDeliveryDataTable($getData, $getLiveDeliveryData, $sucFailMsgIndex, $isFail, $isEmailChecked)
    {    
        //add in live delivery data database
        $condition = array();
        $is_insert = true;
        $dataArr   = array();

        foreach ($getData as $key => $value) {

            if ($key == 'birthdateYear') {
                if ($value != '') {
                    $dataArr['age'] = date('Y') - date('Y', strtotime(date($value . '-m-d')));
                }
            }

            $dataArr[$key] = $value;
        }

        $dataArr['country']         = $getLiveDeliveryData['country'];
        $dataArr['groupName']       = $getLiveDeliveryData['groupName'];
        $dataArr['keyword']         = $getLiveDeliveryData['keyword'];
        $dataArr['source']          = $getLiveDeliveryData['dataSource'];
        $dataArr['isFail']          = $isFail;
        $dataArr['sucFailMsgIndex'] = $sucFailMsgIndex;
        $dataArr['isEmailChecked']  = $isEmailChecked;
        $dataArr['timestamp'] = time();

        $liveDeliveryDataId = ManageData(LIVE_DELIVERY_DATA, $condition, $dataArr, $is_insert);
        
        // GET LAST DELIVERY DATA
        $liveDeliveryCondition   = array('liveDeliveryDataId' => $liveDeliveryDataId);
        $is_single               = true;
        $lastDeliveryData        = GetAllRecord(LIVE_DELIVERY_DATA, $liveDeliveryCondition, $is_single); 

        //we will not send from local
        if ($_SERVER['HTTP_HOST'] != 'localhost') {

            $mailProviders = json_decode($getLiveDeliveryData['mailProvider']);
           

            if(isset($getLiveDeliveryData['delay']) && !empty($getLiveDeliveryData['delay'])){
                $delays = json_decode($getLiveDeliveryData['delay'],true);
            }else{
                $delays = array();
            } 

            if(isset($getLiveDeliveryData['isDuplicate']) && !empty($getLiveDeliveryData['isDuplicate'])){
                $duplicates = json_decode($getLiveDeliveryData['isDuplicate'],true);
            }else{
                $duplicates = array();
            } 
            
            foreach($mailProviders as $mailProvider){
                if($mailProvider != 'none') {
                    //send user data to egoi
                    if ($mailProvider == 'egoi') {

                        // send data to egoi if user is successfully added or duplicate
                        if ($sucFailMsgIndex == 0 || $sucFailMsgIndex == 1) {

                            $country             = $getLiveDeliveryData['country'];
                            $validCountryForEgoi = countryThasListedInEgoi();

                            if (in_array(strtoupper($country), $validCountryForEgoi)) {

                                if (@$getData['birthdateDay'] != '' && @$getData['birthdateMonth'] != '' && @$getData['birthdateYear'] != '') {

                                    $birthDate            = $getData['birthdateYear'] . '-' . $getData['birthdateMonth'] . '-' . $getData['birthdateDay'];
                                    $getData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                }

                                $this->load->model('mdl_egoi');
                                $response = $this->mdl_egoi->sendDataToEgoi($getData, $country);
                            } else {
                                $response = array("result" => "error", "error" => array("msg" => "Country is not defined in E-goi"));
                            }

                            //update to live delivery data response
                            $condition = array('liveDeliveryDataId' => $liveDeliveryDataId);
                            $is_insert = false;
                            $updateArr = array('eGoiResponse' => json_encode($response));

                            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);

                        }

                    }else if ($mailProvider != 'egoi') {                
                        
                        // fetch mail provider data from providers table
                        $providerCondition   = array('id' => $mailProvider);
                        $is_single           = true;
                        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                        // check condition for send data to provider or not. 
                        /* Condition 
                        1. if duplicate true then record must be duplicate (fail message index must be 1). 
                        2. if duplicate false (by default) then fail message index must be 0 or 1.  
                        */
                        $sendToMailProvider = 0;
                        $isDuplicate = array_key_exists($mailProvider,$duplicates)?1:0;

                        if($isDuplicate == 1 && $sucFailMsgIndex == 1){
                            $sendToMailProvider = 1;
                        }else if($isDuplicate == 0 && ($sucFailMsgIndex == 0 || $sucFailMsgIndex == 1)){
                            $sendToMailProvider = 1;
                        }
                        
                        // send data to aweber if user is successfully added or duplicate
                        if ($sendToMailProvider == 1) {

                            $country             = $getLiveDeliveryData['country'];
                            $validCountryForAweber = countryThasListedInAweber();
                        
                            if($providerData['provider'] == AWEBER){
                                $lastDeliveryData['birthDate'] = "";
                                if (in_array(strtoupper($country), $validCountryForAweber)) {
                                    // if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {

                                    //     $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    //     $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                    // } 
                                    // $res = $this->load->model('mdl_aweber');
                                    
                                    // LOGIC FOR SEND DATA TO AWEBER OR QUEUE
                                    $delayDay = 0;
                                    if(isset($delays[$mailProvider])){
                                        $delayDay = $delays[$mailProvider];
                                    }
                                    $provider = AWEBER;
                                    //$res = $this->mdl_aweber->AddEmailToAweberSubscriberList($lastDeliveryData,$country,$mailProvider);
                                    // pre($res);
                                    // die;
                                    // if($delayDay == 0){
                                    //     // NO DELAY INSTANT SEND DATA TO AWEBER
                                    //     $response = $this->mdl_aweber->AddEmailToAweberSubscriberList($lastDeliveryData,$country,$mailProvider);
                                    //     // ADD RECORD IN HISTORY
                                    //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    // }else{
                                        // ADD DATA IN QUEUE FOR DELAY SENDING
                                        addToAweberSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                        // ADD RECORD IN HISTORY
                                        $response = null;
                                        addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    //}                                                               
                                } else {
                                    $response = array("result" => "error", "error" => array("msg" => "Country is not defined in Aweber"));
                                }
                            }else if($providerData['provider'] == CONSTANTCONTACT){
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = CONSTANTCONTACT;
                                // if($delayDay == 0){
                                //     $this->load->model('mdl_constantcontact');
                                //     $response = $this->mdl_constantcontact->AddEmailToContactSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    addToContactSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //}
                            }else if($providerData['provider'] == TRANSMITVIA){
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = TRANSMITVIA;
                                // if($delayDay == 0){
                                //     $this->load->model('mdl_transmitvia');                            
                                //     $response = $this->mdl_transmitvia->AddEmailToTransmitSubscriberList($lastDeliveryData,$providerData['code']);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    addToTransmitviaSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //}
                            }else if($providerData['provider'] == ONGAGE){
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = ONGAGE;
                                // if($delayDay == 0){
                                //     $this->load->model('mdl_ongage');                            
                                //     $response = $this->mdl_ongage->AddEmailToOngageSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    addToOngageSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //}
                            }else if($providerData['provider'] == SENDGRID){
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 
                                $this->load->model('mdl_sendgrid');
                                // LOGIC FOR SEND DATA TO SENDGRID OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = SENDGRID;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO SENDGRID
                                //     $response = $this->mdl_sendgrid->AddEmailToSendgridSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToSendgridSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            }else if($providerData['provider'] == SENDINBLUE){
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 
                                $this->load->model('mdl_sendinblue');
                                // LOGIC FOR SEND DATA TO SENDGRID OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = SENDINBLUE;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO SENDGRID
                                //     $response = $this->mdl_sendinblue->AddEmailToSendInBlueSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToSendinblueSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == SENDPULSE) {
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 
                                $this->load->model('mdl_sendpulse');
                                // LOGIC FOR SEND DATA TO SENDPULSE OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = SENDPULSE;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO SENDPULSE
                                //     $response = $this->mdl_sendpulse->AddEmailToSendpulseSubscriberList($lastDeliveryData,$mailProvider);
                                
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToSendpulseSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == MAILERLITE) {
                                $this->load->model('mdl_mailerlite');
                                // LOGIC FOR SEND DATA TO MAILERLITE OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = MAILERLITE;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO MAILERLITE
                                //     $response = $this->mdl_mailerlite->AddEmailToMailerliteSubscriberList($lastDeliveryData,$mailProvider);
                                
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToMailerliteSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == MAILJET) {
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $mailjetAccountId     = $providerData['aweber_account']; 
                                
                                $mailjetCondition   = array('id' => $mailjetAccountId);
                                $is_single           = true;
                                $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);        
                                
                                if($mailjetAccountData['status'] == 1) {
                                    $lastDeliveryData['birthDate'] = "";
                                    if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                        $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                        $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                    } 

                                    $this->load->model('mdl_mailjet');
                                    // LOGIC FOR SEND DATA TO MAILJET OR QUEUE                            
                                    $delayDay = 0;
                                    if(isset($delays[$mailProvider])){
                                        $delayDay = $delays[$mailProvider];
                                    }
                                    $provider = MAILJET;
                                    // if($delayDay == 0){
                                    //     // NO DELAY INSTANT SEND DATA TO MAILJET
                                    //     $response = $this->mdl_mailjet->AddEmailToMailjetSubscriberList($lastDeliveryData,$mailProvider);
                                    //     // ADD RECORD IN HISTORY
                                    //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    // }else{
                                        // ADD DATA IN QUEUE FOR DELAY SENDING
                                        addToMailjetSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                        // ADD RECORD IN HISTORY
                                        $response = null;
                                        addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    //} 
                                }
                            } else if($providerData['provider'] == CONVERTKIT) {
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 

                                $this->load->model('mdl_convertkit');
                                // LOGIC FOR SEND DATA TO CONVERTKIT OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = CONVERTKIT;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO CONVERTKIT
                                //     $response = $this->mdl_convertkit->AddEmailToConvertkitSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY CONVERTKIT
                                    addToConvertkitSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == MARKETING_PLATFORM) {
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $marketingPlatformAccountId     = $providerData['aweber_account']; 
                                
                                $marketingPlatformCondition   = array('id' => $marketingPlatformAccountId);
                                $is_single           = true;
                                $marketingPlatformAccountData   = GetAllRecord(MARKETING_PLATFORM_ACCOUNTS, $marketingPlatformCondition, $is_single);        
                                
                                if($marketingPlatformAccountData['status'] == 1) {
                                    $lastDeliveryData['birthDate'] = "";
                                    if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                        $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                        $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                    } 

                                    $this->load->model('mdl_marketing_platform');
                                    // LOGIC FOR SEND DATA TO MARKETING PLATFORM OR QUEUE                            
                                    $delayDay = 0;
                                    if(isset($delays[$mailProvider])){
                                        $delayDay = $delays[$mailProvider];
                                    }
                                    $provider = MARKETING_PLATFORM;
                                    // if($delayDay == 0){
                                    //     // NO DELAY INSTANT SEND DATA TO MARKETING PLATFORM
                                    //     $response = $this->mdl_marketing_platform->AddEmailToMarketingPlatformSubscriberList($lastDeliveryData,$mailProvider);                                
                                    //     // ADD RECORD IN HISTORY
                                    //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    // }else{                               
                                        // ADD DATA IN QUEUE FOR DELAY SENDING
                                        addToMarketingPlatformSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                        // ADD RECORD IN HISTORY
                                        $response = null;
                                        addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                    //} 
                                }
                            } else if($providerData['provider'] == ONTRAPORT) {
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                }
                                $this->load->model('mdl_ontraport');
                                // LOGIC FOR SEND DATA TO ONTRAPORT OR QUEUE
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = ONTRAPORT;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO ONTRAPORT
                                //     $response = $this->mdl_ontraport->AddEmailToOntraportSubscriberList($lastDeliveryData,$mailProvider);
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToOntraportSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //}
                            } else if($providerData['provider'] == ACTIVE_CAMPAIGN) {
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 

                                $this->load->model('mdl_active_campaign');
                                // LOGIC FOR SEND DATA TO ACTIVE CAMPAIGN OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = ACTIVE_CAMPAIGN;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO ACTIVE CAMPAIGN
                                //     $response = $this->mdl_active_campaign->AddEmailToActiveCampaignSubscriberList($lastDeliveryData,$mailProvider);                                
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{                               
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToActiveCampaignSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == EXPERT_SENDER) {
                                
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 
                                
                                $this->load->model('mdl_expert_sender');
                                
                                // LOGIC FOR SEND DATA TO EXPERT SENDER OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = EXPERT_SENDER;                            
                                // if($delayDay == 0){
                                    
                                //     // NO DELAY INSTANT SEND DATA TO EXPERT SENDER
                                //     $response = $this->mdl_expert_sender->AddEmailToExpertSenderSubscriberList($lastDeliveryData,$mailProvider);         
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{                               
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToExpertSenderSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // } 
                            
                            } else if($providerData['provider'] == CLEVER_REACH) {
                                
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 
                                
                                $this->load->model('mdl_clever_reach');
                                
                                // LOGIC FOR SEND DATA TO EXPERT SENDER OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = CLEVER_REACH;                            
                                // if($delayDay == 0){
                                    
                                //     // NO DELAY INSTANT SEND DATA TO EXPERT SENDER
                                //     $response = $this->mdl_clever_reach->AddEmailToCleverReachSubscriberList($lastDeliveryData,$mailProvider);         
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{                               
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToCleverReachSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            } else if($providerData['provider'] == OMNISEND) {
                                $lastDeliveryData['birthDate'] = "";
                                if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                                    $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                                    $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
                                } 

                                $this->load->model('mdl_omnisend');
                                // LOGIC FOR SEND DATA TO OMNISEND OR QUEUE                            
                                $delayDay = 0;
                                if(isset($delays[$mailProvider])){
                                    $delayDay = $delays[$mailProvider];
                                }
                                $provider = OMNISEND;
                                // if($delayDay == 0){
                                //     // NO DELAY INSTANT SEND DATA TO OMNISEND
                                //     $response = $this->mdl_omnisend->AddEmailToOmnisendSubscriberList($lastDeliveryData,$mailProvider);                                
                                //     // ADD RECORD IN HISTORY
                                //     addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                // }else{                               
                                    // ADD DATA IN QUEUE FOR DELAY SENDING
                                    addToOmnisendSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay);
                                    // ADD RECORD IN HISTORY
                                    $response = null;
                                    addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$getLiveDeliveryData['groupName'],$getLiveDeliveryData['keyword'],$lastDeliveryData['emailId']);
                                //} 
                            }else{
                                $response = array("result" => "error", "error" => array("msg" => "Wrong provider"));
                            }    
                            // if(isset($delayDay) && $delayDay == 0){
                            //     //update to live delivery data response
                            //     $condition = array('liveDeliveryDataId' => $liveDeliveryDataId);
                            //     $is_insert = false;
                            //     $responseField = $providerData['response_field'];
                            //     $updateArr = array($responseField => json_encode($response));
                            //     ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
                            // } 
                        }
                    }
                }
            }            
            // Send data to Integromat hook
            if ($sucFailMsgIndex == 0 || $sucFailMsgIndex == 1) {
                sendLeadInIntegromat($lastDeliveryData,$getLiveDeliveryData);
            }
        }

    }

    public function addToLiveDeliveryUndefinedDataTable($getData, $sucFailMsgIndex)
    {

        //add in live delivery data database
        $condition = array();
        $is_insert = true;
        $dataArr   = array();

        foreach ($getData as $key => $value) {

            if ($key == 'birthdateYear') {
                if ($value != '') {
                    $dataArr['age'] = date('Y') - date('Y', strtotime(date($value . '-m-d')));
                }
            }

            $dataArr[$key] = $value;
        }

        unset($dataArr['apikey']); //api key is not in the table

        $dataArr['sucFailMsgIndex'] = $sucFailMsgIndex;

        ManageData(LIVE_DELIVERY_UNDEFINED_KEY_DATA, $condition, $dataArr, $is_insert);

    }

    public function updateUserGroupName($getUserData,$getLiveDeliveryData){

        $groupName        = $getLiveDeliveryData['groupName'];
        $country          = $getLiveDeliveryData['country'];  
        $groupNames       = $getUserData['groupName'];
        $groupNameArr     = explode(',', $groupNames);

        if(!in_array($groupName,$groupNameArr)){
            
            $groupNameArr[] = $groupName;
            $groupNameString = implode(',', $groupNameArr);

            //add group name in group_master table
            $this->mdl_csv->insertGroupName($groupName);

            //update group name
            $condition = array('userId' => $getUserData['userId']);
            $updateArr = array('groupName' => $groupNameString);
            $is_insert = false;
            ManageData(USER, $condition, $updateArr, $is_insert);

            // manage country wise group
            $groupCountryCount = GetAllRecordCount(GROUP_COUNTRY_COUNT, $condition = array('groupName' => $groupName, 'country' => $country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupCountryId');

            if ($groupCountryCount == 0) {

                $sql_groupCountryCount = "INSERT INTO " . GROUP_COUNTRY_COUNT . " (groupName, country)  VALUES ('$groupName', '$country')";

                $this->db->query($sql_groupCountryCount);
            }

            if (@$getUserData['gender'] == 'male') {

                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male + 1  WHERE groupName = '$groupName'";
                $this->db->query($sql_groupName);

            }

            if (@$getUserData['gender'] == 'female') {

                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female + 1  WHERE groupName = '$groupName'";
                $this->db->query($sql_groupName);
            }

            if (@$getUserData['gender'] != 'male' && @$getUserData['gender'] != 'female') {

                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other + 1  WHERE groupName = '$groupName'";
                $this->db->query($sql_groupName);
            }
        }
    }

    public function updateUserKeyword($getUserData,$getLiveDeliveryData){

        $keyword        = $getLiveDeliveryData['keyword'];
        $country        = $getLiveDeliveryData['country'];  
        $keywords       = $getUserData['keyword'];
        $keywordArr   = explode(',', $keywords);

        if(!in_array($keyword,$keywordArr)){
            $keywordArr[] = $keyword;
            $keywordString = implode(',', $keywordArr);

            //add group name in keyword_master table
            $this->mdl_csv->insertKeyword($keyword);

            //update keyword name
            $condition = array('userId' => $getUserData['userId']);
            $updateArr = array('keyword' => $keywordString);
            $is_insert = false;
            ManageData(USER, $condition, $updateArr, $is_insert);    
            
            // manage country wise keyword
            $keywordCountryCount = GetAllRecordCount(KEYWORD_COUNTRY_COUNT, $condition = array('keyword' => $keyword, 'country' => $country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'keywordCountryId');

            if ($keywordCountryCount == 0) {
                $sql_keywordCountryCount = "INSERT INTO " . KEYWORD_COUNTRY_COUNT . " (keyword, country, total)  VALUES ('$keyword', '$country', 1)";
                $this->db->query($sql_keywordCountryCount);
            }

            if ($keywordCountryCount > 0) {
                $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total + 1  WHERE keyword = '$keyword' and country = '$country'";
                $this->db->query($sql_keywordCountryCount);
            }

            if (@$_GET['gender'] == 'male') {
                $sql_country = "UPDATE " . COUNTRY_MASTER . " SET male = male + 1  WHERE country = '$country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male + 1  WHERE keyword = '$keyword'";
                $this->db->query($sql_keyword);            
            }

            if (@$_GET['gender'] == 'female') {
                $sql_country = "UPDATE " . COUNTRY_MASTER . " SET female = female + 1  WHERE country = '$country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female + 1  WHERE keyword = '$keyword'";
                $this->db->query($sql_keyword);            
            }

            if (@$_GET['gender'] != 'male' && @$_GET['gender'] != 'female') {

                $sql_country = "UPDATE " . COUNTRY_MASTER . " SET other = other + 1  WHERE country = '$country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other + 1  WHERE keyword = '$keyword'";
                $this->db->query($sql_keyword);            
            }
        }    
    }

    public function ifUserInThisGroupsThenAddUserInThisGroup($getUserData, $getLiveDeliveryData)
    {

        $ifUserInThisGroups    = $getLiveDeliveryData['ifUserInThisGroups'];
        $addTheUserInThisGroup = $getLiveDeliveryData['addTheUserInThisGroup'];

        $ifUserInThisGroupsArr = explode(',', $ifUserInThisGroups);
        $groupName             = $getUserData['groupName'];
        $groupNameArr          = explode(',', $groupName);

        foreach ($groupNameArr as $gn) {

            if (in_array($gn, $ifUserInThisGroupsArr)) {

                if (!in_array($addTheUserInThisGroup, $groupNameArr)) {

                    $groupNameArr[]  = $addTheUserInThisGroup;
                    $groupNameString = implode(',', $groupNameArr);

                    //add group name in group_master table
                    $this->mdl_csv->insertGroupName($addTheUserInThisGroup);

                    // new edited start..
                    $con_groupName   = $addTheUserInThisGroup;
                    $con_countryName = $getLiveDeliveryData['country'];

                    // manage country wise group
                    $groupCountryCount = GetAllRecordCount(GROUP_COUNTRY_COUNT, $condition = array('groupName' => $con_groupName, 'country' => $con_countryName), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupCountryId');

                    if ($groupCountryCount == 0) {

                        $sql_groupCountryCount = "INSERT INTO " . GROUP_COUNTRY_COUNT . " (groupName, country)  VALUES ('$con_groupName', '$con_countryName')";

                        $this->db->query($sql_groupCountryCount);
                    }

                    if (@$getUserData['gender'] == 'male') {

                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male + 1  WHERE groupName = '$con_groupName'";
                        $this->db->query($sql_groupName);

                    }

                    if (@$getUserData['gender'] == 'female') {

                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female + 1  WHERE groupName = '$con_groupName'";
                        $this->db->query($sql_groupName);
                    }

                    if (@$getUserData['gender'] != 'male' && @$getUserData['gender'] != 'female') {

                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other + 1  WHERE groupName = '$con_groupName'";
                        $this->db->query($sql_groupName);

                    }

                    // new edited end..

                    //update group name
                    $condition = array('userId' => $getUserData['userId']);
                    $updateArr = array('groupName' => $groupNameString);
                    $is_insert = false;
                    ManageData(USER, $condition, $updateArr, $is_insert);
                }
                break; //it is very important
            }
        }

    }

    public function addToInboxgameFacebookleadData($getData, $getLiveDeliveryData, $sucFailMsgIndex, $isFail, $isEmailChecked){
        $condition = [];
        $is_insert = true;
        $dataArr = [];


        $dataArr['apikey'] = $getData['apikey'];
        $dataArr['emailId'] = $getData['emailId'];
        $dataArr['country'] = $getLiveDeliveryData['country'];
        $dataArr['groupName']       = $getLiveDeliveryData['groupName'];
        $dataArr['keyword']         = $getLiveDeliveryData['keyword'];
        $dataArr['source']          = $getLiveDeliveryData['dataSource'];
        $dataArr['isFail']          = $isFail;
        $dataArr['sucFailMsgIndex'] = $sucFailMsgIndex;
        $dataArr['isEmailChecked']  = $isEmailChecked;
        $dataArr['dataSourceType'] = $getLiveDeliveryData['dataSourceType']; // 1 - inboxgame data , 2 - facebook lead api
        
        $dataArr['name'] = $getData['name'];
        $dataArr['signupDate'] = $getData['signupDate'];
        $dataArr['timestamp'] = time();
        $dataArr['ip'] = $getData['ip'];
        $dataArr['apiSource'] = $getData['source']; // source data when api call using get live url
        $dataArr['apiCountry'] = $getData['country']; // country data of get API

        $dataArr['createdDate'] = date('Y-m-d H:i:s');
        $dataArr['updatedDate'] = date('Y-m-d H:i:s');

        $liveDeliveryDataId = ManageData(INBOXGAME_FACEBOOKLEAD_DATA, $condition, $dataArr, $is_insert);
        $response = array('result' => 'success', 'msg' => "liveDeliveryDataId: " . $liveDeliveryDataId);
        return $response;
    }
}


