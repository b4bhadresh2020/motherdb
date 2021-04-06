<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Re_tracking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_user_re_tracking');
        $this->load->model('mdl_history');
    }


    /*
        - ajax
        - get total data
    */
    
    public function getReTrackClickerDataCount(){

        $postData = $_POST;
        $countArr = $this->mdl_user_re_tracking->getUserReTrackingDataCount($postData);
        echo json_encode($countArr);
    }

    /*
        ajax
        - process data for with merge tag and without merge tag
    */  

    function re_track_process_user_data(){

        $postData = $this->input->post();
        $campaignId = $postData['campaignName'];
        $start = $postData['re_track_process_start'];
        $perPage = $postData['re_track_process_per_page'];
        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];
        $is_set_except_days = $postData['is_set_except_days'];

        $modelResponse = $this->mdl_user_re_tracking->get_re_track_user_process_data($postData, $start, $perPage);

        $userData   = $modelResponse['userData'];
        $intervalCount = $modelResponse['intervalCount'];
        
        if ($start == 0 && $is_set_except_days == 0) {

            $this->mdl_user_tracking->addInSmsHistoryTable($postData);
            $this->mdl_user_tracking->editIsNewInCampaignTable($campaignId);

            //add data in history table
            if ($csvType == 1) {

                $fileName = 'userdata_with_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'with_merge';

            }else if($csvType == 2){

                $fileName = 'userdata_with_out_merge_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';
                $fileModuleType = 'without_merge';
            }

            if (@$postData['addCountryCodeToBatch'] != '') {
                //get country code
                $countryCode = getCountryCode($postData['country']);
                $fileName    = $countryCode . '_' . $fileName;
            }
            
            $jsonValue = array('campaignName' => $campaignId);
            if ($generalBatchId > 0) {
                $jsonValue['generalBatchName'] = $generalBatchId;
            }
            if($batchId > 0){
                $jsonValue['batchName'] = $batchId;
            }
            $jsonValue = json_encode($jsonValue);
            $isImported = 0;
            $this->mdl_history->addInHistoryTable($fileName, $fileModuleType, $isImported, $jsonValue, $totalCount);
        }

        //add data in batch user table and other table
        $this->mdl_user_re_tracking->addInReTrackBatchUserAndOtherTables($userData,$postData);

        if (@$postData['exceptDays'] > 0) {
            $is_set_except_days = 1;
        }

        $response = array(
            'intervalCount' => $intervalCount,
            'is_set_except_days' => $is_set_except_days
        );

        echo json_encode($response);

    }



}
