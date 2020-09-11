<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_warriors_sms_provider extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
   

    private function MakePostRequest ($url = "", $fields = array())
    {

        try
        {
            // open connection
            $ch = curl_init();
            
            $postData = "";
            foreach($fields as $key => $field){
                $postData .= $key.'='.$field.'&';
            }
            $postData = substr($postData, 0, -1);

            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
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

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }

    
    public function send_sms_with_use_of_warriors_sms($params) {

        if (strtolower($_SERVER['HTTP_HOST']) == 'localhost') {
            $hostName = "http://localhost/motherdb/";
        } else {
            $hostName = "https://suprdat.dk/";
        }
        
        $url = 'https://api.securesmswarriors.com/v02/smsapi/index.php';
        $callback_url = urlencode($hostName.'callback_url_warriors_sms?uniqueKey='.$params['uniqueKey'].'&source='.$hostName);

        unset($params['uniqueKey']);

        $params["key"] = "45DEF8B46756B5";        
        $params["callback_url"] = $callback_url;  
        $params["notification"] = "0";
        

        return $this->MakePostRequest($url, $params);
    }
}