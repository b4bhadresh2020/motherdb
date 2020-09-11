<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged()) {
            redirect(base_url());
        }

        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_history');
    }

    public function manage($start = 0)
    {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage   = 25;
        $batchDataResponse = $this->mdl_user_tracking->getTrackingData($_GET, $start, $perPage);
        $dataCount = $batchDataResponse['batchDataCount'];
        $batchData = $batchDataResponse['batchData'];
        
        $data = pagination_data('tracking/manage/', $dataCount, $start, 3, $perPage, $batchData);

        $data['load_page']       = 'trackingList';
        $data["curTemplateName"] = "tracking/list";
        $data['headerTitle']     = "Tracking List";
        $data['pageTitle']       = "Tracking List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function addEdit()
    {

        $this->form_validation->set_rules('country', 'Country', 'callback_country_validation');
        $this->form_validation->set_rules('campaignName', 'Campaign', 'required');
        //$this->form_validation->set_rules('numberOfSms', 'Number Of SMS', 'required');

        if ($this->form_validation->run() != false) {
            $this->csvWithNormalTag($_POST);
        }

        $data                    = array();
        $data['load_page']       = 'createTracking';
        $data["curTemplateName"] = "tracking/addEdit";
        $data['headerTitle']     = "Tracking";
        $data['pageTitle']       = "Create Tracking";
        $data['suc_msg']         = @GetMsg('suc_msg');

        //get all country
        $data['countries'] = getCountry();

        // //get all group names

        $groupQuery = "SELECT DISTINCT(groupName) FROM `group_master`";
        $data['groups'] = GetDatabyqry($groupQuery);

        //get all keyword names
        $keywordQry       = "SELECT DISTINCT(keyword) FROM keyword_master";
        $data['keywords'] = GetDatabyqry($keywordQry);

        //get country wise campaign.
        $data['campaigns'] = $this->mdl_user_tracking->getCountryWiseCampaign();

        //get batchdata and general batch data
        $data['batchAndGeneralBatchArr'] = $this->mdl_user_tracking->getGroupClickersGeneralGroupClickers();

        if (@GetFormError()) {
            $data['error_msg'] = GetFormError();
        } else {
            $data['error_msg'] = @GetMsg('error_msg');
        }

        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }

        $data['form_action'] = base_url('tracking/addEdit');
        //pre($data);die;
        $this->load->view('commonTemplates/templateLayout', $data);

    }

    /*ajax*/
    public function getGroupByCountry()
    {   
        $conCountry = $this->input->post('conCountry');

        if($conCountry == '') {

            $groupByCountry_sql = "SELECT groupName FROM `group_master`";

        } else {

            $groupByCountry_sql = "SELECT groupName FROM " . GROUP_COUNTRY_COUNT . " WHERE country = '$conCountry'";
        }

        $groupByCountry['groupData'] = $this->db->query($groupByCountry_sql)->result_array();

        echo json_encode($groupByCountry);
    }

    /*ajax*/
    public function getTrackClickerDataCount(){

        $postData = $_POST;
        $countArr = $this->mdl_user_tracking->getUserTrackingDataCount($postData);

        echo json_encode($countArr);
        
    } 

    /*ajax*/ 
    public function process_user_data(){
        
        $postData = $this->input->post();
        $userIdsArr = array();

        if (@$postData['userIdsArr']) {
            $userIdsArr = json_decode($postData['userIdsArr']);
        }

        $batchId = $postData['batchId'];
        $generalBatchId = $postData['generalBatchId'];
        $start = $postData['process_start'];
        $perPage = $postData['process_per_page'];
        $campaignId = $postData['campaignName'];
        $totalCount = $postData['totalCount'];
        $csvType = $postData['csvType'];
        $is_set_except_days = $postData['is_set_except_days'];
        

        //get user data
        $modelResponse = $this->mdl_user_tracking->get_user_process_data($postData, $userIdsArr, $start, $perPage);

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

            $jsonValue = array('batchName' => $batchId,'campaignName' => $campaignId);
            if ($generalBatchId > 0) {
                $jsonValue['generalBatchName'] = $generalBatchId;
            }

            $jsonValue = json_encode($jsonValue);
            $isImported = 0;
            $this->mdl_history->addInHistoryTable($fileName, $fileModuleType, $isImported, $jsonValue, $totalCount);
            
        }
        //add data in batch user table and other table
        $this->mdl_user_tracking->addInBatchUserAndOtherTables($userData,$postData);

        if (@$postData['exceptDays'] > 0) {
            $is_set_except_days = 1;
        }

        $response = array(
            'intervalCount' => $intervalCount,
            'is_set_except_days' => $is_set_except_days
        );

        echo json_encode($response);
    
    }


    public function csvWithNormalTag($postData, $userIdsArr = array(), $start = 0, $perPage = 5000)
    {

        $modelResponse = $this->mdl_user_tracking->getUserTrackingData($postData, $userIdsArr, $start, $perPage);
        $totalCount    = $modelResponse['totalCount'];
        $userData      = $modelResponse['userData'];
        $userIdsArr    = $modelResponse['userIdsArr'];

        if ($totalCount > 0) {

            //normal csv
            $reArrangeArray = $this->mdl_user_tracking->getExcelDataWithNormalTag($userData);

            // file creation
            if ($start == 0) {

                $header   = array('Full Name', 'Last Name', 'Email Id', 'Address', 'Postcode', 'City', 'Country', 'Phone', 'Gender', 'Birthdate Day', 'Birthdate Month', 'Birthdate Year', 'Ip', 'Participated', 'Campaign Source');
                $fileName = 'userdata_with_normal_tag_' . date('Y-m-d H:i:s') . '_Total_' . $totalCount . '_Entries.csv';

                if (@$postData['addCountryCodeToBatch']) {
                    //get country code
                    $countryCode = getCountryCode($postData['country']);
                    $fileName    = $countryCode . '_' . $fileName;
                }

                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Content-Type: application/csv; ");

                //open file to write
                $file = fopen('php://output', 'w');
                fputcsv($file, $header);
                fclose($file);

            }

            //open file to write
            $file = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }
            fclose($file);

            //check if there is more data or not
            if ($perPage == count($userData) && $perPage != $postData['numberOfSms']) {

                $start = $start + $perPage;
                $this->csvWithNormalTag($postData, $userIdsArr, $start);

            } else {
                exit;
            }
        } else if (count($userData) == 0 && $start != 0) {
            exit;
        } else {
            $this->makeBlankCsv();
        }

    }

    public function makeBlankCsv()
    {

        //no data found in excel
        $header         = array();
        $reArrangeArray = array(array('', 'There is no data !'));
        $fileName       = 'blank_csv_' . date('Y-m-d H:i:s') . ".csv";

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/csv; ");

        // file creation
        $file = fopen('php://output', 'w');

        fputcsv($file, $header);
        foreach ($reArrangeArray as $key => $line) {
            fputcsv($file, $line);
        }
        fclose($file);
        exit;
    }


    public function country_validation($country)
    {

        if ($country == '' && @$this->input->post('addCountryCodeToBatch') == 1) {
            $this->form_validation->set_message('country_validation', 'Please select the Country for add country code in file name');
            return false;
        } else {
            return true;
        }
    }

    /*
     *  // AJAX Call
     */

    public function getUrlMoreData()
    {
        $uniqueKey = trim($this->input->post('uniqueKey'));

        //get from redirect_link_clicks
        $condition                 = array('uniqueKey' => $uniqueKey);
        $is_single                 = false;
        $orderBy                   = array('clickDateTime' => 'DESC');
        $getRedirectLinkClicksData = GetAllRecord(REDIRECT_LINK_CLICKS, $condition, $is_single, array(), array(), array($orderBy));

        $data                        = array();
        $data['redirect_click_data'] = $getRedirectLinkClicksData;
        $this->load->view('tracking/redirect_click_detail_table_view', $data);

    }

    /*ajax*/
    public function getCampaign()
    {
        $country               = $this->input->post('country');
        $response              = array();
        $response['campaigns'] = $this->mdl_user_tracking->getCountryWiseCampaign($country);

        echo json_encode($response);
    }



    /**
        * response of ajax json
        *
        * @return Response
    */
    public function auto_search_batch()
    {

        $batchName = $this->input->get('query');
        $this->db->select('batchName');
        $this->db->like('batchName', $batchName);
        $batch_result = $this->db->get(BATCH)->result();

        $data = array();
        foreach ($batch_result as $value) {
            $data[] = $value->batchName;
        }
        echo json_encode($data);
    }



    /**
        * response of ajax json
        *
        * @return Response
    */
    public function auto_search_general_batch()
    {

        $generalBatchName = $this->input->get('query');
        $this->db->select('generalBatchName');
        $this->db->like('generalBatchName', $generalBatchName);
        $general_batch_result = $this->db->get(GENERAL_BATCH)->result();

        $data = array();
        foreach ($general_batch_result as $value) {
            $data[] = $value->generalBatchName;
        }
        echo json_encode($data);
    }


    //ajax call from addEdit_script.php
    function check_time_difference(){
        
        $country = $this->input->post('country');
        $broadcast_specific_time = $this->input->post('broadcast_specific_time');
        $hour = date("H",strtotime($broadcast_specific_time));

        // After 20:00 we are restrict to send sms.
        if($hour >= 20 ){
            $response['err'] = 2;
        }else{
            $diff = get_timezone_wise_difference($country, $broadcast_specific_time);
            $response = array();
            if ($diff > 0) {
                $response['err'] = 0;
                $response['diff_in_sec'] = $diff;
            }else{
                $response['err'] = 1;
            }
        }
        echo json_encode($response);
    }


    //ajax call from addEdit_script.php
    function check_time_difference_in_arr(){

        $country = $this->input->post('country');
        $split_specific_time_arr = $this->input->post('split_specific_time_arr');

        $diff_arr = array();
        $is_time_passed = 0;
        foreach ($split_specific_time_arr as $specified_date) {
            $hour = date("H",strtotime($specified_date));
            if($hour >= 20 ){
                $is_time_passed = 2;
                break;
            }else{
                $diff = get_timezone_wise_difference($country, $specified_date);
                if($diff > 0){
                    $diff_arr[] = $diff;
                }else{
                    $is_time_passed = 1;
                    break;
                }
            }    
        }

        $response = array();
        if ($is_time_passed == 0) {
            $response['err'] = 0;
            $response['diff_in_sec_arr'] = $diff_arr;
        }else if($is_time_passed == 1){
            $response['err'] = 1;
        }else if($is_time_passed == 2){
            $response['err'] = 2;
        }

        echo json_encode($response);
    }

}
