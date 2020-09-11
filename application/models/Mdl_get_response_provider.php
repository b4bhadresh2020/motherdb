<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_get_response_provider extends CI_Model {

	public function __construct() {
        parent::__construct();
    }


    private function MakePostRequest ($url = "", $fields = array(),$apikey)
    {

        try
        {
            // open connection
            $ch = curl_init();
            
            // add the setting to the fields
            // $data = array_merge($fields, $this->settings);
            $encodedData = json_encode($fields);
            
            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader($apikey));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // disable for security
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            
            // execute post
            $result = curl_exec($ch);
            // close connection
            curl_close($ch);
            
            return $this->DecodeResult($result);
        }
        catch(Exception $error)
        {
            return $error->GetMessage();
        }
    }


    private function GetHTTPHeader ($apikey)
    {   

        return array (
            "Accept: application/json; charset=utf-8",
            "Content-Type:application/json",
            "X-Auth-Token: api-key ".$apikey
        );
    }

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }
    

    function send_data_to_get_response($getData,$mailProvider){



        $url = 'https://api.getresponse.com/v3/contacts';
        //$campaignId = $this->getCampaignId($mailProvider);
        $apikey = $this->getApiKey($mailProvider);
        $emailId = $getData['emailId'];
        $ipAddress = @$getData['ip'];
        $timeStamp = time();

        if (trim($ipAddress) == '') {
            $ipAddress = '0.0.0.0';
        }

        if ($mailProvider == 'coregcasino_finland_activel') {

            $financingNeed = @$getData['financingNeed'];
            $phone = @$getData['phone'];
            $firstName = @$getData['firstName'];
            $lastName = @$getData['lastName'];

            $name = $firstName.' '.$lastName;

            if (trim($financingNeed) == '') {
                $financingNeed = '-';
            }

            if (trim($name) == '') {
                //get name from email
                $name = strstr($emailId, '@', true); 
                $firstName = '-';
                $lastName = '-';
            }

            if (trim($phone) == '') {
                $phone = '-';
            }

            $params = array(

                "name" => $name,
                "campaign" => array('campaignId' => '84QGo'),
                "email" => $emailId,
                "ipAddress" => @$ipAddress,
                "customFieldValues" => array(
                    array(
                        "customFieldId" => "VT9pgF", //ex : kL6Nh
                        "value" => array($financingNeed)
                    ),array(
                        "customFieldId" => "VT9n6J", //ex : kL6Nh
                        "value" => array($firstName)
                    ),array(
                        "customFieldId" => "VT9n8Z", //ex : kL6Nh
                        "value" => array($lastName)
                    ),array(
                        "customFieldId" => "VT9TGo", //ex : kL6Nh
                        "value" => array($timeStamp)
                    ),array(
                        "customFieldId" => "VT9TX1", //ex : kL6Nh
                        "value" => array($phone)
                    ),
                )
            );


            
        }else if($mailProvider == 'felina_dk_accepted'){

            $financingNeed = @$getData['financingNeed'];
            $phone = @$getData['phone'];
            $firstName = @$getData['firstName'];
            $lastName = @$getData['lastName'];

            $name = $firstName.' '.$lastName;

            if (trim($financingNeed) == '') {
                $financingNeed = '-';
            }

            if (trim($name) == '') {
                //get name from email
                $name = strstr($emailId, '@', true); 
                $firstName = '-';
                $lastName = '-';
            }

            if (trim($phone) == '') {
                $phone = '-';
            }

            $params = array(

                "name" => $name,
                "campaign" => array('campaignId' => '86gHH'),
                "email" => $emailId,
                "ipAddress" => @$ipAddress,
                "customFieldValues" => array(
                    array(
                        "customFieldId" => "VT98lm", //ex : kL6Nh
                        "value" => array($financingNeed)
                    ),array(
                        "customFieldId" => "VT98wn", //ex : kL6Nh
                        "value" => array($firstName)
                    ),array(
                        "customFieldId" => "VT989O", //ex : kL6Nh
                        "value" => array($lastName)
                    ),array(
                        "customFieldId" => "VT9871", //ex : kL6Nh
                        "value" => array($timeStamp)
                    ),array(
                        "customFieldId" => "VT98TN", //ex : kL6Nh
                        "value" => array($phone)
                    ),
                )
            );

            
        }else if($mailProvider == 'felina_dk_rejected'){

            $financingNeed = @$getData['financingNeed'];
            $phone = @$getData['phone'];
            $firstName = @$getData['firstName'];
            $lastName = @$getData['lastName'];

            $name = $firstName.' '.$lastName;

            if (trim($financingNeed) == '') {
                $financingNeed = '-';
            }

            if (trim($name) == '') {
                //get name from email
                $name = strstr($emailId, '@', true); 
                $firstName = '-';
                $lastName = '-';
            }

            if (trim($phone) == '') {
                $phone = '-';
            }

            $params = array(

                "name" => $name,
                "campaign" => array('campaignId' => '86ghq'),
                "email" => $emailId,
                "ipAddress" => @$ipAddress,
                "customFieldValues" => array(
                    array(
                        "customFieldId" => "VT98lm", //ex : kL6Nh
                        "value" => array($financingNeed)
                    ),array(
                        "customFieldId" => "VT98wn", //ex : kL6Nh
                        "value" => array($firstName)
                    ),array(
                        "customFieldId" => "VT989O", //ex : kL6Nh
                        "value" => array($lastName)
                    ),array(
                        "customFieldId" => "VT9871", //ex : kL6Nh
                        "value" => array($timeStamp)
                    ),array(
                        "customFieldId" => "VT98TN", //ex : kL6Nh
                        "value" => array($phone)
                    ),
                )
            );

        }else if($mailProvider == 'unelmalaina_accepted'){

            $financingNeed = @$getData['financingNeed'];
            $phone = @$getData['phone'];
            $firstName = @$getData['firstName'];
            $lastName = @$getData['lastName'];

            $name = $firstName.' '.$lastName;

            if (trim($financingNeed) == '') {
                $financingNeed = '-';
            }

            if (trim($name) == '') {
                //get name from email
                $name = strstr($emailId, '@', true); 
                $firstName = '-';
                $lastName = '-';
            }

            if (trim($phone) == '') {
                $phone = '-';
            }

            $params = array(

                "name" => $name,
                "campaign" => array('campaignId' => '86w50'),
                "email" => $emailId,
                "ipAddress" => @$ipAddress,
                "customFieldValues" => array(
                    array(
                        "customFieldId" => "VT98lm", //ex : kL6Nh
                        "value" => array($financingNeed)
                    ),array(
                        "customFieldId" => "VT98wn", //ex : kL6Nh
                        "value" => array($firstName)
                    ),array(
                        "customFieldId" => "VT989O", //ex : kL6Nh
                        "value" => array($lastName)
                    ),array(
                        "customFieldId" => "VT9871", //ex : kL6Nh
                        "value" => array($timeStamp)
                    ),array(
                        "customFieldId" => "VT98TN", //ex : kL6Nh
                        "value" => array($phone)
                    ),
                )
            );

        }else if($mailProvider == 'unelmalaina_rejected'){

            $financingNeed = @$getData['financingNeed'];
            $phone = @$getData['phone'];
            $firstName = @$getData['firstName'];
            $lastName = @$getData['lastName'];

            $name = $firstName.' '.$lastName;

            if (trim($financingNeed) == '') {
                $financingNeed = '-';
            }

            if (trim($name) == '') {
                //get name from email
                $name = strstr($emailId, '@', true); 
                $firstName = '-';
                $lastName = '-';
            }

            if (trim($phone) == '') {
                $phone = '-';
            }

            $params = array(

                "name" => $name,
                "campaign" => array('campaignId' => '8nmtN'),
                "email" => $emailId,
                "ipAddress" => @$ipAddress,
                "customFieldValues" => array(
                    array(
                        "customFieldId" => "VT98lm", //ex : kL6Nh
                        "value" => array($financingNeed)
                    ),array(
                        "customFieldId" => "VT98wn", //ex : kL6Nh
                        "value" => array($firstName)
                    ),array(
                        "customFieldId" => "VT989O", //ex : kL6Nh
                        "value" => array($lastName)
                    ),array(
                        "customFieldId" => "VT9871", //ex : kL6Nh
                        "value" => array($timeStamp)
                    ),array(
                        "customFieldId" => "VT98TN", //ex : kL6Nh
                        "value" => array($phone)
                    ),
                )
            );

        }

        
        return $this->MakePostRequest($url,$params,$apikey);
    }

    function getCampaignId($mailProvider){

        $campaignIdArr = array(
            'coregcasino_finland_activel' => '84QGo',
            'felina_dk_accepted' => '86gHH',
            'felina_dk_rejected' => '86ghq',
            'unelmalaina_accepted' => '86w50',
            'unelmalaina_rejected' => '8nmtN',
        );

        return $campaignIdArr[$mailProvider];
    }


    function getApiKey($mailProvider){

        $apikeyArr = array(
            'coregcasino_finland_activel' => 'dgt1essmr06lvp49n97sd5ecx8b2qj56',
            'felina_dk_accepted' => 'kmjguy8xb4z9segkwf0hvcpqawrk37mc',
            'felina_dk_rejected' => 'kmjguy8xb4z9segkwf0hvcpqawrk37mc',
            'unelmalaina_accepted' => 'kmjguy8xb4z9segkwf0hvcpqawrk37mc',
            'unelmalaina_rejected' => 'kmjguy8xb4z9segkwf0hvcpqawrk37mc',
        );

        return $apikeyArr[$mailProvider];
    }

}