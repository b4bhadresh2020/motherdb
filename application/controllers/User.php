<?php

/**
 * 
 */
class User extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_csv');
        $this->load->model('mdl_history');
    }

    public function manage($start = 0) {
        $data = array();
        $data['load_page'] = 'user';
        $data["curTemplateName"] = "user/addEdit";
        $data['headerTitle'] = "Upload CSV";
        $data['pageTitle'] = "Upload CSV";
        $data['suc_msg'] = @GetMsg('suc_msg');
        $data['fail_msg'] = @GetMsg('fail_msg');
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    /**
        AJAX Call
    */
    public function addEdit(){
        
        $colNumber = $this->input->post('colNumber');
        $fieldsName = $this->input->post('fieldsName');
        $customfieldsName = $this->input->post('customfieldsName');
        $groupName = trim($this->input->post('groupName'));
        $keyword = trim($this->input->post('keyword'));
        $country = trim($this->input->post('country'));
        $campaign = trim($this->input->post('campaign'),',');
        $providerName = $this->input->post('providerName');
        $providerList = $this->input->post('providerList');
        $fromDate = $this->input->post('fromDate');
        $startTime = $this->input->post('startTime');
        $endTime = $this->input->post('endTime');
        $perDayRecord = $this->input->post('perDayRecord');

        $response = array();
        
        if (@$_FILES['uploadCsv']['tmp_name'] != '') {

            $content = file(@$_FILES['uploadCsv']['tmp_name']);    

            //check if file is empty or not
            if(empty($content) || (count($content) === 1 && empty(trim($content[0])))){
                
                $response['err'] = 1;
                $response['msg'] = 'CSV file is empty';

            }else{

                //now upload the file
                $res = uploadFile('uploadCsv', '*','uploaded_csv');
                
                if($res['success']) {
                    $path = $res['path'];
                    //insert in db
                    $condition = array();
                    $is_insert = TRUE;

                    if($providerName != 0 && $providerList != 0){
                        $cronInsertData = array(
                            'providerName' => $providerName,
                            'providerList' => $providerList,
                            'fromDate' => $fromDate,
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'country' => $country,
                            'groupName' => $groupName,
                            'keyword' => $keyword,
                            'perDayRecord' => $perDayRecord,
                            'status' => 0                    
                        );

                        $lastInsertedProviderId = ManageData(CSV_FILE_PROVIDER_DATA,$condition,$cronInsertData,$is_insert);
                    }else{
                        $lastInsertedProviderId = null;
                    }

                    

                    $insertArr = array(
                        'colNumber' => $colNumber,
                        'fieldsName' => $fieldsName,
                        'customfieldsName' => $customfieldsName,
                        'groupName' => $groupName,
                        'keyword' => $keyword,
                        'country' => $country,
                        'campaign' => $campaign,
                        'filePath' => $path,
                        'providerId' => $lastInsertedProviderId
                    ); 

                    // manage country wise keyword and group

                    $con_country = $insertArr['country'];
                    $con_keyword = $insertArr['keyword'];
                    $con_groupName = $insertArr['groupName'];

                    $keywordCountryCount = GetAllRecordCount(KEYWORD_COUNTRY_COUNT, $condition = array('keyword' => $con_keyword, 'country' => $con_country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'keywordCountryId');
                    
                    $groupCountryCount = GetAllRecordCount(GROUP_COUNTRY_COUNT, $condition = array('groupName' => $con_groupName, 'country' => $con_country), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupCountryId');

                    if($keywordCountryCount == 0){

                        $sql_keywordCountryCount = "INSERT INTO ".KEYWORD_COUNTRY_COUNT." (keyword, country, total)  VALUES ('$con_keyword', '$con_country', 0)";

                        $this->db->query($sql_keywordCountryCount);
                    } 


                    if($groupCountryCount == 0){

                        $sql_groupCountryCount = "INSERT INTO ".GROUP_COUNTRY_COUNT." (groupName, country)  VALUES ('$con_groupName', '$con_country')";

                        $this->db->query($sql_groupCountryCount);
                    } 

                    $lastInsertedId = ManageData(CSV_FILE_DATA,$condition,$insertArr,$is_insert);

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
                        $lastCronStatusId = ManageData(CRON_STATUS,$condition,$insertArr,$is_insert);

                        //update cronStatusId into csv_file_provider_data
                        $condition = array('id' => $lastInsertedProviderId);
                        $is_insert = FALSE;
                        $updateArr = array('cronStatusId' => $lastCronStatusId);
                        ManageData(CSV_FILE_PROVIDER_DATA,$condition,$updateArr,$is_insert);

                        $this->mdl_csv->insertGroupName($groupName);
                        $this->mdl_csv->insertKeyword($keyword);

                        //add data in history table
                        $explodeFileArr = explode('/', $path);
                        $fileName = array_pop($explodeFileArr);
                        $jsonValue = array('groupName' => $groupName,'keyword' => $keyword,'country' => $country);
                        $jsonValue = json_encode($jsonValue);
                        $this->mdl_history->addInHistoryTable($fileName,'user',1,$jsonValue,$totalRecords);


                        $response['err'] = 0;
                        $response['msg'] = 'File uploaded successfully';

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

    
}