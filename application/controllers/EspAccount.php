<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EspAccount extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
        $this->load->model('mdl_esp_account');
    }

    public function mailjet($start = 0)
    {          
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(MAILJET_ACCOUNTS, $start, $perPage);
       
        $dataCount = $responseData['totalCount'];
        $mailjetAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/mailjet/', $dataCount, $start, 3, $perPage,$mailjetAccounts);
        $data['headerTitle'] = "Mailjet Account List";
        $data['load_page'] = 'espMailjetAc';
        $data["curTemplateName"] = "espAccount/mailjet";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function ontraport($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(ONTRAPORT_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $ontraportAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/ontraport/', $dataCount, $start, 3, $perPage,$ontraportAccounts);
        $data['headerTitle'] = "Ontraport Account List";
        $data['load_page'] = 'espOntraportAc';
        $data["curTemplateName"] = "espAccount/ontraport";

        $this->load->view('commonTemplates/templateLayout', $data);
    }
   
    public function activeCampaign($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(ACTIVE_CAMPAIGN_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $activeCampaignAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/activeCampaign/', $dataCount, $start, 3, $perPage,$activeCampaignAccounts);
        $data['headerTitle'] = "Active Campaign Account List";
        $data['load_page'] = 'espActiveCampaignAc';
        $data["curTemplateName"] = "espAccount/activeCampaign";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function expertSender($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(EXPERT_SENDER_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $expertSenderAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/expertSender/', $dataCount, $start, 3, $perPage,$expertSenderAccounts);
        $data['headerTitle'] = "Expert Sender Account List";
        $data['load_page'] = 'espExpertSenderAc';
        $data["curTemplateName"] = "espAccount/expertSender";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function cleverReach($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(CLEVER_REACH_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $cleverReachAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/cleverReach/', $dataCount, $start, 3, $perPage,$cleverReachAccounts);
        $data['headerTitle'] = "Clever Reach Account List";
        $data['load_page'] = 'espCleverReachAc';
        $data["curTemplateName"] = "espAccount/cleverReach";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function omnisend($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(OMNISEND_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $omnisendAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/omnisend/', $dataCount, $start, 3, $perPage,$omnisendAccounts);
        $data['headerTitle'] = "Omnisend Account List";
        $data['load_page'] = 'espOmnisendAc';
        $data["curTemplateName"] = "espAccount/omnisend";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function sendgrid($start = 0)
    {   
        $data = array();
        $perPage = 5;
        $responseData = $this->mdl_esp_account->get_account_data(SENDGRID_ACCOUNTS, $start, $perPage);
        
        $dataCount = $responseData['totalCount'];
        $sendgridAccounts = $responseData['accounts'];

        $data = pagination_data('espAccount/sendgrid/', $dataCount, $start, 3, $perPage,$sendgridAccounts);
        $data['headerTitle'] = "Sendgrid Account List";
        $data['load_page'] = 'espSendgridAc';
        $data["curTemplateName"] = "espAccount/sendgrid";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function updateStatus(){
        $accountId = $this->input->post('accountId');
        $accountStatus = $this->input->post('accountStatus');
        $accountTable = $this->input->post('accountTable');
        $esp = $this->input->post('esp');
        $ip = $this->input->post('ip');
                
        //update account status active/in-active
        if(!empty($accountTable)) {
            $condition = array('id' => $accountId);
            $is_insert = false;
            $updateArr = array('status' => !$accountStatus);

            $result = ManageData($accountTable, $condition, $updateArr, $is_insert);
            if($result) {
                // INSERT into "account_status_log"
                $condition = array();
                $is_insert = true;
                $dataArr = array(
                    'esp' => $esp,
                    'account_id' => $accountId,
                    'status' => !$accountStatus,
                    'ip' => $ip,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                $accountStatusId = ManageData(ACCOUNT_STATUS_LOG, $condition, $dataArr, $is_insert);
                $response = array('result'=>'success');
            } else {
                $response = array('result'=>'error');
            }
        } else {
            $response = array('result'=>'error');
        }
        echo json_encode($response);
    }

    public function verifyCredential() {
        $esp = $this->input->post('esp');
        $accountId = $this->input->post('accountId');
        $password = trim($this->input->post('password'));
        $accountTable = getAccountTable($esp);

        // verify password
        if(!empty($accountTable)) {
            $condition  = array(
                            'id' => $accountId,
                            'email_password' => $password
                        );
            $is_single    = true;
            $accountData = GetAllRecord($accountTable, $condition, $is_single);
            if(!empty($accountData)) {
                $response = array('result'=>'success');
            } else {
                $response = array('result'=>'error');
            }
        } else {
            $response = array('result'=>'error');
        }
        echo json_encode($response);        
    }

    public function statusLogData($start = 0)
    {  
        $esp = $this->input->get('esp');
        $accountId = $this->input->get('accountId');

        if(!empty($accountId)) {
            $data = array();
            if (@$this->input->get('reset')) {
                $_GET = array();
            }
            $perPage = 10;
            $responseData = $this->mdl_esp_account->get_account_status_log($_GET, $esp, $accountId, $start, $perPage);
            
            $dataCount = $responseData['totalCount'];
            $statusLog = $responseData['statusLog'];
            $data = pagination_data('espAccount/statusLogData/', $dataCount, $start, 3, $perPage,$statusLog);
            
            // get esp function name
            $functionName =  '';
            if($esp == 9) {
                $functionName = 'mailjet';
            }else if($esp == 12){
                $functionName = 'ontraport';
            }else if($esp == 13){
                $functionName = 'activeCampaign';
            }else if($esp == 14){
                $functionName = 'expertSender';
            }else if($esp == 15){
                $functionName = 'cleverReach';
            }else if($esp == 16){
                $functionName = 'omnisend';
            }else if($esp == 5){
                $functionName = 'sendgrid';
            }
            $data['functionName'] = $functionName;
            $data['headerTitle'] = getHeadingName($esp) . ' Status Log';
            $data['provider'] = getHeadingName($esp);
            $data['esp'] = $esp;
            $data['accountId'] = $accountId;
            $data["curTemplateName"] = "espAccount/statusLog";

            $this->load->view('commonTemplates/templateLayout', $data);
        }
    }
}