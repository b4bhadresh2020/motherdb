<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Integromat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_integromat_hook');    
    }
        
    function manage($start = 0){

        $perPage = 25;

        $responseData = $this->mdl_integromat_hook->getAllHook($_GET,$start,$perPage); 

        $dataCount = $responseData['totalCount'];
        $hooks = $responseData['hooks'];

        $data = pagination_data('integromat/manage/', $dataCount, $start, 3, $perPage,$hooks);

        $data['load_page'] = 'integromat';
        $data['headerTitle'] = "Integromat";
        $data["curTemplateName"] = "integromat/list";

        $this->load->view('commonTemplates/templateLayout', $data);
    }  
    
    function getHookData(){
        $id = $this->input->post("id");
        $hook = GetAllRecord(INTEGROMAT_HOOKS,array("id" => $id),true,[],[],[],'id,hook_name,hook_url');
        echo json_encode($hook);
    }

    function addEdit(){
        $id = $this->input->post("id");
        $hook_name = $this->input->post("hook_name");
        $hook_url = $this->input->post("hook_url");
        
        $data = array(
            "hook_name" => $hook_name,
            "hook_url" => $hook_url,
        );

        if(empty($id)){            
            $isExist = GetAllRecordCount(INTEGROMAT_HOOKS,array("hook_url" => $hook_url));
            if(!$isExist){
                $is_insert = true;
                $condition = array();  
                $message = "Hook added successfully";
                $response = ManageData(INTEGROMAT_HOOKS,$condition,$data,$is_insert);
            }else{
                $message = "Hook url already existed";
            }
        }else{
            $is_insert = false;
            $condition = array("id" => $id);
            $message = "Hook update successfully";  
            $response = ManageData(INTEGROMAT_HOOKS,$condition,$data,$is_insert);
        }   
        
        $this->session->set_flashdata("message",$message);
        redirect(site_url().'integromat/manage');        
    }

    function delete(){      
        // RESET LIVE DELIVERY WHERE THIS WEBHOOK IS USED
        $is_insert = false;
        $condition = array("integromatHookId" => $_POST["id"]);
        $updateArr = array("integromatHookId" => 0);
        $response = ManageData(LIVE_DELIVERY,$condition,$updateArr,$is_insert);
        
        $condition = array('id' => $_POST["id"]);
        deleteData(INTEGROMAT_HOOKS,$condition);
        $this->session->set_flashdata("message","Hook reset in live delivery and deleted successfully");
        return true;
     }
}
