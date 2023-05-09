<?php
require_once(FCPATH . 'vendor/autoload.php');
/*
    @counts
    Need this function only in PHP >= 7.2.
*/
function counts($stuff)
{

    if ($stuff == '' || !is_array($stuff)) {
        $stuff = array();
    }

    return count($stuff);
}

#fetch all records to display with filters

function GetAllRecordsTest($table_name, $condition, $is_single, $selected_rows = '')
{
    $ci = &get_instance();


    if ($condition) {
        $ci->db->where($condition);
    }
    if ($selected_rows) {
        $ci->db->select($selected_rows);
    }

    $res = $ci->db->get($table_name);

    if ($is_single) {
        return $res->row_array();
    } else {
        return $res->result_array();
    }
}

function GetAllRecord($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '', $startNo = '')
{
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if ($is_like && count($is_like)) {
        foreach ($is_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->like($key1, $val1);
            }
        }
    }
    if ($or_like && count($or_like)) {
        foreach ($or_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->or_like($key1, $val1);
            }
        }
    }
    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows != "") {
        $ci->db->select($selected_rows);
    }

    if ($startNo != "") {
        $limit = getConfigVal('servicePaginationLimit');
        $start = ($startNo - 1) * $limit;
        $ci->db->limit($limit, $start);
    }
    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetRecordWithLimit($table_name = '', $condition = array(), $join_table = '', $table_id = '', $join_id = '', $type = '', $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '', $startNo = '', $limit = '')
{
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);

    if ($join_table != '')
        $ci->db->join($join_table, "$table_name.$table_id = $join_table.$join_id", $type);

    if ($is_like && count($is_like)) {
        foreach ($is_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->like($key1, $val1);
            }
        }
    }
    if ($or_like && count($or_like)) {
        foreach ($or_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->or_like($key1, $val1);
            }
        }
    }
    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows != "") {
        $ci->db->select($selected_rows);
    }

    if ($startNo != "" && $limit != "") {
        $start = ($startNo - 1) * $limit;
        $ci->db->limit($limit, $start);
    }

    if ($limit != "") {
        $ci->db->limit($limit, 0);
    }

    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetAllRecordCount($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '')
{
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if ($is_like && count($is_like)) {
        foreach ($is_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->like($key1, $val1);
            }
        }
    }
    if ($or_like && count($or_like)) {
        foreach ($or_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->or_like($key1, $val1);
            }
        }
    }
    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows != "") {
        $ci->db->select($selected_rows);
    }
    $res = $ci->db->count_all_results($table_name);
    return $res;
}

function GetAllRecordCountIn($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $is_in = array(), $selected_rows = '')
{
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);

    if ($is_in && count($is_in)) {
        foreach ($is_in as $key => $val) {
            $ci->db->where_in($key, $val);
        }
    }

    if ($is_like && count($is_like)) {
        foreach ($is_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->like($key1, $val1);
            }
        }
    }
    if ($or_like && count($or_like)) {
        foreach ($or_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->or_like($key1, $val1);
            }
        }
    }
    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows != "") {
        $ci->db->select($selected_rows);
    }
    $res = $ci->db->count_all_results($table_name);
    return $res;
}

function GetAllRecordIn($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $is_in = array(), $selected_rows = '')
{
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if ($is_in && count($is_in)) {
        foreach ($is_in as $key => $val) {
            $ci->db->where_in($key, $val);
        }
    }
    if ($is_like && count($is_like)) {
        foreach ($is_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->like($key1, $val1);
            }
        }
    }
    if ($or_like && count($or_like)) {
        foreach ($or_like as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $ci->db->or_like($key1, $val1);
            }
        }
    }
    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows != "") {
        $ci->db->select($selected_rows);
    }
    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetDatabyqry($sql)
{
    $ci = &get_instance();
    $res = $ci->db->query($sql);
    return $res->result_array();
}

#insert update query with filter and flag

function ManageData($table_name = '', $condition = array(), $udata = array(), $is_insert = false)
{
    $resultArr = array();
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if ($is_insert) {
        $ci->db->insert($table_name, $udata);
        $insertid = $ci->db->insert_id();
        return $insertid;
        #return 0;
    } else {
        if ($ci->db->update($table_name, $udata)) {
            return 1;
        } else {
            return 0;
        }
    }
}

#insert update query with filter and flag

function incrementReportData($table_name = '', $condition = array(), $value = 0, $status = 'inc')
{
    $resultArr = array();
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if (@$status && $status == "inc")
        $ci->db->set('reportVal', 'reportVal+' . $value, FALSE);
    if (@$status && $status == "dec")
        $ci->db->set('reportVal', 'reportVal-' . $value, FALSE);
    $ci->db->update($table_name);
    return 0;
}

function incrementData($table_name = '', $condition = array(), $fields = "", $value = 0, $status = 'inc')
{
    $resultArr = array();
    $ci = &get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if (@$status && $status == "inc")
        $ci->db->set($fields, '' . $fields . '+' . $value . '', FALSE);
    if (@$status && $status == "dec")
        $ci->db->set($fields, '' . $fields . '-' . $value . '', FALSE);
    $ci->db->update($table_name);
    return 0;
}

#joinTable

