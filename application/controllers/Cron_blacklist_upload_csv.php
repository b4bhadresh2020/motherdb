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
        //pre($csvFileData);
        
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
                //pre($csvDataArr);
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

                        $isInserted = $this->makeBlacklist($csvDataArr,$csvFileData);
                        
                        if ($isInserted > 0) {
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

                    if (($recordCount + $notDeletedRecords + $emptyRecords) == 5000) {
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

        $colNumber   = json_decode($csvFileData['colNumber'],TRUE);
        $fieldsName  = json_decode($csvFileData['fieldsName'],TRUE);

        //get file name
        $explodeFileArr = explode('/', $csvFileData['filePath']);
        $fileName = array_pop($explodeFileArr);

        // prepare to insertArr
        $dataArr = array();
        $countColNumber = count($colNumber);

        for ($j=0; $j < $countColNumber; $j++) { 
            $dataArr[$fieldsName[$j]] = $csvDataArr[$colNumber[$j] - 1];
        }

        $dataArr['fileName'] = $fileName;
        $blacklistId = ManageData(BLACKLIST, array(), $dataArr, TRUE);   //insert in blacklist
        

        //delete from main table and that table is 'user' but delete all where phone or email is same
        /*if (@$dataArr['emailId'] != '') {
            $this->db->where('emailId', $dataArr['emailId']);
            $this->db->delete(USER);    
        }

        if (@$dataArr['phone'] != '') {
            $this->db->where('phone', $dataArr['phone']);
            $this->db->delete(USER);    
        }*/

        return $blacklistId;

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