<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_user_tracking');
        $this->load->model('mdl_history');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }
        
        $info = $this->mdl_user_tracking->getTrackingData($_GET);

        $perPage = 20;
        $this->session->set_userdata('start', $start);
        $data = pagiationData('tracking/manage/', count($info), $start, 3, $perPage);

        $data['load_page'] = 'trackingList';
        $data["curTemplateName"] = "tracking/list";
        $data['headerTitle'] = "Tracking List";
        $data['pageTitle'] = "Tracking List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }


    public function addEdit(){
        //pre($_POST);die;
        
        $this->form_validation->set_rules('campaignName', 'Campaign Name', 'trim|callback_campaign_name_validation');

        if (@$_POST['csvType'] == 1 || @$_POST['csvType'] == 2) {

            $this->form_validation->set_rules('batchName', 'Batch Name', 'trim|required|callback_batch_name_validation');
            $this->form_validation->set_rules('msg', 'Message', 'trim|required|callback_check_msg');
            $this->form_validation->set_rules('redirectUrl', 'Redirect URL', 'trim|required|callback_check_url');
        }
        
        
        if ($this->form_validation->run() != FALSE) {

            $userData = $this->mdl_user_tracking->getUserTrackingData($_POST);

            if (count($userData) > 0) {

                if (@$_POST['csvType'] == 1 || @$_POST['csvType'] == 2) {
                    

                    //insert batch name in batch table
                    $batchName = $this->input->post('batchName');
                    $campaignId = $this->input->post('campaignName');

                    $condition = array();
                    $insertData = array('batchName' => $batchName);
                    $is_insert = TRUE;
                    $batchId = ManageData(BATCH,$condition,$insertData,$is_insert);

                    //insert msg and redirect URL in batch_link table
                    $msg = $this->input->post('msg');
                    $redirectUrl = $this->input->post('redirectUrl');

                    $condition = array();
                    $insertData = array('batchId' => $batchId, 'msg' => $msg, 'redirectUrl' => $redirectUrl);
                    $is_insert = TRUE;
                    $linkId = ManageData(BATCH_LINK,$condition,$insertData,$is_insert);


                    //insert batchId,userId,uniqueKey in batch_user table
                    foreach ($userData as $value) {

                        //generate unique key (4 random string + unix timestamp)
                        $generateRandomString = generateRandomString(4);
                        $unixTime = time();
                        $uniqueKey = $generateRandomString.$unixTime;

                        $condition = array();
                        $is_insert = TRUE;

                        $insertData = array('batchId' => $batchId,'userId' => $value['userId'],'uniqueKey' => $uniqueKey);    
                        ManageData(BATCH_USER,$condition,$insertData,$is_insert);

                        /*
                            Insert campaignId and userId in user_participated_campaign table
                            first check if same record is already there or not
                            if it is not there then insert otherwise dont
                        */

                        if ($campaignId > 0) {
                            
                            $condition = array('campaignId' => $campaignId,'userId' => $value['userId']);
                            $is_single = TRUE;
                            $getUserPartCampData = GetAllRecord(USER_PARTICIPATED_CAMPAIGN,$condition,$is_single);

                            if (count($getUserPartCampData) == 0) {

                                $condition = array();
                                $is_insert = TRUE;
                                $insertData = array('campaignId' => $campaignId,'userId' => $value['userId']);
                                ManageData(USER_PARTICIPATED_CAMPAIGN,$condition,$insertData,$is_insert);
                            }

                        }

                        /*
                            update last sms date for user
                        */
                            $condition = array('userId' => $value['userId']);
                            $is_insert = FALSE;
                            $updateArr = array('lastSmsDate' => date('Y-m-d'));
                            ManageData(USER,$condition,$updateArr,$is_insert);
                    }

                    //export section

                    if (@$_POST['csvType'] == 1) {
                        
                        //with mergetags
                        $header = array('Name','Number','Url','Unsubscribe Url');
                        $reArrangeArray = $this->mdl_user_tracking->getExcelDataWithMergeTag($_POST,$userData,$batchId);
                        $count = count($reArrangeArray);
                        $fileName = 'userdata_with_merge_tag_'.date('Y-m-d H:i:s').'_Total_'.$count.'_Entries.csv';

                        //add data in history table
                        $jsonValue = array('batchName' => $batchId);
                        $jsonValue = json_encode($jsonValue);
                        $this->mdl_history->addInHistoryTable($fileName,'with_merge',0,$jsonValue,$count);    
                        

                    }else{

                        //without mergetags
                        $header = array('Number','Message');
                        $reArrangeArray = $this->mdl_user_tracking->getExcelDataWithoutMergeTag($_POST,$userData,$batchId);
                        $count = count($reArrangeArray);
                        $fileName = 'userdata_with_out_merge_tag_'.date('Y-m-d H:i:s').'_Total_'.$count.'_Entries.csv';

                        //add data in history table
                        $jsonValue = array('batchName' => $batchId);
                        $jsonValue = json_encode($jsonValue);
                        $this->mdl_history->addInHistoryTable($fileName,'without_merge',0,$jsonValue,$count);  
                    }

                }else{

                    //normal csv
                    $header = array('Full Name','Last Name','Email Id','Address','Postcode','City','Phone','Gender','Birthdate Day','Birthdate Month','Birthdate Year','Ip','Participated','Campaign Source');
                    $reArrangeArray = $this->mdl_user_tracking->getExcelDataWithNormalTag($userData);
                    $count = count($reArrangeArray);
                    $fileName = 'userdata_with_normal_tag_'.date('Y-m-d H:i:s').'_Total_'.$count.'_Entries.csv';
                }
                

            }else{

                //no data found in excel
                $header = array();
                $reArrangeArray = array(array('','There is no data !'));
                $fileName = 'blank_excel_'.date('Y-m-d H:i:s').".csv";
            }

            if (@$_POST['addCountryCodeToBatch']) {
                //get country code
                $countryCode = getCountryCode($_POST['country']);
                $fileName = $countryCode.'_'.$fileName;
            }

            //make CSV
            $this->mdl_user_tracking->makeCSV($header,$reArrangeArray,$fileName);
        }
        $data = array();
        $data['load_page'] = 'createTracking';
        $data["curTemplateName"] = "tracking/addEdit";
        $data['headerTitle'] = "Tracking";
        $data['pageTitle'] = "Create Tracking";
        $data['suc_msg'] = @GetMsg('suc_msg');

        //get all country
        $data['countries'] = getCountry();
        
        //get all group name
        $groupQuery = "SELECT DISTINCT(groupName) FROM `user`";
        $data['groups'] = GetDatabyqry($groupQuery);

        //get country wise campaign.
        $data['campaigns'] = $this->mdl_user_tracking->getCountryWiseCampaign();

        if (@GetFormError()) {
            $data['error_msg'] = GetFormError();
        }else{
            $data['error_msg'] = @GetMsg('error_msg');    
        }

       
        foreach ($_POST as $key => $value) {
            $data[$key] = $value;
        }
        //pre($data);die;
        $this->load->view('commonTemplates/templateLayout', $data);
           
    }



    function campaign_name_validation($campaignName,$phone){
        
        // Campaign Name shuld not blank if phone selection box is selected
        $phone = $this->input->post('phone');
        if ($campaignName == '' && $phone != '') {
            
            $this->form_validation->set_message('campaign_name_validation', 'Campaign Name Field is required');
            return FALSE;

        }else{
            return TRUE;
        }
    }


    function batch_name_validation($str){

        //check duplicate batch name
        $condition = array('batchName' => $str);
        $is_single = true;
        $getBatchName = GetAllRecord(BATCH,$condition,$is_single);

        if (count($getBatchName) > 0) {

            $this->form_validation->set_message('batch_name_validation', 'Batch Name is already in use. Type another Batch Name');
            return FALSE;

        }else{
            return TRUE;
        }
    }

    function check_msg($str){   

        if ($str != '') {
            
            if (\strpos($str, '{name}') !== false) {
            
                if (\strpos($str, '{url}') !== false) {

                    if (\strpos($str, '{unsubscribe_url}') !== false) {
                        return TRUE;
                    }else{
                        $this->form_validation->set_message('check_msg', '{unsubscribe_url} is required in message field');
                        return FALSE;    
                    }

                }else{
                    $this->form_validation->set_message('check_msg', '{url} is required in message field');
                    return FALSE;
                }

            }else{

                $this->form_validation->set_message('check_msg', '{name} is required in message field');
                return FALSE; 
            }

        }else{

            $this->form_validation->set_message('check_msg', 'The Message field is required');
            return FALSE;

        }

        
    }

    function check_url($str){
        
        //check url validation
        
        if ($str == '') {

            $this->form_validation->set_message('check_url', 'The Redirect URL field is required');
            return FALSE;    

        }else if (filter_var($str, FILTER_VALIDATE_URL)){
            return TRUE;
        }else{
            $this->form_validation->set_message('check_url', 'Invalid Redirect URL');
            return FALSE;    
        }
    }



    /*
     *  // AJAX Call
     */

    function getUrlMoreData(){
        $uniqueKey = trim($this->input->post('uniqueKey'));

        //get from redirect_link_clicks
        $condition = array('uniqueKey' => $uniqueKey);
        $is_single = FALSE;
        $orderBy = array('clickDateTime' => 'DESC');
        $getRedirectLinkClicksData = GetAllRecord(REDIRECT_LINK_CLICKS,$condition,$is_single,array(),array(),array($orderBy));

        $data = array();
        $data['redirect_click_data'] = $getRedirectLinkClicksData; 
        $this->load->view('tracking/redirect_click_detail_table_view', $data);
        
    }


    function getCampaign(){
        $country = $this->input->post('country');
        $response = array();
        $response['campaigns'] = $this->mdl_user_tracking->getCountryWiseCampaign($country);

        echo json_encode($response);
    }

    
}