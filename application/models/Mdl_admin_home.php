<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_admin_home extends CI_Model {

	public function __construct() {
        parent::__construct();

    }

    function getTotalLeadCounter($country,$filed,$date,$getAllResponseFieldName){       
        // (count of only record when comes in motherdb)
        $startDate = $date['startDate'];
        $endDate = $date['endDate'];
        if($filed == "total" || $filed == "success" ||  $filed == "fail") {
            $getCounterSql = "SELECT 
                    IFNULL(SUM(CASE isFail
                            WHEN '0' THEN 1
                            ELSE 0
                        END), 0) AS successCount,
                    IFNULL(SUM(CASE isFail
                            WHEN '1' THEN 1
                            ELSE 0
                    END), 0) AS failureCount
                FROM live_delivery_data
                WHERE `country` = '".$country."'";
            
            if($startDate != null && $endDate != null) {
                $getCounterSql .= " AND `createdDate` >= '".$startDate."' AND `createdDate` <= '".$endDate."'";
            }
            $getCounter = $this->db->query($getCounterSql)->row_array();
           
        } else if($filed == "duplicate") {
            $getCounterSql = "SELECT count(*) AS total
                            FROM live_delivery_data
                            WHERE (`country` = '".$country."' AND `createdDate` >= '".$startDate."' AND `createdDate` <= '".$endDate."')"; 
            
            foreach($getAllResponseFieldName As $index => $responseField) {
                if($index == 0) {
                    $getCounterSql .= " AND ( ".$responseField." LIKE '".'%'.'"result":"success"'.'%'."' ";
                } else if($index == count($getAllResponseFieldName)-1) {
                    $getCounterSql .= " OR ".$responseField." LIKE '".'%'.'"result":"success"'.'%'."') ";
                } else {
                    $getCounterSql .= " OR ".$responseField." LIKE '".'%'.'"result":"success"'.'%'."' ";
                }
            }
            $totalAccepted = $this->db->query($getCounterSql)->row_array();
        } else if($filed == "fb_lead_ads" || $filed == "fb_hosted_ads" || $filed == "total_fb") {
            $getCounterSql = "SELECT COUNT(*) AS fbTotal
                FROM inboxgame_facebooklead_data
                WHERE `country` = '".$country."'";
            
            if($startDate != null && $endDate != null) {
                $getCounterSql .= " AND `createdDate` >= '".$startDate."' AND `createdDate` <= '".$endDate."'";
            }
            if($filed == "fb_hosted_ads") {
                $getCounterSql .= " AND dataSourceType = 1";
            } else if($filed == "fb_lead_ads") {
                $getCounterSql .= " AND dataSourceType = 2";
            }
            $getCounter = $this->db->query($getCounterSql)->row_array();           
        } else if($filed == "total_fb_hosted_ads_integromat") {            
            $getCounterSql = "SELECT COUNT(*) AS fbIntegromatTotal
                FROM integromat_lead_data
                WHERE `country` = '".$country."'";
            
            if($startDate != null && $endDate != null) {
                $getCounterSql .= " AND `createdDate` >= '".$startDate."' AND `createdDate` <= '".$endDate."'";
            }
            
            $getCounter = $this->db->query($getCounterSql)->row_array();           
        }

        if($filed == "total") {
            $result = $getCounter['successCount'] + $getCounter['failureCount'];
        } else if($filed == "success") {
            $result = $getCounter['successCount'];
        } else if($filed == "fail") {
            $result = $getCounter['failureCount'];
        } else if($filed == "duplicate") {
            $result = $totalAccepted['total'];
        } else if($filed == "fb_lead_ads" || $filed == "fb_hosted_ads" || $filed == "total_fb") {
            $result = $getCounter['fbTotal'];
        } else if($filed == "total_fb_hosted_ads_integromat") {
            $result = $getCounter['fbIntegromatTotal'];
        } else {
            $result = 0;
        }
        return $result;
    }
}