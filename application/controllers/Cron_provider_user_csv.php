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
                                $response = $this->mdl_sendgrid->AddEmailToSendgridSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, SENDGRID, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
                            } else if($provider['providerName'] == SENDINBLUE){
                                if (@$userData['birthdateDay'] != '' && @$userData['birthdateMonth'] != '' && @$userData['birthdateYear'] != '') {
                                    $birthDate              = $userData['birthdateYear'] . '-' . $userData['birthdateMonth'] . '-' . $userData['birthdateDay'];
                                    $userData['birthDate']  = date('Y-m-d', strtotime($birthDate));
                                } 
                                $responseField = "sendinblueResponse";
                                $mailProvider = $this->getSendInBlueMailProviderId($provider["providerList"]);                                
                                $response = $this->mdl_sendinblue->AddEmailToSendInBlueSubscriberList($userData,$mailProvider);
                                addRecordInHistoryFromCSV($userData, $mailProvider, SENDINBLUE, $response,$provider['groupName'],$provider['keyword'],$userData['emailId']);
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
            "26" => "83", // NO-Sendpulse
            "27" => "84", // CA-Sendpulse
            "28" => "85", // SE-Sendpulse
            "29" => "86", // Freecasinodeal/NZ/olivia
            "30" => "87", // Freecasinodeal/CA/sofia
            "31" => "88", // Freecasinodeal/NO/emma
            "32" => "89", // Freecasinodeal/FI/aida
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
            "1" => "60",  // Australia-camilla 
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