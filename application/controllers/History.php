<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_history');
    }

    public function manage($start = 0) {

        $data = array();


        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 15;
        $responseData = $this->mdl_history->getHistoryData($_GET,$start,$perPage); 

        $dataCount = $responseData['totalCount'];
        $historyData = $responseData['historyData'];

        $data = pagination_data('history/manage/', $dataCount, $start, 3, $perPage,$historyData);

        foreach ($data['listArr'] as &$value) {

            $redirectUrl = $this->getRedirectUrl($value['fileModuleType'],$value['value']);
            $value['redirectUrl'] = $redirectUrl;
        }
        
        $data['load_page'] = 'history';
        $data["curTemplateName"] = "history/list";
        $data['headerTitle'] = "History";
        $data['pageTitle'] = "History";
        $data['start'] = $start;

        $this->load->view('commonTemplates/templateLayout', $data);
    }



    function getRedirectUrl($fileModuleType,$value){

        switch ($fileModuleType) {
            case 'user':
                $url = $this->getUserURL($value);
                return $url;
                break;

            case 'blacklist':
                $url = $this->getBlackListURL($value);
                return $url;
                break;

            case 'enrichment':
                $url = $this->getEnrichmentURL($value);
                return $url;
                break;

            case 'with_merge':
                $url = $this->getMergeURL($value);
                return $url;
                break;

            case 'without_merge':
                $url = $this->getWithoutMergeURL($value);
                return $url;
                break;
            
            default:
                return '#';
                break;
        }
    }


    function getUserURL($value){

        $jsonDecodeValue = json_decode($value,TRUE);
        $getUrl = '?';
        foreach ($jsonDecodeValue as $key => $value) {
            
            if ($value == '0') {
                $value = '';
            }
            $getUrl .= '&'.$key.'='.$value;
        }
        $url = base_url().'userList/manage/0'.$getUrl;
        return $url;
    }



    function getBlackListURL($value){
        
        $jsonDecodeValue = json_decode($value,TRUE);
        
        $getUrl = '?';
        foreach ($jsonDecodeValue as $key => $value) {
            $getUrl .= '&'.$key.'='.$value;
        }

        $url = base_url().'unsubscribe/manage/0/'.$getUrl;
        return $url;

    }


    function getEnrichmentURL($value){ 
        return $this->getUserURL($value);
    }


    function getMergeURL($value){

        $jsonDecodeValue = json_decode($value,TRUE);
        
        $getUrl = '?';
        foreach ($jsonDecodeValue as $key => $value) {
            $getUrl .= '&'.$key.'='.$value;
        }

        $url = base_url().'tracking/manage/0'.$getUrl;
        return $url;

    }


    function getWithoutMergeURL($value){
        return $this->getMergeURL($value);
    }


     /*
     *  delete code starts here
     */

    function delete($historyId = 0) {
        $this->db->where("historyId", $historyId);
        $this->db->delete(HISTORY);
        redirect("history/manage");
    }

}