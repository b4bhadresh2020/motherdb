<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SendpulseToken extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    public function index()
    {
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        $sendpulseAccountCondition   = array();
        $is_single           = false;
        $sendpulseAccountData   = GetAllRecord(SENDPULSE_ACCOUNTS, $sendpulseAccountCondition, $is_single);

        foreach($sendpulseAccountData as $account){
            // NEW ACCESS TOKEN ON EVERY REQUEST
            $data = array(
                "grant_type" => "client_credentials",
                "client_id"  => $account['client_id'],
                "client_secret" => $account['client_secret']
            );  
            $tokenResponse = $client->post(SENDPULSE_TOKEN_URL,[
                'json' => $data 
            ]);
            $tokenResponseBody = $tokenResponse->getBody();
            $newCreds = json_decode($tokenResponseBody, true);

            // NEW ACCESS TOKEN  
            $accessToken = $newCreds['access_token'];            

            // UPDATE LATEST TOKEN IN SENDPULSE ACCOUNT TABLE IN PARICULAR ACOUNT
            $updateCondition   = array(
                'id' => $account['id']
            );
            $updateSendpulseAccountData = array(
                'accessToken' => $accessToken,
            );

            $is_insert = FALSE;
            ManageData(SENDPULSE_ACCOUNTS,$updateCondition,$updateSendpulseAccountData,$is_insert);

            // // Inboxgame DB
            // $this->mdl_inboxgame_db->ManageData(SENDPULSE_ACCOUNTS,$updateCondition,$updateSendpulseAccountData,$is_insert);

            // // Felinafinans DB
            // $this->mdl_felinafinans_db->ManageData(SENDPULSE_ACCOUNTS,$updateCondition,$updateSendpulseAccountData,$is_insert);
        }
    }
}
