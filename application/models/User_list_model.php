<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_list_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_unsubscribed_fileter_data');
    }

    public function getUserData($getData,$start,$perpage) {

        //get all unsubscribe phones
        //$unsubscribedPhoneArr = $this->mdl_unsubscribed_fileter_data->getAllUnsubscribedPhone();

        $global = @$getData['global'];
        $gender = @$getData['gender'];
        $city   = @$getData['city'];
        $country = @$getData['country'];
        $groupName   = @$getData['groupName'];
        $keyword   = @$getData['keyword'];
        $minAge = @$getData['minAge'];
        $maxAge = @$getData['maxAge'];
        $startDate = @$getData['startDate'];
        $endDate = @$getData['endDate'];
        
        $condition = array();
        $is_single = false;
        $is_like = array();

        if (@$global) {
            $is_like['allDataInString'] = $global;
        }

        if (@$gender) {
            $condition['gender'] = strtolower($gender);
        }

        if (@$city) {
            $condition['city'] = $city;   
        }

        if (@$country) {
            $condition['country'] = $country;   
        }        

        if (@$groupName) {
            $condition['groupName REGEXP'] = "[[:<:]]".trim($groupName)."[[:>:]]";   
        }

        if (@$keyword) {
            $condition['keyword REGEXP'] = "[[:<:]]".trim($keyword)."[[:>:]]";   
        }

        if (@$minAge && @$maxAge) {

            $condition['age >='] = $minAge;
            $condition['age <='] = $maxAge;

        }else if(@$minAge || @$maxAge){

            if (@$minAge) {
                $age = $minAge;
            }else{
                $age = $maxAge;
            }
            $condition['age'] = $age;            
        }

        if (@$startDate && @$endDate) {

            $condition['createdDate >='] = $startDate. ' 00:00:00';
            $condition['createdDate <='] = $endDate. ' 23:59:59';

        }else if (@$startDate || @$endDate) {
            
            if (@$startDate) {

                $condition['createdDate >='] = $startDate. ' 00:00:00';
                $condition['createdDate <='] = $startDate. ' 23:59:59';                

            }else{

                $condition['createdDate >='] = $endDate. ' 00:00:00';
                $condition['createdDate <='] = $endDate. ' 23:59:59';

            }
        }

        //$condition['phone !='] = ''; 


        //get count of all records with condition
        $this->db->where($condition);
        $this->db->like($is_like);

        /*if (count($unsubscribedPhoneArr) > 0) {
            $this->db->where_not_in('phone',$unsubscribedPhoneArr);    
        }*/
        $totalCount = $this->db->count_all_results(USER);
        // count ends

        $userdata = array();
        if ($totalCount > 0) {
            
            //start paginated records
            $this->db->where($condition);
            $this->db->like($is_like);

            /*if (count($unsubscribedPhoneArr) > 0) {
                $this->db->where_not_in('phone',$unsubscribedPhoneArr);    
            }*/
            $this->db->limit($perpage,$start);
            $userdata = $this->db->get(USER)->result_array();    
        }
        

        $response = array(
            'totalCount' => $totalCount,
            'userdata' => $userdata
        );


        return $response;

        
    }


}