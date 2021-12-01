<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_admin_home extends CI_Model {

	public function __construct() {
        parent::__construct();

    }

    function getTotalLeadCounter($accountProviders,$filed,$date){
        $aliasName = array_keys($accountProviders)[0];
        $query = "";
        foreach($accountProviders as $tableName => $providerIds){
            $providerId = implode(',',$providerIds);
            $query.= "select count(*) as ".$tableName." from ".$tableName." WHERE providerId IN (".$providerId.") AND createdDate>='".$date['startDate']."' AND createdDate<='".$date['endDate']."'";
            if($filed == "success"){
                $query.= " AND status='1' AND response LIKE '".'%'.'"result":"success"'.'%'."' ";
            }
            $query.= 'UNION ALL ';
        }
        $query = rtrim($query,'UNION ALL ');
        $countTotalLeadQry =  "select sum(counter.".$aliasName.") as total
        from
        (
        ".$query."
        ) counter";
        $query = $this->db->query($countTotalLeadQry);
        $total = $query->result_array()[0];
        return $total['total'];
    }
}