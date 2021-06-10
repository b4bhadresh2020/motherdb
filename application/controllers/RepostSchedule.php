<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RepostSchedule extends CI_Controller
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
        //echo $this->input->get('editId');
        $data = array();
        //get all repost schedule data
        $this->load->model('mdl_repost_schedule');
        $perPage = 20;
        $responseData = $this->mdl_repost_schedule->getRepostscheduleData($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $repostScheduleData = $responseData['repostScheduleData'];
        $data = pagination_data('repostSchedule/addEdit/', $dataCount, $start, 3, $perPage,$repostScheduleData);
        //add provider list array
        if(!empty($data['listArr'])){
            foreach($data['listArr'] as $index=>$curEntry){
                $providerNameArr = $this->mdl_repost_schedule->getProvider(explode(',',$curEntry["providers"]));
                foreach($providerNameArr as $pi => $provider) {
                    $condition = array("providerId" => $provider['id'],'repostScheduleId' => $curEntry['id']);
                    $is_single = TRUE;
                    $getRepostScheduleHistoryData = GetAllRecord(REPOST_SCHEDULE_HISTORY, $condition, $is_single);
                    $repostScheduleTotalSend = (!empty($getRepostScheduleHistoryData)) ? $getRepostScheduleHistoryData['totalSend'] : 0;

                    $providerNameArr[$pi]['listname'] = "<div>- " . $provider['listname'] . " (" . getProviderName($provider['provider']) . ")"."&nbsp&nbsp<span class='send-count'>".$repostScheduleTotalSend."</span></div>";
                }
                $data['listArr'][$index]['providers'] = implode('',array_column($providerNameArr,'listname'));
            }
        }
        //get all apikey 

        // $condition = array('mailProvider' => 'egoi');
        $condition = array("isInActive" => 0);
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array("country","ASC"),array('liveDeliveryId' => 'desc')), 'country,apikey,groupName,keyword,mailProvider,live_status');

        $liveDeliveriesGroups = [];
        foreach ($getLiveDeliveryAllApiKeys as $key => $liveDelivery) {
            $liveDeliveriesGroups[$liveDelivery['country']][] = $liveDelivery;
        }
        $condition = array("isInActive" => 0);
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array("country","ASC"),array('liveDeliveryId' => 'desc')), 'country,apikey,groupName,keyword,mailProvider,live_status');

        $data['apikeys'] = $liveDeliveriesGroups;
        $data['load_page'] = 'repostSchedule';
        $data['headerTitle'] = "repostSchedule";
        $data["curTemplateName"] = "repostSchedule/addEdit";
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
    
    function getProvider()
    {
        $mailProvider = $this->input->post('id');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        echo $providerData['provider'];
    }

    //add schedule to repost schedule
    function addRepostSchedule(){
        $repostScheduleData = $this->input->post();
        $providers = $repostScheduleData['providers'];
        $apikey = $this->input->post('apiKey');
        $deliveryStartDate = date('Y-m-d',strtotime($this->input->post('deliveryStartDate')));
        $deliveryEndDate = date('Y-m-d',strtotime($this->input->post('deliveryEndDate')));
        
        // fetch mail live delivery data from live deliverys table
        $qry = "SELECT liveDeliveryDataId FROM live_delivery_data WHERE apikey = '{$apikey}'  AND emailId != '' AND (sucFailMsgIndex = 0 OR sucFailMsgIndex = 1) AND DATE(createdDate) BETWEEN '{$deliveryStartDate}' AND '{$deliveryEndDate}'  GROUP BY emailId";
        $liveDeliveryData = GetDatabyqry($qry);
        $totalliveDeliveryRecord = count($liveDeliveryData);

        $this->load->model('mdl_repost_schedule');
        $repostScheduleData['providers'] = implode(',',$repostScheduleData['providers']);
        $repostScheduleData['totalRecord'] = $totalliveDeliveryRecord;
        $repostScheduleData['createdDate'] = date('Y-m-d H:i:s');
        $repostScheduleData['updatedDate'] = date('Y-m-d H:i:s');
        $repostScheduleId = $this->mdl_repost_schedule->insertRepostSchedule($repostScheduleData);

        $data['status'] = 'error';
        $data['msg'] = 'Something went wrong!';
        if(!empty($repostScheduleId)){
            $apiQry = "SELECT mailProvider,groupName,keyword FROM live_delivery WHERE apikey = '{$apikey}'";
            $getApiKey = GetDatabyqry($apiQry);
            
            if(!empty($liveDeliveryData)){
               
                $liveDeliveryDataId = array_column($liveDeliveryData,'liveDeliveryDataId');
                foreach($providers as $provider) {
                    // insert record in REPOST_SCHEDULE_HISTORY table
                   
                    $historyData = array(
                        'repostScheduleId' => $repostScheduleId,
                        'providerId' => $provider,
                    );

                    $this->mdl_repost_schedule->addRepostScheduleHistory($historyData);
                    
                    // get provider list code from provider id
                    $providerListCode = getProviderListCode($provider);
                    foreach($liveDeliveryDataId as $liveDataId) {
                        $liverDeliveryData = array(
                            'liveDeliveryDataId' => $liveDataId,
                            'providerId' => $provider,
                            'providerListCode' => $providerListCode,
                            'repostScheduleId'   => $repostScheduleId,
                            'groupName'         =>   ($getApiKey[0]['groupName']) ? $getApiKey[0]['groupName'] : '',
                            'keyword'           => ($getApiKey[0]['keyword']) ? $getApiKey[0]['keyword'] : ''
                        );
                       
                        $this->mdl_repost_schedule->insertRepostScheduleLiveDeliveryData($liverDeliveryData);
                    }
                }
            }
            $data['status'] = 'success';
            $data['msg'] = 'Repost schedule added successfully';
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'Repost schedule not added!';
        }
        echo json_encode($data);
    }

    //update status
    function updateStatus($id,$status){
        //update to live delivery data
        $condition = array('id' => $id);
        $is_insert = FALSE;
        $updateArr = array('status' => $status);
        ManageData(REPOST_SCHEDULE, $condition, $updateArr, $is_insert);
        return redirect('repostSchedule/addEdit');
    }

    function getProviderData(){        
        $providerId = trim($this->input->post('providerId'));
       //get file data
       $condition = array(
           'id' => $providerId
       );
       $is_single = TRUE;
       $providerData = GetAllRecord(CSV_FILE_PROVIDER_DATA,$condition,$is_single,array(),array(),array()); 
       echo json_encode($providerData);
    }

    function getRepostSchedule() {
        $repostScheduleId = trim($this->input->post('repostScheduleId'));
        // get data
        $condition = array(
            'id' => $repostScheduleId
        );
        $is_single = TRUE;
        $repostScheduleData = GetAllRecord(REPOST_SCHEDULE,$condition,$is_single,array(),array(),array());
        echo json_encode($repostScheduleData);
    }

    function updateRepostScheduleData(){         
        $is_insert = FALSE;   
        $condition = array(
            'id' => $this->input->post('id')
        );
        $updateRecord = array(
            'deliveryStartTime' => $this->input->post('deliveryStartTime'),                
            'deliveryEndTime' => $this->input->post('deliveryEndTime'),
            'perDayRecord' => $this->input->post('perDayRecord')                           
        );
        ManageData(REPOST_SCHEDULE,$condition,$updateRecord,$is_insert);
        redirect(site_url().'repostSchedule/addEdit');
     }
}
