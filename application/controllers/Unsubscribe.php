<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Unsubscribe extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_unsubscribed_fileter_data');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }
        
        $perPage = 25;
        $responseData = $this->mdl_unsubscribed_fileter_data->getUnsubscriberUserList($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $unsubscriberData = $responseData['unsubscriberData'];

        $data = pagination_data('unsubscribe/manage/', $dataCount, $start, 3, $perPage, $unsubscriberData);

        $data['load_page'] = 'unsubsriberList';
        $data["curTemplateName"] = "unsubscribe/list";
        $data['headerTitle'] = "Unsubscribed User List";
        $data['pageTitle'] = "Unsubscribed User List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    

    /*
     *  add/edit code starts here
     */

    function addEdit($unsubscriberId = 0) {

        $this->form_validation->set_rules('firstName','First Name', 'required');
        $this->form_validation->set_rules('lastName','Last Name', 'required');
        $this->form_validation->set_rules('phone','Phone', 'callback_check_phone_number['.$unsubscriberId.']');
        $this->form_validation->set_rules('emailId','Email Id', 'callback_check_email['.$unsubscriberId.']');
        $this->form_validation->set_rules('gender','Gender', 'required');
        $this->form_validation->set_rules('country','Country', 'required');

        if ($this->form_validation->run() != FALSE) {

            $postVal = $_POST;
            $fieldArr = array('firstName','lastName','emailId','phone','country','gender');
            $dataArr = array();
            foreach ($fieldArr as $value) {
                $dataArr[$value] = $postVal[$value];
            }

            if ($unsubscriberId > 0) {
                $condition = array("unsubscriberId" => $unsubscriberId);
                $is_add = false;
                $createdId = ManageData(UNSUBSCRIBER, $condition, $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("updateData"));
            } else {
                
                $is_add = true;
                $updatedResponse = ManageData(UNSUBSCRIBER, array(), $dataArr, $is_add);

                $this->deleteRecordFromUserTable($this->input->post('phone'),$this->input->post('emailId'));

                SetMsg('loginSucMsg', loginRegSectionMsg("insertData"));
            }
            redirect("unsubscribe/manage");
        }
        $data = array();
        if ($unsubscriberId > 0) {
            $condition = array("unsubscriberId" => $unsubscriberId);
            $data = GetAllRecord(UNSUBSCRIBER, $condition, true);
        }
        if ($unsubscriberId > 0) {
            $data['addEditTitle'] = "Edit Unsubscriber";
            $data['headerTitle']  = "Edit Unsubscriber";
        }else{
            $data['addEditTitle'] = "Add Unsubscriber";
            $data['headerTitle'] = "Add Unsubscriber";

        }
        $data['load_page'] = 'unsubsriberList';
        $data["unsubscriberId"]  = $unsubscriberId;
        $data['error_msg'] = GetFormError();
        $data["curTemplateName"] = "unsubscribe/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    /*
        * Delete user from user table after you add in unsubscribe/black list
    */

    function deleteRecordFromUserTable($phone,$emailId){

        $this->db->where('phone',$phone);
        $this->db->or_where('emailId',$emailId);
        $recordCount = $this->db->count_all_results(USER);

        if ($recordCount > 0) {
            //now delete record from user table
            $this->db->where('phone', $phone);
            $this->db->or_where('emailId', $emailId);
            $this->db->delete(USER);
        }

    }


    /*
     *  add/edit code ends here
     */

    function check_phone_number($phone,$unsubscriberId){
        
        if ($phone == '') {
            $this->form_validation->set_message('check_phone_number', 'The Phone field is required.');
            return FALSE;
        }else{
            //check phone validation
            $condition = array('unsubscriberId !=' => $unsubscriberId ,'phone' => $phone);
            $is_single = TRUE;
            $getPhoneDataCount = GetAllRecordCount(UNSUBSCRIBER,$condition);
            
            if ($getPhoneDataCount > 0) {
                $this->form_validation->set_message('check_phone_number', 'Phone is already in Black List.');
                return FALSE;  
            }else{
                return TRUE;
            }
        }
    }


    function check_email($emailId,$unsubscriberId){

        if ($emailId != '') {
            $isValidEmail = isValidEmail($emailId);

            if ($isValidEmail == 1) {

                //check email validation
                $condition = array('unsubscriberId !=' => $unsubscriberId ,'emailId' => $emailId);
                $is_single = TRUE;
                $getEmailDataCount = GetAllRecordCount(UNSUBSCRIBER,$condition);
                
                if ($getEmailDataCount > 0) {
                    $this->form_validation->set_message('check_email', 'Email is already in Black List.');
                    return FALSE;  
                }else{
                    return TRUE;
                }
            }else{
                $this->form_validation->set_message('check_email', 'Please enter valid Email');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('check_email', 'Please Enter Email');
            return FALSE;
        }
    }
    
}