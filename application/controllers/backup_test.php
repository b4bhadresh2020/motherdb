<?php

/**
 * 
 */
class Cron_upload_csv extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
    }

    public function upload_csv_data() {
        
        //get file data
        $condition = array();
        $is_single = FALSE;
        $getCsvData = GetAllRecord(CSV_FILE_DATA,$condition,$is_single);
        

        if (count($getCsvData) > 0) {
            
            $fileCount = 0; 
            $csvFileData = $getCsvData[$fileCount];
            
            $csv = FCPATH.$csvFileData['filePath'];
            $file = fopen($csv,"r");
            $header = fgetcsv($file);
             
            //detect delimiter
            $delimiter = $this->detectDelimiter($csv);

            $recordCount = 0;

            while(! feof($file)){

                if (count($header) > 2) {
                    $csvDataArr = fgetcsv($file,$delimiter);
                }else{
                    $csvDataArr = fgetcsv($file,'',$delimiter,"\n");
                }

                if(empty($csvDataArr) || (count($csvDataArr) === 1 && empty(trim($csvDataArr[0])))){
                    //delete record from table
                    $id = $csvFileData['csvFileDataId'];
                    $this->db->delete('csvFileDataId',$id);
                    $this->db->from(CSV_FILE_DATA);
                    //unlink($csv)
                }else{
                    
                    $isInserted = $this->insertInDB($csvDataArr,$csvFileData);

                    if ($isInserted == 1) {
                        $recordCount++;
                    }

                    if ($recordCount == 100) {
                        break;
                    }
                }
            }
        }
    }


    function insertInDB($csvDataArr,$csvFileData){

        $colNumber        = json_decode($csvFileData['colNumber'],TRUE);
        $fieldsName       = json_decode($csvFileData['fieldsName'],TRUE);
        $customfieldsName = json_decode($csvFileData['customfieldsName'],TRUE);
        $groupName        = $csvFileData['groupName'];
        $keyword          = $csvFileData['keyword'];

        // prepare for insert in table
        $dataArr = array();
        $otherVal = array();
        $allDataValInString = '';

        $countColNumber = count($colNumber);

        // prepare array for insert the data
        for ($j=0; $j < $countColNumber; $j++) { 

            //for 'other', make an array and insert in db with convered to json
            if ($fieldsName[$j] == 'other') {

                $otherVal[] = $csvDataArr[$colNumber[$j] - 1];

            }else if($fieldsName[$j] == 'participated'){

                $dataArr[$fieldsName[$j]] = date('Y-m-d H:i:s',strtotime(date('d-m-y H:i',strtotime($csvDataArr[$colNumber[$j] - 1]))));    
            
            }else if ($fieldsName[$j] == 'birthdateYear') {
            
                 $dataArr['age'] = date('Y') - $csvDataArr[$colNumber[$j] - 1];
            
            }else{

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
            $dataArr['otherLable'] = json_encode(array_map('utf8_encode', $customfieldsName));   
            $dataArr['other'] = json_encode(array_map('utf8_encode', $otherVal));   
        }

        $dataArr['groupName'] = $groupName;
        $dataArr['keyword'] = $keyword;

        $allDataValInString .= '+'.$groupName.'+'.$keyword;

        $dataArr['allDataInString'] = $allDataValInString; 
        
        $dataArr = array_map('utf8_encode',$dataArr); 
        
        //insert in db
        $condition = array();
        $is_insert = true;
        $lastInsertedId = ManageData(USER,$condition,$dataArr,$is_insert);

        return $lastInsertedId;
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

    
}