function JoinData($table_name = '', $condition = array(), $join_table = '', $table_id = '', $join_id = '', $type = '', $is_single = false, $order_by = array(), $selected_rows = '', $limit = '')
{
    $ci = &get_instance();
    #$ci->db->select('first_name,last_name');

    if ($condition && count($condition))
        $ci->db->where($condition);
    $ci->db->from($table_name);

    if ($join_table)
        $ci->db->join($join_table, "$table_name.$table_id = $join_table.$join_id", $type);

    if ($order_by && count($order_by)) {
        foreach ($order_by as $key => $val) {
            $cur_filter = array();
            $cur_filter = $val;
            foreach ($cur_filter as $key1 => $val1) {
                $order = $val1 ? $val1 : 'asc';
                $ci->db->order_by($key1, $order);
            }
        }
    }
    if ($selected_rows)
        $ci->db->select($selected_rows);

    if ($limit != "") {
        $ci->db->limit($limit);
    }

    $res = $ci->db->get();
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function JoinDataCount($table_name = '', $condition = array(), $join_table = '', $table_id = '', $join_id = '', $type = '')
{
    $ci = &get_instance();
    #$ci->db->select('first_name,last_name');

    if ($condition && count($condition))
        $ci->db->where($condition);
    $ci->db->from($table_name);

    if ($join_table)
        $ci->db->join($join_table, "$table_name.$table_id = $join_table.$join_id", $type);

    $res = $ci->db->count_all_results();
    return $res;
}

#Creating Pagination Link

function pagiationData($str, $num, $start, $segment, $perpage = 20, $isExpload = FALSE)
{

    $CI = &get_instance();
    $config['base_url'] = site_url('/') . $str;
    $config['total_rows'] = $num;
    if ($perpage) {
        $config['per_page'] = $perpage;
    } else {
        $config['per_page'] = $CI->session->userdata('per_page') ? $CI->session->userdata('per_page') : $perpage;
    }
    $config["reuse_query_string"] = TRUE;
    $config['uri_segment'] = $segment;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['cur_page'] = $start;
    $config['first_tag_open'] = '<li class="first paginate_button">';
    $config['first_tag_close'] = '</li>';
    $config['next_tag_open'] = '<li class="paginate_button">';
    $config['next_tag_close'] = '</li>';
    $config['num_tag_open'] = '<li class="paginate_button">';
    $config['num_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="last paginate_button">';
    $config['last_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="paginate_button">';
    $config['prev_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="paginate_button active"><a href="javascript:;">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_links'] = 1;

    $CI->pagination->initialize($config);

    if ($isExpload == 'true') {
        $query = $CI->db->last_query();
    } else {
        $query = $CI->db->last_query() . " LIMIT " . $start . " , " . $config['per_page'];
    }

    //print_r($query);die;
    $res = $CI->db->query($query);
    $data['listArr'] = $res->result_array();
    $data['num'] = $res->num_rows();
    $data['Total'] = $num;
    $data['start'] = $start;
    $data['links'] = $CI->pagination->create_links();
    $ofpage = ($start + $data['num']);

    if ($num > 0) {

        $start = $start + 1;
        $data['pageinfo'] = 'Showing ' . $start . ' to ' . $ofpage . ' of ' . $data['Total'] . ' entries';
    } else {

        $data['pageinfo'] = "No Records";
    }

    return $data;
}



function pagination_data($str, $total_rows, $start, $segment, $perpage = 20, $result_data = array())
{

    $CI = &get_instance();
    $config['base_url'] = site_url('/') . $str;
    $config['total_rows'] = $total_rows;
    if ($perpage) {
        $config['per_page'] = $perpage;
    } else {
        $config['per_page'] = $CI->session->userdata('per_page') ? $CI->session->userdata('per_page') : $perpage;
    }
    $config["reuse_query_string"] = TRUE;
    $config['uri_segment'] = $segment;
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['cur_page'] = $start;
    $config['first_tag_open'] = '<li class="first paginate_button">';
    $config['first_tag_close'] = '</li>';
    $config['next_tag_open'] = '<li class="paginate_button">';
    $config['next_tag_close'] = '</li>';
    $config['num_tag_open'] = '<li class="paginate_button">';
    $config['num_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="last paginate_button">';
    $config['last_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="paginate_button">';
    $config['prev_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="paginate_button active"><a href="javascript:;">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_links'] = 1;

    $CI->pagination->initialize($config);

    $data['listArr'] = $result_data;
    $data['Total'] = $total_rows;
    $data['start'] = $start;
    $data['links'] = $CI->pagination->create_links();
    $ofpage = ($start + count($result_data));

    if ($total_rows > 0) {

        $start = $start + 1;
        $data['pageinfo'] = 'Showing ' . $start . ' to ' . $ofpage . ' of ' . $data['Total'] . ' entries';
    } else {

        $data['pageinfo'] = "No Records";
    }

    return $data;
}



function GetFormError()
{ //return single error message after form validation
    $CI = &get_instance();
    $errorarr = $CI->form_validation->error_array();
    if (count($errorarr) === 0) {
        return FALSE;
    } else {
        foreach ($errorarr as $key => $val) {
            return $val;
        }
    }
}

function pre($str)
{ //Print prev screen for array
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}

function last_query()
{ //print last executed query
    $CI = &get_instance();
    pre($CI->db->last_query());
}

function ValidImageExt()
{
    $dropdown = array(
        'gif' => 'gif',
        'jpg' => 'jpg',
        'jpeg' => 'jpeg',
        'png' => 'png',
        'bmp' => 'bmp',
    );
    return $dropdown;
}

function uploadFile($uploadFile, $filetype, $folder, $fileName = '')
{
    $CI = &get_instance();
    $resultArr = array();
    $config['max_size'] = '1024000';
    if ($filetype == 'img')
        $config['allowed_types'] = 'gif|jpg|png|jpeg|png';
    if ($filetype == 'All')
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx|zip|xls';
    if ($filetype == 'csv')
        $config['allowed_types'] = 'csv';
    if ($filetype == 'swf')
        $config['allowed_types'] = 'swf';
    if ($filetype == 'mp3')
        $config['allowed_types'] = 'mp3|wma|wav|.ra|.ram|.rm|.mid|.ogg';
    if ($filetype == 'kml')
        $config['allowed_types'] = 'kml|kmz';
    if ($filetype == '*')
        $config['allowed_types'] = '*';


    if (strpos($folder, 'application/views') !== FALSE)
        $config['upload_path'] = './' . $folder . '/';
    else
        $config['upload_path'] = './upload/' . $folder . '/';
    if ($fileName != "")
        $config['file_name'] = $fileName;

    //echo $config['upload_path'];
    if (!is_dir($config['upload_path']))
        mkdir($config['upload_path'], '0777');

    $CI->load->library('upload', $config);
    $CI->upload->initialize($config);

    if (!$CI->upload->do_upload($uploadFile)) {
        $resultArr['success'] = false;
        $resultArr['error'] = $CI->upload->display_errors();
    } else {
        $resArr = $CI->upload->data();
        $resultArr['success'] = true;

        if (strpos($folder, 'application/views') !== FALSE) {
            $resultArr['path'] = $folder . "/" . $resArr['file_name'];
        } else {
            $resultArr['path'] = "upload/" . $folder . "/" . $resArr['file_name'];
        }
    }
    return $resultArr;
}

function uploadMultiFiles($fieldName, $folder, $options = array())
{
    $CI = &get_instance();
    $CI->load->library('upload');
    $response = array();
    $files = $_FILES;
    $cpt = count($_FILES[$fieldName]['name']);
    $options['upload_path'] = "./upload/";
    if (strpos($folder, 'application/views') !== FALSE)
        $options['upload_path'] = './' . $folder . '/';
    else
        $options['upload_path'] = './upload/' . $folder . '/';
    $options['allowed_types'] = '*';
    for ($i = 0; $i < $cpt; $i++) {
        $_FILES[$fieldName]['name'] = $files[$fieldName]['name'][$i];
        $_FILES[$fieldName]['type'] = $files[$fieldName]['type'][$i];
        $_FILES[$fieldName]['tmp_name'] = $files[$fieldName]['tmp_name'][$i];
        $_FILES[$fieldName]['error'] = $files[$fieldName]['error'][$i];
        $_FILES[$fieldName]['size'] = $files[$fieldName]['size'][$i];
        $CI->upload->initialize($options);
        $fileName = $files[$fieldName]['name'][$i];
        //upload the image
        if (!$CI->upload->do_upload($fieldName)) {
            $response['error'][] = $CI->upload->display_errors();
        } else {
            $resArr = $CI->upload->data();
            //$response[] = "upload/" . $resArr['file_name'];
            if (strpos($folder, 'application/views') !== FALSE) {
                $response[] = $folder . "/" . $resArr['file_name'];
            } else {
                $response[] = "upload/" . $folder . "/" . $resArr['file_name'];
            }
        }
    }

    return $response;
}

function SetMsg($var, $msg)
{
    $ci = &get_instance();
    $ci->session->set_flashdata($var, $msg);
}

function GetMsg($var)
{
    $ci = &get_instance();
    return $ci->session->flashdata($var);
}

function getConfigVal($keyParam)
{
    $ci = &get_instance();
    $sql = "select configVal from " . SITECONFIG . " where configKey='$keyParam'";
    $configVal = $ci->db->query($sql)->row_array($sql);
    return isset($configVal['configVal']) ? $configVal['configVal'] : "";
}

function getReportVal($keyParam)
{
    $ci = &get_instance();
    $sql = "select reportVal from " . PAYMENT_REPORT . " where reportKey='$keyParam'";
    $reportVal = $ci->db->query($sql)->row_array($sql);
    return isset($reportVal['reportVal']) ? $reportVal['reportVal'] : "";
}

function loginRegSectionMsg($msgId = "")
{
    $msgArr = array(
        "insertData" => "Data Inserted Successfully.",
        "updateData" => "Data Updated Successfully.",
    );
    if ($msgId !== "")
        return $msgArr[$msgId];
    else
        return $msgArr;
}

function getAccountTableName($provider)
{
    $tableNames = array(
        '1' => 'aweber_accounts',
        '4' => 'ongage_accounts',
        '5' => 'sendgrid_accounts',
        '7' => 'sendpulse_accounts',
        '8' => 'mailerlite_accounts',
        '9' => 'mailjet_accounts',
        '10' => 'convertkit_accounts',
        '11' => 'marketing_platform_accounts',
        '12' => 'ontraport_accounts',
        '13' => 'active_campaign_accounts',
        '14' => 'expert_sender_accounts',
        '15' => 'clever_reach_accounts',
        '16' => 'omnisend_accounts'
    );
    if (array_key_exists($provider, $tableNames)) {
        return $tableNames[$provider];
    }
}

function getProviderList($provider)
{
    $CI = &get_instance();
    // $condition = array(
    //     'provider' => $provider
    // );
    // $is_single = FALSE;
    // $providerList = GetAllRecord(PROVIDERS,$condition,$is_single);
    $espAccountTable = getAccountTableName($provider);
    $CI->db->select('providers.*');
    $CI->db->from(PROVIDERS);
    if (!empty($espAccountTable)) {
        $CI->db->join($espAccountTable, 'providers.aweber_account=' . $espAccountTable . '.id', 'left');
        $CI->db->where($espAccountTable . '.status', 1);
    }
    $CI->db->where('providers.provider', $provider);
    $providerList = $CI->db->get()->result_array();

    if (count($providerList) > 0) {
        return $providerList;
    } else {
        return array();
    }
}

function getProviderName($providerId)
{
    $providerNames = array(
        '1' => 'Aweber',
        '2' => 'Transmitvia',
        '4' => 'Ongage',
        '5' => 'Sendgrid',
        '6' => 'Sendinblue',
        '7' => 'Sendpulse',
        '8' => 'Mailerlite',
        '9' => 'Mailjet',
        '10' => 'Convertkit',
        '11' => 'MarketingPlatform',
        '12' => 'Ontraport',
        '13' => 'ActiveCampaign',
        '14' => 'ExpertSender',
        '15' => 'CleverReach',
        '16' => 'Omnisend'
    );
    return $providerNames[$providerId];
}

function getAweverProviderListName($providerListId)
{
    $aweberList = array(
        '1' => 'Velkomstgaven.com (Norway)',
        '2' => 'Gratispresent.se (Sweden)',
        '3' => 'Velkomstgaven.dk (Denmark)',
        '4' => 'Freecasinodeal.com/no  (Norway)',
        '5' => 'Freecasinodeal.com/fi (Finland)',
        '6' => 'Freecasinodeal.com',
        '7' => 'FI - Katariinasmail',
        '8' => 'NO - Signesmail',
        '9' => 'SE - Frejasmail',
        '10' => 'CA - Getspinn',
        '11' => 'NO - Getspinn',
        '12' => 'NZ - Getspinn',
        '13' => 'Freecasinodeal.com/nz  (New Zealand)',
        '14' => 'DK - Signesmail',
        '15' => 'DK - abbie',
        '16' => 'FI - abbie',
        '17' => 'NO - abbie',
        '18' => 'SE - abbie',
        '19' => 'FreeCasinodeal/ca (Canada)',
        '20' => 'FelinaFinans/se',
        '21' => 'New_gratispresent',
        '22' => 'New_velkomstgaven_dk',
        '23' => 'New_velkomstgaven_com',
        '24' => 'New_velkomstgaven1_com',
        '25' => 'New_unelmalaina',
        '26' => 'Freecasinodeal/NZ (olivia)',
        '27' => 'Freecasinodeal/CA (sofia)',
        '28' => 'Freecasinodeal/NO (emma)',
        '29' => 'Freecasinodeal/FI (aida)',
        '30' => 'Frejasmail1/SE',
        '31' => 'Frejasmail2/SE',
        '32' => 'Signesmail1/DK',
        '33' => 'Katariinasmail1/FI',
        '34' => 'Signesmail1/NO',
        '35' => 'Signesmail2/NO',
        '36' => 'Abbiesmail1/CA',
        '37' => 'Abbiesmail2/CA',
        '38' => 'Ashleysmail/NZ',
        '39' => 'Ashleysmail1/NZ',
        '40' => 'Signesmail/DK',
        '41' => 'Velkomstgaven/NO',
        '42' => 'Velkomstgaven1/NO',
        '43' => 'Gratispresent/SE',
        '44' => 'Gratispresent1/SE',
        '45' => 'FelinaFinans/SE',
        '46' => 'FelinaFinans1/SE',
        '47' => 'FelinaFinansmail/SE',
        '48' => 'Unelmalaina/FI',
        '49' => 'Unelmalaina1/FI',
        '50' => 'Velkomstgaven/DK',
        '51' => 'Velkomstgaven1/DK',
        '52' => 'Getspinn1/CA',
        '53' => 'Getspinnmail/CA',
        '54' => 'Freecamail/CA',
        '55' => 'Gratisprodukttest/SE'
    );
    return $aweberList[$providerListId];
}

function getTransmitviaProviderListName($providerListId)
{
    $transmitviaList = array(
        '1' => 'NO - deveroper',
        '2' => 'SE - deveroper - Loan',
        '3' => 'SE - deveroper',
        '4' => 'FI - deveroper',
        '5' => 'NO Casino - eonoc',
        '6' => 'FI Casino - eonoc',
        '7' => 'FI - eacademyzone',
        '8' => 'NO - eacademyzone',
        '9' => 'SE - eacademyzone',
        '10' => 'SE - Loan - eacademyzone',
        '11' => 'Global Casino Dollars - divinecareca',
        '12' => 'Global Casino EUR - divinecareca',
        '13' => 'NO Casino - divinecareca',
        '14' => 'FI - ElasticEmail',
        '15' => 'SE - ElasticEmail',
        '16' => 'NO - ElasticEmail',
        '17' => 'NO - SparkPost',
        '18' => 'SE - SparkPost',
        '19' => 'FI - SparkPost',
        '20' => 'NO - Amazon',
        '21' => 'SE - Amazon',
        '22' => 'FI - Amazon'

    );
    return $transmitviaList[$providerListId];
}

function getOngageProviderListName($providerListId)
{
    $ongageList = array(
        "1" => "SE - Test", // Australia-camilla => SE - Test
        "2" => "DK - Test", // Australia - Kare => DK - Test
        "3" => "Canada - Camilla",
        "4" => "Canada - Kare",
        "5" => "Sweden - Camilla",
        "6" => "Sweden - Kare",
        "7" => "Norway - Camilla",
        "8" => "Norway - Kare",
        "9" => "Finland  - Camilla",
        "10" => "Finland  - Kare",
        "11" => "New Zealand  - Camilla",
        "12" => "NO - Test", // New Zealand  - Kare => NO - Test
        "13" => "Denmark  - Kare",
        "14" => "Denmark  - Camilla",
        "15" => "FI - Test"
    );
    return $ongageList[$providerListId];
}

function getSendgridProviderListName($providerListId)
{
    $sendgridList = array(
        "1" => "CA-Abbiesmail",
        "2" => "NZ-Ashleysmail",
        "3" => "NZ-Allfreeca",
        "4" => "CA-Allfreeca",
        "5" => "Katariinasmail",
        "6" => "Velkomstgaven/NO",
        "7" => "Gratispresent/SE",
        "8" => "Signesmail/NO",
        "9" => "Dagenspresent/SE",
    );
    return $sendgridList[$providerListId];
}

function getSendInBlueProviderListName($providerListId)
{
    $sendInBlueList = array(
        "1" => "NO",
        "2" => "CA",
        "3" => "NZ",
        "4" => "SE"
    );
    return $sendInBlueList[$providerListId];
}

function getSendPulseProviderListName($providerListId)
{
    $sendPulseList = array(
        "1" => "NO", // NO-Sendpulse
        "2" => "CA", // CA-Sendpulse
        "3" => "SE" // SE-Sendpulse
    );
    return $sendPulseList[$providerListId];
}

function getMailerliteProviderListName($providerListId)
{
    $mailerliteList = array(
        "1" => "DK-Velkomstgaven",
        "2" => "NO-Velkomstgaven.com",
        "3" => "NO-Velkomstgaven.com1",
        "4" => "SE-Gratispresent"
    );
    return $mailerliteList[$providerListId];
}

function getMailjetProviderListName($providerListId)
{
    $mailjetList = array(
        "1" => "Velkomstgaven/DK",
        "2" => "Gratispresent/SE",
        "3" => "Velkomstgaven/NOR",
        "4" => "Freja/SE",
        "5" => "Signesmail/DK",
        "6" => "Signesmail2/NO",
        "7" => "Produkttest/SE",
        "8" => "DagensPresent/SE",
        "9" => "VelkomstgavenVIP/DK",
        "10" => "VelkomstgavenVIP/NO",
    );
    return $mailjetList[$providerListId];
}

function getConvertkitProviderListName($providerListId)
{
    $convertkitList = array(
        "1" => "camilla/DK",
        "2" => "camilla/SE",
        "3" => "camilla/NO",
        "4" => "camilla/FI",
        "5" => "camilla/CA",
        "6" => "camilla/NZ",
        "7" => "Velkomstgaven/NOR",
        "8" => "Gratispresent/SE",
        "9" => "Velkomstgaven1/NOR",
        "10" => "Unelmalaina/FI",
        "11" => "Velkomstgaven/DK"
    );
    return $convertkitList[$providerListId];
}

function getMarketingPlatformProviderListName($providerListId)
{
    $marketingPlatformList = array(
        "1" => "SE-Gratispresent",
        "2" => "NO-Velkomstgaven",
        "3" => "DK-Velkomstgaven",
        "4" => "FI-Unelmalaina",
        "5" => "FreeCasinoDeal-CA",
        "6" => "FreeCasinoDeal-FI",
        "7" => "FreeCasinoDeal-NO",
        "8" => "FreeCasinoDeal-NZ",
        "9" => "NO-Velkomstgaven1"
    );
    return $marketingPlatformList[$providerListId];
}

function getOntraportProviderListName($providerListId)
{
    $ontraportList = array(
        "1" => "Gratispresentmail.se",
        "2" => "Freecasinodeal1/no",
        "3" => "Freecasinodeal1/fi",
        "4" => "Velkomstgavenmail.dk",
        "5" => "Freecasinodeal1/ca",
        "6" => "Freecasinodeal1/nz",
        "7" => "Velkomstgaven/DK",
        "8" => "Velkomstgaven/com",
        "9" => "Gratispresent/SE",
        "10" => "Unelmalaina/FI",
        "11" => "Velkomst/DK",
        "12" => "Signe/DK",
        "13" => "Dagens/SE",
        "14" => "Felina/SE",
        "15" => "Venla/FI",
        "16" => "Katariina/FI",
        "17" => "Allfree/CA",
        "18" => "Abbie/CA",
        "19" => "Ashley/NZ",
        "20" => "Produkt/NO"
    );
    return $ontraportList[$providerListId];
}

function getActiveCampaignProviderListName($providerListId)
{
    $activeCampaignList = array(
        "1" => "Velkomstgaven/NOR",
        "2" => "GratisPresent/SE",
        "3" => "Frejasmail/SE",
        "4" => "Unelmalaina/FI",
        "5" => "Signesmail/NOR",
        "6" => "Katariinasmail/FI",
        "7" => "Velkomstgaven/DK",
        "8" => "Signesmail/DK",
        "9" => "Velkomstgaven1/NO",
        "10" => "gratisprodukttester.com/NO",
        "11" => "dagenspresent.se/SE",
        "12" => "gratispresent1.com",
        "13" => "Velkomstgaven1/DK",
        "14" => "signesmaildk1/DK"

    );
    return $activeCampaignList[$providerListId];
}

function getExpertSenderProviderListName($providerListId)
{
    $expertSenderList = array(
        "1" => "camilla/abbiesmail2.com/CA",
        "2" => "camilla/ashleysmail1.com/NZ",
        "3" => "camilla/felinafinans.se/SE",
        "4" => "camilla/frejasmail2.se/SE",
        "5" => "camilla/katariinasmail1.com/FI",
        "6" => "camilla/signesmail1.dk/DK",
        "7" => "camilla/signesmail2.com/NO",
        "8" => "Kaare/NO-FreeCasinodeal",
        "9" => "Kaare/FI-FreeCasinodeal",
        "10" => "Kaare/CA-FreeCasinodeal",
        "11" => "Kaare/NZ-FreeCasinodeal",
        "12" => "Kaare/CA-GetSpinn",
        "13" => "Kaare/NZ-GetSpinn",
        "14" => "Kaare/NO-GetSpinn",
        "15" => "Kaare/gratispresentmail.se/SE",
        "16" => "Kaare/unelmalainamail.fi/Unelmalaina",
        "17" => "Kaare/Velkomstgaven-NO",
        "18" => "Kaare/DK-Velkomstgaven",
    );
    return $expertSenderList[$providerListId];
}

function getCleverReachListName($providerListId)
{
    $cleverReachList = array(
        "1" => "Velkomstgaven/DK",
        "2" => "Cathrinesmail/CA",
        "3" => "Cathrinesmail/DK",
        "4" => "Cathrinesmail/FI",
        "5" => "Cathrinesmail/NO",
        "6" => "Cathrinesmail/NZ",
        "7" => "Cathrinesmail/SE",
        "8" => "Velkomstgaven/NO",
        "9" => "Gratispresent/SE",
        "10" => "Unelmalaina/FI"
    );
    return $cleverReachList[$providerListId];
}

function getOmnisendListName($providerListId)
{
    $omnisendList = array(
        "1" => "SE-Gratispresent",
        "2" => "NO-Velkomstgaven",
        "3" => "FI-Unelmalaina",
        "4" => "DK-Velkomstgaven"
    );
    return $omnisendList[$providerListId];
}

function getLiveRepostAweverProviderID($providerListId)
{
    $provider = array(
        "1" => "14",  // Velkomstgaven.com (Norway) 
        "2" => "16", // Gratispresent.se (Sweden)
        "3" => "13",  // Velkomstgaven.dk (Denmark)
        "4" => "4",  // Freecasinodeal.com/no  (Norway)
        "5" => "3",  // Freecasinodeal.com/fi (Finland) 
        "6" => "5",  // Freecasinodeal.com 
        "7" => "18",  // FI - Katariinasmail
        "8" => "19",  // NO - Signesmail
        "9" => "27",  // SE - Frejasmail
        "10" => "55",  // CA - Getspinn
        "11" => "56",  // NO - Getspinn
        "12" => "57",  // NZ - Getspinn
        "13" => "61",  // Freecasinodeal.com/nz  (New Zealand)
        "14" => "68",  // DK - Signesmail
        "15" => "71",  // DK - abbie
        "16" => "72",  // FI - abbie
        "17" => "73",  // NO - abbie
        "18" => "74",  // SE - abbie,
        "19" => "75",  // FreeCasinodeal/ca (Canada)
        "20" => "76",  // FelinaFinans/se
        "21" => "78", // New_gratispresent
        "22" => "79", // New_velkomstgaven_dk
        "23" => "80", // New_velkomstgaven_com
        "24" => "81", // New_velkomstgaven1_com
        "25" => "82", // New_unelmalaina
        "26" => "86", // Freecasinodeal/NZ/olivia
        "27" => "87", // Freecasinodeal/CA/sofia
        "28" => "88", // Freecasinodeal/NO/emma
        "29" => "89", // Freecasinodeal/FI/aida
        "30" => "90", // Frejasmail1/SE  
        "31" => "91", // Frejasmail2/SE   
        "32" => "92", // Signesmail1/DK   
        "33" => "93", // Katariinasmail1/FI   
        "34" => "94", // Signesmail1/NO   
        "35" => "95", // Signesmail2/NO  
        "36" => "96", // Abbiesmail1/CA   
        "37" => "97", // Abbiesmail2/CA  
        "38" => "98", // Ashleysmail/NZ   
        "39" => "99", // Ashleysmail1/NZ 
        "40" => "100", // Signesmail/DK
        "41" => "101", // Velkomstgaven/NO
        "42" => "102", // Velkomstgaven1/NO
        "43" => "103", // Gratispresent/SE
        "44" => "104", // Gratispresent1/SE
        "45" => "105", // FelinaFinans/SE
        "46" => "106", // FelinaFinans1/SE
        "47" => "107", // FelinaFinansmail/SE
        "48" => "108", // Unelmalaina/FI
        "49" => "109", // Unelmalaina1/FI
        "50" => "110", // Velkomstgaven/DK
        "51" => "111", // Velkomstgaven1/DK
        "52" => "112", // Getspinn1/CA
        "53" => "113", // Getspinnmail/CA
        "54" => "114", // Freecamail/CA
        "55" => "189", // Gratisprodukttest/SE
    );
    return $provider[$providerListId];
}

function getLiveRepostTransmitviaProviderListID($providerListId)
{
    $provider = array(
        "1" => "28",
        "2" => "29",
        "3" => "30",
        "4" => "31",
        "5" => "32",
        "6" => "33",
        "7" => "34",
        "8" => "35",
        "9" => "36",
        "10" => "37",
        "11" => "38",
        "12" => "39",
        "13" => "40",
        "14" => "41",
        "15" => "42",
        "16" => "43",
        "17" => "44",
        "18" => "45",
        "19" => "21",
        "20" => "24",
        "21" => "25",
        "22" => "26"
    );
    return $provider[$providerListId];
}

function getLiveRepostOngageProviderID($providerId)
{
    $provider = array(
        "1" => "47",  // Australia-camilla 
        "2" => "48",  // Australia - Kare 
        "3" => "49",  // Canada - Camilla 
        "4" => "50",  // Canada - Kare
        "5" => "51",  // Sweden - Camilla
        "6" => "52",  // Sweden - Kare
        "7" => "53",  // Norway - Camilla
        "8" => "54", // Norway - Kare 
        "9" => "58", // Finland  - Camilla 
        "10" => "59",  // Finland  - Kare 
        "11" => "62",  // New zealand  - Camilla 
        "12" => "63",  // New zealand  - Kare 
        "13" => "69",  // Denmark  - kare 
        "14" => "70",  // Denmark  - Camilla 
        "15" => "77",  // FI - Test
    );
    return $provider[$providerId];
}

function getLiveRepostSendgridProviderID($providerId)
{
    $provider = array(
        "1" => "60",  // CA-Abbiesmail
        "2" => "214",  // NZ-Ashleysmail
        "3" => "215",  // NZ-Allfreeca
        "4" => "216",  // CA-Allfreeca
        "5" => "217",  // Katariinasmail
        "6" => "218",  // Velkomstgaven/NO
        "7" => "219",  // Gratispresent/SE
        "8" => "220",  // Signesmail/NO
        "9" => "221",  // Dagenspresent/SE
    );
    return $provider[$providerId];
}

function getLiveRepostSendInBlueProviderID($providerId)
{
    $provider = array(
        "1" => "64",  // NO
        "2" => "65",  // CA
        "3" => "66",  // NZ
        "4" => "67"  // SE
    );
    return $provider[$providerId];
}

function getLiveRepostSendPulseProviderID($providerId)
{
    $provider = array(
        "1" => "83",  // NO-Sendpulse
        "2" => "84",  // CA-Sendpulse
        "3" => "85",  // SE-Sendpulse
    );
    return $provider[$providerId];
}

function getLiveRepostMailerliteProviderID($providerId)
{
    $provider = array(
        "1" => "115",  // DK-Velkomstgaven
        "2" => "116",  // NO-Velkomstgaven.com
        "3" => "117",  // NO-Velkomstgaven.com1
        "4" => "118",  // SE-Gratispresent
    );
    return $provider[$providerId];
}

function getLiveRepostMailjetProviderID($providerId)
{
    $provider = array(
        "1" => "119",  // Velkomstgaven/DK
        "2" => "120",  // Gratispresent/SE
        "3" => "127",  // Velkomstgaven/NOR
        "4" => "188",  // Freja/SE
        "5" => "190",  //Signesmail/DK
        "6" => "191",   //Signesmail2/NO
        "7" => "192",  //Produkttest/SE
        "8" => "210", //DagensPresent/SE
        "9" => "211", //VelkomstgavenVIP/DK
        "10" => "212" //VelkomstgavenVIP/NO
    );
    return $provider[$providerId];
}

function getLiveRepostConvertkitProviderID($providerId)
{
    $provider = array(
        "1" => "121",  // DK
        "2" => "122",  // SE
        "3" => "123",  // NO
        "4" => "124",  // FI
        "5" => "125",  // CA
        "6" => "126",  // NZ
        "7" => "128",  // NOR
        "8" => "129",  // SE
        "9" => "130",  // NOR
        "10" => "131", // FI
        "11" => "132"  // DK
    );
    return $provider[$providerId];
}

function getLiveRepostMarketingPlatformProviderID($providerId)
{
    $provider = array(
        "1" => "133",  // SE-Gratispresent
        "2" => "134",  // NO-Velkomstgaven
        "3" => "135",  // DK-Velkomstgaven
        "4" => "136",  // FI-Unelmalaina
        "5" => "137",  // FreeCasinoDeal-CA
        "6" => "138",  // FreeCasinoDeal-FI
        "7" => "139",  // FreeCasinoDeal-NO
        "8" => "140",  // FreeCasinoDeal-NZ   
        "9" => "148",  // NO-Velkomstgaven1     
    );
    return $provider[$providerId];
}

function getLiveRepostOntraportProviderID($providerId)
{
    $provider = array(
        "1" => "141",  // Gratispresentmail.se
        "2" => "142",  // Freecasinodeal1/no
        "3" => "143",  // Freecasinodeal1/fi
        "4" => "144",  // Velkomstgavenmail.dk
        "5" => "145",  // Freecasinodeal1/ca
        "6" => "146",  // Freecasinodeal1/nz
        "7" => "194",  // Velkomstgaven/DK
        "8" => "195",  // Velkomstgaven/com
        "9" => "196",  // Gratispresent/SE
        "10" => "197",  // Unelmalaina/FI
        "11" => "200",  // Velkomst/DK
        "12" => "201",  // Signe/DK
        "13" => "202",  // Dagens/SE
        "14" => "203",  // Felina/SE
        "15" => "204",  // Venla/FI
        "16" => "205",  // Katariina/FI
        "17" => "206",  // Allfree/CA
        "18" => "207",  // Abbie/CA
        "19" => "208",  // Ashley/NZ
        "20" => "209",  // Produkt/NO

    );
    return $provider[$providerId];
}

function getLiveRepostActiveCampaignProviderID($providerId)
{
    $provider = array(
        "1" => "147",  // Velkomstgaven/NOR
        "2" => "163",  // GratisPresent/SE
        "3" => "164",  // Frejasmail/SE
        "4" => "165",  // Unelmalaina/FI
        "5" => "166",  // Signesmail/NOR
        "6" => "167",  // Katariinasmail/FI
        "7" => "168",  // Velkomstgaven/DK
        "8" => "169",  // Signesmail/DK
        "9" => "193",   // Velkomstgaven/NO
        "10" => "198",   // gratisprodukttester.com/NO
        "11" => "199",   // dagenspresent.se/SE
        "12" => "213",   // gratispresent1.com
        "13" => "222",   // Velkomstgaven1/DK
        "14" => "223",   // signesmaildk1/DK
    );
    return $provider[$providerId];
}

function getLiveRepostExpertSenderProviderID($providerId)
{
    $provider = array(
        "1" => "149",  // camilla/abbiesmail2.com/CA
        "2" => "150",  // camilla/ashleysmail1.com/NZ
        "3" => "151",  // camilla/felinafinans.se/SE
        "4" => "152",  // camilla/frejasmail2.se/SE
        "5" => "153",  // camilla/katariinasmail1.com/FI
        "6" => "154",  // camilla/signesmail1.dk/DK
        "7" => "155",  // camilla/signesmail2.com/NO
        "8" => "170",  // Kaare/NO-FreeCasinodeal
        "9" => "171",  // Kaare/FI-FreeCasinodeal
        "10" => "172", // Kaare/CA-FreeCasinodeal
        "11" => "173", // Kaare/NZ-FreeCasinodeal
        "12" => "174", // Kaare/CA-GetSpinn
        "13" => "175", // Kaare/NZ-GetSpinn
        "14" => "176", // Kaare/NO-GetSpinn
        "15" => "177", // Kaare/gratispresentmail.se/SE
        "16" => "178", // Kaare/unelmalainamail.fi/Unelmalaina
        "17" => "179", // Kaare/Velkomstgaven-NO
        "18" => "180", // Kaare/DK-Velkomstgaven
    );
    return $provider[$providerId];
}

function getLiveRepostCleverReachProviderID($providerId)
{
    $provider = array(
        "1" => "156",  // Velkomstgaven/DK
        "2" => "157",  // Cathrinesmail/CA
        "3" => "158",  // Cathrinesmail/DK
        "4" => "159",  // Cathrinesmail/FI        
        "5" => "160",  // Cathrinesmail/NO
        "6" => "161",  // Cathrinesmail/NZ
        "7" => "162",  // Cathrinesmail/SE
        "8" => "185",  // Velkomstgaven/NO
        "9" => "186",  // Gratispresent/SE
        "10" => "187", // Unelmalaina/FI
    );
    return $provider[$providerId];
}

function getLiveRepostOmnisendProviderID($providerId)
{
    $provider = array(
        "1" => "181",  // SE-Gratispresent 
        "2" => "182",  // NO-Velkomstgaven
        "3" => "183",  // FI-Unelmalaina
        "4" => "184",  // DK-Velkomstgaven
    );
    return $provider[$providerId];
}
/*
  ++++++++++++++++++++++++++++++++++++++++++++++
  Mail send shortcut function.
  Pass parameters according described in functions
  parameters.
  ++++++++++++++++++++++++++++++++++++++++++++++
 */

function sendMail($toEmail, $subject = '', $mail_body, $from_email = '', $from_name = '', $file_path = '')
{
    $C = &get_instance();

    $from_email  = getConfigVal('fromEmailId');
    $from_name   = getConfigVal('fromEmailName');

    $C->load->library('email');
    $config['mailtype'] = 'html';
    $config['protocol'] = 'sendmail';
    $config['mailpath'] = '/usr/sbin/sendmail';
    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;

    $C->email->initialize($config);
    $C->email->from($from_email, $from_name);
    $C->email->to($toEmail);
    $C->email->subject($subject);
    $C->email->message($mail_body);
    //$C->email->send();
    if ($C->email->send()) {
        return 1;
    } else {
        return 0;
    }
    //echo $C->email->print_debugger();

}

/*
  ++++++++++++++++++++++++++++++++++++++++++++++
  Sending Mail from local
  Pass parameters according described in functions
  parameters.
  ++++++++++++++++++++++++++++++++++++++++++++++
 */

function sendLocalMail($emailId, $subject, $mail_body, $senderId = "", $rpl_to_email = '')
{
    if ($rpl_to_email == '')
        $rpl_to_email = getConfigVal("emailSender");
    if ($senderId == '')
        $senderId = getConfigVal("emailSender");

    $C = &get_instance();
    $emailData['smtpHost'] = getConfigVal("smtpHost");
    $emailData['smtpPort'] = getConfigVal("smtpPort");
    $emailData['smtpUname'] = getConfigVal("smtpUname");
    $emailData['smtpPass'] = getConfigVal("smtpPass");

    $C->load->helper('phpmailer');
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->IsHTML(true); // send as HTML	
    $mail->SMTPAuth = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "ssl";
    //    $mail->SMTPSecure = "tls";
    if (!empty($emailData)) {
        $mail->Host = $emailData['smtpHost'];
        $mail->Port = $emailData['smtpPort'];
        $mail->Username = $emailData['smtpUname'];
        $mail->Password = $emailData['smtpPass'];
    } else {
        $mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        //$mail->Port       = 465; 
        $mail->Port = 587;
        $mail->Username = "devobrijesh@gmail.com";
        $mail->Password = "devo@123";
    }
    $mail->AddReplyTo($rpl_to_email, "");
    $mail->SetFrom($senderId, '');
    $mail->Subject = $subject;
    $mail->Body = $mail_body;
    $mail->AltBody = "Plain text message";
    $emails = explode(",", $emailId);

    foreach ($emails as $email)
        $mail->AddAddress($email);
    if (!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else
        return true;
    #		echo 'message send successfuuly';
}

function cleanString($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function reformatPrice($num, $precision = 1)
{
    if ($num >= 1000 && $num < 1000000) {
        $n_format = number_format($num / 1000, $precision) . 'K';
    } else if ($num >= 1000000 && $num < 1000000000) {
        $n_format = number_format($num / 1000000, $precision) . 'M';
    } else if ($num >= 1000000000) {
        $n_format = number_format($num / 1000000000, $precision) . 'B';
    } else {
        $n_format = $num;
    }
    return $n_format;
}

function is_logged()
{
    $ci = &get_instance();
    if ($ci->session->userdata('adminId') > 0)
        return true;
    else
        return false;
}

function is_admin()
{
    $ci = &get_instance();
    if ($ci->session->userdata('role') == 0)
        return true;
    else
        return false;
}

function GetCurUserInfo()
{
    $ci = &get_instance();
    $curUserId = $ci->session->userdata('adminId');
    $ci->db->where('adminId', $curUserId);
    return $ci->db->get(ADMINMASTER)->row_array();
}

function getResizeImagePath($imagePath, $height = 125, $width = 125)
{
    if (strtolower($_SERVER['HTTP_HOST']) == 'localhost') {
        return $imagePath;
    } else {
        return site_url("imagePath?img=" . $imagePath . "&h=$height" . "w=$width");
    }
}

function getReceivedProjectAmount($projectId)
{
    $record = GetAllRecord(PROJECT_PAYMENT, array("projectId" => $projectId), "", "", "", array());
    $amount = 0;
    for ($i = 0; $i < count($record); $i++) {
        $amount += $record[$i]["payment"];
    }
    return $amount;
}

function displayErrorMsg($errorMsg = "")
{
    return '<div class="alert alert-danger alert-dismissible fade in" role="alert"><strong>Error</strong> ' . $errorMsg . '</div>';
}

function displaySucMsg($sucMsg = "")
{
    return '<div class="alert alert-success alert-dismissible fade in" role="alert"><strong>Success</strong> ' . $sucMsg . '</div>';
}

function checkLoginAccess()
{
    $ci = &get_instance();
    if ($ci->session->userdata('adminId') > 0)
        return true;
    else
        redirect("login");
}

function usMoneyFormat($value)
{
    return number_format($value, 2);
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getRandomIp()
{
    return mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);
}

function sendMessage($params)
{
    $mobile = $params["mobile"];
    $message = urlencode($params["message"]);
    $output = file_get_contents("http://login.arihantsms.com/vendorsms/pushsms.aspx?user=cipherhex&password=UBtNiZlY45z3Md00qe&msisdn=" . $mobile . "&sid=CIPHHX&msg=" . $message . "&fl=0&gwid=2");
}

//function InsertRecord($collectionName="",$insertData=array()){
//    $ci = & get_instance();
//    $insertObj  = $ci->mongo_db->insert($collectionName, $insertData);
//    return $insertObj->{'$id'};
//}
//
//function UpdateRecord($collectionName="",$updateData=array(),$conditionArr=array()){
//    $ci = & get_instance();
//    $ci->mongo_db->where($conditionArr);    
//    return $ci->mongo_db->update($collectionName, $updateData);
//}
//
//function GenerateMongoObj($id =""){
//    return new MongoId($id);
//}


/*

    @deleteData
    -> delete table data

*/

function deleteData($table_name = '', $condition = array())
{
    $ci = &get_instance();
    $ci->db->where($condition);
    $ci->db->delete($table_name);
    return 1;
}

/*

    @imageUnlink
    -> unlink means delete image on edit and delete
*/

function imageUnlink($imagePath = '')
{

    if (is_readable($imagePath) && unlink($imagePath)) {
        echo 1;
    } else {
        echo 0;
    }
}

/*
    -> isValidEmail
    -> emailid format valid or not
    
*/
function isValidEmail($emailId = '')
{
    if (filter_var($emailId, FILTER_VALIDATE_EMAIL)) {

        return 1; //Yes
    } else {
        return 0; //No
    }
}

/*
    -> isValidDeliverableEmail
    -> emailid valid for delivery or not
    
*/
function isValidDeliverableEmail($emailId)
{
    try {
        $apikey = "8fafe17031cd31997be8835f2e3264741112461af4dc494d6f3da0980636b13c";
        $data = array(
            'email' => $emailId,
            'api_key' => $apikey
        );

        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        $body = $client->get("https://api.thechecker.co/v2/verify", [
            'query' => $data
        ]);

        $responseBody = json_decode($body->getBody(true), true);
        if ($responseBody['result'] == "deliverable") {
            return 1;
        } else {
            return 0;
        }
    } catch (\Throwable $th) {
        return -1;
    }
}

/*
===========
    -> @createOtp
    -> start code create otp here
    -> create random number length = 6
*/

function createOtp()
{
    $numbers = "0123456789";
    $charactersLength = strlen($numbers);
    $otpNumber = '';
    for ($i = 0; $i < 6; $i++) {
        $otpNumber .= $numbers[rand(0, $charactersLength - 1)];
    }
    return $otpNumber;
}


/*
    ====== common function here =======
    @sendOtpMail
    -> start code send otp mail
*/
function sendMailOtp($emailId, $fullName, $otpNumber)
{

    $condition = array();

    $toEmail    = $emailId;
    $subject    = "OTP Verify";
    $mail_body  = "";
    $mail_body  .= "Dear " . $fullName . ",<br /><br />";
    $mail_body  .= "Our OTP for the registration for Go Badger Management is : " . $otpNumber . ". It will be expired in 10 minutes.";
    $mail_body  .= "<br/>Thank you,";
    $mail_body  .= "<br/>Go Badger Team";
    $mailResponse = sendMail($toEmail, $subject, $mail_body);   // send mail here

    if ($mailResponse == 0) {

        $response['err'] = 0;
        $response['otp'] = $otpNumber;
        $response['msg'] = "OTP has been sent successfully to your email id";
    } else {

        $response['err'] = 2;
        $response['msg'] = "Failed to send OTP to your email id";
    }
    return $response;
}
/* -> end code send otp mail */


/*
====== common function here =======
    @sendMailForgetPassword
    -> start code send otp mail
*/
function sendMailForgetPassword($emailId, $fullName)
{

    $generatePassword = generateRandomString(12);

    $dataArr = array(
        'password'     => md5($generatePassword),
    );

    $toEmail    = $emailId;
    $subject    = "Forget Password";
    $mail_body  = "";
    $mail_body  .= "Dear " . $fullName . ",<br /><br />";
    $mail_body  .= "Your new auto-generated password is " . $generatePassword . ". Please login with it and change the password again of your choice for security reason.<br/><br/>";
    $mail_body  .= "Thank you,<br/><br/>";
    $mail_body  .= "Go Badger.<br/><br/>";

    $mailResponse = sendMail($toEmail, $subject, $mail_body);   // send mail here

    if ($mailResponse == 0) {

        $response['err'] = 0;
        $response['password'] = $generatePassword;
        $response['msg'] = "Mail has been sent to your registered Email ID. Please find your login details there.";
    } else {

        $response['err'] = 1;
        $response['msg'] = "Problem occured. Please try again later.";
    }
    return $response;
}
/* -> end code send otp mail */
/*
    @sendNotification
    -> send notification user trash bin collect request (start code here)
*/
function sendNotification($fcmToken = "", $title = '', $body = '', $status = '')
{

    // store variable fcmToken id
    $registrationIds = $fcmToken;

    // notification code start here
    //$icon  = base_url()."images/splash_screen_logo.png";
    $fields = array(
        'to'   => $registrationIds,
        'notification' =>  array(
            'title'    => $title,
            'body'     => $body,
            'status'   => $status,
            //'icon'   => 'myicon', 
            //'image'  => $icon,
            'sound'    => "default"
        ),
        'data' => array(
            'title'    => $title,
            'body'     => $body,
            'status'   => $status,
            //'icon'   => 'myicon', 
            //'image'  => $icon,
            'sound'  => "default"
        )
    );


    //echo json_encode($fields);die;
    $headers = array('Authorization: key=' . API_ACCESS_KEY, 'Content-Type: application/json');
    #Send Reponse To FireBase Server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === false) {
        /*die('Curl failed:' .curl_errno($ch));
            echo $result;*/
        return 0;
    } else {
        //return $result;
        return 1;
        //$resultData = json_decode($result, TRUE);
    }
    //echo  $result ."<br>";
    curl_close($ch);
}


function getCountry()
{
    $condition = array();
    $is_single = FALSE;
    $countries = GetAllRecord(COUNTRY_MASTER, $condition, $is_single);

    if (count($countries) > 0) {
        return $countries;
    } else {
        return array();
    }
}

function getCountryCode($country)
{
    $countriesWithCode = array('DK' => '+45', 'SE' => '+46', 'NOR' => '+47', 'FI' => '+358', 'UK' => '+44', 'AU' => '+43', 'DE' => '+49', 'CA' => '+1', 'NL' => '+31', 'NZ' => '+64');
    return $countriesWithCode[$country];
}

function getAllCountryCode()
{
    $countryCode = array('DK' => '45', 'SE' => '46', 'NOR' => '47', 'FI' => '358', 'UK' => '44', 'AU' => '43', 'DE' => '49', 'CA' => '1', 'NL' => '31', 'NZ' => '64');
    return $countryCode;
}

function getAllSmsApiProvider()
{
    $sms_providers = array('forty_two' => 'Fourty Two', 'cp_sms' => 'CP SMS', 'warriors_sms' => 'Warriors SMS'/*, 'sms_edge' => 'SMS Edge', 'mmd_smart' => 'MMD Smart', 'in_mobile' => 'In Mobile', 'sinch'=>'Sinch'*/);
    return $sms_providers;
}


function encrypt($pure_string)
{

    return bin2hex(openssl_encrypt($pure_string, 'AES-128-CBC', ENCRYPT_KEY));
}

/**

 * Returns decrypted original string

 */

function decrypt($encrypted_string)
{

    return openssl_decrypt(hex2bin($encrypted_string), 'AES-128-CBC', ENCRYPT_KEY);
}


//get datetime diff in hours
function getDateTimeDiffInHours($startDate, $endDate)
{
    return round((strtotime($endDate) - strtotime($startDate)) / 3600, 1);
}


function countryThasListedInEgoi()
{
    return array('DK', 'NO', 'SE');
}

function countryThasListedInAweber()
{
    return array('DK', 'NOR', 'SE', 'FI', 'UK', 'NL', 'CA', 'NZ');
}

function getListIdForAllAweber($aweberListId, $country)
{
    if ($country != '') {

        $aweberAllListArr = array(
            '1' => array('DK' => 5237847, 'SE' => 5217417, 'FI' => 5237852, 'NOR' => 5217426, 'UK' => '', 'DE' => '', 'CA' => 5221106, 'NL' => 5514395),
            '2' => array('DK' => 5297593, 'SE' => 5297594, 'FI' => 5297596, 'NOR' => 5297595, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '3' => array('DK' => '', 'SE' => '', 'FI' => '', 'NOR' => 5327219, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '4' => array('DK' => '', 'SE' => '', 'FI' => 5327235, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '5' => array('DK' => 5353599, 'SE' => '', 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '6' => array('DK' => '', 'SE' => '', 'FI' => '', 'NOR' => 5384430, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '7' => array('DK' => '', 'SE' => '', 'FI' => 5384432, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '8' => array('DK' => 5518965, 'SE' => '', 'FI' => '', 'NOR' => 5518966, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '9' => array('DK' => '', 'SE' => '', 'FI' => '', 'NOR' => 5562033, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '10' => array('DK' => '', 'SE' => 5518967, 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '11' => array('DK' => '', 'SE' => '', 'FI' => 5327235, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '12' => array('DK' => '', 'SE' => '', 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => '')
        );

        return $aweberAllListArr[$aweberListId][strtoupper($country)];
    } else {
        return '';
    }
}

function reformat_number_format($percentage = 0)
{

    $percentage = number_format((float)$percentage, 8, '.', '');
    return floatval($percentage);
}


function get_timezone_wise_difference($country = 'DK', $set_date = '')
{

    $country_wise_time_zone = array('DK' => 'Europe/Copenhagen', 'SE' => 'Europe/Stockholm', 'NOR' => 'Europe/Oslo', 'FI' => 'Europe/Helsinki', 'UK' => 'Europe/London', 'DE' => 'Europe/Berlin', 'AU' => 'Australia/Canberra', 'NL' => 'Europe/Amsterdam', 'CA' => 'America/Regina');

    $set_time_zone_region = $country_wise_time_zone[$country];
    date_default_timezone_set($set_time_zone_region);

    $diff = strtotime($set_date) - time();
    return $diff;
}

function get_current_time_of_country($country)
{

    $country_wise_time_zone = array('DK' => 'Europe/Copenhagen', 'SE' => 'Europe/Stockholm', 'NOR' => 'Europe/Oslo', 'FI' => 'Europe/Helsinki', 'UK' => 'Europe/London', 'DE' => 'Europe/Berlin', 'AU' => 'Australia/Canberra', 'NL' => 'Europe/Amsterdam', 'CA' => 'America/Regina');

    $set_time_zone_region = $country_wise_time_zone[$country];
    date_default_timezone_set($set_time_zone_region);

    $currentTime = time();
    return $currentTime;
}

function addToAweberSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(AWEBER_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToTransmitviaSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(TRANSMITVIA_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToContactSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(CONTACT_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToOngageSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(ONGAGE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToSendgridSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(SENDGRID_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToSendinblueSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(SENDINBLUE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToSendpulseSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(SENDPULSE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToMailerliteSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(MAILERLITE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToMailjetSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(MAILJET_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToConvertkitSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(CONVERTKIT_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToMarketingPlatformSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(MARKETING_PLATFORM_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToOntraportSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(ONTRAPORT_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToActiveCampaignSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(ACTIVE_CAMPAIGN_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToExpertSenderSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(EXPERT_SENDER_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToCleverReachSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(CLEVER_REACH_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToOmnisendSubscriberQueue($liveDeliveryDataId, $mailProvider, $delayDay)
{
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => ($delayDay == 0) ? time() : strtotime('+' . $delayDay . ' day', strtotime('9am')),
        "deliveryDate" => ($delayDay == 0) ? date('Y-m-d') : date("Y-m-d", strtotime('+' . $delayDay . ' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(OMNISEND_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addRecordInHistory($lastDeliveryData, $mailProvider, $provider, $response, $groupName, $keyword, $emailId = NULL)
{
    $historyData = array(
        'liveDeliveryDataId' => $lastDeliveryData['liveDeliveryDataId'],
        'providerId' => $mailProvider,
        'provider' => $provider,
        'emailId' => (isset($lastDeliveryData['emailId']) && $lastDeliveryData['emailId'] != "") ? $lastDeliveryData['emailId'] : $emailId,
        'groupName' => $groupName,
        'keyword' => $keyword,
        'updateDate' => date("Y-m-d"),
        'updateDateTime' => date("Y-m-d H:i:s"),
        'response' => json_encode($response)
    );
    if ($response != null) {
        if ($response['result'] == "success") {
            $historyData['status'] = 1; // success
        } else {
            $historyData['status'] = 2; // error - already subscribe + other error
        }
    } else {
        $historyData['status'] = 0; // pending
    }

    $condition = array();
    $is_insert = true;
    ManageData(EMAIL_HISTORY_DATA, $condition, $historyData, $is_insert);
}

function addRecordInHistoryFromCSV($lastDeliveryData, $mailProvider, $provider, $response, $groupName, $keyword, $emailId = NULL)
{
    $historyData = array(
        'userId' => isset($lastDeliveryData['userId']) ? $lastDeliveryData['userId'] : "-",
        'providerId' => $mailProvider,
        'provider' => $provider,
        'emailId' => (isset($lastDeliveryData['emailId']) && $lastDeliveryData['emailId'] != "") ? $lastDeliveryData['emailId'] : $emailId,
        'groupName' => $groupName,
        'keyword' => $keyword,
        'updateDate' => date("Y-m-d"),
        'updateDateTime' => date("Y-m-d H:i:s"),
        'response' => json_encode($response)
    );
    if ($response != null) {
        if ($response['result'] == "success") {
            $historyData['status'] = 1; // success
        } else {
            $historyData['status'] = 2; // error - already subscribe + other error
        }
    } else {
        $historyData['status'] = 0; // pending
    }

    $condition = array();
    $is_insert = true;
    ManageData(EMAIL_HISTORY_DATA, $condition, $historyData, $is_insert);
}

function getProviderIdUsingTransmitviaList($code)
{
    $condition = array(
        "code" => $code
    );
    $is_single = TRUE;
    $provider = GetAllRecord(PROVIDERS, $condition, $is_single);
    return $provider['id'];
}

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

function getSubscribeDetails($listId, $email)
{
    $condition = array(
        'providerId' => $listId,
        'emailId' => $email,
        'status' => '1'
    );
    $is_single = TRUE;
    $getEmailDetail = GetAllRecord(EMAIL_HISTORY_DATA, $condition, $is_single, array(), array(), array(), 'emailId,response');
    $emailResponse = json_decode($getEmailDetail['response'], true);

    return $emailResponse;
}

function getProviderListCode($providerId)
{
    $CI = &get_instance();
    $condition = array(
        'id' => $providerId
    );
    $is_single = TRUE;
    $getProviderListCode = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array(), 'code');
    return $getProviderListCode['code'];
}

function getProviderID($account, $listId, $provider)
{
    $CI = &get_instance();
    $condition = array(
        'aweber_account' => $account,
        'code' => $listId,
        'provider' => $provider
    );
    $is_single = TRUE;
    $getProviderID = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array(), 'id');
    return $getProviderID['id'];
}

function getLivedeliveryDetail($email, $responseField)
{
    $CI = &get_instance();
    $getDetail = $CI->db->select('emailId,country,' . $responseField)
        ->from(LIVE_DELIVERY_DATA)
        ->where('emailId', $email)
        ->like($responseField, 'success')
        ->get()->row_array();

    return $getDetail;
}

function getAlreadySubscribeLivedeliveryDetail($email, $responseField)
{
    $CI = &get_instance();
    $getDetail = $CI->db->select('emailId,country,' . $responseField)
        ->from(LIVE_DELIVERY_DATA)
        ->where('emailId', $email)
        ->like($responseField, 'Subscriber already subscribed')
        ->get()->row_array();

    return $getDetail;
}

function getProviderDetail($mainProviderId)
{
    $CI = &get_instance();
    $condition = array(
        'id' => $mainProviderId
    );
    $is_single = TRUE;
    $getProvider = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array());
    return $getProvider;
}

function getCsvUserResponseField($emailServiceProvider)
{
    $responseField = array(
        '1' => 'aweberResponse',
        '2' => 'transmitviaResponse',
        '4' => 'ongageResponse',
        '5' => 'sendgridResponse',
        '6' => 'sendinblueResponse',
        '7' => 'sendpulseResponse',
        '8' => 'mailerliteResponse',
        '9' => 'mailjetResponse',
        '10' => 'convertkitResponse',
        '11' => 'marketingPlatformResponse',
        '12' => 'ontraportResponse',
        '13' => 'activeCampaignResponse',
        '14' => 'expertSenderResponse',
        '15' => 'cleverReachResponse',
        '16' => 'omniSendResponse'
    );
    return $responseField[$emailServiceProvider];
}

function getCsvUserDetail($userIds, $listId, $responseField)
{
    $CI = &get_instance();
    $getDetail = [];
    if (!empty($userIds)) {
        $getDetail =  $CI->db->select('csv_cron_user_data.*,csv_file_provider_data.providerName,csv_file_provider_data.providerList,csv_providers_detail.originalProvider')
            ->from(CSV_CRON_USER_DATA)
            ->join(CSV_FILE_PROVIDER_DATA, 'csv_cron_user_data.providerId = csv_file_provider_data.id', 'left')
            ->join(CSV_PROVIDERS_DETAIL, 'csv_file_provider_data.providerName = csv_providers_detail.providerName AND csv_file_provider_data.providerList = csv_providers_detail.providerList', 'left')
            ->where('csv_cron_user_data.userId IN (' . $userIds . ')')
            ->where('csv_cron_user_data.status', 1)
            ->where('csv_providers_detail.originalProvider', $listId)
            ->like($responseField, 'success')
            ->get()->row_array();
    }
    return $getDetail;
}

function getAlreadySubscribeCsvUserDetail($userIds, $listId, $responseField)
{
    $CI = &get_instance();
    $getDetail = [];
    if (!empty($userIds)) {
        $getDetail =  $CI->db->select('csv_cron_user_data.*,csv_file_provider_data.providerName,csv_file_provider_data.providerList,csv_providers_detail.originalProvider')
            ->from(CSV_CRON_USER_DATA)
            ->join(CSV_FILE_PROVIDER_DATA, 'csv_cron_user_data.providerId = csv_file_provider_data.id', 'left')
            ->join(CSV_PROVIDERS_DETAIL, 'csv_file_provider_data.providerName = csv_providers_detail.providerName AND csv_file_provider_data.providerList = csv_providers_detail.providerList', 'left')
            ->where('csv_cron_user_data.userId IN (' . $userIds . ')')
            ->where('csv_cron_user_data.status', 1)
            ->where('csv_providers_detail.originalProvider', $listId)
            ->like($responseField, 'Subscriber already subscribed')
            ->get()->row_array();
    }
    return $getDetail;
}

function checkAccountStatus($mailProvider)
{
    $providerCondition   = array('id' => $mailProvider);
    $is_single           = true;
    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
    $provider     = $providerData['provider'];
    $accountId    = $providerData['aweber_account'];
    $accountTable = getAccountTableName($provider);

    $condition   = array('id' => $accountId);
    $is_single   = true;
    $accountData = GetAllRecord($accountTable, $condition, $is_single);
    return $accountData;
}

//delay account table name
function getDelayAccountTableName($provider)
{
    $tableNames = array(
        '1' => 'aweber_delay_user_data',
        '4' => 'ongage_delay_user_data',
        '5' => 'sendgrid_delay_user_data',
        '7' => 'sendpulse_delay_user_data',
        '8' => 'mailerlite_delay_user_data',
        '9' => 'mailjet_delay_user_data',
        '10' => 'convertkit_delay_user_data',
        '11' => 'marketing_platform_delay_user_data',
        '12' => 'ontraport_delay_user_data',
        '13' => 'active_campaign_delay_user_data',
        '14' => 'expert_sender_delay_user_data',
        '15' => 'clever_reach_delay_user_data',
        '16' => 'omnisend_delay_user_data'
    );
    if (array_key_exists($provider, $tableNames)) {
        return $tableNames[$provider];
    }
}

// get condition: dashboard stats by jalpa
function getCondition($chooseFilter = "td", $month = null, $year = null)
{

    if ($chooseFilter == 'td') {

        //get td = today's clicks and registrations
        $today = date('Y-m-d');
        $startDate = $today;
        $endDate = $today;
    } elseif ($chooseFilter == 'yd') {

        //get yd = yester's records
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $startDate = $yesterday;
        $endDate = $yesterday;
    } elseif ($chooseFilter == 'lSvnD') {

        //get lSvnD = last seven day's records
        $lastSevenDay   = date('Y-m-d', strtotime("-7 days"));
        $today          = date('Y-m-d');

        $startDate = $lastSevenDay;
        $endDate = $today;
    } elseif ($chooseFilter == 'lThrtyD') {

        //get lThrtyD = current month records
        $lastThirtyDay  = date('Y-m-01');
        $today          = date('Y-m-d');

        $startDate = $lastThirtyDay;
        $endDate = $today;
    } elseif ($chooseFilter == 'dM') {
        //get dM = daynamic month records end of january
        if ($month != null && $year != null) {
            $timestamp    = strtotime($month . " " . $year);
            $startDate = date('Y-m-01', $timestamp);
            $endDate  = date('Y-m-t', strtotime($startDate));
        }
    }

    $condition = array(
        'startDate' => $startDate . ' ' . '00:00:00',
        'endDate'   => $endDate . ' ' . '23:59:59'
    );

    return $condition;
}

// get all esp response field from provider - DAB
function getAllResponseFieldName()
{
    $CI = &get_instance();
    $getProvideDetail = $CI->db->select('response_field')
        ->from('providers')
        ->where_in('provider', [9, 12, 13, 14, 15, 16])
        ->get()->result_array();
    $responseFields = array_column($getProvideDetail, 'response_field');
    return $responseFields;
}

// get table name according to esp - DAB
function getAccountTable($esp)
{
    $table = "";
    if ($esp == 9) {
        $table = 'mailjet_accounts';
    } else if ($esp == 12) {
        $table = 'ontraport_accounts';
    } else if ($esp == 13) {
        $table = 'active_campaign_accounts';
    } else if ($esp == 14) {
        $table = 'expert_sender_accounts';
    } else if ($esp == 15) {
        $table = 'clever_reach_accounts';
    } else if ($esp == 16) {
        $table = 'omnisend_accounts';
    } else if ($esp == 5) {
        $table = 'sendgrid_accounts';
    }
    return $table;
}

// get IP - DAB
function getIPAddress()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function getHeadingName($esp)
{
    $heading = '';
    if ($esp == 9) {
        $heading = 'Mailjet';
    } else if ($esp == 12) {
        $heading = 'Ontraport';
    } else if ($esp == 13) {
        $heading = 'Active Campaign';
    } else if ($esp == 14) {
        $heading = 'Expert Sender';
    } else if ($esp == 15) {
        $heading = 'Clever Reach';
    } else if ($esp == 16) {
        $heading = 'Omnisend';
    } else if ($esp == 9) {
        $heading = 'Sendgrid';
    }
    return $heading;
}

function getAccountStatusLog($esp, $accountId)
{
    $condition = array(
        'account_status_log.esp' => $esp,
        'account_status_log.account_id' => $accountId
    );
    $is_single = false;
    $dataCount = GetAllRecordCount(ACCOUNT_STATUS_LOG, $condition, $is_single);
    return $dataCount;
}

function sendLeadInIntegromat($lastDeliveryData, $getLiveDeliveryData)
{
    try {
        if ($getLiveDeliveryData['integromatHookId'] != 0) {
            $hookData = GetAllRecord(INTEGROMAT_HOOKS, array('id' => $getLiveDeliveryData['integromatHookId']), true);

            $lastDeliveryData['birthDate'] = "";
            if (@$lastDeliveryData['birthdateDay'] != '0' && @$lastDeliveryData['birthdateMonth'] != '0' && @$lastDeliveryData['birthdateYear'] != '0') {
                $birthDate            = $lastDeliveryData['birthdateYear'] . '-' . $lastDeliveryData['birthdateMonth'] . '-' . $lastDeliveryData['birthdateDay'];
                $lastDeliveryData['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }


            $integromatUserData = [
                'firstname' => $lastDeliveryData['firstName'],
                'email' => $lastDeliveryData['emailId'],
                'gender' => $lastDeliveryData['gender'],
                'birthdate' => $lastDeliveryData['birthDate'],
                'country' => $lastDeliveryData['country'],
                'phone' => $lastDeliveryData['phone'],
                'countryCode' => getCountryCode($lastDeliveryData['country']),
                'timestamp'  => strtotime(date('Y-m-d H:i:s'))
            ];
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();
            $subscriberUrl = $hookData['hook_url'];
            $body = $client->post($subscriberUrl, [
                'form_params' => $integromatUserData,
            ]);
            $response =  $body->getBody();

            $is_insert = true;
            $live_delivery_integromat_data = array(
                'liveDeliveryId' => $getLiveDeliveryData['liveDeliveryId'],
                'liveDeliveryDataId' => $lastDeliveryData['liveDeliveryDataId'],
                'integromatHookId' => $getLiveDeliveryData['integromatHookId'],
                'response' => $response,
                'created_at' => date('Y-m-d H:i')
            );
            ManageData(LIVE_DELIVERY_INTEGROMAT_DATA, [], $live_delivery_integromat_data, $is_insert);
        }
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        return json_encode(array("result" => "error", "error" => array("msg" => "Bad Request")));
    }
}
