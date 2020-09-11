<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sms_edge_sms_provider extends CI_Model {

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
            $encodedData = http_build_query($fields, '', '&'); //when uses query_string

            //$encodedData = json_encode($fields);
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

        return array (
            "content-type: application/x-www-form-urlencoded"
        );

    }

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }



    public function send_sms_with_use_of_sms_edge($params) {
        
        $url = 'https://api.smsedge.com/v1/sms/send-single/';
        $params['url'] = "https://api.smsedge.com/v1/sms/send-single/";
        $params['api_key'] = 'L_dZ1ZUvlqktWIrlH6'; 
        return $this->MakePostRequest($url, $params);
    }


    
}