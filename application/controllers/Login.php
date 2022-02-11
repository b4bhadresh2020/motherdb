<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    private $gaobj;

    function __construct() {

        parent::__construct();

        if(is_logged() && is_admin()){
            redirect("adminHome");
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->library('GoogleAuthenticator');
        $this->gaobj = new GoogleAuthenticator();
    }

    /*
     *  login part code start here
     */

    function index() {
        $this->load->library('session');
        $adminLoginUname = $this->input->post("adminLoginUname");
        
        $adminLoginPassword = $this->input->post("adminLoginPassword");

        $this->form_validation->set_rules('gCode', 'Code', 'required|max_length[6]|callback_verify_google_tfa_code');
        $this->form_validation->set_rules('adminLoginUname', 'User Name', 'required');
        $this->form_validation->set_rules('adminLoginPassword', 'Password', 'required|callback_validate_uname_pass');
        
        if ($this->form_validation->run() != FALSE) {

            $condition = array(
                'adminUname'    => $adminLoginUname,
                'adminPassword' =>  md5($adminLoginPassword)
            );
            $this->db->where($condition);
            $curAdminInfo = $this->db->get(ADMINMASTER)->row_array();
            $logindata = array(
                'adminId'    => $curAdminInfo['adminId'],
                'adminUname' => $curAdminInfo['adminUname'],
                'name'       => $curAdminInfo['fullname'],
                'role'       => $curAdminInfo['role']
            );
            
            $this->session->set_userdata($logindata);
            if($curAdminInfo['role'] == 0){
                redirect('adminHome');
            }else{
                redirect('mailUnsubscribe');
            }
        }
        
        
        $errorMsg = GetMsg('adminErrorMsg');
        if (trim($errorMsg) == "")
            $data['error_msg'] = GetFormError();
        else
            $data['error_msg'] = $errorMsg;
        $data['suc_msg'] = GetMsg('adminSucMsg');
        $data["adminLoginUname"] = $adminLoginUname;
        $data['gCode'] = '';
        $data["curTemplateName"] = "login/login";
        $this->load->view('login/login', $data);
    }

    function validate_uname_pass($uname) {
        
        $adminLoginUname = $this->input->post("adminLoginUname");
        $adminLoginPassword = $this->input->post("adminLoginPassword");
        $this->db->where('adminUname', $adminLoginUname);
        $isUnameExits = $this->db->get(ADMINMASTER)->num_rows();
        if ($isUnameExits == 0) {
            $this->form_validation->set_message('validate_uname_pass', "Please Enter a valid Username.");
            return false;
        } else {
            $condition = array(
                'adminUname'    => $adminLoginUname,
                'adminPassword' =>  md5($adminLoginPassword)
            );
            $this->db->where($condition);
            $isUnameExits = $this->db->get(ADMINMASTER)->num_rows();
            if ($isUnameExits == 0) {
                $this->form_validation->set_message('validate_uname_pass', "Please Enter a valid Username and Password.");
                return false;
            } else {
                $this->db->where($condition);
                $curAdminInfo = $this->db->get(ADMINMASTER)->row_array();

                if ($curAdminInfo["isInActive"] == 1) {
                    $this->form_validation->set_message('validate_uname_pass', "Please contact to admin.");
                    return false;
                } else {
                    return true;
                }
            }
        }
    }


    function verify_google_tfa_code($oneCode){

        // 2 factor authentication codes...

        if ($oneCode != '') {

            if (strlen($oneCode) == 6) {
                
                $secret = GOOGLE_2FA_SECRET; //$gaobj->createSecret();
                $checkResult = $this->gaobj->verifyCode($secret, $oneCode, 2); // 2 = 2*30sec clock tolerance
                if ($checkResult) {
                    return true;
                }else{
                    $this->form_validation->set_message('verify_google_tfa_code', "Invalid Code");
                    return false;
                }    
            }else{
                $this->form_validation->set_message('verify_google_tfa_code', "Invalid Code");
                return false;
            }
        }else{
            $this->form_validation->set_message('verify_google_tfa_code', "Please Enter the Code");
            return false;
        }

    }


    // function getqrcode(){
    //     $secret = GOOGLE_2FA_SECRET;
    //     $getQRCodeGoogleUrl = $this->gaobj->getQRCodeGoogleUrl('Name',$secret,'Service Name');
    //     header("location:".$getQRCodeGoogleUrl);
    // }

    /*
     *  login part code end here
     */

    function getCode(){
        $secret = GOOGLE_2FA_SECRET; 
        $checkResult = $this->gaobj->getCode($secret);
        return $checkResult;
    }
}
