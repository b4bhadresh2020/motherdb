<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_forty_two_sms_provider extends CI_Model {

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
                "Authorization: " . $settings['Authorization']
        );

    }

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }

    private function getSettings(){

        $settings = array();
        //authorization token for forty two sms provider
        $settings['Authorization'] = 'Token 75a94f7e-b409-4dc4-bda7-32071aa3c503';  //Token bcdd900c-79f3-4b8d-8bbc-6ef30c3d7ea8

        return $settings;
    }



    public function send_sms_with_use_of_forty_two($params) {
        
        $url = 'https://rest.fortytwo.com/1/sms';
        return $this->MakePostRequest($url, $params);
    }


    
}