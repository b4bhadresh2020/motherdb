<?php

/**
 * 
 */
class Cron_provider_user_csv extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_aweber');
        $this->load->model('mdl_transmitvia');
        $this->load->model('mdl_ongage');
        $this->load->model('mdl_sendgrid');
        $this->load->model('mdl_sendinblue');
        $this->load->model('mdl_sendpulse');
        $this->load->model('mdl_mailerlite');
        $this->load->model('mdl_mailjet');
        $this->load->model('mdl_convertkit');
        $this->load->model('mdl_marketing_platform');
        $this->load->model('mdl_ontraport');
        $this->load->model('mdl_active_campaign');
        $this->load->model('mdl_expert_sender');
        $this->load->model('mdl_clever_reach');
        $this->load->model('mdl_omnisend');
    }

    public function index() {
        
        //create log file.
        //$cronFilePath = APPPATH."logs/csvcron/cron_running.txt";
        //$cronWriteFile = fopen($cronFilePath, 'a');
        //fwrite($cronWriteFile,"-----------".date("Y-m-d H:i")."-----------"."\n");
        
        //get file data
        $condition = array(
            'fromDate <=' => date('Y-m-d'),
            'status' => 1
        );
        $is_single = FALSE;
        $csvProviderData = GetAllRecord(CSV_FILE_PROVIDER_DATA,$condition,$is_single,array(),array(),array());
        // pre($csvProviderData);die;
        //loopwise check csv file and send data to provider
        foreach ($csvProviderData as $provider) {

            $currentTimestamp = get_current_time_of_country($provider["country"]);
            $currentTime = date("H:i:00",$currentTimestamp);
            $currentTotalMinute = $this->convertTimeToMinute($currentTime);

            $startTime = $provider["startTime"];
            $startTotalMinute = $this->convertTimeToMinute($startTime);
            
            $endTime = $provider["endTime"];
            $endTotalMinute = $this->convertTimeToMinute($endTime);

            /* echo $currentTotalMinute . "<br>";
            echo $startTotalMinute . "<br>";
            echo $endTotalMinute . "<br>";
            die; */
            
            // Here we check current time not exceed with end time of country wise
            if($currentTotalMinute >= $startTotalMinute && $currentTotalMinute <= $endTotalMinute){
                
                //create log file.
                $responseFilePath = APPPATH."logs/csvcron/provider_".$provider["id"].".txt";
                $writeFile = fopen($responseFilePath, 'a');
                fwrite($writeFile,"-----------".date("Y-m-d H:i")."-----------"."\n");
                
                //calculate no of record need to process in 5 minute.
                $recordLimit = $this->calculateRecords($provider['startTime'],$provider['endTime'],$provider['perDayRecord']);  

                // count record using limit.
                $is_single = TRUE;
                $sentRecordCondition = array(
                    'providerId' => $provider['id'],
                    'status' => 0                
                );
                $totalQueueRecord = GetAllRecordCount(CSV_CRON_USER_DATA,$sentRecordCondition,$is_single,array(),array(),array());  
                
                if($totalQueueRecord){
                    
                    // get today total number of send records
                    $is_single = TRUE;
                    $sendRecordcondition = array(
                        'providerId' => $provider['id'],
                        'status' => 1
                    );

                    $sendRecordLikecondition = array(
                        array(
                            'sendDate' => date('Y-m-d')
                        )
                    );

                    $totalTodaySendRecord = GetAllRecordCount(CSV_CRON_USER_DATA,$sendRecordcondition,$is_single,$sendRecordLikecondition,array(),array());
                    
                    // get records as per limit   
                    $is_single = FALSE;             
                    $csvProviderUserData = GetRecordWithLimit(CSV_CRON_USER_DATA,$sentRecordCondition,'user','userId','userId','left',$is_single,array(),array(),array(),'','',$recordLimit);
                    foreach($csvProviderUserData as $userData){                  

                        $totalTodaySendRecord++;
                        $response = "";
                        //$responseField = "aweberResponse";
                        if($totalTodaySendRecord <= $provider['perDayRecord']){
                            // send data as per selected provider
                            if($provider['providerName'] == AWEBER){
                                $country = $provider['country'];
                                $validCountryForAweber = $this->countryListedInAweber();
                                $mailProvider = $this->getAweberMailProviderId($provider["providerList"]);
                                $responseField = "aweberResponse";
                                if (in_array(strtoupper($country), $validCountryForAweber)) {       
                                    if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                        $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                        $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                    } 
                                    $response = $this->mdl_aweber->AddEmailToAweberSubscriberList($userData,$country,$mailProvider);
                                    addRecordInHistoryFromCSV($userData, $mailProvider, AWEBER, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                                } else {
                                    $response = array("result" => "error", "error" => array("msg" => "Country is not defined in Aweber"));
                                }
                            }else if($provider['providerName'] == TRANSMITVIA){
                                $responseField = "transmitviaResponse";
                                $mailProvider = $this->getTransmitviaMailProviderId($provider["providerList"]);                                
                                $response = $this->mdl_transmitvia->AddEmailToTransmitSubscriberList($userData,$mailProvider);
                                // GET ORIGINAL PROVIDER ID FROM PROVIDERS SCHEMA
                                $mailProviderOriginal = getProviderIdUsingTransmitviaList($mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProviderOriginal, TRANSMITVIA, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            }else if($provider['providerName'] == ONGAGE){
                                $responseField = "ongageResponse";
                                $mailProvider = $this->getOngageMailProviderId($provider["providerList"]);                                
                                $response = $this->mdl_ongage->AddEmailToOngageSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, ONGAGE, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            }else if($provider['providerName'] == SENDGRID){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "sendgridResponse";
                                $mailProvider = $this->getSendgridMailProviderId($provider["providerList"]);
                                
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $sendgridAccountId     = $providerData['aweber_account']; 
                                
                                $sendgridCondition   = array('id' => $sendgridAccountId);
                                $is_single           = true;
                                $sendgridAccountData   = GetAllRecord(SENDGRID_ACCOUNTS, $sendgridCondition, $is_single);

                                if($sendgridAccountData['status'] == 1) {
                                    $response = $this->mdl_sendgrid->AddEmailToSendgridSubscriberList($userData,$mailProvider);
                                    addRecordInHistoryFromCSV($userData, $mailProvider, SENDGRID, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                                } else {
                                    $response = array("result" => "error","error" => array("msg" => "Account is closed"));
                                }
                            } else if($provider['providerName'] == SENDINBLUE){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "sendinblueResponse";
                                $mailProvider = $this->getSendInBlueMailProviderId($provider["providerList"]);                                
                                $response = $this->mdl_sendinblue->AddEmailToSendInBlueSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, SENDINBLUE, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == SENDPULSE){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "sendpulseResponse";
                                $mailProvider = $this->getSendPulseProviderId($provider["providerList"]);                                
                                $response = $this->mdl_sendpulse->AddEmailToSendpulseSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, SENDPULSE, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == MAILERLITE){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "mailerliteResponse";
                                $mailProvider = $this->getMailerliteProviderId($provider["providerList"]);                                
                                $response = $this->mdl_mailerlite->AddEmailToMailerliteSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, MAILERLITE, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == MAILJET){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "mailjetResponse";
                                $mailProvider = $this->getMailjetProviderId($provider["providerList"]);    
                                
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $mailjetAccountId     = $providerData['aweber_account']; 
                                
                                $mailjetCondition   = array('id' => $mailjetAccountId);
                                $is_single           = true;
                                $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);
                                if($mailjetAccountData['status'] == 1) { 
                                    $response = $this->mdl_mailjet->AddEmailToMailjetSubscriberList($userData,$mailProvider);
                                    addRecordInHistoryFromCSV($userData, $mailProvider, MAILJET, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                                } else {
                                    $response = array("result" => "error","error" => array("msg" => "Account is closed"));
                                }
                                
                            } else if($provider['providerName'] == CONVERTKIT){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "convertkitResponse";
                                $mailProvider = $this->getConvertkitProviderId($provider["providerList"]);                                
                                $response = $this->mdl_convertkit->AddEmailToConvertkitSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, CONVERTKIT, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            }  else if($provider['providerName'] == MARKETING_PLATFORM){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "marketingPlatformResponse";
                                $mailProvider = $this->getMarketingPlatformProviderId($provider["providerList"]); 
                                
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $marketingPlatformAccountId     = $providerData['aweber_account']; 
                                
                                $marketingPlatformCondition   = array('id' => $marketingPlatformAccountId);
                                $is_single           = true;
                                $marketingPlatformAccountData   = GetAllRecord(MARKETING_PLATFORM_ACCOUNTS, $marketingPlatformCondition, $is_single);
                                if($marketingPlatformAccountData['status'] == 1) {
                                    $response = $this->mdl_marketing_platform->AddEmailToMarketingPlatformSubscriberList($userData,$mailProvider);
                                    addRecordInHistoryFromCSV($userData, $mailProvider, MARKETING_PLATFORM, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                                } else {
                                    $response = array("result" => "error","error" => array("msg" => "Account is closed"));
                                }
                            } else if($provider['providerName'] == ONTRAPORT){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "ontraportResponse";
                                $mailProvider = $this->getOntraportProviderId($provider["providerList"]);                                
                                $response = $this->mdl_ontraport->AddEmailToOntraportSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, ONTRAPORT, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == ACTIVE_CAMPAIGN){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "activeCampaignResponse";
                                $mailProvider = $this->getActiveCampaignProviderId($provider["providerList"]);                                
                                $response = $this->mdl_active_campaign->AddEmailToActiveCampaignSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, ACTIVE_CAMPAIGN, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == EXPERT_SENDER){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "expertSenderResponse";
                                $mailProvider = $this->getExpertSenderProviderId($provider["providerList"]);                                
                                $response = $this->mdl_expert_sender->AddEmailToExpertSenderSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, EXPERT_SENDER, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == CLEVER_REACH){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "cleverReachResponse";
                                $mailProvider = $this->getCleverReachProviderId($provider["providerList"]);                                
                                $response = $this->mdl_clever_reach->AddEmailToCleverReachSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, CLEVER_REACH, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == OMNISEND){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "omniSendResponse";
                                $mailProvider = $this->getOmnisendProviderId($provider["providerList"]); 
                                
                                // fetch mail provider data from providers table
                                $providerCondition   = array('id' => $mailProvider);
                                $is_single           = true;
                                $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
                                $omnisendAccountId     = $providerData['aweber_account']; 
                                
                                $omnisendCondition   = array('id' => $omnisendAccountId);
                                $is_single           = true;
                                $omnisendAccountData   = GetAllRecord(OMNISEND_ACCOUNTS, $omnisendCondition, $is_single);
                                if($omnisendAccountData['status'] == 1) {
                                    $response = $this->mdl_omnisend->AddEmailToOmnisendSubscriberList($userData,$mailProvider);
                                    addRecordInHistoryFromCSV($userData, $mailProvider, OMNISEND, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                                } else {
                                    $response = array("result" => "error","error" => array("msg" => "Account is closed"));
                                }
                            }
                            // update status of sended record
                            $is_insert = FALSE;
                            $updateCondition = array('providerUserId' => $userData['providerUserId']);
                            $updateData = array('status' => 1,'sendDate' => date('Y-m-d H:i'), $responseField =>json_encode($response));
                            ManageData(CSV_CRON_USER_DATA,$updateCondition,$updateData,$is_insert);

                            // update datewise total number of record counter.

                            // count record using limit.
                            $is_single = TRUE;
                            $historyRecordCondition = array(
                                'providerId' => $provider['id'],
                                'providerName' => $provider['providerName'],                
                                'providerList' => $provider['providerList'],                
                                'sendDate' => date("Y-m-d")                
                            );
                            $totalHistoryRecord = GetAllRecordCount(CSV_FILE_PROVIDER_HISTORY,$historyRecordCondition,$is_single,array(),array(),array()); 
                            
                            if($totalHistoryRecord){
                                // update record in csv_cron_user_history
                                $sql_country = "UPDATE ".CSV_FILE_PROVIDER_HISTORY." SET totalSend = totalSend + 1  WHERE providerId = ".$provider['id']." and providerName = ".$provider['providerName']." and providerList = ".$provider['providerList']." and sendDate = '".date("Y-m-d")."'";
                                $this->db->query($sql_country);         

                            }else{
                                // add new record in csv_cron_user_history
                                $is_insert = TRUE;                                
                                $condition = array();
                                $historyNewRecord = array(
                                    'providerId' => $provider['id'],
                                    'providerName' => $provider['providerName'],                
                                    'providerList' => $provider['providerList'],                
                                    'sendDate' => date("Y-m-d"),
                                    'totalSend' => '1'         
                                );
                                ManageData(CSV_FILE_PROVIDER_HISTORY,$condition,$historyNewRecord,$is_insert); 
                            }

                            fwrite($writeFile,"providerUserId : ".$userData['providerUserId']." userId : ".$userData['userId']." provider : ".$provider['providerName']." response : ".json_encode($response)."\n");                        
                        }
                    }                
                }else{
                    // all are served then update status completed in provider table csv_file_provider_data
                    $is_insert = FALSE;
                    $updateCondition = array('id' => $provider["id"]);
                    $updateData = array('status' => 2);
                    ManageData(CSV_FILE_PROVIDER_DATA,$updateCondition,$updateData,$is_insert);
                }
                fclose($writeFile);
            }
        }
        //fclose($cronWriteFile);
        //echo "Cron Execution End";
    }

    // need to update country as per provider list country
    public function countryListedInAweber(){
        return array('DK','NOR','SE','FI','UK','NL','CA','NZ');
    }

    public function getAweberMailProviderId($providerId){
        $provider = array(
            "1" => "14",  // Velkomstgaven.com (Norway) 
            "2" => "16", // Gratispresent.se (Sweden)
            "3" => "13",  // Velkomstgaven.dk (Denmark)
            "4" => "4",  // Freecasinodeal.com/no  (Norway)
            "5" => "3",  // Freecasinodeal.com/fi (Finland) 
            "6" => "5",  // Freecasinodeal.com 
            "7" => "18",  // FI - Katariinasmail
            "8" => "19",  // NO - Signesmail
            "9" => "27",  // SE - Frejasmail
            "10" => "55",  // CA - Getspinn
            "11" => "56",  // NO - Getspinn
            "12" => "57",  // NZ - Getspinn
            "13" => "61",  // Freecasinodeal.com/nz  (New Zealand)
            "14" => "68",  // DK - Signesmail
            "15" => "71", // DK - abbie
            "16" => "72", // FI - abbie
            "17" => "73", // NO - abbie
            "18" => "74", // SE - abbie
            "19" => "75", // FreeCasinodeal/ca (Canada)
            "20" => "76", // FelinaFinans/se
            "21" => "78", // New_gratispresent
            "22" => "79", // New_velkomstgaven_dk
            "23" => "80", // New_velkomstgaven_com
            "24" => "81", // New_velkomstgaven1_com
            "25" => "82", // New_unelmalaina
            "26" => "86", // Freecasinodeal/NZ/olivia
            "27" => "87", // Freecasinodeal/CA/sofia
            "28" => "88", // Freecasinodeal/NO/emma
            "29" => "89", // Freecasinodeal/FI/aida
            "30" => "90", // Frejasmail1/SE  
            "31" => "91", // Frejasmail2/SE   
            "32" => "92", // Signesmail1/DK   
            "33" => "93", // Katariinasmail1/FI   
            "34" => "94", // Signesmail1/NO   
            "35" => "95", // Signesmail2/NO  
            "36" => "96", // Abbiesmail1/CA   
            "37" => "97", // Abbiesmail2/CA  
            "38" => "98", // Ashleysmail/NZ   
            "39" => "99", // Ashleysmail1/NZ  
            "40" => "100", // Signesmail/DK
            "41" => "101", // Velkomstgaven/NO
            "42" => "102", // Velkomstgaven1/NO
            "43" => "103", // Gratispresent/SE
            "44" => "104", // Gratispresent1/SE
            "45" => "105", // FelinaFinans/SE
            "46" => "106", // FelinaFinans1/SE
            "47" => "107", // FelinaFinansmail/SE
            "48" => "108", // Unelmalaina/FI
            "49" => "109", // Unelmalaina1/FI
            "50" => "110", // Velkomstgaven/DK
            "51" => "111", // Velkomstgaven1/DK
            "52" => "112", // Getspinn1/CA
            "53" => "113", // Getspinnmail/CA
            "54" => "114", // Freecamail/CA,
            "55" => "189", // Gratisprodukttest/SE
        );
        return $provider[$providerId];
    
    }

    public function getOngageMailProviderId($providerId){
        $provider = array(
            "1" => "47",  // Australia-camilla 
            "2" => "48",  // Australia - Kare 
            "3" => "49",  // Canada - Camilla 
            "4" => "50",  // Canada - Kare
            "5" => "51",  // Sweden - Camilla
            "6" => "52",  // Sweden - Kare
            "7" => "53",  // Norway - Camilla
            "8" => "54",  // Norway - Kare 
            "9" => "58", // Finland  - Camilla 
            "10" => "59",  // Finland  - Kare 
            "11" => "62",  // New zealand  - Camilla 
            "12" => "63",  // New zealand  - Kare 
            "13" => "69",  // Denmark  - Kare 
            "14" => "70",  // Denmark  - Camilla 
            "15" => "77",  // FI - Test
        );
        return $provider[$providerId];
    }

    public function getSendgridMailProviderId($providerId){
        $provider = array(
            "1" => "60",  // CA-Abbiesmail 
            "2" => "214",  // NZ-Ashleysmail
            "3" => "215",  // NZ-Allfreeca
            "4" => "216",  // CA-Allfreeca
            "5" => "217",  // Katariinasmail
            "6" => "218",  // Velkomstgaven/NO
            "7" => "219",  // Gratispresent/SE
        );
        return $provider[$providerId];
    }

    public function getTransmitviaMailProviderId($providerId){
        $provider = array(
            "1" => "nl833sd4boade",
            "2" => "pr7186vdfd270",
            "3" => "yo5216xrr9576",  
            "4" => "kx4442jcq188b",  
            "5" => "op491f70q6344",  
            "6" => "xm300dcfhff2c",  
            "7" => "fb049dv76cd0c",  
            "8" => "gx4464j5vz1e4",  
            "9" => "xz046ves46a42",  
            "10" => "ky135pw7nk914",  
            "11" => "mv639733yp8a7",  
            "12" => "lm897h5mo8626",  
            "13" => "fn85378sw9d0b",  
            "14" => "ej569m0d3h58e",  
            "15" => "jz932fvv60d05",  
            "16" => "xh4477gqap01a",  
            "17" => "em899p199ca86",  
            "18" => "nq313azc9off4",  
            "19" => "rk963n8mlzbf7",  
            "20" => "vq4332j3xbfc0",  
            "21" => "bw3936mqqq855",  
            "22" => "la425f30kz146" 
        );
        return $provider[$providerId];
    }

    public function getSendInBlueMailProviderId($providerId){
        $provider = array(
            "1" => "64",  // NO
            "2" => "65",  // CA
            "3" => "66",  // NZ
            "4" => "67"  // SE
        );
        return $provider[$providerId];
    }

    public function getSendPulseProviderId($providerId){
        $provider = array(
            "1" => "83",  // NO-Sendpulse
            "2" => "84",  // CA-Sendpulse
            "3" => "85",  // SE-Sendpulse
        );
        return $provider[$providerId];
    }

    public function getMailerliteProviderId($providerId){
        $provider = array(
            "1" => "115",  // DK-Velkomstgaven
            "2" => "116",  // NO-Velkomstgaven.com
            "3" => "117",  // NO-Velkomstgaven.com1
            "4" => "118",  // SE-Gratispresent
        );
        return $provider[$providerId];
    }

    public function getMailjetProviderId($providerId){
        $provider = array(
            "1" => "119",  // Velkomstgaven/DK
            "2" => "120",  // Gratispresent/SE
            // "3" => "127",   // Velkomstgaven/NOR
            "4" => "188",  //Freja/SE
            "5" => "190",  //Signesmail/DK
            "6" => "191",  //Signesmail2/NO
            "7" => "192",  //Produkttest/SE
            "8" => "210",  //DagensPresent/SE
            "9" => "211",  //VelkomstgavenVIP/DK
            "10" => "212",  //VelkomstgavenVIP/NO

        );
        return $provider[$providerId];
    }

    public function getConvertkitProviderId($providerId){
        $provider = array(
            "1" => "121",  // DK
            "2" => "122",  // SE
            "3" => "123",  // NO
            "4" => "124",  // FI
            "5" => "125", // CA
            "6" => "126",  // NZ
            "7" => "128",  // NOR
            "8" => "129",  // SE
            "9" => "130",  // NOR
            "10" => "131", // FI
            "11" => "132"  // DK
        );
        return $provider[$providerId];
    }

    public function getMarketingPlatformProviderId($providerId){
        $provider = array(
            // "1" => "133",  // SE-Gratispresent
            // "2" => "134",  // NO-Velkomstgaven
            // "3" => "135",  // DK-Velkomstgaven
            // "4" => "136",  // FI-Unelmalaina
            // "5" => "137",  // FreeCasinoDeal-CA
            // "6" => "138",  // FreeCasinoDeal-FI
            // "7" => "139",  // FreeCasinoDeal-NO
            // "8" => "140",  // FreeCasinoDeal-NZ  
            // "9" => "148",  // NO-Velkomstgaven1
        ); 
        return $provider[$providerId];
    }

    public function getOntraportProviderId($providerId){
        $provider = array(
            "1" => "141",  // Gratispresentmail.se
            "2" => "142",  // Freecasinodeal1/no
            "3" => "143",  // Freecasinodeal1/fi
            "4" => "144",  // Velkomstgavenmail.dk
            "5" => "145",  // Freecasinodeal1/ca
            "6" => "146",  // Freecasinodeal1/nz
            "7" => "194",  // Velkomstgaven/DK
            "8" => "195",  // Velkomstgaven/com
            "9" => "196",  // Gratispresent/SE
            "10" => "197",  // Unelmalaina/FI 
            "11" => "200",  // Velkomst/DK 
            "12" => "201",  // Signe/DK 
            "13" => "202",  // Dagens/SE      
            "14" => "203",  // Felina/SE 
            "15" => "204",  // Venla/FI 
            "16" => "205",  // Katariina/FI 
            "17" => "206",  // Allfree/CA 
            "18" => "207",  // Abbie/CA 
            "19" => "208",  // Ashley/NZ
            "20" => "209"   // Produkt/NO  
        );
        return $provider[$providerId];
    }
    
    public function getActiveCampaignProviderId($providerId){
        $provider = array(
            "1" => "147",  // Velkomstgaven/NOR
            "2" => "163",  // GratisPresent/SE
            "3" => "164",  // Frejasmail/SE
            "4" => "165",  // Unelmalaina/FI
            "5" => "166",  // Signesmail/NOR
            "6" => "167",  // Katariinasmail/FI
            "7" => "168",  // Velkomstgaven/DK
            "8" => "169",  // Signesmail/DK
            "9" => "193",   // Velkomstgaven/NO
            "10" => "198",  // gratisprodukttester.com/NO
            "11" => "199",  // dagenspresent.se/SE
            "12" => "213",  // gratispresent1.com

        );
        return $provider[$providerId];
    }

    public function getExpertSenderProviderId($providerId){
        $provider = array(
            "1" => "149",  // camilla/abbiesmail2.com/CA
            "2" => "150",  // camilla/ashleysmail1.com/NZ
            "3" => "151",  // camilla/felinafinans.se/SE
            "4" => "152",  // camilla/frejasmail2.se/SE
            "5" => "153",  // camilla/katariinasmail1.com/FI
            "6" => "154",  // camilla/signesmail1.dk/DK
            "7" => "155",  // camilla/signesmail2.com/NO,
            "8" => "170",  // Kaare/NO-FreeCasinodeal
            "9" => "171",  // Kaare/FI-FreeCasinodeal
            "10" => "172", // Kaare/CA-FreeCasinodeal
            "11" => "173", // Kaare/NZ-FreeCasinodeal
            "12" => "174", // Kaare/CA-GetSpinn
            "13" => "175", // Kaare/NZ-GetSpinn
            "14" => "176", // Kaare/NO-GetSpinn
            "15" => "177", // Kaare/gratispresentmail.se/SE
            "16" => "178", // Kaare/unelmalainamail.fi/Unelmalaina
            "17" => "179", // Kaare/Velkomstgaven-NO
            "18" => "180", // Kaare/DK-Velkomstgaven
        );
        return $provider[$providerId];
    }

    public function getCleverReachProviderId($providerId){
        $provider = array(
            "1" => "156",  // Velkomstgaven/DK 
            "2" => "157",  // Cathrinesmail/CA
            "3" => "158",  // Cathrinesmail/DK
            "4" => "159",  // Cathrinesmail/FI        
            "5" => "160",  // Cathrinesmail/NO
            "6" => "161",  // Cathrinesmail/NZ
            "7" => "162",  // Cathrinesmail/SE
            "8" => "185",  // Velkomstgaven/NO
            "9" => "186",  // Gratispresent/SE
            "10" => "187", // Unelmalaina/FI
        );
        return $provider[$providerId];
    }

    public function getOmnisendProviderId($providerId){
        $provider = array(
            "1" => "181",  // SE-Gratispresent 
            "2" => "182",  // NO-Velkomstgaven
            "3" => "183",  // FI-Unelmalaina
            "4" => "184",  // DK-Velkomstgaven
        );
        return $provider[$providerId];
    }
    
    public function convertTimeToMinute($time){

        $splitTime = explode(":",$time);
        $totalMinute = ($splitTime[0]*60)+$splitTime[1];
        return $totalMinute;
    }

    public function calculateRecords($fromTime,$toTime,$totalRecords){
        $fromTimes = explode(":",$fromTime);
        $toTimes = explode(":",$toTime);

        $fromTotalMinutes =  $fromTimes[0]*60 + $fromTimes[1];
        $toTotalMinutes =  $toTimes[0]*60 + $toTimes[1];
        $remainTotalMinute = $toTotalMinutes - $fromTotalMinutes;

        $cronTotalMinutes = 5;  // cron will be run on each 4 minutes.
        $recordsLimit = ceil(($totalRecords * $cronTotalMinutes) / $remainTotalMinute);
        return $recordsLimit;
    }

}