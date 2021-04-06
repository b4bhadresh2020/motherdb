<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Employee extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_employee');    
    }
        
    function manage($start = 0){

        $perPage = 25;

        $responseData = $this->mdl_employee->getAllEmployee($_GET,$start,$perPage); 

        $dataCount = $responseData['totalCount'];
        $unsubscriberData = $responseData['employees'];

        $data = pagination_data('employee/manage/', $dataCount, $start, 3, $perPage,$unsubscriberData);

        $data['load_page'] = 'employee';
        $data['headerTitle'] = "Employee";
        $data["curTemplateName"] = "employee/list";

        $this->load->view('commonTemplates/templateLayout', $data);
    }  
    
    function getEmployeeData(){
        $employeeId = $this->input->post("employeeId");
        $employee = GetAllRecord(ADMINMASTER,array("adminId" => $employeeId),true,[],[],[],'adminId,adminUname,fullname,isInActive,role');
        echo json_encode($employee);
    }

    function addEdit(){
        $adminId = $this->input->post("adminId");
        $adminUname = $this->input->post("adminUname");
        $fullname = $this->input->post("fullname");
        $isInActive = $this->input->post("isInActive");
        $role = $this->input->post("role");        
        
        $data = array(
            "adminUname" => $adminUname,
            "fullname" => $fullname,
            "isInActive" => $isInActive,
            "role" => $role,
            "isDelete" => 0
        );

        if(empty($adminId)){            
            $isExist = GetAllRecordCount(ADMINMASTER,array("adminUname" => $adminUname,"isDelete" => 0));
            if(!$isExist){
                $is_insert = true;
                $condition = array();  
                $data["adminPassword"] = md5($this->input->post("password")); 
                $message = "Account created successfully";
                $response = ManageData(ADMINMASTER,$condition,$data,$is_insert);
            }else{
                $message = "Username already existed";
            }
        }else{
            $isExist = GetAllRecordCount(ADMINMASTER,array("adminUname" => $adminUname,"isDelete" => 0,"adminId <>" => $adminId));
            if(!$isExist){
                $is_insert = false;
                $condition = array("adminId" => $adminId);
                $message = "Account update successfully";  
                $response = ManageData(ADMINMASTER,$condition,$data,$is_insert);
            }else{
                $message = "Username already existed";
            }
        }   
        
        $this->session->set_flashdata("message",$message);
        redirect(site_url().'employee/manage');        
    }

    function delete(){      
         
        $condition = array('adminId' => $_POST["employeeId"]);
        $is_insert = FALSE;
        $updateArr = array('isDelete' => "1");
        ManageData(ADMINMASTER,$condition,$updateArr,$is_insert);
        $this->session->set_flashdata("message","Account delete successfully");
        return true;
     }
}
