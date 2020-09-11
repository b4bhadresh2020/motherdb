<?php

/**
 * 
 */
class Cron_upload_csv extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_cron');
    }

    public function upload_csv_data() {
        
        //get file data
        $condition = array();
        $is_single = TRUE;
        $csvFileData = GetAllRecord(CSV_FILE_DATA,$condition,$is_single,array(),array(),array(array('csvFileDataId' => 'ASC')));
        //pre($csvFileData);die;
        if (count($csvFileData) > 0) {

            $this->updateConfigTable(1);        //update cron is running

            $campaignIdArr = array();

            //get campaign id
            if ($csvFileData['campaign'] != '') {
                     
                $campaigns = $csvFileData['campaign'];
                $campaignsArr = explode(',', $campaigns);
                $country   = $csvFileData['country'];

                foreach ($campaignsArr as $campaignName) {
                    
                    //check if campaign is already exist or not
                    $campaignName = trim($campaignName);
                    $condition = array('campaignName' => $campaignName,'country' => $country);
                    $is_single = TRUE;
                    $this->db->limit(1);
                    $campaignData = GetAllRecord(CAMPAIGN,$condition,$is_single);

                    if (count($campaignData) > 0) {

                        $campaignId = $campaignData['campaignId'];
                        if(!in_array($campaignId, $campaignIdArr)){
                            $campaignIdArr[] = $campaignId;     //will avoid duplication
                        }
                        
                    }else{
                       //add to campaign table
                       $condition = array(); 
                       $insertArr = array('campaignName' => $campaignName,'country' => $country);
                       $is_insert = TRUE;
                       $campaignId = ManageData(CAMPAIGN,$condition,$insertArr,$is_insert);
                       $campaignIdArr[] = $campaignId;
                    }
                }
            } 


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
            $notInstertedRecords = 0;
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
                    $id = $csvFileData['csvFileDataId'];
                    $this->db->where('csvFileDataId',$id);
                    $this->db->delete(CSV_FILE_DATA);
                    fclose($file); //close file before unlink the file
                    unlink($csv);
                    $isFileRunning = 2; // 1 = running, 2 = completed
                    echo 'file ends';
                    break; // break while once file unlinked.
                
                }else{
                    
                    if($csvDataArr != ''){

                        $isInserted = $this->insertInDB($csvDataArr,$csvFileData,$campaignIdArr);
                        
                        if ($isInserted > 0) {
                            $recordCount++;
                            ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                        }else{
                            $notInstertedRecords++;
                            ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                        }

                    }else{
                        $emptyRecords++;
                        ftruncate($file, $this->lineStart($file));  // truncate from beginning of line
                    }
                    /*echo $recordCount + $notInstertedRecords + $emptyRecords;
                    echo '<br />';*/
                    if (($recordCount + $notInstertedRecords + $emptyRecords) == 5000) {
                        fclose($file);
                        break;
                    }
                }
            }

            //update some stats
            $this->mdl_cron->updateAllStatusInCronStatus($csvFileData['filePath'],$recordCount,$notInstertedRecords,$emptyRecords,$isFileRunning);
            $this->updateConfigTable(0);    //update cron is not running

        }else{

            $isCronRunning = getConfigVal('isCronRunning');

            if ($isCronRunning != 2) {     
                $this->updateConfigTable(2);    
            }
            
            echo 'No pending files';
        }
        
    }

    function updateConfigTable($configVal){

        //update site_config table
        $condition = array("configKey" => "isCronRunning");
        $dataArr   = array("configVal" => $configVal);
        ManageData(SITECONFIG, $condition, $dataArr, FALSE);

    }

    function insertInDB($csvDataArr,$csvFileData,$campaignIdArr){

        $colNumber        = json_decode($csvFileData['colNumber'],TRUE);
        $fieldsName       = json_decode($csvFileData['fieldsName'],TRUE);
        $customfieldsName = json_decode($csvFileData['customfieldsName'],TRUE);
        $groupName        = $csvFileData['groupName'];
        $keyword          = $csvFileData['keyword'];
        $country          = $csvFileData['country'];
        $providerId       = $csvFileData['providerId'];

        // prepare for insert in table
        $dataArr = array();
        $otherVal = array();
        $allDataValInString = '';

        $countColNumber = count($colNumber);

        if(count($csvDataArr) >= count($fieldsName)){

            for ($j=0; $j < $countColNumber; $j++) { 

                //for 'other', make an array and insert in db with convered to json
                if ($fieldsName[$j] == 'other') {

                    $otherVal[] = $csvDataArr[$colNumber[$j] - 1];

                }else{

                    if ($fieldsName[$j] == 'birthdateYear') {
                        if ($fieldsName[$j] != '') {
                            $dataArr['age'] = date('Y') - date('Y',strtotime(date($csvDataArr[$colNumber[$j] - 1].'-m-d')));    
                        }
                        
                    }

                    $dataArr[$fieldsName[$j]] = $csvDataArr[$colNumber[$j] - 1];
                }
                
                //make all value in string for global search purpose
                if ($allDataValInString == '') {
                    $allDataValInString = $csvDataArr[$colNumber[$j] - 1];
                }else{
                    $allDataValInString .= '+'.$csvDataArr[$colNumber[$j] - 1];
                }
            }

            if (count($customfieldsName) > 0) {
                /*$dataArr['otherLable'] = json_encode(array_map('utf8_encode', $customfieldsName));   
                $dataArr['other'] = json_encode(array_map('utf8_encode', $otherVal)); */

                $dataArr['otherLable'] = json_encode($customfieldsName);   
                $dataArr['other'] = json_encode($otherVal); 
            }

            $dataArr['groupName'] = $groupName;
            $dataArr['keyword'] = $keyword;
            $dataArr['country'] = $country;

            $allDataValInString .= '+'.$groupName.'+'.$keyword.'+'.$country;

            $dataArr['allDataInString'] = $allDataValInString; 
            $dataArr['r_id'] = rand(1,10000); //make random number between 1 to 10000

            //$dataArr = array_map('utf8_encode',$dataArr); 
            
            //insert in db
            $condition = array();
            $is_insert = true;

            // new edited start

            $con_country = $dataArr['country'];
            $con_keyword = $dataArr['keyword'];
            $con_groupName = $dataArr['groupName'];


            $sql_keywordCountryCount = "UPDATE ".KEYWORD_COUNTRY_COUNT." SET total = total + 1  WHERE keyword = '$con_keyword' and country = '$con_country'";
            $this->db->query($sql_keywordCountryCount);

            if(@$dataArr['gender'] == 'male'){

                $sql_country = "UPDATE ".COUNTRY_MASTER." SET male = male + 1  WHERE country = '$con_country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET male = male + 1  WHERE keyword = '$con_keyword'";
                $this->db->query($sql_keyword);

                $sql_groupName = "UPDATE ".GROUP_MASTER." SET male = male + 1  WHERE groupName = '$con_groupName'";
                $this->db->query($sql_groupName);

            } 

            if(@$dataArr['gender'] == 'female'){

                $sql_country = "UPDATE ".COUNTRY_MASTER." SET female = female + 1  WHERE country = '$con_country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET female = female + 1  WHERE keyword = '$con_keyword'";
                $this->db->query($sql_keyword);

                $sql_groupName = "UPDATE ".GROUP_MASTER." SET female = female + 1  WHERE groupName = '$con_groupName'";
                $this->db->query($sql_groupName);
            }


            if(@$dataArr['gender'] != 'male' && @$dataArr['gender'] != 'female'){

                $sql_country = "UPDATE ".COUNTRY_MASTER." SET other = other + 1  WHERE country = '$con_country'";
                $this->db->query($sql_country);

                $sql_keyword = "UPDATE ".KEYWORD_MASTER." SET other = other + 1  WHERE keyword = '$con_keyword'";
                $this->db->query($sql_keyword);

                $sql_groupName = "UPDATE ".GROUP_MASTER." SET other = other + 1  WHERE groupName = '$con_groupName'";
                $this->db->query($sql_groupName);

            }

            // new edited end
            $lastInsertedId = ManageData(USER,$condition,$dataArr,$is_insert);

            if($providerId != null){
                // add data in csv user data with provider 
                $csv_user_data = array(
                    'providerId' => $providerId,
                    'userId' => $lastInsertedId
                );
                $lastProviderUserId = ManageData(CSV_CRON_USER_DATA,$condition,$csv_user_data,$is_insert);
            }        
        }else{
            $lastInsertedId = 0;
        }

        if ($lastInsertedId > 0 && count($campaignIdArr) > 0) {
            // insert in user_participated_campaign
            $this->insertInUserParticipatedCampaign($lastInsertedId,$campaignIdArr);
        }

        return $lastInsertedId;
    }


    function insertInUserParticipatedCampaign($userId,$campaignIdArr){

        //insert in table
        foreach ($campaignIdArr as $campaignId) {
            
            $condition = array();
            $insertArr = array('campaignId' => $campaignId,'userId' => $userId);
            $is_insert = TRUE;
            ManageData(USER_PARTICIPATED_CAMPAIGN,$condition,$insertArr,$is_insert);
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