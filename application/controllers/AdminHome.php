<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminHome extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!is_logged()) {
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_batchstat');
        $this->load->model('mdl_admin_home');

    }

    public function index($start = 0)
    {   
        $data = array();

        // keyword % and batchstat
        $keywordDataCount = GetAllRecordCount(KEYWORD_MASTER, array());

        $infoData = array();
        if($keywordDataCount > 0){
            $infoData        = GetAllRecord(KEYWORD_MASTER, array(), "");
        }
        $perPage         = 15;
        $data['keyword'] = pagination_data('AdminHome/index/', $keywordDataCount, $start, 3, $perPage, $infoData);


        /*$responseData = $this->mdl_batchstat->getBatchStatData($_GET,$start,$perPage); 
        $dataCount = $responseData['totalCount'];
        $batchStatData = $responseData['batchStatData'];

        $data['batchstat'] = pagination_data('AdminHome/index/', $dataCount, $start, 3, $perPage,$batchStatData);*/


        // result %
        $infoData_result_count = GetAllRecordCount(ENRICHMENT_CRON_STATUS, array());

        $infoData_result = array();
        if ($infoData_result_count > 0) {
            $infoData_result = GetAllRecord(ENRICHMENT_CRON_STATUS, array(), "");    
        }
        
        $data['result']  = pagination_data('AdminHome/index/', $infoData_result_count, $start, 3, $perPage, $infoData_result);

        //dashboard lead counter stat
        $response = [];
        $allCountries = getCountry();
        foreach($allCountries as $countries){

            $condition   = array('country' => $countries['country']);
            $activeAccountCondition = array('provider' => [9,13,12,14,15,16]);
            $is_single   = false;
            $providers   = GetAllRecordIn(PROVIDERS, $condition, $is_single,array(),array(),array(),$activeAccountCondition,"id,provider");

            $accountProviders = array();
            foreach($providers as $provider){

                $tableName = getDelayAccountTableName($provider['provider']);
                $accountProviders[$tableName][] = $provider['id'];

            }
            
            if(count($accountProviders) > 0){

                $response[$countries['country']]['total'] = $this->getCounterByCustomFilter($accountProviders,'total');
                $response[$countries['country']]['success'] = $this->getCounterByCustomFilter($accountProviders,'success');
                $response[$countries['country']]['fail'] = $this->getCounterByCustomFilter($accountProviders,'fail');
                $response[$countries['country']]['duplicate'] = $this->getCounterByCustomFilter($accountProviders,'duplicate');
            
            }
            
        }   
        $data['countriesStat'] = $response;
        
        //common fileds
        $data['statFileds'] = $this->getStatFileds();

        // Country wise
        $returnOfFunc               = $this->getCountryWiseKeywordPer();
        $data['countryKeywordPers'] = $returnOfFunc['newCountryArr'];
        $data['onlyCountry']        = $returnOfFunc['onlyCountry'];

        // Male / Female %
        $data['CountryWise'] = $this->CountryWise();
        $data['GroupWise']   = $this->GroupWise();

        $data['load_page']       = 'dashboard';
        $data["curTemplateName"] = "dashboard/dashboard";
        $data['error_msg']       = GetMsg('loginErrorMsg');
        $data['suc_msg']         = GetMsg('loginSucMsg');
        $data['headerTitle']     = "Dashboard";
        $data['pageTitle']       = "Dashboard";
        $data['start']           = $start;

        $this->load->view('commonTemplates/templateLayout', $data);
    }


    function getCounterByCustomFilter($accountProviders,$filed){

        $response['today'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,$this->getCondition('td'));
        $response['yesterday'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,$this->getCondition('yd'));
        $response['current_week'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,$this->getCondition('lSvnD'));
        $response['current_month'] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,$this->getCondition('lThrtyD'));

        //january month dainamic data get
        $monthNum = date('m')-1;
        $year = date('Y');
        for($month=$monthNum;$month>=1;$month--){
            $monthName = date("F", mktime(0, 0, 0, $month, 10));
            $response[$monthName] = $this->mdl_admin_home->getTotalLeadCounter($accountProviders,$filed,$this->getCondition('dM',$monthName,$year));
        }
        return $response;
        
    }

    function getStatFileds(){

        $fileds = [];
        $fileds['today'] = 1;
        $fileds['yesterday'] = 2;
        $fileds['current_week'] = 7;
        $currentMonthDate = $this->getCondition('lThrtyD');
        $fileds['current_month'] = $this->getDays($currentMonthDate['startDate'],$currentMonthDate['endDate']);
        
        //end of the january month daynamic data get
        $monthNum = date('m')-1;
        $year = date('Y');
        for($month=$monthNum;$month>=1;$month--){
            $monthName = date("F", mktime(0, 0, 0, $month, 10));
            $prevMonth = $this->getCondition('dM',$monthName,$year);
            $fileds[$monthName] = $this->getDays($prevMonth['startDate'],$prevMonth['endDate']);
        }        
        return $fileds;
    }

    function getDays($startDate,$endDate){
        $datetime1 = new DateTime($startDate);
        $datetime2 = new DateTime($endDate);
        $days = $datetime1->diff($datetime2);
        return $days->format('%a')+1;
    }
    
    function getCondition($chooseFilter = "td",$month=null,$year=null){

        if ($chooseFilter == 'td') {
 
             //get td = today's clicks and registrations
             $today = date('Y-m-d');
             $startDate = $today;
             $endDate = $today;
                 
         }elseif ($chooseFilter == 'yd') {
 
             //get yd = yester's records
             $yesterday = date('Y-m-d',strtotime("-1 day"));
             $startDate = $yesterday;
             $endDate = $yesterday;
 
         }elseif ($chooseFilter == 'lSvnD') {
             
             //get lSvnD = last seven day's records
             $lastSevenDay   = date('Y-m-d',strtotime("-7 days"));
             $today          = date('Y-m-d');
 
             $startDate = $lastSevenDay;
             $endDate = $today;            
 
         }elseif ($chooseFilter == 'lThrtyD') {
             
             //get lThrtyD = current month records
             $lastThirtyDay  = date('Y-m-01');
             $today          = date('Y-m-d');
 
             $startDate = $lastThirtyDay;
             $endDate = $today;   
 
         }elseif($chooseFilter == 'dM'){

            //get dM = daynamic month records end of january
            if($month!=null && $year != null){
                $timestamp    = strtotime($month." ".$year);
                $startDate = date('Y-m-01', $timestamp);
                $endDate  =date('Y-m-t',strtotime($startDate));
            }

         }

        $condition = array(
            'startDate' => $startDate.' '.'00:00:00',
            'endDate'   => $endDate.' '.'23:59:59'
        );             
         return $condition;        
     }
    
    public function getCountryWiseKeywordPer(){

        $newCountryArr = array();
        $countryCount = $this->getAllCountryCount();
        $onlyCountry = array();
        $mixArr = array();
        $get_keyword_sql = "SELECT DISTINCT(keyword) FROM keyword_country_count";
        $get_keyword = $this->db->query($get_keyword_sql)->result_array();

        foreach ($get_keyword as $key => $value_main) {

            $keyword_con = $value_main['keyword'];

            $keyword_country_sql = "SELECT keyword, country, sum(total) as keywordCount FROM keyword_country_count WHERE keyword = '$keyword_con' group by country";

            $keyword_country = $this->db->query($keyword_country_sql)->result_array();


            foreach ($keyword_country as &$value) {

                if($countryCount[$value['country']] > 0){
                    $percentage = ($value['keywordCount'] / $countryCount[$value['country']]) * 100;    
                } else {
                    $percentage = 0;
                }

                $value['percentage'] = reformat_number_format($percentage);    
            }

            $mixArr[] = $keyword_country;
        }   


        if (count($mixArr) > 0) {
            foreach ($mixArr as $index => $offset) {
                foreach ($offset as $data) {

                    if (!in_array($data['country'], $onlyCountry)) {
                        $onlyCountry[] = $data['country'];
                    }
                    
                    $newCountryArr[$data['country']][$data['keyword']] = $data['percentage'].','.$data['keywordCount'];  
                }
            }
        }


        // for total country count
        for ($i=0; $i < sizeof($onlyCountry); $i++) { 
            if(!in_array($countryCount[$onlyCountry[$i]], $onlyCountry)){
                $onlyCountry[$i] = $onlyCountry[$i].','.$countryCount[$onlyCountry[$i]];
            }
        }

        
        $returnArr = array();
        $returnArr['newCountryArr'] = $newCountryArr;
        $returnArr['onlyCountry'] = $onlyCountry;
        return $returnArr;
        
    }

    public function getAllCountryCount(){
        
        $countryCount_sql = 'SELECT country,sum(male) + sum(female) + sum(other) as total FROM '.COUNTRY_MASTER.' GROUP BY country';
        $countryCount = $this->db->query($countryCount_sql)->result_array();
        
        foreach ($countryCount as $key => $value) {
            $returnArrCountry[$value['country']] = $value['total'];
        }

        return $returnArrCountry;
    }

    public function CountryWise()
    {

        $countryData = GetAllRecord(COUNTRY_MASTER, $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'country, male, female', $startNo = '');

        $returnArr = array();
        foreach ($countryData as $key_main => $result_main) {

            $total     = $result_main['male'] + $result_main['female'];
            
            if($total > 0){

                $malePer =  round($result_main['male'] / $total * 100, 2) . '%';
                $femalePer  = round($result_main['female'] / $total * 100, 2) . '%';

            } else {

                $malePer =  '0 %';
                $femalePer  = ' 0%';

            }

            $finalArr = array();

            $dataArr['country']      = $result_main['country'];
            $dataArr['male_count']   = $result_main['male'];
            $dataArr['female_count'] = $result_main['female'];
            $dataArr['male_per']     = $malePer;
            $dataArr['female_per']   = $femalePer;
            $dataArr['total']        = $total;

            $returnArr[] = $dataArr;
        }

        return $returnArr;
    }

    public function GroupWise()
    {

        $groupData = GetAllRecord(GROUP_MASTER, $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupName, male, female', $startNo = '');

        $returnArr = array();
        foreach ($groupData as $key_main => $result_main) {

            $total     = $result_main['male'] + $result_main['female'];
            
            if($total > 0){

                $malePer =  round($result_main['male'] / $total * 100, 2) . '%';
                $femalePer  = round($result_main['female'] / $total * 100, 2) . '%';

            } else {

                $malePer =  '0 %';
                $femalePer  = ' 0%';

            }

            $finalArr = array();

            $dataArr['groupName']    = $result_main['groupName'];
            $dataArr['male_count']   = $result_main['male'];
            $dataArr['female_count'] = $result_main['female'];
            $dataArr['male_per']     = $malePer;
            $dataArr['female_per']   = $femalePer;
            $dataArr['total']        = $total;

            $returnArr[] = $dataArr;
        }

        return $returnArr;
    }

    public function changePassword()
    {
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required');

        if ($this->form_validation->run() != false) {

            $password        = $this->input->post("password");
            $confirmPassword = $this->input->post("confirmPassword");

            if ($password == $confirmPassword) {
                $dataArr = array(
                    'adminPassword' => md5($this->input->post("password")),
                );
                $condition        = array("adminId" => $this->session->userdata('adminId'));
                $is_add           = false;
                $createdCatalogId = ManageData(ADMINMASTER, $condition, $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("updateData"));

                $data                    = array();
                $data['suc_msg']         = "Password Changed Successfully!";
                $data['headerTitle']     = "Change Password";
                $data['load_page']       = 'changePassword';
                $data["curTemplateName"] = "dashboard/changePassword";
                $this->load->view('commonTemplates/templateLayout', $data);
            } else {
                $data                    = array();
                $data['error_msg']       = "Password not match, please try again.";
                $data['headerTitle']     = "Change Password";
                $data['load_page']       = 'changePassword';
                $data["curTemplateName"] = "dashboard/changePassword";
                $this->load->view('commonTemplates/templateLayout', $data);
            }
        } else {
            $data                    = array();
            $data                    = $_POST;
            $data['error_msg']       = GetFormError();
            $data['headerTitle']     = "Change Password";
            $data['load_page']       = 'changePassword';
            $data["curTemplateName"] = "dashboard/changePassword";

            $this->load->view('commonTemplates/templateLayout', $data);
        }
    }

    /*
    @verifyAdminPassword
    ->check admin password after view details
     */
    public function verifyAdminPassword()
    {

        $condition = array(
            "adminId"       => $this->session->userdata('adminId'),
            'adminPassword' => md5($this->input->post("adminPassword")),
        );
        $checkAdminPassword = GetAllRecord(ADMINMASTER, $condition, true);

        if (count($checkAdminPassword) > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
    
}
