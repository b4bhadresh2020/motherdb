<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MaleFemalePercentage extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged()) {
            redirect(base_url());
        }

    }

    public function manage($start = 0)
    {

        $data                    = array();
        $data['CountryWise']     = $this->CountryWise();
        $data['GroupWise']       = $this->GroupWise();
        $data['load_page']       = 'maleFemalePercentage';
        $data["curTemplateName"] = "maleFemalePercentage/list";
        $data['headerTitle']     = "Male and Female Percentage";
        $data['pageTitle']       = "Male and Female Percentage";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function CountryWise()
    {

        $countryData = GetAllRecord(COUNTRY_MASTER, $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'country, male, female', $startNo = '');

        $returnArr = array();
        foreach ($countryData as $key_main => $result_main) {

            $total   = $result_main['male'] + $result_main['female'];

            if($total > 0) {

                $malePer =  round($result_main['male'] / $total * 100, 2) . '%';
                $femalePer  = round($result_main['female'] / $total * 100, 2) . '%';
           
            } else {

                $malePer =  '0 %';
                $femalePer  = '0 %';

            }


            $finalArr = array();

            $dataArr['country'] = $result_main['country'];
            $dataArr['male_count'] = $result_main['male'];
            $dataArr['female_count'] = $result_main['female'];
            $dataArr['male_per'] = $malePer; 
            $dataArr['female_per'] = $femalePer;
            $dataArr['total'] = $total;

            $returnArr[] = $dataArr;
        }

        return $returnArr;
    }

    public function GroupWise()
    {

        $groupData = GetAllRecord(GROUP_MASTER, $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupName, male, female', $startNo = '');

        $returnArr = array();
        foreach ($groupData as $key_main => $result_main) {

            $total   = $result_main['male'] + $result_main['female'];
            
            if($total > 0){

                $malePer =  round($result_main['male'] / $total * 100, 2) . '%';
                $femalePer  = round($result_main['female'] / $total * 100, 2) . '%';

            } else {

                $malePer =  '0 %';
                $femalePer  = '0 %';

            }

            $finalArr = array();

            $dataArr['groupName'] = $result_main['groupName'];
            $dataArr['male_count'] = $result_main['male'];
            $dataArr['female_count'] = $result_main['female'];
            $dataArr['male_per'] = $malePer; 
            $dataArr['female_per'] = $femalePer;
            $dataArr['total'] = $total;

            $returnArr[] = $dataArr;
        }

        return $returnArr;
    }

}
