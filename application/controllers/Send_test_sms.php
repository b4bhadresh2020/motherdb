<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Send_test_sms extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }


    /*
    ======
        @defaultResponse
    */
    function defaultResponse(){
        $response['err'] = 1;
        $response['msg'] = "Something goes wrong. Please try again later";
        $this->sendData(@$response);
    }

    /*
    ======
        @sendData
        ->set response
    */
    function sendData($response){
        echo json_encode(@$response);
    }

    /*
     *  send_sms starts: ajax call
     */

    function send_sms() {

        $postData = $this->input->post();
        $test_popup_service_provider = $postData['test_popup_service_provider'];
       
        switch ($test_popup_service_provider) {
            case "forty_two":
                $this->send_test_sms_to_forty_two($_POST);
                break;
            case "cp_sms":
                $this->send_test_sms_to_cp_sms($_POST);
                break;
            case "warriors_sms":
                $this->send_test_sms_to_warriors_sms($_POST);
                break;    
            case "sms_edge":
                $this->send_test_sms_to_sms_edge($_POST);
                break;
            case "mmd_smart":
                
                break;
            case "in_mobile":
                
                break;
            case "sinch":
                $this->send_sms_to_sinch($_POST);
                break;
            default:
                $this->defaultResponse();
        }
    }

    /*
     * send_sms ends
     */


    function send_test_sms_to_forty_two($postData){

        $this->load->model('mdl_forty_two_sms_provider');

        $response = array();
        $mobile = $postData['test_popup_prefix'].$postData['test_popup_mobile_number'];
        $test_msg = $postData['test_msg'];
        $test_popup_sender_id = $postData['test_popup_sender_id'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $uniqueKey = "test0";

        $replacedUrl = $domain.'r/'.$uniqueKey;
        if(!empty($unsubscribeDomain)){
            $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
        }else{
            $unsubscribe_url = $domain.$uniqueKey;
        }  

        $fixedString   = ["{url}","{unsubscribe_url}"];
        $replacedWith = [$replacedUrl,$unsubscribe_url];
        $replacedMsg = str_replace($fixedString,$replacedWith,$test_msg);

        $params = array(
            'destinations' => array(array('number' => $mobile)),
            'sms_content' => array(
                'message' => $replacedMsg,
                'sender_id' => $test_popup_sender_id 
            )
        );

        $model_response = $this->mdl_forty_two_sms_provider->send_sms_with_use_of_forty_two($params);
        if (@$model_response['result_info'] != '') {
                
            if($model_response['result_info']['status_code'] == 200){
                $response['err'] = 0;
            }else{
                $response['err'] = 1;
            }
            $response['msg'] = $model_response['result_info']['description'];
            
        }else{
            //error
            $response['err'] = 1;
            $response['msg'] = "There is some problem occures. Please try again later.";
        }

        $this->sendData($response);
        
    }

    function send_test_sms_to_cp_sms($postData){

        $this->load->model('mdl_cp_sms_provider');

        $response = array();
        $mobile = $postData['test_popup_prefix'].$postData['test_popup_mobile_number'];
        $test_msg = $postData['test_msg'];
        $test_popup_sender_id = $postData['test_popup_sender_id'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $uniqueKey = "test0";

        $replacedUrl = $domain.'r/'.$uniqueKey;
        if(!empty($unsubscribeDomain)){
            $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
        }else{
            $unsubscribe_url = $domain.$uniqueKey;
        } 

        $fixedString   = ["{url}","{unsubscribe_url}"];
        $replacedWith = [$replacedUrl,$unsubscribe_url];
        $replacedMsg = str_replace($fixedString,$replacedWith,$test_msg);

        $params = array(
            'to' => $mobile,
            'message' => $replacedMsg,
            'from' => $test_popup_sender_id
        );

        $model_response = $this->mdl_cp_sms_provider->send_sms_with_use_of_cp_sms($params);
        
        if (@$model_response['success']) {
            //error
            $response['err'] = 0;
            $response['msg'] = 'SMS has been sent successfuuly';
        }else{
            //ok
            $response['err'] = 1;
            $response['msg'] = $model_response['error']['message'];
        }

        $this->sendData($response);        
    }

    function send_test_sms_to_sms_edge($postData){

        $this->load->model('mdl_sms_edge_sms_provider');

        $response = array();
        $mobile = $postData['test_popup_prefix'].$postData['test_popup_mobile_number'];
        $test_msg = $postData['test_msg'];
        $test_popup_sender_id = $postData['test_popup_sender_id'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $uniqueKey = "test0";

        $replacedUrl = $domain.'r/'.$uniqueKey;
        if(!empty($unsubscribeDomain)){
            $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
        }else{
            $unsubscribe_url = $domain.$uniqueKey;
        } 

        $fixedString   = ["{url}","{unsubscribe_url}"];
        $replacedWith = [$replacedUrl,$unsubscribe_url];
        $replacedMsg = str_replace($fixedString,$replacedWith,$test_msg);

        $params = array(
            'to' => $mobile,
            'text' => $replacedMsg,
            'from' => $test_popup_sender_id,
        );

        $model_response = $this->mdl_sms_edge_sms_provider->send_sms_with_use_of_sms_edge($params);

        if (@$model_response['success'] == 'true') {
            //error
            $response['err'] = 0;
            $response['msg'] = 'SMS has been sent successfuuly';
        }else{
            //ok
            $response['err'] = 1;
            $response['msg'] = $model_response['errors'][0];
        }

        $this->sendData($response);
    }

    function send_sms_to_sinch($postData){

        $this->load->model('mdl_sms_sinch_provider');

        $response = array();
        $mobile = $postData['test_popup_prefix'].$postData['test_popup_mobile_number'];
        $test_msg = $postData['test_msg'];
        $test_popup_sender_id = $postData['test_popup_sender_id'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $uniqueKey = "test0";

        $replacedUrl = $domain.'r/'.$uniqueKey;
        if(!empty($unsubscribeDomain)){
            $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
        }else{
            $unsubscribe_url = $domain.$uniqueKey;
        } 

        $fixedString   = ["{url}","{unsubscribe_url}"];
        $replacedWith = [$replacedUrl,$unsubscribe_url];
        $replacedMsg = str_replace($fixedString,$replacedWith,$test_msg);

        $params = array(
            'to' => array($mobile),
            'body' => $replacedMsg,
            'from' => $test_popup_sender_id,
        );

        $model_response = $this->mdl_sms_sinch_provider->send_sms_with_use_of_sinch($params);

        if (@$model_response['success'] == 'true') {
            //error
            $response['err'] = 0;
            $response['msg'] = 'SMS has been sent successfully';
        }else{
            //ok
            $response['err'] = 1;
            $response['msg'] = $model_response['errors'][0];
        }

        $this->sendData($response);
    }

    function send_test_sms_to_warriors_sms($postData){

        $this->load->model('mdl_warriors_sms_provider');

        $response = array();
        $mobile = $postData['test_popup_prefix'].$postData['test_popup_mobile_number'];
        $test_msg = $postData['test_msg'];
        $test_popup_sender_id = $postData['test_popup_sender_id'];
        $domain = $postData['domain'];
        $unsubscribeDomain = $postData['unsubscribeDomain'];
        $uniqueKey = "test0";

        $replacedUrl = $domain.'r/'.$uniqueKey;
        if(!empty($unsubscribeDomain)){
            $unsubscribe_url = $unsubscribeDomain.$uniqueKey;
        }else{
            $unsubscribe_url = $domain.$uniqueKey;
        } 

        $fixedString   = ["{url}","{unsubscribe_url}"];
        $replacedWith = [$replacedUrl,$unsubscribe_url];
        $replacedMsg = str_replace($fixedString,$replacedWith,$test_msg);

        $params = array(
            'contacts' => $mobile,
            'msg' => urlencode($replacedMsg),
            'senderid' => urlencode($test_popup_sender_id),
            'uniqueKey' => $uniqueKey
        );

        $model_response = $this->mdl_warriors_sms_provider->send_sms_with_use_of_warriors_sms($params);
        
        if (@$model_response['status'] == "OK") {
            if (@$model_response['delivery'] == "sent") {
                $response['err'] = 0;
                $response['msg'] = 'SMS has been sent successfully';
            }else{
                $response['err'] = 0;
                $response['msg'] = 'SMS has been sent successfully';
            }            
        }else{            
            $response['err'] = 1;
            $response['msg'] = $model_response['error_msg'];
        }

        $this->sendData($response);        
    }
}