<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mdl_mailjet extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        require_once(FCPATH . 'vendor/autoload.php');
    }


    function AddEmailToMailjetSubscriberList($getData, $mailjetListId)
    {

        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailjetListId);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
        $mailjetAccountId     = $providerData['aweber_account'];

        $mailjetCondition   = array('id' => $mailjetAccountId);
        $is_single           = true;
        $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);
        $api_key = $mailjetAccountData['api_key'];
        $secret_key = $mailjetAccountData['secret_key'];

        //LIST ID 
        $list_id = $providerData['code'];

        // Find tag value
        if (isset($getData["otherLable"]) && isset($getData["other"])) {
            $otherLabel = json_decode($getData["otherLable"]);
            $otherData = json_decode($getData["other"]);

            $searchIndex = array_search("Tag", $otherLabel, true);
            if ($searchIndex !== FALSE) {
                $tagValue = $otherData[$searchIndex];
            } else {
                $tagValue = "";
            }
        } else if (isset($getData["tag"])) {
            $tagValue = $getData["tag"];
        } else {
            $tagValue = "";
        }

        // LOG ENTRY
        $logPath    = FCPATH . "log/mailjet/";
        $fileName   = date("Ymd") . "_log.txt";
        $logFile    = fopen($logPath . $fileName, "a");
        $logData    = $getData['emailId'] . " " . $getData['firstName'] . " " . $getData['lastName'] . " " . time() . "\n";
        fwrite($logFile, $logData);
        fclose($logFile);

        try {

            $body = [
                "Name" => @$getData['firstName'] . ' ' . @$getData['lastName'],
                "Action" => "addnoforce",
                "Email" => @$getData['emailId'],
                "Properties" => []
            ];

            if (!empty($getData['firstName'])) {
                $body['Properties']['name'] = @$getData['firstName'] . ' ' . @$getData['lastName'];
                $body['Properties']['firstname'] = @$getData['firstName'];
            }
            if (!empty($getData['lastName'])) {
                $body['Properties']['lastname'] = @$getData['lastName'];
            }
            if (!empty($getData['phone'])) {
                $body['Properties']['phone'] = @$getData['phone'];
            }
            if (!empty($getData['gender'])) {
                $body['Properties']['gender'] = @$getData['gender'];
            }
            if (!empty($getData['address'])) {
                $body['Properties']['address'] = @$getData['address'];
            }
            if (!empty($getData['postCode'])) {
                $body['Properties']['postcode'] = @$getData['postCode'];
            }
            if (!empty($getData['city'])) {
                $body['Properties']['city'] = @$getData['city'];
            }
            if (!empty($getData['birthDate'])) {
                $body['Properties']['birthdate'] = @$getData['birthDate'] . ' 00:00:00';
            }
            if (!empty($tagValue)) {
                $body['Properties']['tag'] = $tagValue;
            }
            $bodyData = json_encode($body);

            try {
                // previos: https://api.mailjet.com/v3/REST/contact/managemanycontacts
                $newsubscriberUrl = "https://api.mailjet.com/v3/REST/contactslist/" . $list_id . "/managecontact";
                $body = $client->post($newsubscriberUrl, [
                    'body' => $bodyData,
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'auth' => [
                        $api_key, $secret_key
                    ]
                ]);
                $responseCode = $body->getStatusCode();
                $subscriber = json_decode($body->getBody(), true);

                if ($responseCode == "201") {
                    $ContactID = $subscriber['Data'][0]['ContactID']; // previous: JobID
                    return array("result" => "success", "data" => array("id" => $ContactID, "isContactID" => true)); //isContactID: false (JobID)
                } else if ($responseCode == "401") {
                    return array("result" => "error", "error" => array("msg" => "Unauthorized"));
                } else {
                    return array("result" => "error", "error" => array("msg" => "Unknown Error Response"));
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
                if (strpos($errorMsg, "OpenSSL SSL_connect") !== false) {
                    return array("result" => "error", "error" => array("msg" => "Account is temporary closed by ESP"));
                } else {
                    return array("result" => "error", "error" => array("msg" => $errorMsg));
                }
            }

            // catch any exceptions thrown during the process and print the errors to screen
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode == "400") {
                return array("result" => "error", "error" => array("msg" => $statusCode . " - Bad Request"));
            } else {
                return array("result" => "error", "error" => array("msg" => $statusCode . " - Subscriber already subscribed"));
            }
        }
    }
}
