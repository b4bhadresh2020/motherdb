<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_repost_schedule extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function insertRepostSchedule($repostScheduleData){
        //Insert repost schedule data in repost schedule table
        $condition = array();
        $keywordDataArr = $repostScheduleData;
        $is_insert = TRUE;
        $id = ManageData(REPOST_SCHEDULE,$condition,$keywordDataArr,$is_insert);
        return $id;
    }

    public function insertRepostScheduleLiveDeliveryData($liverDeliveryData){
         //Insert repost schedule live delivery data in repost schedule live delivery table
         $condition = array();
         $keywordDataArr = $liverDeliveryData;
         $is_insert = TRUE;
         ManageData(REPOST_SCHEDULE_LIVE_DELIVERY_DATA,$condition,$keywordDataArr,$is_insert);
    }

    public function getRepostscheduleData($getData,$start,$perpage) {

        $condition = array();
        //get count of all records with condition
        $this->db->select()
            ->from(REPOST_SCHEDULE)
            ->where($condition);
        $totalCount = $this->db->count_all_results();

        //start paginated records
        $this->db->select("*")
            ->from(REPOST_SCHEDULE)
            ->where($condition)
            ->limit($perpage,$start);
        $repostScheduleData = $this->db->get()->result_array();
        //last_query();die;
        $response = array(
            'totalCount' => $totalCount,
            'repostScheduleData' => $repostScheduleData
        );
        return $response;
        
    }

    public function getProvider($providerIdArr){
        $ci = & get_instance();
        $ci->db->select('listname,provider');
        $ci->db->where_in('id',$providerIdArr);
        $res = $ci->db->get(PROVIDERS);
        return $res->result_array();
    }
}