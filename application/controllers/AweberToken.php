<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AweberToken extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
        $this->load->model('mdl_inboxgame_db');
        $this->load->model('mdl_felinafinans_db');
    }

    public function index()
    {
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        $aweberAccountCondition   = array();
        $is_single           = false;
        $aweberAccountData   = GetAllRecord(AWEBER_ACCOUNTS, $aweberAccountCondition, $is_single);

        foreach($aweberAccountData as $account){
            // REFRESH TOKEN ON EVERY REQUEST
            $tokenResponse = $client->post(
                AWEBER_TOKEN_URL, [
                    'auth' => [
                        AWEBER_CLIENT_ID, AWEBER_CLIENT_SECRET                ],
                    'json' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $account['refreshToken']
                    ]
                ]
            );
            $tokenResponseBody = $tokenResponse->getBody();
            $newCreds = json_decode($tokenResponseBody, true);
            
            // NEW ACCESS TOKEN AND REFRESH TOKEN 
            $accessToken = $newCreds['access_token'];
            $refreshToken = $newCreds['refresh_token'];

            // UPDATE LATEST TOKEN IN AWEBER ACCOUNT TABLE IN PARICULAR ACOUNT
            $updateCondition   = array(
                'id' => $account['id']
            );
            $updateAweberAccountData = array(
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken
            );

            $is_insert = FALSE;
            ManageData(AWEBER_ACCOUNTS,$updateCondition,$updateAweberAccountData,$is_insert);
            // Inboxgame DB
            $this->mdl_inboxgame_db->ManageData(AWEBER_ACCOUNTS,$updateCondition,$updateAweberAccountData,$is_insert);

            // Felinafinans DB
            $this->mdl_felinafinans_db->ManageData(AWEBER_ACCOUNTS,$updateCondition,$updateAweberAccountData,$is_insert);
        }
    }
}
