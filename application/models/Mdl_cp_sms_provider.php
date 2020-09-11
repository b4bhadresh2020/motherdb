<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_cp_sms_provider extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    

    private function MakePostRequest ($url = "", $fields = array())
    {

        try
        {
            // open connection
            $ch = curl_init();
            
            // add the setting to the fields
            // $data = array_merge($fields, $this->settings);
            //$encodedData = http_build_query($fields, '', '&'); when use query_string
            
            $encodedData = json_encode($fields);
            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHTTPHeader());
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


    private function GetHTTPHeader ()
    {
        $settings = $this->getSettings();

        return array (
            "Content-Type: application/json; charset=utf-8",
            "authorization: Basic ".$settings['apiKey']
        );

    }

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }


    /*
    *   apiKey  = base64_encode($username . ':' . $apiKey)
    *
    *   Or you can simply use "Base64 encode" as apiKey from account
    */

    private function getSettings(){

        $settings = array();
        //authorization token for cp sms provider
        $settings['username'] = 'cmenetwork'; 
        $settings['apiKey'] = "Y21lbmV0d29yazphODQ1ZDIyYy1hOWU0LTRmNmUtOGE0Ni05OTJjMTVlZmZlZTY=";  

        return $settings;
    }



    public function send_sms_with_use_of_cp_sms($params) {
        
        $url = 'https://api.cpsms.dk/v2/send';
        return $this->MakePostRequest($url, $params);
    }


    
}