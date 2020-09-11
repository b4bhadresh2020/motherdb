<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
use GuzzleHttp\Exception\ClientException;

class Mdl_ongage extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
        

    }

    function AddEmailToOngageSubscriberList($getData,$ongageListId){
       
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $ongageListId);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);       

        try {           
            
            $ongageAccountId  = $providerData['aweber_account'];           
            $listId = $providerData['code'];

            // Get ongage account details.
            $ongageAccountCondition   = array('id' => $ongageAccountId);
            $is_single           = true;
            $ongageAccountData   = GetAllRecord(ONGAGE_ACCOUNTS, $ongageAccountCondition, $is_single);
           
            // LOG ENTRY
            $logPath    = FCPATH."log/ongage/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $providerData['aweber_account']." ".$providerData['listname']." ".$getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $data = array(                
                'email' => $getData['emailId'],
                'first_name' => $getData['firstName'],
                'last_name' => $getData['lastName'],
                'gender' => $getData['gender'],
                'phone' => $getData['phone'],
                'country' => $getData['country'],
                'address' => $getData['address']." ".$getData['city']." ".$getData['postCode'],
            );

            $body = $client->post(ONGAGE_API_PATH.$listId.ONGAGE_API_CONTACT_PATH, [
                    'json' => $data, 
                    'headers' => [
                        'X_USERNAME' => $ongageAccountData['email_id'],
                        'X_PASSWORD' => $ongageAccountData['email_password'],
                        'X_ACCOUNT_CODE' => $ongageAccountData['account_code']
                    ]
            ]);
            
            $responseBody = json_decode($body->getBody(true));
            $subscriberID = $responseBody->payload->_id;
            return array("result" => "success","data" => array("id" => $subscriberID));
    
            // catch any exceptions thrown during the process and print the errors to screen
        } catch (ClientException $exception) {
            $responseBody = json_decode($exception->getResponse()->getBody(true));
            $errorCode = $responseBody->payload->code;
            $errorMessage = $responseBody->payload->errors->error_message;
            $responseMessage = $errorCode." ".$errorMessage;
            return array("result" => "error","error" => array("msg" => $responseMessage));
        }
    }
}