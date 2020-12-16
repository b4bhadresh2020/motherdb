<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LiveDelivery extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        
        $condition = array();

        $dataCount = GetAllRecordCount(LIVE_DELIVERY, $condition);

        $liveDeliveryData = array();
        if ($dataCount > 0) {
            $liveDeliveryData = GetAllRecord(LIVE_DELIVERY, $condition, "",array(),array(),array(array('liveDeliveryId' => 'DESC')));    
        }
        
        $perPage = 15;
        $data = pagination_data('liveDelivery/manage/', $dataCount, $start, 3, $perPage,$liveDeliveryData);
        $data['headerTitle'] = "Live Delivery";
        $data['load_page'] = 'liveDelivery';
        $data["curTemplateName"] = "liveDelivery/list";
        $data['start'] = $start;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */


    /*
     *  add/edit code starts here
     */

    function addEdit($liveDeliveryId = 0) {

        $this->form_validation->set_rules('country','Country', 'required'); 
        $this->form_validation->set_rules('mailProvider','Mail Provider', 'callback_check_mail_provider'); 
        $this->form_validation->set_rules('identifier','Identifier', 'required'); 
        $this->form_validation->set_rules('groupName','Group Name', 'required'); 
        $this->form_validation->set_rules('keyword','Keyword', 'required'); 
        $this->form_validation->set_rules('ifUserInThisGroups','', 'callback_ifUserInThisGroups_validation'); 

        if ($this->form_validation->run() != FALSE) {

            $postVal = $_POST;
            $fieldArr = array('country','mailProvider','identifier','groupName','keyword','dataSource','delay','isDuplicate','checkEmail','checkPhone');
            $dataArr = array();
            foreach ($fieldArr as $value) {                 
                $dataArr[$value] = ($value == "mailProvider" || $value == "delay" || $value == "isDuplicate")?isset($postVal[$value])?json_encode($postVal[$value]):"":$postVal[$value];
            }

            if(@$this->input->post('ifUserInThisGroups') != '' && @$this->input->post('addTheUserInThisGroup') != ''){
                
                $ifUserInThisGroups = trim($this->input->post('ifUserInThisGroups'),',');
                $addTheUserInThisGroup = trim($this->input->post('addTheUserInThisGroup'),',');

                $dataArr['ifUserInThisGroups'] = $ifUserInThisGroups;
                $dataArr['addTheUserInThisGroup'] = $addTheUserInThisGroup;

            }else{
                
                $dataArr['ifUserInThisGroups'] = '';
                $dataArr['addTheUserInThisGroup'] = '';
            }            
            
            if ($liveDeliveryId > 0) {

                $condition = array("liveDeliveryId" => $liveDeliveryId);
                $is_add = false;
                ManageData(LIVE_DELIVERY, $condition, $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("updateData"));

            } else {
                
                $is_add = true;
                $liveDeliveryId = ManageData(LIVE_DELIVERY, array(), $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("insertData"));
            }

            //create apikey by encrypted createdId and update it to table
            $apikey = @encrypt($liveDeliveryId);

            $liveDeliveryUrl = LIVE_DELIVERY_URL_DOMAIN."live_delivery_api/rest?apikey=".$apikey."&emailId=email&firstName=firstname&lastName=lastname&phone=phone&gender=gender&address=address&postCode=postcode&city=city&birthdateDay=birthdateDay&birthdateMonth=birthdateMonth&birthdateYear=birthdateYear&age=age&ip=ip&optinurl=optinurl&optindate=optindate&tag=tag";

            //update  apikey,liveDeliveryUrl
            $condition = array('liveDeliveryId' => $liveDeliveryId);
            $is_insert = FALSE;
            $updateArr = array(
                'apikey' => $apikey,
                'liveDeliveryUrl' => $liveDeliveryUrl
            );
            ManageData(LIVE_DELIVERY,$condition,$updateArr,$is_insert);

            redirect("liveDelivery/manage");
        }
        $data = array();
        if ($liveDeliveryId > 0) {
            $condition = array("liveDeliveryId" => $liveDeliveryId);
            $data = GetAllRecord(LIVE_DELIVERY, $condition, true);
        }
        if ($liveDeliveryId > 0) {
            $data['addEditTitle'] = "Edit Live Delivery";
            $data['headerTitle']  = "Edit Live Delivery";
        }else{
            $data['addEditTitle'] = "Add Live Delivery";
            $data['headerTitle'] = "Add Live Delivery";

        }        

        $data['load_page'] = 'liveDelivery';
        $data["liveDeliveryId"]  = $liveDeliveryId;
        $data['error_msg'] = GetFormError();
        $data["curTemplateName"] = "liveDelivery/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  add/edit code ends here
     */


    function check_mail_provider(){

        $identifier = $this->input->post('identifier');
        $mailProviders = $this->input->post('mailProvider');

        if (!isset($mailProviders)) {

            $this->form_validation->set_message('check_mail_provider', 'The Mail Provider field is required.');
            return FALSE;

        }else{
            return TRUE;
        }
    }


    function ifUserInThisGroups_validation($ifUserInThisGroups){

        $addTheUserInThisGroup = $this->input->post('addTheUserInThisGroup');

        if ($ifUserInThisGroups == '' && $addTheUserInThisGroup != '') {

            $this->form_validation->set_message('ifUserInThisGroups_validation', 'The "If user in these groups" field is required.');
            return FALSE;

        }else if($ifUserInThisGroups != '' && $addTheUserInThisGroup == ''){
            
            $this->form_validation->set_message('ifUserInThisGroups_validation', 'The "Add the user in this group" field is required.');
            return FALSE;

        }else{
            return TRUE;
        }
    }

    /* Change active,inactive status
     ------------------------------------*/

    function changeActiveInActiveStatus(){
        
        $liveDeliveryId = $this->input->post('id');
        $status     = $this->input->post('status');

        $condition = array("liveDeliveryId" => $liveDeliveryId);
        $dataArr['isInActive'] = $status;
        
        
        echo $updatedStatus = ManageData(LIVE_DELIVERY,$condition,$dataArr,false);
    }


    /*function create_contacts(){
        $this->load->model('mdl_get_response_provider');

        $getData = array(
            'emailId' => 'ketan.sar@gmail.com',
            'firstName' => 'Ketan',
            'lastName' => 'Sar',
            'phone' => '2659548523',
            'ip' => '123.201.228.95',
            'financingNeed' => 'yes' 
        );


        $mailProvider = 'felina_dk_accepted';

        $response = $this->mdl_get_response_provider->send_data_to_get_response($getData,$mailProvider);

        pre($response);
    }*/

}