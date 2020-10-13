<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Repost extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged())
            redirect(base_url());
    }

    public function addEdit($start = 0)
    {

        $data = array();
        //get all apikey 

        // $condition = array('mailProvider' => 'egoi');
        $condition = array();
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(), 'apikey,groupName,keyword,mailProvider');

        $data['apikeys'] = $getLiveDeliveryAllApiKeys;
        $data['load_page'] = 'repost';
        $data['headerTitle'] = "Repost";
        $data["curTemplateName"] = "repost/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    //ajax call
    function getApiKeyData()
    {

        $apikey = $this->input->post('apikey');

        $apiQry = "SELECT mailProvider,groupName,keyword FROM live_delivery WHERE apikey = '{$apikey}'";
        $getApiKey = GetDatabyqry($apiQry);

        $qry = "SELECT * FROM live_delivery_data WHERE apikey = '{$apikey}' AND emailId != '' AND (sucFailMsgIndex = 0 OR sucFailMsgIndex = 1) GROUP BY emailId";
        $getApiKeyData = GetDatabyqry($qry);

        $response = array();
        if (count($getApiKeyData) > 0) {

            $response['err'] = 0;
            $response['provider'] = $getApiKey[0]['mailProvider'];
            $response['groupName'] = $getApiKey[0]['groupName'];
            $response['keyword'] = $getApiKey[0]['keyword'];
            $response['apiData'] = $getApiKeyData;
        } else {

            $response['err'] = 1;
            $response['provider'] = $getApiKey[0]['mailProvider'];
            $response['msg'] = 'No Data Available for this Api Key';
        }

        echo json_encode($response);
    }



    function addDataToEgoi()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $country = $apiDataDetail['country'];

        $validCountryForEgoi = countryThasListedInEgoi();

        if (in_array(strtoupper($country), $validCountryForEgoi)) {

            if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

                $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

                $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }

            $this->load->model('mdl_egoi');
            $response = $this->mdl_egoi->sendDataToEgoi($apiDataDetail, $country);
        } else {
            $response = array("result" => "error", "error" => array("msg" => "Country is not defined in E-goi"));
        }

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $updateArr = array('eGoiResponse' => json_encode($response));

        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToAweber()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $country = $apiDataDetail['country'];
        $provider = AWEBER;
        $validCountryForAweber = countryThasListedInAweber();

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (in_array(strtoupper($country), $validCountryForAweber)) {

            if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

                $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

                $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }

            $this->load->model('mdl_aweber');
            $response = $this->mdl_aweber->AddEmailToAweberSubscriberList($apiDataDetail, $country, $mailProvider);
            // ADD RECORD IN HISTORY
            addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword);
        } else {
            $response = array("result" => "error", "error" => array("msg" => "Country is not defined in Aweber"));
        }

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToConstantContact()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = CONSTANTCONTACT;
        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        $this->load->model('mdl_constantcontact');
        $response = $this->mdl_constantcontact->AddEmailToContactSubscriberList($apiDataDetail, $mailProvider);

        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToTransmitvia()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = TRANSMITVIA;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        $this->load->model('mdl_transmitvia');
        $response = $this->mdl_transmitvia->AddEmailToTransmitSubscriberList($apiDataDetail, $providerData['code']);

        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword);
        
        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToOngage()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = ONGAGE;
        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        $this->load->model('mdl_ongage');
        $response = $this->mdl_ongage->AddEmailToOngageSubscriberList($apiDataDetail, $mailProvider);

        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToSendgrid()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = SENDGRID;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_sendgrid');
        $response = $this->mdl_aweber->AddEmailToSendgridSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function getProvider()
    {
        $mailProvider = $this->input->post('id');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        echo $providerData['provider'];
    }
}
