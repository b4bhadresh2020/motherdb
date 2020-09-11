<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

class Salus extends CI_Controller
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: content-type');
        parent::__construct();

        $this->load->model('mdl_csv');
        $this->load->model('mdl_cron');
    }

    /*
     *  list code starts here
     */

    public function manage($protocol, $domain)
    {

        $getData = $_GET;

        if (!empty($getData)) {

            $status = '';

            if (isset($getData['prefill'])) {

                $emailId    = $getData['prefill']['email'];
                $phone      = $getData['prefill']['phone'];
                $loanAmount = $getData['prefill']['loan_amount'];
                $loanPeriod = $getData['prefill']['loan_period'];
                $status     = 0;

            } else if (isset($getData['status'])) {

                $emailId    = $getData['email'];
                $phone      = $getData['phone'];
                $loanAmount = $getData['loan_amount'];
                $loanPeriod = $getData['loan_period'];

                if ($getData['status'] == 'duplicate') {
                    $status = 1;
                } else if ($getData['status'] == 'lowquality') {
                    $status = 2;
                }
            }

            if (!empty($emailId) && !empty($phone) && !empty($loanAmount) && !empty($loanPeriod)) {

                $isValidEmail = isValidEmail($emailId);

                if ($isValidEmail == 1) {

                    $domainExpload = explode('.', $domain);
                    $keyword       = $domainExpload[0];
                    $groupName     = $domainExpload[1];

                    //insert in user table
                    $condition       = array();
                    $is_insert       = true;
                    $allDataInString = $emailId . '+' . $phone . '+' . $groupName . '+' . $keyword;
                    $r_id = rand(1,10000); //make random number between 1 to 10000
                    $insertData      = array('emailId' => $emailId, 'phone' => $phone, 'groupName' => $groupName, 'keyword' => $keyword, 'allDataInString' => $allDataInString, 'r_id' => $r_id);
                    $userId          = ManageData(USER, $condition, $insertData, $is_insert);

                    if ($userId > 0) {
                        //insert record in loan_master table
                        $condition    = array();
                        $is_insert    = true;
                        $insertData   = array('userId' => $userId, 'loanAmount' => $loanAmount, 'loanPeriod' => $loanPeriod, 'domainName' => $keyword, 'status' => $status);
                        $loanMasterId = ManageData(LOAN_MASTER, $condition, $insertData, $is_insert);

                        if ($loanMasterId > 0) {

                            $this->mdl_csv->insertGroupName($groupName);
                            $this->mdl_csv->insertKeyword($keyword);

                            if (@$getData['gender'] == 'male') {

                                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male + 1  WHERE keyword = '$keyword'";
                                $this->db->query($sql_keyword);

                                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male + 1  WHERE groupName = '$groupName'";
                                $this->db->query($sql_groupName);

                            }

                            if (@$getData['gender'] == 'female') {

                                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female + 1  WHERE keyword = '$keyword'";
                                $this->db->query($sql_keyword);

                                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female + 1  WHERE groupName = '$groupName'";
                                $this->db->query($sql_groupName);
                            }

                            if (@$getData['gender'] != 'male' && @$getData['gender'] != 'female') {

                                $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other + 1  WHERE keyword = '$keyword'";
                                $this->db->query($sql_keyword);

                                $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other + 1  WHERE groupName = '$groupName'";
                                $this->db->query($sql_groupName);
                            }
                            

                            echo 'Data Inserted';

                        } else {

                            //delete record from user table
                            $tables = array(USER);
                            $this->db->where("userId", $userId);
                            $this->db->delete($tables);
                            echo 'Error occurs. Data is not inserted';
                        }
                    } else {
                        echo 'Error occurs. Data is not inserted';
                    }
                } else {
                    echo 'Invalid Email Format';
                }
            } else {
                echo 'Data is Empty';
            }

        } else {
            echo 'Data is Empty';
        }

    }

    /*
 *  list code ends here
 */

}
