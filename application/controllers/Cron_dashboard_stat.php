<?php

/**
 * 
 */
class Cron_dashboard_stat extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_admin_home');
    }

    public function getDashboardStat(){

        $countries = getCountry();
        foreach($countries  as $country){
            $condition = array(
                'countryId' => $country['countryId'],
                'filed'     => 'total'
            );
            $is_single = true;
            $dashboardStat = GetAllRecord(DASHBOARD_STATS, $condition, $is_single);
            if(empty($dashboardStat)){
                pre($dashboardStat);die;
                $this->mdl_admin_home->getDashboardTotalRecord();
            }else{

            }
        }

    }   
}