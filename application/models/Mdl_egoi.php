<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_egoi extends CI_Model {

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
            $encodedData = http_build_query($fields, '', '&');
            
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
        return array ("Accept: application/json; charset=utf-8");
    }

    private function DecodeResult ($input = '')
    {
        return json_decode($input, TRUE);
    }
    

    function sendDataToEgoi($getData,$country){

        $url = 'http://api.e-goi.com/v2/rest.php';
        $listID = $this->getListId($country);
        $apikey = '4f3d8708f731bbd49da618fa1ec36e67283e5ee3';

        $phone = '';

        if (@$getData['phone'] != '') {
            $country_code = $this->get_country_code($country);
            $phone = $country_code.'-'.$getData['phone'];
        }

        if ($country == 'SE') {

            $params = array(
                    'method' => 'addSubscriber',
                    'type' => 'json',
                    'functionOptions[apikey]' => $apikey,
                    'functionOptions[listID]' => $listID,
                    'functionOptions[email]' => @$getData['emailId'],
                    'functionOptions[first_name]' => @$getData['firstName'],
                    'functionOptions[last_name]' => @$getData['lastName'],
                    'functionOptions[cellphone]' => $phone,
                    'functionOptions[validate_phone]' => 0,
                    'functionOptions[birth_date]' => @$getData['birthDate'],
                    'functionOptions[extra_17]' => @$getData['ip'],
                    'functionOptions[extra_18]' => @$getData['gender'],
                    'functionOptions[extra_19]' => @$getData['address'],
                    'functionOptions[extra_20]' => @$getData['postCode'],
                    'functionOptions[extra_21]' => @$getData['city'],
                    'functionOptions[extra_22]' => $country,
                    'functionOptions[extra_40]' => @$getData['age'],
                    'functionOptions[extra_24]' => @$getData['optinurl'],
                    'functionOptions[extra_25]' => @$getData['optindate']

            );
            
        }else if($country == 'DK'){

            $params = array(
                    'method' => 'addSubscriber',
                    'type' => 'json',
                    'functionOptions[apikey]' => $apikey,
                    'functionOptions[listID]' => $listID,
                    'functionOptions[email]' => @$getData['emailId'],
                    'functionOptions[first_name]' => @$getData['firstName'],
                    'functionOptions[last_name]' => @$getData['lastName'],
                    'functionOptions[cellphone]' => $phone,
                    'functionOptions[validate_phone]' => 0,
                    'functionOptions[birth_date]' => @$getData['birthDate'],
                    'functionOptions[extra_7]' => @$getData['ip'],
                    'functionOptions[extra_26]' => @$getData['gender'],
                    'functionOptions[extra_27]' => @$getData['address'],
                    'functionOptions[extra_28]' => @$getData['postCode'],
                    'functionOptions[extra_29]' => @$getData['city'],
                    'functionOptions[extra_30]' => $country,
                    'functionOptions[extra_41]' => @$getData['age'],
                    'functionOptions[extra_31]' => @$getData['optinurl'],
                    'functionOptions[extra_32]' => @$getData['optindate']

            );

        }else if($country == 'NO'){

            $params = array(
                    'method' => 'addSubscriber',
                    'type' => 'json',
                    'functionOptions[apikey]' => $apikey,
                    'functionOptions[listID]' => $listID,
                    'functionOptions[email]' => @$getData['emailId'],
                    'functionOptions[first_name]' => @$getData['firstName'],
                    'functionOptions[last_name]' => @$getData['lastName'],
                    'functionOptions[cellphone]' => $phone,
                    'functionOptions[validate_phone]' => 0,
                    'functionOptions[birth_date]' => @$getData['birthDate'],
                    'functionOptions[extra_16]' => @$getData['ip'],
                    'functionOptions[extra_33]' => @$getData['gender'],
                    'functionOptions[extra_34]' => @$getData['address'],
                    'functionOptions[extra_35]' => @$getData['postCode'],
                    'functionOptions[extra_36]' => @$getData['city'],
                    'functionOptions[extra_37]' => $country,
                    'functionOptions[extra_42]' => @$getData['age'],
                    'functionOptions[extra_38]' => @$getData['optinurl'],
                    'functionOptions[extra_39]' => @$getData['optindate']

            );

        }

        
        return $this->MakePostRequest($url, $params);
    }

    private function getListId($country){
        
        $ctry = array(
            'DK' => 2, 
            'SE' => 6, 
            'NOR' => 5, 
            'FI' => '', 
            'UK' => '', 
            'DE' => '', 
            'CA' => '', 
            'AU' => '',
            'NL' => ''
        );

        return $ctry[$country];

    }


    private function get_country_code($country){
        $countriesWithCode = array('DK'=>'45','SE'=>'46','NOR'=>'47','FI'=>'358','UK'=>'44','AU'=>'43','DE' => '49','CA' => '1','NL' => '31');
        return $countriesWithCode[$country];
    }



}