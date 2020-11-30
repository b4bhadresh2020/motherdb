<?php

/*
    @counts
    Need this function only in PHP >= 7.2.
*/
function counts($stuff){

    if ($stuff == '' || !is_array($stuff)) {
        $stuff = array();
    }

    return count($stuff);
}

#fetch all records to display with filters

function GetAllRecordsTest($table_name, $condition, $is_single, $selected_rows=''){
    $ci = & get_instance();
    

    if ($condition){
        $ci->db->where($condition);
    }
    if($selected_rows) {
        $ci->db->select($selected_rows);
    }

    $res = $ci->db->get($table_name);
    
    if ($is_single){
        return $res->row_array();
    } else{
        return $res->result_array();
    }
}

function GetAllRecord($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '',$startNo = '') {
    $ci = & get_instance();
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
    if($selected_rows != "") {
        $ci->db->select($selected_rows);
    }

    if($startNo != ""){
        $limit = getConfigVal('servicePaginationLimit');
        $start = ($startNo - 1)*$limit;
        $ci->db->limit($limit,$start);
    }
    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetRecordWithLimit($table_name = '', $condition = array(), $join_table = '', $table_id = '', $join_id = '', $type = '',$is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '',$startNo = '',$limit = '') {
    $ci = & get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);

    if ($join_table != '')
        $ci->db->join($join_table, "$table_name.$table_id = $join_table.$join_id",$type);

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
    if($selected_rows != "") {
        $ci->db->select($selected_rows);
    }

    if($startNo != "" && $limit != ""){       
        $start = ($startNo - 1)*$limit;
        $ci->db->limit($limit,$start);
    }

    if($limit != "") {
        $ci->db->limit($limit,0);
    }

    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetAllRecordCount($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = '') {
    $ci = & get_instance();
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
    if($selected_rows != "") {
        $ci->db->select($selected_rows);
    }
    $res = $ci->db->count_all_results($table_name);
    return $res;
}

function GetAllRecordIn($table_name = '', $condition = array(), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $is_in = array(), $selected_rows = '') {
    $ci = & get_instance();
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
    if($selected_rows != "") {
        $ci->db->select($selected_rows);
    }
    $res = $ci->db->get($table_name);
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

function GetDatabyqry($sql) {
    $ci = & get_instance();
    $res = $ci->db->query($sql);
    return $res->result_array();
}

#insert update query with filter and flag

function ManageData($table_name = '', $condition = array(), $udata = array(), $is_insert = false) {
    $resultArr = array();
    $ci = & get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if ($is_insert) {
        $ci->db->insert($table_name, $udata);
        $insertid = $ci->db->insert_id();
        return $insertid;
        #return 0;
    } else {
        if($ci->db->update($table_name, $udata)){
            return 1;
        }else{
            return 0;
        }
        
    }
}

#insert update query with filter and flag

function incrementReportData($table_name = '', $condition = array(), $value = 0, $status = 'inc') {
    $resultArr = array();
    $ci = & get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if(@$status && $status == "inc")
        $ci->db->set('reportVal', 'reportVal+'.$value, FALSE);
    if(@$status && $status == "dec")
        $ci->db->set('reportVal', 'reportVal-'.$value, FALSE);
    $ci->db->update($table_name);
    return 0;
}

function incrementData($table_name = '', $condition = array(), $fields = "", $value = 0, $status = 'inc') {
    $resultArr = array();
    $ci = & get_instance();
    if ($condition && count($condition))
        $ci->db->where($condition);
    if(@$status && $status == "inc")
        $ci->db->set($fields, ''.$fields.'+'.$value.'', FALSE);
    if(@$status && $status == "dec")
        $ci->db->set($fields, ''.$fields.'-'.$value.'', FALSE);
    $ci->db->update($table_name);
    return 0;
}

#joinTable

function JoinData($table_name = '', $condition = array(), $join_table = '', $table_id = '', $join_id = '', $type = '',$is_single = false,$order_by = array(),$selected_rows = '',$limit = '') {
    $ci = & get_instance();
    #$ci->db->select('first_name,last_name');
    
    if ($condition && count($condition))
        $ci->db->where($condition);
    $ci->db->from($table_name);
    
    if ($join_table)
        $ci->db->join($join_table, "$table_name.$table_id = $join_table.$join_id",$type);

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
    if($selected_rows)
        $ci->db->select($selected_rows);

    if($limit != ""){
        $ci->db->limit($limit);
    }
    
    $res = $ci->db->get();
    if ($is_single)
        return $res->row_array();
    else
        return $res->result_array();
}

#Creating Pagination Link

function pagiationData($str, $num, $start, $segment, $perpage = 20, $isExpload = FALSE) {

    $CI = & get_instance();
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
    }else{
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

    }else{

        $data['pageinfo'] = "No Records";
    }

    return $data;
}



function pagination_data($str, $total_rows, $start, $segment, $perpage = 20,$result_data = array()) {

    $CI = & get_instance();
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

    }else{

        $data['pageinfo'] = "No Records";
    }

    return $data;
}



