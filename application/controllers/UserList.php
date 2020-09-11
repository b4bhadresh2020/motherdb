<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UserList extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged()) {
            redirect(base_url());
        }

        $this->load->model('user_list_model');
        $this->load->model('mdl_history');
    }

    public function manage($start = 0)
    {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage   = 25;
        $userDataResponse = $this->user_list_model->getUserData($_GET, $start, $perPage);

        $dataTotalCount = $userDataResponse['totalCount'];
        $userdata = $userDataResponse['userdata'];

        $data = pagination_data('userList/manage/', $dataTotalCount, $start, 3, $perPage, $userdata);

        $data['load_page']       = 'userlist';
        $data["curTemplateName"] = "user/list";
        $data['headerTitle']     = "User List";
        $data['pageTitle']       = "User List";
        $data['dataCount']       = $dataTotalCount;
        $data['totalUserCount']  = GetAllRecordCount(USER);
        
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function exportCsv($global = '', $gender = '', $city = '', $country = '', $groupName = '', $keyword = '', $minAge = '', $maxAge = '', $startDate = '', $endDate = '', $start = 0, $perpage = 5000)
    {
        $getData = array(
            'global'    => urldecode($global),
            'gender'    => $gender,
            'city'      => urldecode($city),
            'country'   => $country,
            'groupName' => urldecode($groupName),
            'keyword'   => urldecode($keyword),
            'minAge'    => $minAge,
            'maxAge'    => $maxAge,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        );
        $userDataResponse = $this->user_list_model->getUserData($getData, $start, $perpage);

        $userdataCount = $userDataResponse['totalCount'];
        $userdata      = $userDataResponse['userdata'];

        if (count($userdata) > 0) {

            $reArrangeArray = array();
            $keyArr         = array('firstName', 'lastName', 'emailId', 'address', 'postCode', 'city', 'phone', 'gender', 'birthdateDay', 'birthdateMonth', 'birthdateYear', 'ip', 'participated', 'campaignSource');

            for ($i = 0; $i < count($userdata); $i++) {

                foreach ($keyArr as $value) {
                    $reArrangeArray[$i][$value] = $userdata[$i][$value];
                }

            }

            // file creation
            if ($start == 0) {

                $header   = array('Full Name', 'Last Name', 'Email Id', 'Address', 'Postcode', 'City', 'Phone', 'Gender', 'Birthdate Day', 'Birthdate Month', 'Birthdate Year', 'Ip', 'Participated', 'Campaign Source');
                $count    = $userdataCount;
                $filename = 'userdata_' . date('Y-m-d H:i:s') . '_Total_' . $count . '_Entries.csv';

                //add data in history table
                $jsonValue = json_encode($getData);
                $this->mdl_history->addInHistoryTable($filename, 'user', 0, $jsonValue, $count);

                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");

                $file = fopen('php://output', 'w');
                fputcsv($file, $header);
                fclose($file);
            }

            $file = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);

            if ($perpage == count($userdata)) {

                $start = $start + $perpage;
                $this->exportCsv($global, $gender, $city, $country, $groupName, $keyword, $minAge, $maxAge, $startDate, $endDate, $start);

            } else {
                exit;
            }

        } else if (count($userdata) == 0 && $start != 0) {
            exit;
        } else {

            $header         = array();
            $reArrangeArray = array(array('', 'There is no data !'));
            $filename       = 'blank_excel_' . date('Y-m-d H:i:s') . ".csv";

            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
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

    }

    /*
     *  delete code starts here
     */

    public function delete($userId = 0, $start = 0)
    {

        $tables = array(USER);

        $this->db->where("userId", $userId);
        $this->db->delete($tables);
        redirect("userList/manage/" . $start);
    }

    /*
     *  unlink(unsubscribe) code starts here
     */

    public function unlink($userId = 0, $start = 0)
    {

        //get user detail and insert it to unsubscribe list
        $condition = array('userId' => $userId);
        $is_single = true;
        $this->db->limit(1);
        $getUserDetail = GetAllRecord(USER, $condition, $is_single);

        if (count($getUserDetail) > 0) {

            $fieldArr = array('firstName', 'lastName', 'emailId', 'phone', 'country', 'gender');

            $dataArr = array();
            foreach ($fieldArr as $value) {
                $dataArr[$value] = $getUserDetail[$value];
            }

            //check duplicate entry
            $condition = $dataArr;
            $is_single = true;
            $this->db->limit(1);
            $getDupRec = GetAllRecord(UNSUBSCRIBER, $condition, $is_single);

            if (count($getDupRec) == 0) {
                $lastInsertedId = ManageData(UNSUBSCRIBER, array(), $dataArr, true);

                // die;

                //delete from main table and that is user but delete all where phone or email is same
                if ($getUserDetail['emailId'] != '') {

                    $condition     = array('emailId' => $getUserDetail['emailId']);
                    $userEmailData = GetAllRecord(USER, $condition, $is_single = false);

                    foreach ($userEmailData as $key => $value) {

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

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
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

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                            $this->db->query($sql_keywordCountryCount_zero);

                        }

                        if (@$value['gender'] != 'male' && @$value['gender'] != 'female') {

                            $sql_country = "UPDATE " . COUNTRY_MASTER . " SET other = other - 1  WHERE country = '$con_country'";
                            $this->db->query($sql_country);

                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other - 1 WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_keyword);

                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other - 1 WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_groupName);

                            $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                            $this->db->query($sql_keywordCountryCount);

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                            $this->db->query($sql_keywordCountryCount_zero);

                        }
                    }

                    $this->db->where('emailId', $getUserDetail['emailId']);
                    $this->db->delete(USER);

                }

                if ($getUserDetail['phone'] != '') {

                    $condition     = array('phone' => $getUserDetail['phone']);
                    $userPhoneData = GetAllRecord(USER, $condition, $is_single = false);

                    foreach ($userPhoneData as $key => $value) {

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

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
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

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                            $this->db->query($sql_keywordCountryCount_zero);

                        }

                        if (@$value['gender'] != 'male' && @$value['gender'] != 'female') {

                            $sql_country = "UPDATE " . COUNTRY_MASTER . " SET other = other - 1  WHERE country = '$con_country'";
                            $this->db->query($sql_country);

                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other - 1 WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_keyword);

                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other - 1 WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_groupName);

                            $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                            $this->db->query($sql_keywordCountryCount);

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                            $this->db->query($sql_keywordCountryCount_zero);

                        }
                    }

                    $this->db->where('phone', $getUserDetail['phone']);
                    $this->db->delete(USER);
                }

                //delete from batch_user
                /*$this->db->where('userId', $userId);
            $this->db->delete(BATCH_USER);  */
            }

        }
        redirect("userList/manage/" . $start);
    }

}
