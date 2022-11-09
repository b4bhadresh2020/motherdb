<?php

/**
 * 
 */
class Enrichment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged()) {
            redirect(base_url());
        } else if (is_logged() && !is_admin()) {
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_csv');
        $this->load->model('mdl_history');
    }

    public function manage($start = 0)
    {
        $data = array();
        $data['load_page'] = 'enrichment';
        $data["curTemplateName"] = "enrichment/addEdit";
        $data['headerTitle'] = "Upload CSV";
        $data['pageTitle'] = "Upload CSV";
        $data['suc_msg'] = @GetMsg('suc_msg');
        $data['fail_msg'] = @GetMsg('fail_msg');
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    /**
        AJAX Call
     */
    public function addEdit()
    {

        $fieldsName = $this->input->post('fieldsName');
        $colNumber = $this->input->post('colNumber');
        $lookingFor = $this->input->post('lookingFor');
        $groupName = trim($this->input->post('groupName'));
        $keyword = trim($this->input->post('keyword'));
        $search_against_groupName = trim($this->input->post('search_against_groupName'));
        $search_against_keyword = trim($this->input->post('search_against_keyword'));
        $search_against_country = $this->input->post('search_against_country');

        $response = array();

        if (@$_FILES['uploadCsv']['tmp_name'] != '') {

            $content = file(@$_FILES['uploadCsv']['tmp_name']);

            //check if file is empty or not
            if (empty($content) || (count($content) === 1 && empty(trim($content[0])))) {

                $response['err'] = 1;
                $response['msg'] = 'CSV file is empty';
            } else {

                //now upload the file
                $res = uploadFile('uploadCsv', '*', 'enrichment_csv');

                if ($res['success']) {
                    $path = $res['path'];
                    //insert in db
                    $condition = array();
                    $is_insert = TRUE;
                    $insertArr = array(
                        'colNumber' => $colNumber,
                        'fieldsName' => $fieldsName,
                        'lookingFor' => $lookingFor,
                        'groupName' => $groupName,
                        'keyword' => $keyword,
                        'search_against_groupName' => $search_against_groupName,
                        'search_against_keyword' => $search_against_keyword,
                        'search_against_country' => $search_against_country,
                        'filePath' => $path
                    );

                    $lastInsertedId = ManageData(ENRICHMENT_CSV_FILE, $condition, $insertArr, $is_insert);

                    if ($lastInsertedId > 0) {

                        //get total number of records from file
                        $uploadedCsv = $_FILES['uploadCsv']['tmp_name'];
                        $fp = new SplFileObject($uploadedCsv, 'r');
                        $header = preg_replace( "/\r|\n/", "", $fp->current());

                        $fp->seek(PHP_INT_MAX);         // got last line of file
                        $totalRecords = $fp->key();     // get last line's number
                        $fp->rewind();                  // go to first line 
                        $fp = null;                     // close file by null (Because there is only method to close the file in splFileObject)

                        //insert record in cron_status table
                        $condition = array();
                        $is_insert = TRUE;
                        $insertArr = array('filePath' => $path, 'totalRecords' => $totalRecords, 'groupName' => $groupName, 'keyword' => $keyword, 'header' => json_encode(explode(",",$header)));
                        ManageData(ENRICHMENT_CRON_STATUS, $condition, $insertArr, $is_insert);

                        $this->mdl_csv->insertGroupName($groupName);
                        $this->mdl_csv->insertKeyword($keyword);

                        //add data in history table
                        $explodeFileArr = explode('/', $path);
                        $fileName = array_pop($explodeFileArr);
                        $jsonValue = array('groupName' => $groupName, 'keyword' => $keyword);
                        $jsonValue = json_encode($jsonValue);
                        $this->mdl_history->addInHistoryTable($fileName, 'enrichment', 1, $jsonValue, $totalRecords);

                        $response['err'] = 0;
                        $response['msg'] = "File uploaded successfully";
                    } else {
                        $response['err'] = 1;
                        $response['msg'] = 'Something went wrong. File uploading process failed';
                    }
                } else {
                    $response['err'] = 1;
                    $response['msg'] = 'Something went wrong. File uploading process failed';
                }
            }
        } else {
            $response['err'] = 1;
            $response['msg'] = 'Something went wrong. Please try again later';
        }

        echo json_encode($response);
    }
}
