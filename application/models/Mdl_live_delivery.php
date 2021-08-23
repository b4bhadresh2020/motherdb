<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_live_delivery extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getLiveDeliveryStatData($getData,$start,$perPage,$wantToDataRecords = FALSE) {

        $apikey = @$getData['apikey'];
        $chooseFilter = @$getData['chooseFilter'];

        // if apikey is blank
        if(empty($apikey)){
            $condition = array('isInActive' => 0);
            $is_single = TRUE;
            $getApiKey = GetAllRecord(LIVE_DELIVERY,$condition,$is_single,array(),array(),array(array('liveDeliveryId' => 'DESC')),'apikey,groupName,keyword');
            $apikey = $getApiKey['apikey'];
        }
        
        
        //for return value
        if ($chooseFilter == 'td') {

            //get td = today's successCount and failureCount
            $today = date('Y-m-d');

            if ($wantToDataRecords) {
                $filteredData = $this->getFilteredData($today,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($today,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($today,$today,$apikey);    
            }
                
        }elseif ($chooseFilter == 'yd') {

            //get yd = yester's successCount and failureCount
            $yesterday = date('Y-m-d',strtotime("-1 day"));

            if ($wantToDataRecords) {
                $filteredData = $this->getFilteredData($yesterday,$yesterday,$apikey,$start,$perPage,$getData);    
            }else{
                $countsArr  = $this->getCounts($yesterday,$yesterday,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($yesterday,$yesterday,$apikey);    
            }
                
            

        }elseif ($chooseFilter == 'lSvnD') {
            
            //get lSvnD = last seven day's successCount and failureCount
            $lastSevenDay   = date('Y-m-d',strtotime("-7 days"));
            $today          = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($lastSevenDay,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($lastSevenDay,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($lastSevenDay,$today,$apikey);    
            }


        }elseif ($chooseFilter == 'lThrtyD') {
            
            //get lThrtyD = last thirty day's successCount and failureCount
            $lastThirtyDay  = date('Y-m-d',strtotime("-1 month"));
            $today          = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($lastThirtyDay,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($lastThirtyDay,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($lastThirtyDay,$today,$apikey);    
            }

        }elseif ($chooseFilter == 'wTd') {

            //get wTd = week to day's successCount and failureCount
            // week to day means currnt week's first date to current date
            $day        = date('w');
            $weekToDate = date('Y-m-d', strtotime('-'.$day.' days'));
            $today      = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($weekToDate,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($weekToDate,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($weekToDate,$today,$apikey);    
            }
            
        }elseif ($chooseFilter == 'mTd') {

            //get mTd = month to day's successCount and failureCount
            // month to day means currnt month's first date to current date
            $monthToDate    = date('Y-m-01');
            $today          = date('Y-m-d');    

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($monthToDate,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($monthToDate,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($monthToDate,$today,$apikey);    
            }

        }elseif ($chooseFilter == 'qTd') {

            //get qTd = quarter to day's successCount and failureCount
            // quarter to day means currnt quarter's first date to current date
            $current_quarter = ceil(date('n') / 3);
            $quarterToDate   = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
            $today           = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($quarterToDate,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($quarterToDate,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($quarterToDate,$today,$apikey);    
            }

        }elseif ($chooseFilter == 'yTd') {
            
            //get yTd = year to day's successCount and failureCount
            // year to day means currnt year's first date to current date
            $yearToDate = date('Y-01-01');
            $today      = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($yearToDate,$today,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($yearToDate,$today,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($yearToDate,$today,$apikey);    
            }

        }elseif ($chooseFilter == 'pw') {
            
            //get pw = previous week's successCount and failureCount
            $previousWeek = date('Y-m-d',strtotime("-14 days"));
            $lastSevenDay = date('Y-m-d',strtotime("-7 days"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousWeek,$lastSevenDay,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($previousWeek,$lastSevenDay,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($previousWeek,$lastSevenDay,$apikey);      
            }

        }elseif ($chooseFilter == 'pm') {
            
            //get pm = previous month's successCount and failureCount
            $previousMonth = date('Y-m-d',strtotime("-2 months"));
            $lastMonthDay  = date('Y-m-d',strtotime("-1 month"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousMonth,$lastMonthDay,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($previousMonth,$lastMonthDay,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($previousMonth,$lastMonthDay,$apikey);    
            }

        }elseif ($chooseFilter == 'pq') {
            
            //get py = previous quarter's successCount and failureCount
            $current_quarter = ceil(date('n') / 3);

            //previous quarter
            $year = date('Y');
            $previousQuaterMonth = ($current_quarter * 3) - 5;

            if($previousQuaterMonth < 1){
               $previousQuaterMonth = "10";
               $year = $year-1;
           }

            $previousQuarter = date('Y-m-d', strtotime($year . '-' . ($previousQuaterMonth) . '-1'));
            $currentQuarter  = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousQuarter,$currentQuarter,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($previousQuarter,$currentQuarter,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($previousQuarter,$currentQuarter,$apikey);    
            }

        }elseif ($chooseFilter == 'py') {
            
            //get py = previous year's successCount and failureCount
            $previousYear = date('Y-m-d',strtotime("-2 years"));
            $lastYearDay  = date('Y-m-d',strtotime("-1 year"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousYear,$lastYearDay,$apikey,$start,$perPage,$getData);
            }else{
                $countsArr  = $this->getCounts($previousYear,$lastYearDay,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($previousYear,$lastYearDay,$apikey);    
            }

        }elseif ($chooseFilter == 'cd') {
            
            //get cd = custom's successCount and failureCount
            $startDate = $getData['startDate'];
            $endDate = $getData['endDate'];

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($startDate,$endDate,$apikey,$start,$perPage,$getData);  
            }else{
                $countsArr  = $this->getCounts($startDate,$endDate,$apikey);
                $rejectDetailCountsArr = $this->getRejectDetailCount($startDate,$endDate,$apikey);    
            }

        }else{


            if (!@$apikey) {

                $condition = array();
                $is_single = TRUE;
                $getApiKeys = GetAllRecord(LIVE_DELIVERY,$condition,$is_single,array(),array(),array(array('liveDeliveryId' => 'DESC')),'apikey');

                if (count($getApiKeys) > 0) {
                    $apikey = $getApiKeys['apikey'];
                }else{
                    $countsArr =  array('successCount' => 0,'failureCount' => 0, 'checkEmailCount' => 0);
                    $rejectDetailCountsArr = array('duplicateCount' => 0, 'blacklistCount' => 0, 'duplicateCount' => 0, 'blacklistCount' => 0, 'serverIssue' => 0, 'apiKeyIsNotActive' => 0, 'emailIsRequired' => 0, 'phoneIsRequired' => 0, 'emailIsBlank' => 0, 'phoneIsBlank' => 0, 'invalidEmailFormat' => 0, 'invalidPhone' => 0, 'invalidGender' => 0, 'teliaMxBlock' => 0, 'luukkuMxBlock' => 0 , 'ppMxBlock' => 0, 'alreadyUnsubscribed' => 0, 'yahooMxBlock' => 0, 'icloudMxBlock' => 0);

                    return array('countsArr' => $countsArr,'rejectDetailCountsArr' => $rejectDetailCountsArr,'filteredData' => array());
                }
            }


            if($wantToDataRecords){

                //get data

                $condition = array('apikey' => $apikey);

                if (@$getData['chooseSucFailRes'] != '') {
                    $chooseSucFailRes = $getData['chooseSucFailRes'];
                    $condition['sucFailMsgIndex'] = $chooseSucFailRes; 
                }

                if (@$getData['globleSearch'] != '' ) {

                    $globleSearch = trim($getData['globleSearch']);
                    $where = '(firstName LIKE "%'.$globleSearch.'%" OR lastName LIKE "%'.$globleSearch.'%" OR emailId LIKE "%'.$globleSearch.'%" OR city LIKE "%'.$globleSearch.'%")';
                    $this->db->where($where);
                }

                $is_single = FALSE;
                $this->db->limit($perPage,$start);
                $filteredData = GetAllRecord(LIVE_DELIVERY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryDataId' => 'DESC')));
                //---------------------------------------------------------------------------------------------------

            }else{


                //---------------------------------------------------------------------------------------------------
                //get all successCount and failureCount
                $condition = array(
                    'apikey' => $apikey,
                    'isFail' => 0
                );
                $successCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);

                $condition = array(
                    'apikey' => $apikey,
                    'isFail' => 1
                );
                $failureCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);

                $condition = array(
                    'apikey' => $apikey,
                    'isEmailChecked' => 1
                );
                $checkEmailCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);

                $countsArr =  array('successCount' => $successCount,'failureCount' => $failureCount, 'checkEmailCount' => $checkEmailCount);
                //---------------------------------------------------------------------------------------------------

                //get reject count in detail

                $reasonArr = array(1 => 'duplicateCount', 2 => 'blacklistCount', 3 => 'serverIssue', 4 => 'apiKeyIsNotActive', 5 => 'emailIsRequired', 6 => 'phoneIsRequired', 7 => 'emailIsBlank', 8 => 'phoneIsBlank', 9 => 'invalidEmailFormat', 10 => 'invalidPhone', 11 => 'invalidGender', 12 => 'teliaMxBlock', 13 => 'luukkuMxBlock', 14 => 'ppMxBlock', 15 => 'alreadyUnsubscribed', 16 => 'yahooMxBlock', 17 => 'icloudMxBlock' );

                $rejectDetailCountsArr = array();
                for ($i=1; $i <= count($reasonArr); $i++) { 

                     $condition = array(
                        'apikey' => $apikey,
                        'sucFailMsgIndex' => $i
                    );

                    $rejectCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);
                    $rejectDetailCountsArr[$reasonArr[$i]] = $rejectCount;
                }

                //---------------------------------------------------------------------------------------------------
            }
            
        }

        if($wantToDataRecords == TRUE){
             $countsArr = array();
             $rejectDetailCountsArr = array();
        }else{
            $filteredData = array();   
        }
        $returnArr = array('countsArr' => $countsArr,'rejectDetailCountsArr' => $rejectDetailCountsArr,'filteredData' => $filteredData);

        return $returnArr;
        
    }


    function getCounts($startDate,$endDate,$apikey){

        if ($this->isDateWithoutTime($startDate) == 'true') {
            $startDate = $startDate.' 00:00:00';
        }

        if ($this->isDateWithoutTime($endDate) == 'true') {
            $endDate = $endDate.' 23:59:59';   
        }

        $condition = array(
            'createdDate >=' => $startDate,
            'createdDate <=' => $endDate,
            'apikey' => $apikey,
            'isFail' => 0
        );
        $successCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);

        $condition = array(
            'createdDate >=' => $startDate,
            'createdDate <=' => $endDate,
            'apikey' => $apikey,
            'isFail' => 1
        );
        $failureCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);

        $condition = array(
            'createdDate >=' => $startDate,
            'createdDate <=' => $endDate,
            'apikey' => $apikey,
            'isEmailChecked' => 1
        );
        $checkEmailCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);
       
        return array('successCount' => $successCount,'failureCount' => $failureCount,'checkEmailCount' => $checkEmailCount);
    }



    function getRejectDetailCount($startDate,$endDate,$apikey){

        $reasonArr = array(
            1 => 'duplicateCount', 
            2 => 'blacklistCount',
            3 => 'serverIssue',
            4 => 'apiKeyIsNotActive',
            5 => 'emailIsRequired',
            6 => 'phoneIsRequired',
            7 => 'emailIsBlank',
            8 => 'phoneIsBlank',
            9 => 'invalidEmailFormat',
            10 => 'invalidPhone',
            11 => 'invalidGender',
            12 => 'teliaMxBlock',
            13 => 'luukkuMxBlock',
            14 => 'ppMxBlock',
            15 => 'alreadyUnsubscribed',
            16 => 'yahooMxBlock',
            17 => 'icloudMxBlock'
        );

        if ($this->isDateWithoutTime($startDate) == 'true') {
            $startDate = $startDate.' 00:00:00';
        }

        if ($this->isDateWithoutTime($endDate) == 'true') {
            $endDate = $endDate.' 23:59:59';   
        }

        $rejectCountArr = array();
        for ($i=1; $i <= count($reasonArr); $i++) { 

             $condition = array(
                'createdDate >=' => $startDate,
                'createdDate <=' => $endDate,
                'apikey' => $apikey,
                'sucFailMsgIndex' => $i
            );

            $rejectCount = GetAllRecordCount(LIVE_DELIVERY_DATA,$condition);
            $rejectCountArr[$reasonArr[$i]] = $rejectCount;
        }

        return $rejectCountArr;
    }



    function getFilteredData($startDate,$endDate,$apikey,$start,$perPage,$getData){
        
        if ($this->isDateWithoutTime($startDate) == 'true') {
            $startDate = $startDate.' 00:00:00';
        }

        if ($this->isDateWithoutTime($endDate) == 'true') {
            $endDate = $endDate.' 23:59:59';   
        }

        $condition = array(
            'createdDate >=' => $startDate,
            'createdDate <=' => $endDate,
            'apikey' => $apikey
        );

        if (@$getData['chooseSucFailRes'] != '') {
            $chooseSucFailRes = $getData['chooseSucFailRes'];
            $condition['sucFailMsgIndex'] = $chooseSucFailRes; 
        }

        if (@$getData['globleSearch'] != '' ) {

            $globleSearch = trim($getData['globleSearch']);
            $where = '(firstName LIKE "%'.$globleSearch.'%" OR lastName LIKE "%'.$globleSearch.'%" OR emailId LIKE "%'.$globleSearch.'%"  OR city LIKE "%'.$globleSearch.'%")';
            $this->db->where($where);
        }

        $is_single = FALSE;
        $this->db->limit($perPage,$start);
        $filteredData = GetAllRecord(LIVE_DELIVERY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryDataId' => 'DESC')));
        
        return $filteredData;
    }



    //check date format of startdate and enddate

    function isDateWithoutTime($dateFormat){

        if (preg_match("/^((([1-9]\d{3})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(([1-9]\d{3})\-02\-(0[1-9]|1\d|2[0-8]))|(([1-9]\d(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/",$dateFormat)) 
        {
        
            return 'true';
        
        } else {
        
            return 'false';
        }
    }


}