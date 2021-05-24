<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MailProviderStatistics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
    }

    public function index()
    {

        $data['load_page'] = 'mailProviderStatistics';
        $data['headerTitle'] = "Mail Provider Statistics";
        $data["curTemplateName"] = "mailProviderStatistics/report";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getMailProviderData()
    {
        
        $provider = $this->input->post('provider');
        $list = $this->input->post('list');
        $deliveryDate = $this->input->post('deliveryDate');
        
        $providerStatisticalData = [];
        
        $response_messages = [
            "1" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "403 -","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "2" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "Service Unavailable", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "3" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "Wrong provider", "manual" => "already served"],
            "4" => [ "success" => "success", "subscriber_exist" => "412 " ,  "auth_fail" => "401 -", "bad_fail" => "429 ", "blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "5" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "6" => [ "success" => "success", "subscriber_exist" => "Contact already exist", "auth_fail" => "401 -", "bad_fail" => "Invalid phone number", "blacklisted" => "blacklisted", "host" => "Request already received", "manual" => "already served"],
            "7" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "8" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "9" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "10" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "11" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
            "12" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "401 -", "bad_fail" => "Bad Request","blacklisted" => "blacklisted", "host" => "resolve host", "manual" => "already served"],
        ];

        //get provider detail
        $condition = array("id" => $list);
        $is_single = TRUE;
        $providerListDetail = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array(), 'provider,response_field');
        
        //get all apikey 
        $condition = array("isInActive" => 0);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(array("mailProvider" => '"'.$list.'"')), array(), array(), 'apikey,mailProvider,delay');

        $liveDeliveryInstant = [];
        $liveDeliveryDelay = [];

        foreach ($liveDeliveries as $liveDelivery) {
            if(!empty($liveDelivery['delay'])){
                $delays = json_decode($liveDelivery['delay'],true);
                if($delays[$list] == 0){
                    $liveDeliveryInstant[] = $liveDelivery['apikey'];
                }else{
                    $liveDeliveryDelay[] = $liveDelivery['apikey'];
                }
            }else{
                $liveDeliveryInstant[] = $liveDelivery['apikey'];
            }
        }

        // get data from delay table.
        $delayTableName = "";
        switch($providerListDetail['provider']){
            case 1:
                $delayTableName = AWEBER_DELAY_USER_DATA;
                break;
            case 2:
                $delayTableName = TRANSMITVIA_DELAY_USER_DATA;
                break;
            case 3:
                $delayTableName = CONTACT_DELAY_USER_DATA;
                break;
            case 4:
                $delayTableName = ONGAGE_DELAY_USER_DATA;
                break;
            case 5:
                $delayTableName = SENDGRID_DELAY_USER_DATA;
                break;
            case 6:
                $delayTableName = SENDINBLUE_DELAY_USER_DATA;
                break; 
            case 7:
                $delayTableName = SENDPULSE_DELAY_USER_DATA;
                break;
            case 8:
                $delayTableName = MAILERLITE_DELAY_USER_DATA;
                break;
            case 9:
                $delayTableName = MAILJET_DELAY_USER_DATA;
                break;
            case 10:
                $delayTableName = CONVERTKIT_DELAY_USER_DATA;
                break;
            case 11:
                $delayTableName = MARKETING_PLATFORM_DELAY_USER_DATA;
                break;
            case 12:
                $delayTableName = ONTRAPORT_DELAY_USER_DATA;
                break;
        }
        
        if(count($liveDeliveryInstant)>0){
            // GET DATA FROM LIVE DELIVERY DATA TABLE
            $this->db->select('apikey,groupName,keyword,
                                count(*) as total,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['success'].'%" THEN 1
                                    ELSE 0
                                END) AS success,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['subscriber_exist'].'%" THEN 1
                                    ELSE 0
                                END) AS subscriber_exist,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['auth_fail'].'%" THEN 1
                                    ELSE 0
                                END) AS auth_fail,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['bad_fail'].'%" THEN 1
                                    ELSE 0
                                END) AS bad_fail,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['blacklisted'].'%" THEN 1
                                    ELSE 0
                                END) AS blacklisted,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['host'].'%" THEN 1
                                    ELSE 0
                                END) AS host,
                                SUM(CASE 
                                    WHEN '.$providerListDetail["response_field"].' like "%'.$response_messages[$providerListDetail['provider']]['manual'].'%" THEN 1
                                    ELSE 0
                                END) AS manual');
            $this->db->from(LIVE_DELIVERY_DATA);
            $this->db->where_in("apikey",$liveDeliveryInstant);
            $this->db->where("DATE(createdDate)",$deliveryDate);
            $this->db->group_by("apikey");
            $liveDeliveryInstantData = $this->db->get()->result_array();
        }else{
            $liveDeliveryInstantData = [];
        }    
        
        if(count($liveDeliveryDelay)){
            // GET DATA FROM LIVE DELIVERY DATA TABLE
            $this->db->select('apikey,groupName,keyword,
                                count(*) as total,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['success'].'%" THEN 1
                                    ELSE 0
                                END) AS success,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['subscriber_exist'].'%" THEN 1
                                    ELSE 0
                                END) AS subscriber_exist,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['auth_fail'].'%" THEN 1
                                    ELSE 0
                                END) AS auth_fail,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['bad_fail'].'%" THEN 1
                                    ELSE 0
                                END) AS bad_fail,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['blacklisted'].'%" THEN 1
                                    ELSE 0
                                END) AS blacklisted,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['host'].'%" THEN 1
                                    ELSE 0
                                END) AS host,
                                SUM(CASE 
                                    WHEN response like "%'.$response_messages[$providerListDetail['provider']]['manual'].'%" THEN 1
                                    ELSE 0
                                END) AS manual');
            $this->db->from($delayTableName);
            $this->db->join(LIVE_DELIVERY_DATA,LIVE_DELIVERY_DATA.'.liveDeliveryDataId ='.$delayTableName.'.liveDeliveryDataId');
            $this->db->where_in("apikey",$liveDeliveryDelay);
            $this->db->where("deliveryDate",$deliveryDate);
            $this->db->where("providerId",$list);
            $this->db->group_by("apikey");
            $liveDeliveryDelayData = $this->db->get()->result_array();
        }else{
            $liveDeliveryDelayData = [];
        }

        if(count($liveDeliveryInstantData) > 0 && count($liveDeliveryDelayData) > 0){
            $liveDeliveryStastic = array_merge($liveDeliveryInstantData,$liveDeliveryDelayData);
        }else if(count($liveDeliveryInstantData) > 0){
            $liveDeliveryStastic = $liveDeliveryInstantData;
        }else if(count($liveDeliveryDelayData) > 0){
            $liveDeliveryStastic = $liveDeliveryDelayData;
        }else{
            $liveDeliveryStastic = [];
        }

        $data['load_page'] = 'mailProviderStatistics';
        $data['headerTitle'] = "Mail Provider Statistics";
        $data['curTemplateName'] = "mailProviderStatistics/report";
        $data['liveDeliveryStastic'] = $liveDeliveryStastic;
        $data['provider'] = $provider;
        $data['list'] = $list;
        $data['deliveryDate'] = $deliveryDate;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getProviderList(){
        $provider = $this->input->post("provider");
        $condition = array("provider" => $provider);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array(), 'id,listname');
        echo json_encode($liveDeliveries);
    }
}
