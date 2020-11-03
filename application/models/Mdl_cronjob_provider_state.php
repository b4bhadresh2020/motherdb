<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_cronjob_provider_state extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }


    public function get_provider_data($getData,$start,$perpage){
        
        $condition = array(
            "status !=" => "3"
        );
        $dataCount = GetAllRecordCount(CSV_FILE_PROVIDER_DATA,$condition);

        $cronjobProviderStatData = array();

        if ($dataCount > 0) {
            $this->db->limit($perpage,$start);
            $is_single = false;
            $cronjobProviderStatData = JoinData(CSV_FILE_PROVIDER_DATA, $condition,CRON_STATUS,"cronStatusId","cronStatusId","left",$is_single,array(),'','');     
        }

        foreach ($cronjobProviderStatData as $key => $provider) {
            // count record using limit.
            $is_single = TRUE;
            $sentRecordCondition = array(
                'providerId' => $provider['id'],
                'status' => 1                
            );
            $totalSentRecord = GetAllRecordCount(CSV_CRON_USER_DATA,$sentRecordCondition,$is_single,array(),array(),array());  
            $cronjobProviderStatData[$key]['totalSentRecords'] = $totalSentRecord;
        }

        $response = array(
            'totalCount' => $dataCount,
            'cronjobProviderStatData' => $cronjobProviderStatData 
        );

        return $response;
        
    }

    public function get_provider_history_data($getData,$start,$perpage,$id){

        $condition = array(
            "providerId" => $id
        );

        $email = @$getData['email'];
        $sendDate = @$getData['sendDate'];      

        
        if (@$email) {
            $condition['emailId'] = $email;
        }

        if (@$sendDate) {
            $condition['sendDate'] = $sendDate;
        }        
        
        $is_single = false;
        $userData = array();        
        $this->db->from(CSV_CRON_USER_DATA);
        $this->db->join(USER,'user.userId = csv_cron_user_data.userId');
        $this->db->join(CSV_FILE_PROVIDER_DATA,'csv_file_provider_data.id = csv_cron_user_data.providerId');
        $this->db->where($condition);
        $totalUserData = $this->db->count_all_results();

        $this->db->select('firstName,lastName,emailId,aweberResponse,transmitviaResponse,ongageResponse,sendgridResponse,sendDate,providerName,providerList,csv_cron_user_data.status');
        $this->db->from(CSV_CRON_USER_DATA);
        $this->db->join(USER,'user.userId = csv_cron_user_data.userId');
        $this->db->join(CSV_FILE_PROVIDER_DATA,'csv_file_provider_data.id = csv_cron_user_data.providerId');
        $this->db->where($condition);
        $this->db->limit($perpage,$start);
        $userData = $this->db->get()->result_array();

        $dataCount = $totalUserData;

        $response = array(
            'totalCount' => $dataCount,
            'cronjobProviderHistoryData' => $userData 
        );

        return $response;           
    }
}