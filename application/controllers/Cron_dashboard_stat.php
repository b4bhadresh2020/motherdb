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

    public function addDashboardStatData() {
        $today = date('Y-m-d');
        $currentYear = date('Y');
        $prevYear = date('Y')-1;
        $prevMonthNum = date('m')-1;
        $curretMonth = strtolower(date('F'));

        $prevMonth = []; // 2021 (march, feb, jan)
        for($month=$prevMonthNum;$month>=1;$month--){
            $monthName = strtolower(date("F", mktime(0, 0, 0, $month, 10)));
            $prevMonth[] = $monthName;
        }

        $remainMonth = []; // before year (2020 remaining year) (april, may, june...dec -2020)
        $nextMonthNumYA = date('m') + 1; // next month list but year ago
        for($month=$nextMonthNumYA;$month<=12;$month++){
            $monthName = strtolower(date("F", mktime(0, 0, 0, $month, 10)));
            $remainMonth[] = $monthName;
        }

        // get country data
        $is_single = false;
        $countryList = GetAllRecord(COUNTRY_MASTER, array(), $is_single, array(), array(), array(), 'countryId, country');

        foreach($countryList as $ct) {
            $country = $ct['country'];

            // get dashboard stat detail year wise
            $condition = array(
                'year' => $currentYear,
                'countryId' => $ct['countryId']
            );
            $is_single = false;
            $dashboardStats = GetAllRecord(DASHBOARD_STATS, $condition, $is_single);

            $condition = array('country' => $country);
            $activeAccountCondition = array('provider' => [9,13,12,14,15,16]);  
            $is_single = false;
            $providers = GetAllRecordIn(PROVIDERS, $condition, $is_single, array(), array(), array(), $activeAccountCondition, "id,provider");

            $accountProviders = [];
            foreach($providers as $provider) {
                $tableName = getDelayAccountTableName($provider['provider']);
                $accountProviders[$tableName][] = $provider['id'];
            }
            if(count($accountProviders) > 0) {
                if(empty($dashboardStats)) {
                    // field array
                    $fields = ['total', 'success', 'fail', 'duplicate'];
                    foreach($fields as $field) {
                        $response = [];
                        $response[$country][$field] = $this->getCounterByCustomFilter($accountProviders, $field);
                        $statCounts = $response[$country][$field];
                    
                        $dashboard_stats_data = array(
                            'year' => $currentYear,
                            'countryId' => $ct['countryId'],
                            'field' => $field,
                            'today' => $statCounts['today'],
                            'yesterday' => $statCounts['yesterday'],
                            'lastSevenDay' => $statCounts['current_week'],
                            $curretMonth => $statCounts['current_month']
                        );
                        foreach($prevMonth as $pm) {
                            $dashboard_stats_data[$pm] = $statCounts[$pm];
                        }
                        foreach($remainMonth as $rm) {
                            $dashboard_stats_data[$rm] = $statCounts[$rm];
                        }
                        $dashboard_stats_data['createdDate'] = date('Y-m-d H:i:s');
                        $dashboard_stats_data['updatedDate'] = date('Y-m-d H:i:s');
                        
                        $condition = array();
                        $is_insert = true;
                        $dashboardStatsId = ManageData(DASHBOARD_STATS, $condition, $dashboard_stats_data, $is_insert);
                    }
                } else {
                    foreach($dashboardStats as $dashboardStat) {
                        $response = [];
                        $field = $dashboardStat['field'];
                        $response[$country][$field] = $this->getCounterByCustomFilter($accountProviders, $field);
                        $statCounts = $response[$country][$field];

                        $update_stats_data = array(
                            'today' => $statCounts['today'],
                            'yesterday' => $statCounts['yesterday'],
                            'lastSevenDay' => $statCounts['current_week'],
                            $curretMonth => $statCounts['current_month']
                        );
                        foreach($prevMonth as $pm) {
                            $update_stats_data[$pm] = $statCounts[$pm];
                        }
                        foreach($remainMonth as $rm) {
                            $update_stats_data[$rm] = $statCounts[$rm];
                        }
                        $update_stats_data['updatedDate'] = date('Y-m-d H:i:s');

                        $condition = array('id' => $dashboardStat['id']);
                        $is_insert = false;
                        ManageData(DASHBOARD_STATS, $condition, $update_stats_data, $is_insert);
                    }
                }
            }
        }
    }

    function getCounterByCustomFilter($accountProviders,$filed){

        $response['today'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('td'));
        $response['yesterday'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('yd'));
        $response['current_week'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('lSvnD'));
        $response['current_month'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('lThrtyD'));

        //january month dainamic data get
        $monthNum = date('m')-1;
        $year = date('Y');
        for($month=$monthNum;$month>=1;$month--){
            $monthName = strtolower(date("F", mktime(0, 0, 0, $month, 10)));
            $response[$monthName] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('dM',$monthName,$year));
        }
        
        $curretMonth = date('m') + 1;
        $prevYear = date('Y')-1;
        for($month=$curretMonth;$month<=12;$month++){
            $monthName = strtolower(date("F", mktime(0, 0, 0, $month, 10)));
            $response[$monthName] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,getCondition('dM',$monthName,$prevYear));
        }
        return $response;

    }
}