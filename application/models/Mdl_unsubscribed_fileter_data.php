<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_unsubscribed_fileter_data extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_other_db');
    }

    public function getUserFilteredDataByPhone($toBeFilteredArr,$needOnlyPhoneArr = FALSE) {

        $unsubscribePhoneArr = array(); // all unsubscribed phone array 
       
        $unsubscribeList = $this->mdl_other_db->getUnsubscriberListByUniqueKey();

        if (count($unsubscribeList) > 0) {

            //get phone number from unique id
            foreach ($unsubscribeList as $value) {
                $qry = "SELECT `phone` FROM `user` WHERE `userId` IN ( SELECT userId FROM `batch_user` WHERE `uniqueKey` = '{$value}' )";
                $phoneArr = GetDatabyqry($qry);
                
                if (count($phoneArr) > 0) {
                    if ($phoneArr[0]['phone'] != '') {
                        if (!in_array($phoneArr[0]['phone'], $unsubscribePhoneArr)) {
                            $unsubscribePhoneArr[] = $phoneArr[0]['phone'];
                        }
                    }
                }
            }
        }

        //get phone numbers from unsubscriber table too
        $condition = array();
        $is_single = FALSE;
        $getPhoneData = GetAllRecord(UNSUBSCRIBER,$condition,$is_single,array(),array(),array(),'phone');

        if (count($getPhoneData) > 0) {
            foreach ($getPhoneData as $value) {
                if (!in_array($value['phone'],$unsubscribePhoneArr)) {
                    $unsubscribePhoneArr[] = $value['phone'];
                }
            }
        }
        
        if ($needOnlyPhoneArr) {

            return $unsubscribePhoneArr;

        }else{

            if (count($unsubscribePhoneArr) > 0) {
            
                $userFilteredData = array();
                $i = 0;
                foreach ($toBeFilteredArr as $value) {

                    //check if phone is not in unsubscribePhoneArr
                    if (!in_array($value['phone'], $unsubscribePhoneArr)) {
                        $userFilteredData[] = $toBeFilteredArr[$i];
                    }
                    $i++;
                }

            }else{
                $userFilteredData = $toBeFilteredArr;
            }

            return $userFilteredData;   
        }
             
    }


    public function getAllUnsubscribedPhone(){

        return $this->getUserFilteredDataByPhone(array(),TRUE);
    }


    public function getUnsubscriberUserList($getData,$start,$perpage){

        $global = @$getData['global'];
        $fileName = @$getData['fileName'];

        $condition = array();

        if(@$fileName){
            $condition['fileName'] = $fileName;
        }

        $like = array();
        if (@$global) {

            $search   = trim($global); 
            $fieldArr = array('firstName','lastName','phone','emailId','country','gender');
        
            foreach ($fieldArr as $value) {
                $like[$value] = $search;
            }
            
        }

        $is_single = FALSE;
        $unsubscriberCount = GetAllRecordCount(UNSUBSCRIBER,$condition,$is_single,array(),array($like),array());

        $unsubscriberData = array();
        if ($unsubscriberCount > 0) {
            $this->db->limit($perpage,$start);
            $unsubscriberData = GetAllRecord(UNSUBSCRIBER,$condition,$is_single,array(),array($like),array(array('unsubscriberId' => 'DESC')));
        }

        $response = array(
            'totalCount' => $unsubscriberCount,
            'unsubscriberData' => $unsubscriberData 
        );

        return $response;

        /*$where = '';
        $where2 = '';

        if (count($getData) > 0) {
            
            $global  = @$getData['global'];
            
            if($global){

                $whArr = array('firstName','lastName','phone','emailId','country','gender');

                $where = "WHERE ";
                $where2 = "WHERE ";

                for ($i=0; $i < count($whArr); $i++) { 

                    if ($i == count($whArr) - 1) {
                        $where .= "`mu`.".$whArr[$i]." LIKE '%{$global}%' ";
                        $where2 .= $whArr[$i]." LIKE '%{$global}%' ";
                    }else{
                        $where .= "`mu`.".$whArr[$i]." LIKE '%{$global}%' OR ";
                        $where2 .= $whArr[$i]." LIKE '%{$global}%' OR ";
                    }
                    
                }
            }
        }

        $qry = "SELECT `mu`.`firstName`, `mu`.`lastName`, `mu`.`phone`,`mu`.`emailId`,`mu`.`country`,`mu`.`gender`, `mus`.`createdDate`
                        FROM `".DEFAULT_DB."`.`batch_user` AS `mbu`
                        JOIN `".DEFAULT_DB."`.`user` AS `mu` ON `mu`.`userId` = `mbu`.`userId`
                        JOIN `".DEFAULT_DB."`.`batch` AS `mb` ON `mb`.`batchId` = `mbu`.`batchId`
                        JOIN `".OTHER_DB."`.`sms_unsubscriber_list` AS `mus` ON `mus`.`uniqueKey` = `mbu`.`uniqueKey` 
                        ".$where;
        $qry .= " UNION ";
        $qry .= "SELECT `firstName`,`lastName`,`phone`,`emailId`,`country`,`gender`,`createdDate`
                FROM `unsubscriber` ".$where2;



        $unsubscriberData = GetDatabyqry($qry);

        // last_query();
        // pre($unsubscriberData);die;

        return $unsubscriberData;*/
        
    }

    
}