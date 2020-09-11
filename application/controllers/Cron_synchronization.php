<?php

/**
 *
 */
class Cron_synchronization extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function data_synchronise()
    {

        $limit             = 2000;
        $isSyncCronRunning = getConfigVal('isSyncCronRunning');

        if ($isSyncCronRunning == 1) {

            //get email user count
            $emailCountQry = "SELECT COUNT(userId) AS `emailCount` FROM user WHERE emailId IN (SELECT emailId FROM unsubscriber WHERE emailId != '' )";
            $emailCount    = $this->db->query($emailCountQry)->row_array();
            //pre($emailCount);

            if ($emailCount['emailCount'] > 0) {

                //compare with 'email'
                if ($emailCount['emailCount'] <= $limit) {
                    $limit = $emailCount['emailCount'];
                }

                $userEmailData_sql = "SELECT gender, keyword, country, groupName FROM " . USER . " WHERE emailId IN (SELECT emailId FROM unsubscriber WHERE emailId != '') LIMIT " . $limit;

                $userEmailData = $this->db->query($userEmailData_sql)->result_array();

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

                        $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other - 1  WHERE keyword = '$con_keyword'";
                        $this->db->query($sql_keyword);

                        $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other - 1  WHERE groupName = '$con_groupName'";
                        $this->db->query($sql_groupName);

                        $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                        $this->db->query($sql_keywordCountryCount);

                        $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                        $this->db->query($sql_keywordCountryCount_zero);

                    }
                }

                $qryEmail = "DELETE FROM user WHERE emailId IN (SELECT emailId FROM unsubscriber WHERE emailId != '') LIMIT " . $limit;

                //$started = microtime(true);

                $this->db->query($qryEmail);

                //$end = microtime(true);

                //$difference = $end - $started;
                //$queryTime = number_format($difference, 10);
                //pre($queryTime);
                //last_query();
            } else {

                //get phone user count
                $phoneCountQry = "SELECT COUNT(userId) AS `phoneCount` FROM user WHERE phone IN (SELECT phone FROM unsubscriber WHERE phone != '' )";
                $phoneCount    = $this->db->query($phoneCountQry)->row_array();
                //pre($phoneCount);

                if ($phoneCount['phoneCount'] > 0) {

                    //now compare with 'phone'
                    if ($phoneCount['phoneCount'] <= $limit) {
                        $limit = $phoneCount['phoneCount'];
                    }

                    $userPhoneData_sql = "SELECT gender, keyword, country, groupName FROM " . USER . " WHERE phone IN (SELECT phone FROM unsubscriber WHERE phone != '' ) LIMIT " . $limit;

                    $userPhoneData = $this->db->query($userPhoneData_sql)->result_array();

                    pre(sizeof($userPhoneData));

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

                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other - 1  WHERE keyword = '$con_keyword'";
                            $this->db->query($sql_keyword);

                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other - 1  WHERE groupName = '$con_groupName'";
                            $this->db->query($sql_groupName);

                            $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total - 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
                            $this->db->query($sql_keywordCountryCount);

                            $sql_keywordCountryCount_zero = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = 0  WHERE total < 0";
                            $this->db->query($sql_keywordCountryCount_zero);

                        }
                    }

                    $qryPhone = "DELETE FROM user WHERE phone IN (SELECT phone FROM unsubscriber WHERE phone != '') LIMIT " . $limit;

                    //$started = microtime(true);

                    $this->db->query($qryPhone);

                    //$end = microtime(true);

                    //$difference = $end - $started;
                    //$queryTime = number_format($difference, 10);
                    //pre($queryTime);
                    //last_query();

                } else {

                    //update status
                    $isSyncCronRunning = 0;
                    $condition         = array("configKey" => 'isSyncCronRunning');
                    $dataArr           = array("configVal" => $isSyncCronRunning);
                    ManageData(SITECONFIG, $condition, $dataArr, false);
                }
            }

        } else {
            echo 'Please click SYNC button';
        }
    }

}
