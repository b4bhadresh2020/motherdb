<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class countryWiseKeywordPercentage extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        if(!is_logged())
            redirect(base_url());
    }

    public function manage($start = 0) {

        $data = array();

        $returnOfFunc = $this->getCountryWiseKeywordPer();
        $data['countryKeywordPers'] = $returnOfFunc['newCountryArr'];
        $data['onlyCountry'] = $returnOfFunc['onlyCountry'];
        $data['load_page'] = 'countryWiseKeywordPercentage';
        $data["curTemplateName"] = "countryWiseKeywordPercentage/list";
        $data['headerTitle'] = "Country Wise Keyword Percentage";
        $data['pageTitle'] = "Country Wise Keyword Percentage";
        $data['start'] = $start;
        
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    function getCountryWiseKeywordPer(){

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


    function getAllCountryCount(){
        
        $countryCount_sql = 'SELECT country,sum(male) + sum(female) + sum(other) as total FROM '.COUNTRY_MASTER.' GROUP BY country';
        $countryCount = $this->db->query($countryCount_sql)->result_array();
        
        foreach ($countryCount as $key => $value) {
            $returnArrCountry[$value['country']] = $value['total'];
        }

        return $returnArrCountry;
    }

}