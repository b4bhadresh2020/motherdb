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
            
            if($filed == "total"){
                
                $query.= " AND status!='0' ";
                
            }else if($filed == "success"){

                $query.= " AND status='1' AND response LIKE '".'%'.'"result":"success"'.'%'."' ";

            }else if($filed == "fail"){

                $query.= " AND response LIKE '".'%'.'"result":"error"'.'%'."' ";

            }else if($filed == "duplicate"){

                $query.= " AND (response LIKE '%Subscriber already%' OR  response LIKE '%Email is present%') ";

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