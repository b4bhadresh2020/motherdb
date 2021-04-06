<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ChangePassword extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!is_logged()) {
            redirect(base_url());
        }
    }

    public function index()
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
    
}
