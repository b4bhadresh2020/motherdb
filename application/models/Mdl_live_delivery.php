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
                $getCounterArr = $this->getStatCounter($apikey,$today,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];
            }
                
        }elseif ($chooseFilter == 'yd') {

            //get yd = yester's successCount and failureCount
            $yesterday = date('Y-m-d',strtotime("-1 day"));

            if ($wantToDataRecords) {
                $filteredData = $this->getFilteredData($yesterday,$yesterday,$apikey,$start,$perPage,$getData);    
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$yesterday,$yesterday);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];    
            }
                
            

        }elseif ($chooseFilter == 'lSvnD') {
            
            //get lSvnD = last seven day's successCount and failureCount
            $lastSevenDay   = date('Y-m-d',strtotime("-7 days"));
            $today          = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($lastSevenDay,$today,$apikey,$start,$perPage,$getData);
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$lastSevenDay,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];  
            }


        }elseif ($chooseFilter == 'lThrtyD') {
            
            //get lThrtyD = last thirty day's successCount and failureCount
            $lastThirtyDay  = date('Y-m-d',strtotime("-1 month"));
            $today          = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($lastThirtyDay,$today,$apikey,$start,$perPage,$getData);
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$lastThirtyDay,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];    
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
                $getCounterArr = $this->getStatCounter($apikey,$weekToDate,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];
            }
            
        }elseif ($chooseFilter == 'mTd') {

            //get mTd = month to day's successCount and failureCount
            // month to day means currnt month's first date to current date
            $monthToDate    = date('Y-m-01');
            $today          = date('Y-m-d');    

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($monthToDate,$today,$apikey,$start,$perPage,$getData);
            }else{ 
                $getCounterArr = $this->getStatCounter($apikey,$monthToDate,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr']; 
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
                $getCounterArr = $this->getStatCounter($apikey,$quarterToDate,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr']; 
            }

        }elseif ($chooseFilter == 'yTd') {
            
            //get yTd = year to day's successCount and failureCount
            // year to day means currnt year's first date to current date
            $yearToDate = date('Y-01-01');
            $today      = date('Y-m-d');

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($yearToDate,$today,$apikey,$start,$perPage,$getData);
            }else{   
                $getCounterArr = $this->getStatCounter($apikey,$yearToDate,$today);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr']; 
            }

        }elseif ($chooseFilter == 'pw') {
            
            //get pw = previous week's successCount and failureCount
            $previousWeek = date('Y-m-d',strtotime("-14 days"));
            $lastSevenDay = date('Y-m-d',strtotime("-7 days"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousWeek,$lastSevenDay,$apikey,$start,$perPage,$getData);
            }else{ 
                $getCounterArr = $this->getStatCounter($apikey,$previousWeek,$lastSevenDay);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];  
            }

        }elseif ($chooseFilter == 'pm') {
            
            //get pm = previous month's successCount and failureCount
            $previousMonth = date('Y-m-d',strtotime("-2 months"));
            $lastMonthDay  = date('Y-m-d',strtotime("-1 month"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousMonth,$lastMonthDay,$apikey,$start,$perPage,$getData);
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$previousMonth,$lastMonthDay);
                $countsArr = $getCounterArr['countsArr'];   
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];   
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
                $getCounterArr = $this->getStatCounter($apikey,$previousQuarter,$currentQuarter);
                $countsArr = $getCounterArr['countsArr'];
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];
            }

        }elseif ($chooseFilter == 'py') {
            
            //get py = previous year's successCount and failureCount
            $previousYear = date('Y-m-d',strtotime("-2 years"));
            $lastYearDay  = date('Y-m-d',strtotime("-1 year"));

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($previousYear,$lastYearDay,$apikey,$start,$perPage,$getData);
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$previousYear,$lastYearDay);
                $countsArr = $getCounterArr['countsArr'];
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];   
            }

        }elseif ($chooseFilter == 'cd') {
            
            //get cd = custom's successCount and failureCount
            $startDate = $getData['startDate'];
            $endDate = $getData['endDate'];

            if($wantToDataRecords){
                $filteredData = $this->getFilteredData($startDate,$endDate,$apikey,$start,$perPage,$getData);  
            }else{
                $getCounterArr = $this->getStatCounter($apikey,$startDate,$endDate);
                $countsArr = $getCounterArr['countsArr'];
                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];   
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
                    $rejectDetailCountsArr = array('duplicateCount' => 0, 'blacklistCount' => 0, 'duplicateCount' => 0, 'blacklistCount' => 0, 'serverIssue' => 0, 'apiKeyIsNotActive' => 0, 'emailIsRequired' => 0, 'phoneIsRequired' => 0, 'emailIsBlank' => 0, 'phoneIsBlank' => 0, 'invalidEmailFormat' => 0, 'invalidPhone' => 0, 'invalidGender' => 0, 'teliaMxBlock' => 0, 'luukkuMxBlock' => 0 , 'ppMxBlock' => 0, 'alreadyUnsubscribed' => 0, 'yahooMxBlock' => 0, 'icloudMxBlock' => 0,'gmxMxBlock' => 0, 'duplicateOld' => 0);

                    return array('countsArr' => $countsArr,'rejectDetailCountsArr' => $rejectDetailCountsArr,'filteredData' => array());
                }
            }

            if($wantToDataRecords){

                //get data

                $condition = array('apikey' => $apikey);
                $is_single = true;
                $getLiveDelivery = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(),array(array('liveDeliveryId' => 'asc')),'apikey,dataSourceType');
                $dataSourceType = $getLiveDelivery['dataSourceType'];

                if (@$getData['chooseSucFailRes'] != '') {
                    $chooseSucFailRes = $getData['chooseSucFailRes'];
                    $condition['sucFailMsgIndex'] = $chooseSucFailRes; 
                }
               
                if($dataSourceType == 1 || $dataSourceType == 2) {
                    if (@$getData['globleSearch'] != '' ) {

                        $globleSearch = trim($getData['globleSearch']);
                        $where = 'emailId LIKE "%'.$globleSearch.'%"';
                        $this->db->where($where);
                    }
    
                    $is_single = FALSE;
                    $this->db->limit($perPage,$start);   
                    $condition['dataSourceType'] = $dataSourceType;
                    $filteredData = GetAllRecord(INBOXGAME_FACEBOOKLEAD_DATA,$condition,$is_single,array(),array(),array(array('id' => 'DESC')));
                } else {
                    if (@$getData['globleSearch'] != '' ) {

                        $globleSearch = trim($getData['globleSearch']);
                        $where = '(firstName LIKE "%'.$globleSearch.'%" OR lastName LIKE "%'.$globleSearch.'%" OR emailId LIKE "%'.$globleSearch.'%" OR city LIKE "%'.$globleSearch.'%")';
                        $this->db->where($where);
                    }
    
                    $is_single = FALSE;
                    $this->db->limit($perPage,$start);
                    $filteredData = GetAllRecord(LIVE_DELIVERY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryDataId' => 'DESC')));
                }
                //---------------------------------------------------------------------------------------------------

            }else{
                //---------------------------------------------------------------------------------------------------
                //get all successCount and failureCount
                $getCounterArr = $this->getStatCounter($apikey);

                $rejectDetailCountsArr = $getCounterArr['rejectDetailCountsArr'];
                $countsArr = $getCounterArr['countsArr'];
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
            17 => 'icloudMxBlock',
            18 => 'gmxMxBlock',
            19 => 'duplicateOld'
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
        $condition = array('apikey'=>$apikey);
        $is_single = true;
        $getLiveDelivery = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array('liveDeliveryId'=>'asc')),'apikey,dataSourceType');
        $dataSourceType = $getLiveDelivery['dataSourceType'];

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
       
        if($dataSourceType == 1 || $dataSourceType == 2) {
            if (@$getData['globleSearch'] != '' ) {
                $globleSearch = trim($getData['globleSearch']);
                $this->db->where('emailId LIKE "%'.$globleSearch.'%"');
            }
            $is_single = false;
            $this->db->limit($perPage, $start);
            $filteredData = GetAllRecord(INBOXGAME_FACEBOOKLEAD_DATA, $condition, $is_single, array(), array(), array(array('id'=>'DESC')));
        } else {
            if (@$getData['globleSearch'] != '' ) {

                $globleSearch = trim($getData['globleSearch']);
                $where = '(firstName LIKE "%'.$globleSearch.'%" OR lastName LIKE "%'.$globleSearch.'%" OR emailId LIKE "%'.$globleSearch.'%"  OR city LIKE "%'.$globleSearch.'%")';
                $this->db->where($where);
            }
    
            $is_single = FALSE;
            $this->db->limit($perPage,$start);
            $filteredData = GetAllRecord(LIVE_DELIVERY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryDataId' => 'DESC')));
        }
        
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

    //count for all stat
    function getStatCounter($apikey,$startDate=null,$endDate=null){
        $condition = array('apikey'=>$apikey);
        $is_single = true;
        $getLiveDelivery = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(),array(array('liveDeliveryId' => 'asc')),'apikey,dataSourceType');
        $dataSourceType = $getLiveDelivery['dataSourceType'];
        
        if($dataSourceType == 1 || $dataSourceType == 2) {
            $tableName = 'inboxgame_facebooklead_data';
        } else {
            $tableName = 'live_delivery_data';
        }
        
        $getCounterSql ="SELECT
            SUM(CASE isFail
                    WHEN '0' THEN 1
                    ELSE 0
                END) AS successCount
            , SUM(CASE isFail
                        WHEN '1' THEN 1
                        ELSE 0
                    END) AS failureCount
            , SUM(CASE isEmailChecked
                    WHEN '1' THEN 1
                    ELSE 0
                END) AS checkEmailCount
            , SUM(CASE sucFailMsgIndex
                    WHEN '1' THEN 1
                    ELSE 0
                END) AS duplicateCount
            , SUM(CASE sucFailMsgIndex
                WHEN '2' THEN 1
                ELSE 0
                END) AS blacklistCount
            , SUM(CASE sucFailMsgIndex
                WHEN '3' THEN 1
                ELSE 0
                END) AS serverIssue
            , SUM(CASE sucFailMsgIndex
                WHEN '4' THEN 1
                ELSE 0
                END) AS apiKeyIsNotActive
            , SUM(CASE sucFailMsgIndex
                WHEN '5' THEN 1
                ELSE 0
                END) AS emailIsRequired
            , SUM(CASE sucFailMsgIndex
                WHEN '6' THEN 1
                ELSE 0
                END) AS phoneIsRequired
            , SUM(CASE sucFailMsgIndex
                WHEN '7' THEN 1
                ELSE 0
                END) AS emailIsBlank
            , SUM(CASE sucFailMsgIndex
                WHEN '8' THEN 1
                ELSE 0
                END) AS phoneIsBlank
            , SUM(CASE sucFailMsgIndex
                WHEN '9' THEN 1
                ELSE 0
                END) AS invalidEmailFormat
            , SUM(CASE sucFailMsgIndex
                WHEN '10' THEN 1
                ELSE 0
                END) AS invalidPhone
            , SUM(CASE sucFailMsgIndex
                WHEN '11' THEN 1
                ELSE 0
                END) AS invalidGender
            , SUM(CASE sucFailMsgIndex
                WHEN '12' THEN 1
                ELSE 0
                END) AS teliaMxBlock
            , SUM(CASE sucFailMsgIndex
                WHEN '13' THEN 1
                ELSE 0
                END) AS luukkuMxBlock
            , SUM(CASE sucFailMsgIndex
                WHEN '14' THEN 1
                ELSE 0
                END) AS ppMxBlock
            , SUM(CASE sucFailMsgIndex
                WHEN '15' THEN 1
                ELSE 0
                END) AS alreadyUnsubscribed
            , SUM(CASE sucFailMsgIndex
                WHEN '16' THEN 1
                ELSE 0
                END) AS yahooMxBlock
            , SUM(CASE sucFailMsgIndex
                WHEN '17' THEN 1
                ELSE 0
                END) AS icloudMxBlock
            , SUM(CASE sucFailMsgIndex
                WHEN '18' THEN 1
                ELSE 0
                END) AS gmxMxBlock
            , SUM(CASE sucFailMsgIndex
            WHEN '19' THEN 1
            ELSE 0
            END) AS duplicateOld
        FROM $tableName
        WHERE `apikey` ='".$apikey."'";

        if($startDate != null || $endDate != null){
            if ($this->isDateWithoutTime($startDate) == 'true') {
                $startDate = $startDate.' 00:00:00';
            }
    
            if ($this->isDateWithoutTime($endDate) == 'true') {
                $endDate = $endDate.' 23:59:59';   
            }

            $getCounterSql .= " AND  `createdDate` >= '".$startDate."' AND `createdDate` <= '".$endDate."'";
        }
        $getCounter = $this->db->query($getCounterSql)->result_array()[0];
        $successCount = $getCounter['successCount'];
        $failureCount = $getCounter['failureCount'];
        $checkEmailCount = $getCounter['checkEmailCount'];


        $countsArr =  array('successCount' => $successCount,'failureCount' => $failureCount, 'checkEmailCount' => $checkEmailCount);

        $reasonArr = array(1 => 'duplicateCount', 2 => 'blacklistCount', 3 => 'serverIssue', 4 => 'apiKeyIsNotActive', 5 => 'emailIsRequired', 6 => 'phoneIsRequired', 7 => 'emailIsBlank', 8 => 'phoneIsBlank', 9 => 'invalidEmailFormat', 10 => 'invalidPhone', 11 => 'invalidGender', 12 => 'teliaMxBlock', 13 => 'luukkuMxBlock', 14 => 'ppMxBlock', 15 => 'alreadyUnsubscribed', 16 => 'yahooMxBlock', 17 => 'icloudMxBlock', 18 => 'gmxMxBlock', 19 => 'duplicateOld' );

        //get reject count in detail
        $rejectDetailCountsArr = array();
        for ($i=1; $i <= count($reasonArr); $i++) { 
            $rejectDetailCountsArr[$reasonArr[$i]] = $getCounter[$reasonArr[$i]];
        }

        return array(
            'countsArr'             => $countsArr,
            'rejectDetailCountsArr' => $rejectDetailCountsArr
        );
    }

}