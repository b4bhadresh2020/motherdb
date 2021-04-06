<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
    }

    public function manage($start = 0) {

        $data = array();
        $data['load_page'] = 'delete';
        $data['headerTitle'] = "Delete Data";
        $data["curTemplateName"] = "delete/list";
        $this->load->view('commonTemplates/templateLayout', $data);
    }



    /*
        AJAX CALL
    */
    function getDeleteDataCount(){

        $country = $this->input->post('country');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');

        $response = array();

        if ($country == '' && $groupName == '' && $keyword == '') {

            $response['err'] = 1;        
            $response['msg'] = 'Country OR Group Name OR Keyword is required';

        }else{

            $condition = array();

            if (@$country) {
                $condition['country'] = $country;   
            }

            if (@$groupName) {
                $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";   
            }

            if (@$keyword) {
                $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
            }

            //get userIds
            $is_single = FALSE;
            $getUserDataCount = GetAllRecordCount(USER,$condition);

            if ($getUserDataCount > 0) {
                $response['err'] = 0;        
                $response['count'] = $getUserDataCount;
            }else{
                $response['err'] = 1;        
                $response['msg'] = 'No data found';
            }
        }

        echo json_encode($response);
    }



    /*
        AJAX CALL
    */
    function deleteDataRecursively(){

        $country = $this->input->post('country');
        $groupName = $this->input->post('groupName');
        $keyword = $this->input->post('keyword');

        $response = array();

        if ($country == '' && $groupName == '' && $keyword == '') {

            $response['err'] = 1;        
            $response['msg'] = 'Country OR Group Name OR Keyword is required';

        }else{

            $condition = array();

            if (@$country) {
                $condition['country'] = $country;   
            }

            if (@$groupName) {
                $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";   
            }

            if (@$keyword) {
                $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
            }

            //get userIds
            $is_single = FALSE;
            $limit = 500;  //may client wants to increase the limit
            $this->db->limit($limit);
            $getUserData = GetAllRecord(USER,$condition,$is_single,array(),array(),array(),'userId,groupName,keyword,country,allDataInString, gender');


            if (count($getUserData) > 0) {

                foreach ($getUserData as $ud) {

                    $updateArr = array();

                     //update allDataInString
                    $currentAllDataInString = $ud['allDataInString']; 
                    $explodeAllDataInString = explode('+', $currentAllDataInString);

                    //for country
                    if(@$country){

                        $updateArr['country'] = '';

                        //for allDataInString
                        $stringCountryKey = array_search($country, $explodeAllDataInString);
                        unset($explodeAllDataInString[$stringCountryKey]);

                    }

                    //for groupName
                    if(@$groupName){

                        $currentGroupData = $ud['groupName'];                       // comma separated string 
                        $explodeGroup = explode(',', $currentGroupData);            // make array
                        $groupKey = array_search($groupName, $explodeGroup);        // search key by value
                        unset($explodeGroup[$groupKey]);                            // remove that key
                        $implodeGroup = implode(',',$explodeGroup);                 // make again comma separated string
                        $updateArr['groupName'] = $implodeGroup;

                        //for allDataInString
                        $stringGroupNameKey = array_search($groupName, $explodeAllDataInString);
                        unset($explodeAllDataInString[$stringGroupNameKey]);

                        // new edited start
                        $con_groupName = $ud['groupName'];


                        if(@$ud['gender'] == 'male'){

                            $sql_country = "UPDATE ".GROUP_MASTER." SET male = male - 1  WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_country);

                        } 

                        if(@$ud['gender'] == 'female'){

                            $sql_country = "UPDATE ".GROUP_MASTER." SET female = female - 1  WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_country);

                        }

                        if(@$ud['gender'] != 'male' && @$ud['gender'] != 'female'){

                            $sql_groupName = "UPDATE ".GROUP_MASTER." SET other = other - 1  WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_groupName);

                        }

                        // new edited end


                    }


                    //for keyword
                    if(@$keyword){

                        $currentKeywordData = $ud['keyword'];                           // comma separated string 
                        $explodeKeyword = explode(',', $currentKeywordData);            // make array
                        $keywordKey = array_search($keyword, $explodeKeyword);          // search key by value
                        unset($explodeKeyword[$keywordKey]);                            // remove that key
                        $implodeKeyword = implode(',',$explodeKeyword);                 // make again comma separated string
                        $updateArr['keyword'] = $implodeKeyword;

                        //for allDataInString
                        $stringKeywordKey = array_search($keyword, $explodeAllDataInString);
                        unset($explodeAllDataInString[$stringKeywordKey]);

                        // new edited start
                        $con_keyword = $ud['keyword'];


                        $sql_keywordCountryCount = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = total - 1  WHERE keyword = '$con_keyword'";
                    
                        $this->db->query($sql_keywordCountryCount);

                        $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";

                        $this->db->query($sql_keywordCountryCount_zero);

                        

                        if(@$ud['gender'] == 'male'){

                            $sql_country = "UPDATE ".KEYWORD_MASTER." SET male = male - 1  WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_country);

                        } 

                        if(@$ud['gender'] == 'female'){

                            $sql_country = "UPDATE ".KEYWORD_MASTER." SET female = female - 1  WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_country);

                        }

                        if(@$ud['gender'] != 'male' && @$ud['gender'] != 'female'){

                            $sql_groupName = "UPDATE ".KEYWORD_MASTER." SET other = other - 1  WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_groupName);

                        }

                        // new edited end
                    }



                    // if(@$country AND @$keyword){

                    //     $con_country = $ud['country'];
                    //     $con_keyword = $ud['keyword'];

                    //     $sql_keywordCountryCount = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = total - 1  WHERE country = '$con_country' AND keyword = '$con_keyword'";

                    //     $this->db->query($sql_keywordCountryCount);

                    //     $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";

                    //     $this->db->query($sql_keywordCountryCount_zero);

                    // } else if(@$country) {

                    //     $con_country = $ud['country'];

                    //     $sql_keywordCountryCount = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = total - 1  WHERE country = '$con_country'";

                    //     $this->db->query($sql_keywordCountryCount);

                    //     $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";

                    //     $this->db->query($sql_keywordCountryCount_zero);

                    // } else if(@$keyword) {

                    //     $con_keyword = $ud['keyword'];

                    //     $sql_keywordCountryCount = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = total - 1  WHERE keyword = '$con_keyword'";
                    
                    //     $this->db->query($sql_keywordCountryCount);

                    //     $sql_keywordCountryCount_zero = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = 0  WHERE total < 0";

                    //     $this->db->query($sql_keywordCountryCount_zero);

                    // }


                    //now make '+' separated string
                    $implodeAllDataInString = implode('+',$explodeAllDataInString);                 // make again comma separated string
                    $updateArr['allDataInString'] = $implodeAllDataInString;

                    //now update record
                    $condition = array('userId' => $ud['userId']);
                    $is_insert = FALSE;
                    ManageData(USER,$condition,$updateArr,$is_insert);
                    
                }

                $response['err'] = 0;
                $response['count'] = count($getUserData);                    

            }else{  

                $response['err'] = 2;        
                $response['msg'] = 'Record Removed Successfully';        
            }
        }

        echo json_encode($response);
    }

}