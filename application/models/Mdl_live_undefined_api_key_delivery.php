<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_live_undefined_api_key_delivery extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getLiveDeliveryUndefinedApiKeyStatData($getData,$start,$perPage) {

        $chooseFilter = @$getData['chooseFilter'];
        
        //for return value
        if ($chooseFilter == 'td') {

            //get td = today's successCount and failureCount
            $today = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($today,$today);
            $filteredDataCount = $this->getFilteredData($today,$today,$start,$perPage);
                
        }elseif ($chooseFilter == 'yd') {

            //get yd = yester's successCount and failureCount
            $yesterday = date('Y-m-d',strtotime("-1 day"));
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($yesterday,$yesterday);
            $filteredDataCount = $this->getFilteredData($yesterday,$yesterday,$start,$perPage);

        }elseif ($chooseFilter == 'lSvnD') {
            
            //get lSvnD = last seven day's successCount and failureCount
            $lastSevenDay   = date('Y-m-d',strtotime("-7 days"));
            $today          = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($lastSevenDay,$today);
            $filteredDataCount = $this->getFilteredData($lastSevenDay,$today,$start,$perPage);

        }elseif ($chooseFilter == 'lThrtyD') {
            
            //get lThrtyD = last thirty day's successCount and failureCount
            $lastThirtyDay  = date('Y-m-d',strtotime("-1 month"));
            $today          = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($lastThirtyDay,$today);
            $filteredDataCount = $this->getFilteredData($lastThirtyDay,$today,$start,$perPage);

        }elseif ($chooseFilter == 'wTd') {

            //get wTd = week to day's successCount and failureCount
            // week to day means currnt week's first date to current date
            $day        = date('w');
            $weekToDate = date('Y-m-d', strtotime('-'.$day.' days'));
            $today      = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($weekToDate,$today);
            $filteredDataCount = $this->getFilteredData($weekToDate,$today,$start,$perPage);
            
        }elseif ($chooseFilter == 'mTd') {

            //get mTd = month to day's successCount and failureCount
            // month to day means currnt month's first date to current date
            $monthToDate    = date('Y-m-01');
            $today          = date('Y-m-d');  
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($monthToDate,$today);  
            $filteredDataCount = $this->getFilteredData($monthToDate,$today,$start,$perPage);

        }elseif ($chooseFilter == 'qTd') {

            //get qTd = quarter to day's successCount and failureCount
            // quarter to day means currnt quarter's first date to current date
            $current_quarter = ceil(date('n') / 3);
            $quarterToDate   = date('Y-m-d', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
            $today           = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($quarterToDate,$today);
            $filteredDataCount = $this->getFilteredData($quarterToDate,$today,$start,$perPage);

        }elseif ($chooseFilter == 'yTd') {
            
            //get yTd = year to day's successCount and failureCount
            // year to day means currnt year's first date to current date
            $yearToDate = date('Y-01-01');
            $today      = date('Y-m-d');
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($yearToDate,$today);
            $filteredDataCount = $this->getFilteredData($yearToDate,$today,$start,$perPage);

        }elseif ($chooseFilter == 'pw') {
            
            //get pw = previous week's successCount and failureCount
            $previousWeek = date('Y-m-d',strtotime("-14 days"));
            $lastSevenDay = date('Y-m-d',strtotime("-7 days"));
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($previousWeek,$lastSevenDay);
            $filteredDataCount = $this->getFilteredData($previousWeek,$lastSevenDay,$start,$perPage);

        }elseif ($chooseFilter == 'pm') {
            
            //get pm = previous month's successCount and failureCount
            $previousMonth = date('Y-m-d',strtotime("-2 months"));
            $lastMonthDay  = date('Y-m-d',strtotime("-1 month"));
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($previousMonth,$lastMonthDay);
            $filteredDataCount = $this->getFilteredData($previousMonth,$lastMonthDay,$start,$perPage);

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
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($previousQuarter,$currentQuarter);
            $filteredDataCount = $this->getFilteredData($previousQuarter,$currentQuarter,$start,$perPage);

        }elseif ($chooseFilter == 'py') {
            
            //get py = previous year's successCount and failureCount
            $previousYear = date('Y-m-d',strtotime("-2 years"));
            $lastYearDay  = date('Y-m-d',strtotime("-1 year"));
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($previousYear,$lastYearDay);
            $filteredDataCount = $this->getFilteredData($previousYear,$lastYearDay,$start,$perPage);

        }elseif ($chooseFilter == 'cd') {
            
            //get cd = custom's successCount and failureCount
            $startDate = $getData['startDate'];
            $endDate = $getData['endDate'];
            $rejectDetailCountsArr = $this->getRejectUndefinedApiKeyDetailCount($startDate,$endDate);
            $filteredDataCount = $this->getFilteredData($startDate,$endDate,$start,$perPage);

        }else{


            //get reject count in detail

            $reasonArr = array(
                1 => 'undefinedApiKey', 
                2 => 'blankApiKey',
                3 => 'invalidApiKey'
            );

            $rejectDetailCountsArr = array();
            for ($i=1; $i <= count($reasonArr); $i++) { 

                $condition = array('sucFailMsgIndex' => $i);

                $rejectCount = GetAllRecordCount(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition);
                $rejectDetailCountsArr[$reasonArr[$i]] = $rejectCount;
            }

            //---------------------------------------------------------------------------------------------------


            //get data
            $filteredDataCount = GetAllRecordCount(LIVE_DELIVERY_UNDEFINED_KEY_DATA);

            $condition = array();
            $is_single = FALSE;
            $this->db->limit($perPage,$start);
            GetAllRecord(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryUndefinedApiKeyDataId' => 'DESC')));
            //---------------------------------------------------------------------------------------------------
            
        }
        $returnArr = array('rejectDetailCountsArr' => $rejectDetailCountsArr,'filteredDataCount' => $filteredDataCount);

        return $returnArr;
        
    }



    function getRejectUndefinedApiKeyDetailCount($startDate,$endDate){

        $reasonArr = array(
            1 => 'undefinedApiKey', 
            2 => 'blankApiKey',
            3 => 'invalidApiKey'
        );

        $rejectCountArr = array();
        for ($i=1; $i <= count($reasonArr); $i++) { 

             $condition = array(
                'createdDate >=' => $startDate.' 00:00:00',
                'createdDate <=' => $endDate.' 23:59:59',
                'sucFailMsgIndex' => $i
            );

            $rejectCount = GetAllRecordCount(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition);
            $rejectCountArr[$reasonArr[$i]] = $rejectCount;
        }

        return $rejectCountArr;
    }


    function getFilteredData($startDate,$endDate,$start,$perPage){
        
        $condition = array(
            'createdDate >=' => $startDate.' 00:00:00',
            'createdDate <=' => $endDate.' 23:59:59'
        );
        $filteredDataCount = GetAllRecordCount(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition);

        $condition = array(
            'createdDate >=' => $startDate.' 00:00:00',
            'createdDate <=' => $endDate.' 23:59:59'
        );
        $is_single = FALSE;
        $this->db->limit($perPage,$start);
        GetAllRecord(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryUndefinedApiKeyDataId' => 'DESC')));

        return $filteredDataCount;
    }


}