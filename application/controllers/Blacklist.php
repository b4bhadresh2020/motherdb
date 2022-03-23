<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blacklist extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_history');
        $this->load->model('mdl_blacklist');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }
        
        $perPage = 25;
        $responseData = $this->mdl_blacklist->getblacklistUsers($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $blacklistData = $responseData['blacklistData'];
        
        $data = pagination_data('blacklist/manage/', $dataCount, $start, 3, $perPage,$blacklistData);

        $data['load_page'] = 'blacklist';
        $data["curTemplateName"] = "blacklist/list";
        $data['headerTitle'] = "Blacklist User List";
        $data['pageTitle'] = "Blacklist User List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }


    /*
     *  add/edit code starts here
     */

    function addEdit($blacklistId = 0) {

        /*$this->form_validation->set_rules('firstName','First Name', 'required');
        $this->form_validation->set_rules('lastName','Last Name', 'required');*/
        $this->form_validation->set_rules('phone','Phone', 'callback_check_phone_number['.$blacklistId.']');
        //$this->form_validation->set_rules('emailId','Email Id', 'callback_check_email['.$blacklistId.']');
        /*$this->form_validation->set_rules('gender','Gender', 'required');*/
        //$this->form_validation->set_rules('country','Country', 'required');

        if ($this->form_validation->run() != FALSE) {

            $postVal = $_POST;
            $fieldArr = array('firstName','lastName','emailId','phone','country','gender');
            $dataArr = array();
            foreach ($fieldArr as $value) {
                $dataArr[$value] = $postVal[$value];
            }

            if ($blacklistId > 0) {
                $condition = array("blacklistId" => $blacklistId);
                $is_add = false;
                $createdId = ManageData(BLACKLIST, $condition, $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("updateData"));
            } else {
                
                $is_add = true;
                $updatedResponse = ManageData(BLACKLIST, array(), $dataArr, $is_add);

                //$this->deleteRecordFromUserTable($this->input->post('phone'),$this->input->post('emailId'));
                $this->deleteRecordFromUserTable($this->input->post('phone'));

                SetMsg('loginSucMsg', loginRegSectionMsg("insertData"));
            }
            redirect("blacklist/manage");
        }
        $data = array();
        if ($blacklistId > 0) {
            $condition = array("blacklistId" => $blacklistId);
            $data = GetAllRecord(BLACKLIST, $condition, true);
        }
        if ($blacklistId > 0) {
            $data['addEditTitle'] = "Edit Blacklist User";
            $data['headerTitle']  = "Edit Blacklist User";
        }else{
            $data['addEditTitle'] = "Add Blacklist User";
            $data['headerTitle'] = "Add Blacklist User";

        }
        $data['load_page'] = 'blacklist';
        $data["blacklistId"]  = $blacklistId;
        $data['error_msg'] = GetFormError();
        $data["curTemplateName"] = "blacklist/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    function check_phone_number($phone,$blacklistId){
        
        if ($phone == '') {
            $this->form_validation->set_message('check_phone_number', 'The Phone field is required.');
            return FALSE;
        }else{
            //check phone validation
            $condition = array('blacklistId !=' => $blacklistId ,'phone' => $phone);
            $is_single = TRUE;
            $getPhoneDataCount = GetAllRecordCount(BLACKLIST,$condition);
            
            if ($getPhoneDataCount > 0) {
                $this->form_validation->set_message('check_phone_number', 'Phone is already in Black List.');
                return FALSE;  
            }else{
                return TRUE;
            }
        }
    }


    function check_email($emailId,$blacklistId){

        if ($emailId != '') {
            $isValidEmail = isValidEmail($emailId);

            if ($isValidEmail == 1) {

                //check email validation
                $condition = array('blacklistId !=' => $blacklistId ,'emailId' => $emailId);
                $is_single = TRUE;
                $getEmailDataCount = GetAllRecordCount(BLACKLIST,$condition);
                
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



    /*
        * Delete user from user table after you add in unsubscribe/black list
    */

    function deleteRecordFromUserTable($phone,$emailId=''){

        $this->db->where('phone',$phone);
        //$this->db->or_where('emailId',$emailId);
        $recordCount = $this->db->count_all_results(USER);

        if ($recordCount > 0) {

            $this->db->where('phone', $phone);
            //$this->db->or_where('emailId', $emailId);
            $userRecord = $this->db->get(USER)->result_array();

            // pre($userRecord); die;

            // manage masters table data
            
            foreach ($userRecord as $key => $value) {

                $con_country   = $value['country'];
                $con_keyword   = $value['keyword'];
                $con_groupName = $value['groupName'];
            
                if (@$value['gender'] == 'male') {
                    
                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET male = male - 1 WHERE country = '$con_country'";
                    $this->db->query($sql_country);

                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male - 1 WHERE keyword = '$con_keyword'";
                    $this->db->query($sql_keyword);

                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male - 1 WHERE groupName = '$con_groupName'";
                    $this->db->query($sql_groupName);


                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                    $this->db->query($sql_keywordCountryCount);

                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                    $this->db->query($sql_keywordCountryCount_zero);

                }

                if (@$value['gender'] == 'female') {
                   
                    $sql_country = "UPDATE " . COUNTRY_MASTER . " SET female = female - 1 WHERE country = '$con_country'";
                    $this->db->query($sql_country);

                    $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female - 1 WHERE keyword = '$con_keyword'";
                    $this->db->query($sql_keyword);

                    $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female - 1 WHERE groupName = '$con_groupName'";
                    $this->db->query($sql_groupName);


                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                    $this->db->query($sql_keywordCountryCount);

                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                    $this->db->query($sql_keywordCountryCount_zero);

                }

                if(@$value['gender'] != 'male' && @$value['gender'] != 'female'){

                    $sql_country = "UPDATE ".COUNTRY_MASTER." SET other = other - 1  WHERE country = '$con_country'";
                    $this->db->query($sql_country);

                    $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET other = other - 1  WHERE keyword = '$con_keyword'";
                    $this->db->query($sql_keyword);

                    $sql_groupName = "UPDATE ".GROUP_MASTER." SET other = other - 1  WHERE groupName = '$con_groupName'";
                    $this->db->query($sql_groupName);

                    $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                    $this->db->query($sql_keywordCountryCount);

                    $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";
                    $this->db->query($sql_keywordCountryCount_zero);

                }
            }


            //now delete record from user table

            $this->db->where('phone', $phone);
            //$this->db->or_where('emailId', $emailId);
            $this->db->delete(USER);
        }

    }


    public function upload($start = 0) {
        $data = array();
        $data['load_page'] = 'blackListUpload';
        $data["curTemplateName"] = "blacklist/addEditCSV";
        $data['headerTitle'] = "Upload Blacklist CSV";
        $data['pageTitle'] = "Upload Blacklist CSV";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    /**
        AJAX Call
    */
    public function addEditCSV(){
        
        $colNumber = $this->input->post('colNumber');
        $fieldsName = $this->input->post('fieldsName');

        $response = array();
        
        if (@$_FILES['uploadCsv']['tmp_name'] != '') {

            $content = file(@$_FILES['uploadCsv']['tmp_name']);    

            //check if file is empty or not
            if(empty($content) || (count($content) === 1 && empty(trim($content[0])))){
                
                $response['err'] = 1;
                $response['msg'] = 'CSV file is empty';

            }else{

                //now upload the file
                $res = uploadFile('uploadCsv', '*','blacklist_csv');
                
                if($res['success']) {
                    $path = $res['path'];
                    //insert in db
                    $condition = array();
                    $is_insert = TRUE;
                    $insertArr = array(
                        'colNumber' => $colNumber,
                        'fieldsName' => $fieldsName,
                        'filePath' => $path
                    );

                    $lastInsertedId = ManageData(BLACKLIST_CSV_FILE,$condition,$insertArr,$is_insert);

                    if ($lastInsertedId > 0) {

                        //get total number of records from file
                        $uploadedCsv = $_FILES['uploadCsv']['tmp_name'];
                        $fp = new SplFileObject($uploadedCsv, 'r');
                        $fp->seek(PHP_INT_MAX);         // got last line of file
                        $totalRecords = $fp->key();     // get last line's number
                        $fp->rewind();                  // go to first line 
                        $fp = null;                     // close file by null (Because there is only method to close the file in splFileObject)

                        //insert record in cron_status table
                        $condition = array();
                        $is_insert = TRUE;
                        $insertArr = array('filePath' => $path,'totalRecords' => $totalRecords);
                        ManageData(BLACKLIST_CRON_STATUS,$condition,$insertArr,$is_insert);

                        //add data in history table
                        $explodeFileArr = explode('/', $path);
                        $fileName = array_pop($explodeFileArr);
                        $jsonValue = array('fileName' => $fileName);
                        $jsonValue = json_encode($jsonValue);
                        $this->mdl_history->addInHistoryTable($fileName,'blacklist',1,$jsonValue,$totalRecords);

                        $response['err'] = 0;
                        $response['msg'] = "File uploaded successfully";

                    }else{
                        $response['err'] = 1;
                        $response['msg'] = 'Something went wrong. File uploading process failed';
                    }
                }else{
                    $response['err'] = 1;
                    $response['msg'] = 'Something went wrong. File uploading process failed';
                }
            }
        }else{
            $response['err'] = 1;
            $response['msg'] = 'Something went wrong. Please try again later';
        }
        
        echo json_encode($response);
        
    }

    public function blacklistIP($start = 0) {
        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }
        
        $perPage = 25;
        $responseData = $this->mdl_blacklist->getBlacklistIP($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $blacklistIPData = $responseData['blacklistIPData'];
        
        $data = pagination_data('blacklist/blacklistIP/', $dataCount, $start, 3, $perPage,$blacklistIPData);

        $data['load_page'] = 'blacklistIP';
        $data["curTemplateName"] = "blacklistIP/list";
        $data['headerTitle'] = "Blacklist IP List";
        $data['pageTitle'] = "Blacklist IP List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  add/edit code starts here
     */

    function addEditBlacklistIP() {

        $this->form_validation->set_rules('ip','IP', 'required');
        if ($this->form_validation->run() != FALSE) {
            // current login user
            $condition = array(
                'adminId' => $this->session->userdata('adminId')
            );
            $getAdminUser = GetAllRecord(ADMINMASTER,$condition,true);
            
            $dataArr = array();
            $dataArr['ip'] = $this->input->post('ip');
            $dataArr['added_by'] = $getAdminUser['adminUname'];
            $dataArr['created_at'] = date('Y-m-d H:i:s');
            
            $is_add = true;
            $response = ManageData(IP_BLACKLIST, array(), $dataArr, $is_add);      
            SetMsg('loginSucMsg', loginRegSectionMsg("insertData"));
            redirect("blacklist/blacklistIP");
        }
        $data = array();
        $data['addEditTitle'] = "Add Blacklist IP";
        $data['headerTitle'] = "Add Blacklist IP";

        $data['load_page'] = 'blacklistIP';
        $data['error_msg'] = GetFormError();
        $data["curTemplateName"] = "blacklistIP/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function deleteBlacklistIP() {
        $response = [];
        $blacklistIPId = $this->input->post('blacklistIPId');
        
        $this->db->where('id', $blacklistIPId);
        $result = $this->db->delete(IP_BLACKLIST);
        if($result) {
            $response = array('result' => 'success');
        } else {
            $response = array('result' => 'error');
        }
        echo json_encode($response);
    }

}