<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LiveDelieryStatistics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged())
            redirect(base_url());
    }

    public function chart()
    {
        $data = array();

        //get all apikey 
        $condition = array();
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(), 'apikey,groupName,keyword,mailProvider');

        $data['apikeys'] = $getLiveDeliveryAllApiKeys;
        $data['load_page'] = 'liveDeliveryStatistics';
        $data['headerTitle'] = "Live Delivery Statistics";
        $data["curTemplateName"] = "LiveDelieryStatistics/chart";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    // //ajax call
    // function getApiKeyData()
    // {

    //     $apikey = $this->input->post('apikey');

    //     $apiQry = "SELECT mailProvider,groupName,keyword FROM live_delivery WHERE apikey = '{$apikey}'";
    //     $getApiKey = GetDatabyqry($apiQry);

    //     $qry = "SELECT * FROM live_delivery_data WHERE apikey = '{$apikey}' AND emailId != '' AND (sucFailMsgIndex = 0 OR sucFailMsgIndex = 1) GROUP BY emailId";
    //     $getApiKeyData = GetDatabyqry($qry);

    //     $response = array();
    //     if (count($getApiKeyData) > 0) {

    //         $response['err'] = 0;
    //         $response['provider'] = $getApiKey[0]['mailProvider'];
    //         $response['groupName'] = $getApiKey[0]['groupName'];
    //         $response['keyword'] = $getApiKey[0]['keyword'];
    //         $response['apiData'] = $getApiKeyData;
    //     } else {

    //         $response['err'] = 1;
    //         $response['provider'] = $getApiKey[0]['mailProvider'];
    //         $response['msg'] = 'No Data Available for this Api Key';
    //     }

    //     echo json_encode($response);
    // }

    function getMailProviderData()
    {
        $apikey = $this->input->post('apikey');

        // fetch mail provider data from providers table
        $providerCondition   = array('apikey' => $apikey);
        $is_single           = true;
        $providerData        = GetAllRecord(LIVE_DELIVERY, $providerCondition, $is_single, [], [], [], 'mailProvider,delay');
        echo "<pre>";
        print_r($providerData);
        die;
        echo $providerData['provider'];
    }
}