function GetFormError() { //return single error message after form validation
    $CI = & get_instance();
    $errorarr = $CI->form_validation->error_array();
    if (count($errorarr) === 0) {
        return FALSE;
    } else {
        foreach ($errorarr as $key => $val) {
            return $val;
        }
    }
}

function pre($str) { //Print prev screen for array
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}

function last_query() { //print last executed query
    $CI = & get_instance();
    pre($CI->db->last_query());
}

function ValidImageExt() {
    $dropdown = array('gif' => 'gif',
        'jpg' => 'jpg',
        'jpeg' => 'jpeg',
        'png' => 'png',
        'bmp' => 'bmp',
    );
    return $dropdown;
}

function uploadFile($uploadFile, $filetype, $folder, $fileName = '') {
    $CI = & get_instance();
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
    if(!is_dir($config['upload_path']))
            mkdir($config['upload_path'],'0777');

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

function uploadMultiFiles($fieldName, $folder, $options = array()) {
    $CI = & get_instance();
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

function SetMsg($var, $msg) {
    $ci = & get_instance();
    $ci->session->set_flashdata($var, $msg);
}

function GetMsg($var) {
    $ci = & get_instance();
    return $ci->session->flashdata($var);
}

function getConfigVal($keyParam) {
    $ci = & get_instance();
    $sql = "select configVal from ".SITECONFIG." where configKey='$keyParam'";
    $configVal = $ci->db->query($sql)->row_array($sql);
    return isset($configVal['configVal']) ? $configVal['configVal'] : "";
}

function getReportVal($keyParam) {
    $ci = & get_instance();
    $sql = "select reportVal from ".PAYMENT_REPORT." where reportKey='$keyParam'";
    $reportVal = $ci->db->query($sql)->row_array($sql);
    return isset($reportVal['reportVal']) ? $reportVal['reportVal'] : "";
}

function loginRegSectionMsg($msgId = "") {
    $msgArr = array(
        "insertData" => "Data Inserted Successfully.",        
        "updateData" => "Data Updated Successfully.",                
    );
    if ($msgId !== "")
        return $msgArr[$msgId];
    else
        return $msgArr;
}

function getProviderList($provider) {
    $condition = array(
        'provider' => $provider
    );
    $is_single = FALSE;
    $providerList = GetAllRecord(PROVIDERS,$condition,$is_single);

    if (count($providerList) > 0) {
        return $providerList;
    }else{
        return array();
    }
}

function getProviderName($providerId){
    $providerNames = array(
        '1' => 'Aweber',
        '2' => 'Transmitvia',
        '4' => 'Ongage',
        '5' => 'Sendgrid',
        '6' => 'Sendinblue'
    );
    return $providerNames[$providerId];
}

function getAweverProviderListName($providerListId){
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
    );
    return $aweberList[$providerListId];
}

function getTransmitviaProviderListName($providerListId){
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

function getOngageProviderListName($providerListId){
    $ongageList = array(
        "1" => "Australia-camilla",
        "2" => "Australia - Kare",
        "3" => "Canada - Camilla",
        "4" => "Canada - Kare",
        "5" => "Sweden - Camilla",
        "6" => "Sweden - Kare",
        "7" => "Norway - Camilla",
        "8" => "Norway - Kare",
        "9" => "Finland  - Camilla",
        "10" => "Finland  - Kare",
        "11" => "New Zealand  - Camilla",
        "12" => "New Zealand  - Kare",
        "13" => "Denmark  - Kare",
        "14" => "Denmark  - Camilla",
    );
    return $ongageList[$providerListId];
}

function getSendgridProviderListName($providerListId){
    $sendgridList = array(
        "1" => "CA",
    );
    return $sendgridList[$providerListId];
}

function getSendInBlueProviderListName($providerListId){
    $sendInBlueList = array(
        "1" => "NO",
        "2" => "CA",
        "3" => "NZ",
        "4" => "SE"
    );
    return $sendInBlueList[$providerListId];
}

function getLiveRepostAweverProviderID($providerListId){
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
    );
    return $provider[$providerListId];
}

function getLiveRepostTransmitviaProviderListID($providerListId){
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

function getLiveRepostOngageProviderID($providerId){
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
        "14" => "70"  // Denmark  - Camilla 
    );
    return $provider[$providerId];
}

function getLiveRepostSendgridProviderID($providerId){
    $provider = array(
        "1" => "60",  // CA
    );
    return $provider[$providerId];
}

function getLiveRepostSendInBlueProviderID($providerId){
    $provider = array(
        "1" => "64",  // NO
        "2" => "65",  // CA
        "3" => "66",  // NZ
        "4" => "67"  // SE
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

function sendMail($toEmail, $subject='', $mail_body, $from_email = '', $from_name = '', $file_path = '') {
    $C = & get_instance();
    
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
    if($C->email->send()){
        return 1;
    }else{
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

function sendLocalMail($emailId, $subject, $mail_body, $senderId = "", $rpl_to_email = '') {
    if ($rpl_to_email == '')
        $rpl_to_email = getConfigVal("emailSender");
    if ($senderId == '')
        $senderId = getConfigVal("emailSender");

    $C = & get_instance();
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

function cleanString($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function reformatPrice($num, $precision = 1) {
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

function is_logged() {
    $ci = & get_instance();
    if ($ci->session->userdata('adminId') > 0)
        return true;
    else
        return false;
}

function GetCurUserInfo() {
    $ci = & get_instance();
    $curUserId = $ci->session->userdata('adminId');
    $ci->db->where('adminId', $curUserId);
    return $ci->db->get(ADMINMASTER)->row_array();
}

function getResizeImagePath($imagePath, $height = 125, $width = 125) {
    if (strtolower($_SERVER['HTTP_HOST']) == 'localhost') {
        return $imagePath;
    } else {
        return site_url("imagePath?img=" . $imagePath . "&h=$height" . "w=$width");
    }
}

function getReceivedProjectAmount($projectId) {
    $record = GetAllRecord(PROJECT_PAYMENT, array("projectId" => $projectId), "", "", "", array());
    $amount = 0;
    for($i = 0; $i < count($record); $i++) {
        $amount += $record[$i]["payment"];
    }
    return $amount;
}

function displayErrorMsg($errorMsg=""){
    return '<div class="alert alert-danger alert-dismissible fade in" role="alert"><strong>Error</strong> '.$errorMsg.'</div>';
}

function displaySucMsg($sucMsg=""){
    return '<div class="alert alert-success alert-dismissible fade in" role="alert"><strong>Success</strong> '.$sucMsg.'</div>';
}

function checkLoginAccess() {
    $ci = & get_instance();
    if ($ci->session->userdata('adminId') > 0)
        return true;
    else
        redirect("login");
}

function usMoneyFormat($value) {
    return number_format($value, 2);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getRandomIp(){
    return mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
}

function sendMessage($params) {
    $mobile = $params["mobile"];
    $message = urlencode($params["message"]);
    $output = file_get_contents("http://login.arihantsms.com/vendorsms/pushsms.aspx?user=cipherhex&password=UBtNiZlY45z3Md00qe&msisdn=".$mobile."&sid=CIPHHX&msg=".$message."&fl=0&gwid=2");
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

function deleteData($table_name = '', $condition = array()){
    $ci = & get_instance();
    $ci->db->where($condition);
    $ci->db->delete($table_name);
    return 1;
}

/*

    @imageUnlink
    -> unlink means delete image on edit and delete
*/

function imageUnlink($imagePath = ''){

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
function isValidEmail($emailId = ''){
    if (filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
        return 1; //Yes
    }else{
        return 0; //No
    }
}


/*
===========
    -> @createOtp
    -> start code create otp here
    -> create random number length = 6
*/

function createOtp(){
    $numbers = "0123456789";
    $charactersLength = strlen($numbers);
    $otpNumber = '';
    for ($i = 0; $i < 6; $i++) {
        $otpNumber.= $numbers[rand(0, $charactersLength - 1) ];
    }
    return $otpNumber;
}


/*
    ====== common function here =======
    @sendOtpMail
    -> start code send otp mail
*/
function sendMailOtp($emailId,$fullName,$otpNumber){
    
    $condition = array();
    
    $toEmail    = $emailId;
    $subject    = "OTP Verify";
    $mail_body  = "";
    $mail_body  .= "Dear ".$fullName.",<br /><br />";
    $mail_body  .= "Our OTP for the registration for Go Badger Management is : ". $otpNumber.". It will be expired in 10 minutes.";  
    $mail_body  .= "<br/>Thank you,";
    $mail_body  .= "<br/>Go Badger Team";
    $mailResponse = sendMail($toEmail, $subject, $mail_body);   // send mail here

    if($mailResponse == 0){

        $response['err'] = 0;
        $response['otp'] = $otpNumber;
        $response['msg'] = "OTP has been sent successfully to your email id";
    }else{

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
function sendMailForgetPassword($emailId,$fullName) {
    
    $generatePassword = generateRandomString(12);

    $dataArr = array(
        'password'     => md5($generatePassword),
    );

    $toEmail    = $emailId;
    $subject    = "Forget Password";
    $mail_body  = "";
    $mail_body  .= "Dear ".$fullName.",<br /><br />";
    $mail_body  .= "Your new auto-generated password is ".$generatePassword.". Please login with it and change the password again of your choice for security reason.<br/><br/>";  
    $mail_body  .= "Thank you,<br/><br/>";  
    $mail_body  .= "Go Badger.<br/><br/>";  

    $mailResponse = sendMail($toEmail, $subject, $mail_body);   // send mail here

    if($mailResponse == 0) {

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
function sendNotification($fcmToken = "",$title = '',$body ='',$status = ''){

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
         if($result === false){
            /*die('Curl failed:' .curl_errno($ch));
            echo $result;*/
            return 0;
        }else{
            //return $result;
            return 1;
            //$resultData = json_decode($result, TRUE);
        }
        //echo  $result ."<br>";
        curl_close($ch);
}   


function getCountry(){
    $condition = array();
    $is_single = FALSE;
    $countries = GetAllRecord(COUNTRY_MASTER,$condition,$is_single);

    if (count($countries) > 0) {
        return $countries;
    }else{
        return array();
    }
}

function getCountryCode($country){
    $countriesWithCode = array('DK'=>'+45','SE'=>'+46','NOR'=>'+47','FI'=>'+358','UK'=>'+44','AU'=>'+43','DE' => '+49','CA' => '+1','NL' => '+31','NZ' => '+64');
    return $countriesWithCode[$country];
}

function getAllCountryCode(){
    $countryCode = array('DK'=>'45','SE'=>'46','NOR'=>'47','FI'=>'358','UK'=>'44','AU'=>'43','DE' => '49','CA' => '1','NL' => '31','NZ' => '64');
    return $countryCode;   
}

function getAllSmsApiProvider(){
    $sms_providers = array('forty_two' => 'Fourty Two', 'cp_sms' => 'CP SMS', 'warriors_sms' => 'Warriors SMS'/*, 'sms_edge' => 'SMS Edge', 'mmd_smart' => 'MMD Smart', 'in_mobile' => 'In Mobile', 'sinch'=>'Sinch'*/);
    return $sms_providers;
}


function encrypt($pure_string) {
    
    return bin2hex(openssl_encrypt($pure_string, 'AES-128-CBC', ENCRYPT_KEY));

}

/**

 * Returns decrypted original string

 */

function decrypt($encrypted_string) {
    
    return openssl_decrypt(hex2bin($encrypted_string), 'AES-128-CBC', ENCRYPT_KEY);
}


//get datetime diff in hours
function getDateTimeDiffInHours($startDate,$endDate){
    return round((strtotime($endDate) - strtotime($startDate))/3600, 1);
}


function countryThasListedInEgoi(){
    return array('DK','NO','SE');
}

function countryThasListedInAweber(){
    return array('DK','NOR','SE','FI','UK','NL','CA','NZ');
}

function getListIdForAllAweber($aweberListId,$country){
    if ($country != '') {
        
        $aweberAllListArr = array(
            '1' => array('DK' => 5237847, 'SE' => 5217417, 'FI' => 5237852, 'NOR' => 5217426, 'UK' => '', 'DE' => '', 'CA' => 5221106, 'NL' => 5514395),
            '2' => array('DK' => 5297593, 'SE' => 5297594, 'FI' => 5297596, 'NOR' => 5297595, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '3' => array('DK' => '', 'SE' => '', 'FI' => '', 'NOR' => 5327219, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''), 
            '4' => array('DK' => '', 'SE' => '', 'FI' => 5327235, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '5' => array('DK' => 5353599,'SE' => '', 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '6' => array('DK' => '','SE' => '', 'FI' => '', 'NOR' => 5384430, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '7' => array('DK' => '','SE' => '', 'FI' => 5384432, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '8' => array('DK' => 5518965,'SE' => '', 'FI' => '', 'NOR' => 5518966, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '9' => array('DK' => '','SE' => '', 'FI' => '', 'NOR' => 5562033, 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '10' => array('DK' => '','SE' => 5518967, 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '11' => array('DK' => '','SE' => '', 'FI' => 5327235, 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => ''),
            '12' => array('DK' => '','SE' => '', 'FI' => '', 'NOR' => '', 'UK' => '', 'DE' => '', 'CA' => '', 'NL' => '')
        );

        return $aweberAllListArr[$aweberListId][strtoupper($country)];
    }else{
        return '';
    }
}

function reformat_number_format($percentage = 0){

    $percentage = number_format((float)$percentage, 8, '.', '');
    return floatval($percentage);
}


function get_timezone_wise_difference($country = 'DK', $set_date = ''){

    $country_wise_time_zone = array('DK' => 'Europe/Copenhagen', 'SE' => 'Europe/Stockholm', 'NOR' => 'Europe/Oslo', 'FI' => 'Europe/Helsinki', 'UK' => 'Europe/London', 'DE' => 'Europe/Berlin', 'AU' => 'Australia/Canberra', 'NL' => 'Europe/Amsterdam', 'CA' => 'America/Regina'); 

    $set_time_zone_region = $country_wise_time_zone[$country];
    date_default_timezone_set($set_time_zone_region);

    $diff = strtotime($set_date) - time();    
    return $diff;

}

function get_current_time_of_country($country){

    $country_wise_time_zone = array('DK' => 'Europe/Copenhagen', 'SE' => 'Europe/Stockholm', 'NOR' => 'Europe/Oslo', 'FI' => 'Europe/Helsinki', 'UK' => 'Europe/London', 'DE' => 'Europe/Berlin', 'AU' => 'Australia/Canberra', 'NL' => 'Europe/Amsterdam', 'CA' => 'America/Regina'); 

    $set_time_zone_region = $country_wise_time_zone[$country];
    date_default_timezone_set($set_time_zone_region);

    $currentTime = time();    
    return $currentTime;

}

function addToAweberSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(AWEBER_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToTransmitviaSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(TRANSMITVIA_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToContactSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(CONTACT_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToOngageSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(ONGAGE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToSendgridSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(SENDGRID_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addToSendinblueSubscriberQueue($liveDeliveryDataId,$mailProvider,$delayDay){
    $liveDeliveryDelayData = array(
        "liveDeliveryDataId" => $liveDeliveryDataId,
        "providerId" => $mailProvider,
        "delayDay" => $delayDay,
        "currentTimestamp" => time(),
        "deliveryTimestamp" => strtotime('+'.$delayDay.' day', strtotime('9am')),
        "deliveryDate" => date("Y-m-d",strtotime('+'.$delayDay.' day', time())),
        "status" => 0
    );
    $condition = array();
    $is_insert = true;
    ManageData(SENDINBLUE_DELAY_USER_DATA, $condition, $liveDeliveryDelayData, $is_insert);
}

function addRecordInHistory($lastDeliveryData,$mailProvider,$provider,$response,$groupName,$keyword){
    $historyData = array(
        'liveDeliveryDataId' => $lastDeliveryData['liveDeliveryDataId'],
        'providerId' => $mailProvider,
        'provider' => $provider,
        'groupName' => $groupName,
        'keyword' => $keyword,
        'updateDate' => date("Y-m-d"),
        'updateDateTime' => date("Y-m-d H:i:s"),
        'response' => json_encode($response)
    );
    if($response != null){
        if($response['result'] == "success"){
            $historyData['status'] = 1; // success
        }else{
            $historyData['status'] = 2; // error - already subscribe + other error
        }
    }else{
        $historyData['status'] = 0; // pending
    }

    $condition = array();
    $is_insert = true;
    ManageData(EMAIL_HISTORY_DATA, $condition, $historyData, $is_insert);
}

function addRecordInHistoryFromCSV($lastDeliveryData,$mailProvider,$provider,$response,$groupName,$keyword){
    $historyData = array(
        'userId' => isset($lastDeliveryData['userId'])?$lastDeliveryData['userId']:"-",
        'providerId' => $mailProvider,
        'provider' => $provider,
        'groupName' => $groupName,
        'keyword' => $keyword,
        'updateDate' => date("Y-m-d"),
        'updateDateTime' => date("Y-m-d H:i:s"),
        'response' => json_encode($response)
    );
    if($response != null){
        if($response['result'] == "success"){
            $historyData['status'] = 1; // success
        }else{
            $historyData['status'] = 2; // error - already subscribe + other error
        }
    }else{
        $historyData['status'] = 0; // pending
    }

    $condition = array();
    $is_insert = true;
    ManageData(EMAIL_HISTORY_DATA, $condition, $historyData, $is_insert);
}

function getProviderIdUsingTransmitviaList($code){
    $condition = array(
        "code" => $code
    );
    $is_single = TRUE;
    $provider = GetAllRecord(PROVIDERS,$condition,$is_single);
    return $provider['id'];   
}