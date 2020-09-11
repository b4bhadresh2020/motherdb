<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Salus_list_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getSalusData($getData,$start,$perpage) {

        $domainName = @$getData['domainName'];
        $status = @$getData['status'];

        $condition = array();
        $is_single = false;
        
        if (@$domainName) {
            $condition['loan_master.domainName'] = $domainName;
        }

        if (isset($status)) {
            $condition['loan_master.status'] = $status;
        }

        //get count of all records with condition
        $this->db->select()
            ->from(USER)
            ->join(LOAN_MASTER, USER.'.userId = '.LOAN_MASTER.'.userId')
            ->where($condition);
        $totalCount = $this->db->count_all_results();

        // count ends

        //start paginated records
        $this->db->select(USER.'.userId, '.USER.'.emailId,'.USER.'.phone,'.LOAN_MASTER.'.loanAmount,'.LOAN_MASTER.'.loanPeriod,'.LOAN_MASTER.'.domainName, '.LOAN_MASTER.'.status')
            ->from(USER)
            ->join(LOAN_MASTER, USER.'.userId = '.LOAN_MASTER.'.userId')
            ->where($condition)
            ->limit($perpage,$start);
        $salusData = $this->db->get()->result_array();
        //last_query();die;
        $response = array(
            'totalCount' => $totalCount,
            'salusData' => $salusData
        );
        return $response;
        
    }

    


}