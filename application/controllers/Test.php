<?php

/**
 * 
 */
class Test extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());
    }

    public function manage($start = 0) {
        $data = array();
        $data['load_page'] = 'test';
        $data["curTemplateName"] = "test/addEdit";
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
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');
        $numberOfInstertedEnteries = 0;

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
                    $insertArr = array(
                        'colNumber' => $colNumber,
                        'fieldsName' => $fieldsName,
                        'customfieldsName' => $customfieldsName,
                        'groupName' => $groupName,
                        'keyword' => $keyword,
                        'filePath' => $path
                    );

                    $lastInsertedId = ManageData(CSV_FILE_DATA,$condition,$insertArr,$is_insert);

                    if ($lastInsertedId > 0) {
                        
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


    
}