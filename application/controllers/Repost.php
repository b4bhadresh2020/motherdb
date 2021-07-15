<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Repost extends CI_Controller
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

    public function addEdit($start = 0)
    {

        $data = array();
        //get all apikey 

        // $condition = array('mailProvider' => 'egoi');
        $condition = array("isInActive" => 0);
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array("country","ASC"),array('liveDeliveryId' => 'desc')), 'country,apikey,groupName,keyword,mailProvider,live_status');

        $liveDeliveriesGroups = [];
        foreach ($getLiveDeliveryAllApiKeys as $key => $liveDelivery) {
            $liveDeliveriesGroups[$liveDelivery['country']][] = $liveDelivery;
        }

        $data['apikeys'] = $liveDeliveriesGroups;
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
            addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);
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
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword,$apiDataDetail['emailId']);

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
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword,$apiDataDetail['emailId']);
        
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
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response,$groupName, $keyword,$apiDataDetail['emailId']);

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
        $response = $this->mdl_sendgrid->AddEmailToSendgridSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToSendInBlue()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = SENDINBLUE;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_sendinblue');
        $response = $this->mdl_sendinblue->AddEmailToSendInBlueSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToSendpulse()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = SENDPULSE;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_sendpulse');
        $response = $this->mdl_sendpulse->AddEmailToSendpulseSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToMailerlite()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = MAILERLITE;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_mailerlite');
        $response = $this->mdl_mailerlite->AddEmailToMailerliteSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToMailjet()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = MAILJET;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
        $mailjetAccountId     = $providerData['aweber_account']; 

        $mailjetCondition   = array('id' => $mailjetAccountId);
        $is_single           = true;
        $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);

        if($mailjetAccountData['status'] == 1) {
            if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

                $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

                $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }

            $this->load->model('mdl_mailjet');
            $response = $this->mdl_mailjet->AddEmailToMailjetSubscriberList($apiDataDetail, $mailProvider);
            // ADD RECORD IN HISTORY
            addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

            //update to live delivery data
            $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
            $is_insert = FALSE;
            $responseField = $providerData['response_field'];
            $updateArr = array($responseField => json_encode($response));
            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
        }
    }

    function addDataToConvertkit()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = CONVERTKIT;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_convertkit');
        $response = $this->mdl_convertkit->AddEmailToConvertkitSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToMarketingPlatform()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = MARKETING_PLATFORM;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
        $marketingPlatformAccountId     = $providerData['aweber_account']; 

        $marketingPlatformCondition   = array('id' => $marketingPlatformAccountId);
        $is_single           = true;
        $marketingPlatformAccountData   = GetAllRecord(MARKETING_PLATFORM_ACCOUNTS, $marketingPlatformCondition, $is_single);

        if($marketingPlatformAccountData['status'] == 1) {
            if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

                $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

                $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }

            $this->load->model('mdl_marketing_platform');
            $response = $this->mdl_marketing_platform->AddEmailToMarketingPlatformSubscriberList($apiDataDetail, $mailProvider);
            // ADD RECORD IN HISTORY
            addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

            //update to live delivery data
            $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
            $is_insert = FALSE;
            $responseField = $providerData['response_field'];
            $updateArr = array($responseField => json_encode($response));
            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
        }
    }

    function addDataToOntraport()
    {
        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = ONTRAPORT;
        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {
            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];
            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }
        $this->load->model('mdl_ontraport');
        $response = $this->mdl_ontraport->AddEmailToOntraportSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);
        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToActiveCampaign()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = ACTIVE_CAMPAIGN;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_active_campaign');
        $response = $this->mdl_active_campaign->AddEmailToActiveCampaignSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToExpertSender()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = EXPERT_SENDER;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_expert_sender');
        $response = $this->mdl_expert_sender->AddEmailToExpertSenderSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

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

    function addDataToCleverReach()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = CLEVER_REACH;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

            $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

            $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }

        $this->load->model('mdl_clever_reach');
        $response = $this->mdl_clever_reach->AddEmailToCleverReachSubscriberList($apiDataDetail, $mailProvider);
        // ADD RECORD IN HISTORY
        addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

        //update to live delivery data
        $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
        $is_insert = FALSE;
        $responseField = $providerData['response_field'];
        $updateArr = array($responseField => json_encode($response));
        ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    }

    function addDataToOmnisend()
    {

        $apiDataDetail = $this->input->post('apiDataDetail');
        $mailProvider = $this->input->post('provider');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $provider = OMNISEND;

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
        $omnisendAccountId     = $providerData['aweber_account']; 

        $omnisendCondition   = array('id' => $omnisendAccountId);
        $is_single           = true;
        $omnisendAccountData   = GetAllRecord(OMNISEND_ACCOUNTS, $omnisendCondition, $is_single);

        if($omnisendAccountData['status'] == 1) {
            if (@$apiDataDetail['birthdateDay'] != '' && @$apiDataDetail['birthdateMonth'] != '' && @$apiDataDetail['birthdateYear'] != '') {

                $birthDate = $apiDataDetail['birthdateYear'] . '-' . $apiDataDetail['birthdateMonth'] . '-' . $apiDataDetail['birthdateDay'];

                $apiDataDetail['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }

            $this->load->model('mdl_omnisend');
            $response = $this->mdl_omnisend->AddEmailToOmnisendSubscriberList($apiDataDetail, $mailProvider);
            // ADD RECORD IN HISTORY
            addRecordInHistory($apiDataDetail, $mailProvider, $provider, $response, $groupName, $keyword,$apiDataDetail['emailId']);

            //update to live delivery data
            $condition = array('liveDeliveryDataId' => $apiDataDetail['liveDeliveryDataId']);
            $is_insert = FALSE;
            $responseField = $providerData['response_field'];
            $updateArr = array($responseField => json_encode($response));
            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
        }
    }
}
