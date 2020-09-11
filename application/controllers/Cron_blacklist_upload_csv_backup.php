<?php

/**
 * 
 */
class Cron_blacklist_upload_csv extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_cron');
    }

    public function blacklist_upload_csv() {
        
        //get file data
        $condition = array();
        $is_single = TRUE;
        $csvFileData = GetAllRecord(BLACKLIST_CSV_FILE,$condition,$is_single,array(),array(),array(array('blacklistCsvFileId' => 'ASC')));
        
        if (count($csvFileData) > 0) {

            $this->updateBlacklistCronStatus(1);        //update cron is running

            /*
                Open file to check wether file is 'comma' separated or 'colon' separate
            */

            $csv = FCPATH.$csvFileData['filePath'];
            $fileHeader = fopen($csv,"r");
            $header = fgetcsv($fileHeader);

            //detect delimiter
            $delimiter = $this->detectDelimiter($csv);
            fclose($fileHeader);
            
            /*
                File close
            */

            /*
                open file to get header in array
            */

            $fileHeaderArr = fopen($csv,"r");
            if (count($header) > 2) {
                $csvHeaderArr = fgetcsv($fileHeaderArr,$delimiter);
            }else{
                $csvHeaderArr = fgetcsv($fileHeaderArr,'',$delimiter,"\n");
            }
            fclose($fileHeaderArr);
            /*
                File close
            */        


            /*
                open file for read and write the data
            */

            $file = fopen($csv, 'r+');       // open for read/write

            fseek($file, -1, SEEK_END);              // move to end of file

            $recordCount = 0;
            $emptyRecords = 0;
            $notDeletedRecords = 0;
            $isFileRunning = 1;          // 1 = running, 2 = completed

            while (fstat($file)['size']) {

                $this->lineStart($file);                // move to beginning of line

                if (count($header) > 2) {
                    $csvDataArr = fgetcsv($file,$delimiter);
                }else{
                    $csvDataArr = fgetcsv($file,'',$delimiter,"\n");
                }
                
                if($csvHeaderArr == $csvDataArr){
                
                    //delete record from table
                    $id = $csvFileData['blacklistCsvFileId'];
                    $this->db->where('blacklistCsvFileId',$id);
                    $this->db->delete(BLACKLIST_CSV_FILE);
                    fclose($file); //close file before unlink the file
                    unlink($csv);
                    $isFileRunning = 2; // 1 = running, 2 = completed
                    echo 'file ends';
                    break; // break while once file unlinked.
                
                }else{
                    
                    if($csvDataArr != ''){

                        $isUpdated = $this->makeBlacklist($csvDataArr,$csvFileData);
                        
                        if ($isUpdated > 0) {
                            $recordCount++;
                            ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                        }else{
                            $notDeletedRecords++;
                            ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                        }

                    }else{
                        $emptyRecords++;
                        ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                    }

                    if (($recordCount + $notDeletedRecords + $emptyRecords) == 250) {
                        fclose($file);
                        break;
                    }
                }
            }

            //update some stats
            $this->mdl_cron->updateAllStatusInBlackListCronStatus($csvFileData['filePath'],$recordCount,$notDeletedRecords,$emptyRecords,$isFileRunning);
            $this->updateBlacklistCronStatus(0);    //update cron is not running

        }else{

            $isBlacklistCronRunning = getConfigVal('isBlacklistCronRunning');

            if ($isBlacklistCronRunning != 2) {     
                $this->updateBlacklistCronStatus(2);    
            }
            
            echo 'No pending files';
        }
        
    }

    function updateBlacklistCronStatus($configVal){

        //update site_config table
        $condition = array("configKey" => "isBlacklistCronRunning");
        $dataArr   = array("configVal" => $configVal);
        ManageData(SITECONFIG, $condition, $dataArr, FALSE);

    }

    function makeBlacklist($csvDataArr,$csvFileData){

        $colNumber        = json_decode($csvFileData['colNumber'],TRUE);
        $fieldsName       = json_decode($csvFileData['fieldsName'],TRUE);

        //get file name
        $explodeFileArr = explode('/', $csvFileData['filePath']);
        $fileName = array_pop($explodeFileArr);

        // prepare to get conditions data 
        $condition = array();

        $countColNumber = count($colNumber);

        // prepare array for delete data
        for ($j=0; $j < $countColNumber; $j++) { 

            //we need only email or phone
            if ($fileName[$j] == 'emailId' || $fileName[$j] == 'phone') {
                $condition[$fieldsName[$j]] = $csvDataArr[$colNumber[$j] - 1];
            }
        }
        
        if (@$condition['emailId'] != '' && @$condition['phone'] != '') {
            $this->db->where('emailId',$condition['emailId']);
            $this->db->or_where('emailId',$condition['emailId']);
        }else if(@$condition['emailId'] != ''){
            $this->db->where('emailId',$condition['emailId']);
        }else if(@$condition['phone'] != ''){
            $this->db->where('phone',$condition['phone']);
        }
        
        $userCount = $this->db->count_all_results(USER);

        if ($userCount > 0) {
            
            //get user data

            $this->db->select('userId,firstName,lastName,emailId,phone,country,gender');

            if (@$condition['emailId'] != '' && @$condition['phone'] != '') {
                $this->db->where('emailId',$condition['emailId']);
                $this->db->or_where('emailId',$condition['emailId']);
            }else if(@$condition['emailId'] != ''){
                $this->db->where('emailId',$condition['emailId']);
            }else if(@$condition['phone'] != ''){
                $this->db->where('phone',$condition['phone']);
            }
            $this->db->limit(1);
            $getUserData = $this->db->get(USER)->row_array();


            //check this data is already in unsubscribe or not

            if (@$condition['emailId'] != '' && @$condition['phone'] != '') {
                $this->db->where('emailId',$condition['emailId']);
                $this->db->or_where('emailId',$condition['emailId']);
            }else if(@$condition['emailId'] != ''){
                $this->db->where('emailId',$condition['emailId']);
            }else if(@$condition['phone'] != ''){
                $this->db->where('phone',$condition['phone']);
            }
            
            $unsubscriberCount = $this->db->count_all_results(UNSUBSCRIBER);

            if ($unsubscriberCount == 0) {

                $fieldArr = array('firstName','lastName','emailId','phone','country','gender');
                $dataArr = array();
                foreach ($fieldArr as $value) {
                    $dataArr[$value] = $getUserData[$value];
                }
                $dataArr['fileName'] = $fileName;
                $lastInsertedId = ManageData(UNSUBSCRIBER, array(), $dataArr, TRUE);   

                if ($lastInsertedId > 0) {

                    //delete from main table and that is 'user' but delete all where phone or email is same
                    if ($getUserData['emailId'] != '') {
                        $this->db->where('emailId', $getUserData['emailId']);
                        $this->db->delete(USER);    
                    }

                    if ($getUserData['phone'] != '') {
                        $this->db->where('phone', $getUserData['phone']);
                        $this->db->delete(USER);    
                    }

                    return 1;

                }else{
                    return 0;
                }

            }else{

            }
            

            //check duplicate entry
            $condition = $dataArr;
            $is_single = TRUE;
            $this->db->limit(1);
            $getDupRec = GetAllRecord(UNSUBSCRIBER,$condition,$is_single);

            if (count($getDupRec) == 0) {
                
                 

                
            }else{
                return 1;
            }

        }else{
            //insert in blacklist
        }
        //get data from user table with above condition
        $is_single = TRUE;
        $order_by = array('userId' => 'DESC');
        $this->db->limit(1);
        $getUserData = GetAllRecord(USER,$condition,$is_single,array(),array(),array($order_by),'userId,firstName,lastName,emailId,phone,country,gender');
        
        if(count($getUserData) > 0){}else{
            return 0;    
        }
        
        
    }


    /* @param string $csvFile Path to the CSV file
    * @return string Delimiter
    */
    public function detectDelimiter($csvFile)
    {
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        );

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle); 
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }


    function lineStart($file) {
        $position = ftell($file);
        while (fgetc($file) != "\n") {
            fseek($file, --$position);
            if ($position == 0) break;
        }
        return $position;
    }
    
}