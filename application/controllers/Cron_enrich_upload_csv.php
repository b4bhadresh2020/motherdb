<?php

/**
 *
 */
class Cron_enrich_upload_csv extends CI_Controller
{
    public $insertCount = 0;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_cron');
    }

    public function enrich_upload_csv()
    {

        //get file data
        $condition   = array();
        $is_single   = true;
        $csvFileData = GetAllRecord(ENRICHMENT_CSV_FILE, $condition, $is_single, array(), array(), array(array('enrichmentCsvFileId' => 'ASC')));

        if (isset($csvFileData) && count($csvFileData) > 0) {

            $this->updateEnrichCronStatus(1); //update cron is running

            /*
            Open file to check wether file is 'comma' separated or 'colon' separate
             */

            $csv        = FCPATH . $csvFileData['filePath'];
            $fileHeader = fopen($csv, "r");
            $header     = fgetcsv($fileHeader);

            //detect delimiter
            $delimiter = $this->detectDelimiter($csv);
            fclose($fileHeader);

            /*
            File close
             */

            /*
            open file to get header in array
             */

            $fileHeaderArr = fopen($csv, "r");
            if (count($header) > 2) {
                $csvHeaderArr = fgetcsv($fileHeaderArr, $delimiter);
            } else {
                $csvHeaderArr = fgetcsv($fileHeaderArr, '', $delimiter, "\n");
            }
            fclose($fileHeaderArr);
            /*
            File close
             */

            /*
            open file for read and write the data
             */

            $file = fopen($csv, 'r+'); // open for read/write

            fseek($file, -1, SEEK_END); // move to end of file

            $recordCount       = 0;
            $emptyRecords      = 0;
            $notUpdatedRecords = 0;
            $isFileRunning     = 1; // 1 = running, 2 = completed

            // Get cronjob status id

            $condition   = array("filePath" => $csvFileData['filePath']);
            $is_single   = true;
            $enrichCronStatusData = GetAllRecord(ENRICHMENT_CRON_STATUS, $condition, $is_single, array(), array(), array());
            $enrichCronStatusId = $enrichCronStatusData['enrichCronStatusId'];

            while (fstat($file)['size']) {

                $this->lineStart($file); // move to beginning of line

                if (count($header) > 2) {
                    $csvDataArr = fgetcsv($file, $delimiter);
                } else {
                    $csvDataArr = fgetcsv($file, '', $delimiter, "\n");
                }

                if ($csvHeaderArr == $csvDataArr) {

                    //delete record from table
                    $id = $csvFileData['enrichmentCsvFileId'];
                    $this->db->where('enrichmentCsvFileId', $id);
                    $this->db->delete(ENRICHMENT_CSV_FILE);
                    fclose($file); //close file before unlink the file
                    unlink($csv);
                    $isFileRunning = 2; // 1 = running, 2 = completed
                    echo 'file ends';
                    break; // break while once file unlinked.

                } else {

                    if ($csvDataArr != '') {

                        $isUpdated = $this->doEnrichment($csvDataArr, $csvFileData, $enrichCronStatusId);

                        if ($isUpdated > 0) {
                            $recordCount++;
                            $this->insertCount++;
                            ftruncate($file, $this->lineStart($file)); // truncate from beginning of line
                        } else {
                            $notUpdatedRecords++;
                            ftruncate($file, $this->lineStart($file)); // truncate from beginning of line
                        }

                        // Update Status for the records.


                    } else {
                        $emptyRecords++;
                        ftruncate($file, $this->lineStart($file)); // truncate from beginning of line
                    }

                    if (($recordCount + $notUpdatedRecords + $emptyRecords) == 250) {
                        fclose($file);
                        break;
                    }
                }
            }

            //update some stats
            $this->mdl_cron->updateAllStatusInEnrichCronStatus($csvFileData['filePath'], $recordCount, $notUpdatedRecords, $emptyRecords, $isFileRunning);
            $this->updateEnrichCronStatus(0); //update cron is not running

        } else {

            $isEnrichCronRunning = getConfigVal('isEnrichCronRunning');

            if ($isEnrichCronRunning != 2) {
                $this->updateEnrichCronStatus(2);
            }

            echo 'No pending files';
        }
    }

    public function updateEnrichCronStatus($configVal)
    {

        //update site_config table
        $condition = array("configKey" => "isEnrichCronRunning");
        $dataArr   = array("configVal" => $configVal);
        ManageData(SITECONFIG, $condition, $dataArr, false);
    }

    public function doEnrichment($csvDataArr, $csvFileData, $enrichCronStatusId)
    {
        $colNumber                = json_decode($csvFileData['colNumber'], true);
        $fieldsName               = json_decode($csvFileData['fieldsName'], true);
        $lookingFor               = json_decode($csvFileData['lookingFor'], true);
        $groupName                = $csvFileData['groupName'];
        $keyword                  = $csvFileData['keyword'];
        $search_against_groupName = $csvFileData['search_against_groupName'];
        $search_against_keyword   = $csvFileData['search_against_keyword'];
        $search_against_country   = $csvFileData['search_against_country'];

        // prepare to get conditions data
        $condition = array();
        $like_condition = array();

        $countColNumber = count($colNumber);

        // prepare array for insert the data
        for ($j = 0; $j < $countColNumber; $j++) {
            $condition[$fieldsName[$j]] = trim(str_replace("=", "", $csvDataArr[$colNumber[$j] - 1]), '"');
        }

        //looking for condition
        if (count($lookingFor) > 0) {
            foreach ($lookingFor as $value) {
                $condition[$value . ' !='] = '';
            }
        }

        //search against groupName,keyword,country condition

        if (@$search_against_groupName) {
            $condition['groupName REGEXP'] = "\\b" . trim($search_against_groupName) . "\\b";
        }

        if (@$search_against_keyword) {
            $condition['keyword REGEXP'] = "\\b" . trim($search_against_keyword) . "\\b";
        }

        if (@$search_against_country) {
            $condition['country'] = $search_against_country;
        }

        if (key_exists("phone", $condition)) {
            $like_condition["phone"] = $condition["phone"];
            unset($condition["phone"]);
        }
        if (key_exists("emailId", $condition)) {
            $like_condition["emailId"] = $condition["emailId"];
            unset($condition["emailId"]);
        }

        //get user count with above condition

        $this->db->from(USER);
        $this->db->where($condition);
        if (key_exists("phone", $like_condition) && key_exists("emailId", $like_condition)) {
            $this->db->group_start();
            $this->db->like('phone', $like_condition["phone"]);
            $this->db->or_like('emailId', $like_condition["emailId"]);
            $this->db->group_end();
        } else if (key_exists("phone", $like_condition)) {
            $this->db->like('phone', $like_condition["phone"]);
        } else if (key_exists("emailId", $like_condition)) {
            $this->db->like('emailId', $like_condition["emailId"]);
        }
        $this->db->order_by('userId', "desc");
        $this->db->select('userId,groupName,keyword,allDataInString,gender,country');
        $getUsersData = $this->db->get()->result_array();

        if (count($getUsersData) > 0) {

            $isUpdatedTotal = 0;

            if (count($getUsersData) > 0) {

                foreach ($getUsersData as $getUserData) {

                    // first add in allDataInString field if groupName or keyword
                    $allDataInString = $getUserData['allDataInString'];

                    //now, add comma separated groupname
                    $userGroupName = $getUserData['groupName'];
                    $groupExArr    = explode(',', $userGroupName);

                    if (!in_array($groupName, $groupExArr)) {
                        $groupExArr[] = $groupName;
                        $allDataInString .= '+' . $groupName;
                    }
                    $groupImpldStr = implode(',', $groupExArr);

                    //And now, add comma separated keyword
                    $userKeyword  = $getUserData['keyword'];
                    $keywordExArr = explode(',', $userKeyword);

                    if (!in_array($keyword, $keywordExArr)) {
                        $keywordExArr[] = $keyword;
                        $allDataInString .= '+' . $keyword;
                    }
                    $keywordImpldStr = implode(',', $keywordExArr);

                    // new edited start

                    $userGender  = $getUserData['gender'];
                    $userCountry = $getUserData['country'];
                    $userGroup   = $getUserData['groupName'];

                    $keywordExArr_old = explode(',', $userKeyword);
                    $keyword_status   = 'not_update';

                    if (!in_array($csvFileData['keyword'], $keywordExArr_old)) {
                        $keyword_status = 'is_update';
                    }

                    $groupExArr_old = explode(',', $userGroupName);
                    $group_status   = 'not_update';

                    if (!in_array($csvFileData['groupName'], $groupExArr_old)) {
                        $group_status = 'is_update';
                    }

                    $new_keyword   = $csvFileData['keyword'];
                    $new_groupName = $csvFileData['groupName'];

                    if ($this->insertCount == 0) {

                        if ($keyword_status == 'is_update') {

                            $keywordCountryCount = GetAllRecordCount(KEYWORD_COUNTRY_COUNT, $condition = array('keyword' => $new_keyword, 'country' => $userCountry), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'keywordCountryId');

                            if ($keywordCountryCount == 0) {

                                $sql_keywordCountryCount = "INSERT INTO " . KEYWORD_COUNTRY_COUNT . " (keyword, country, total)  VALUES ('$new_keyword', '$userCountry', 0)";

                                $this->db->query($sql_keywordCountryCount);
                            }
                        }

                        if ($group_status == 'is_update') {

                            $groupCountryCount = GetAllRecordCount(GROUP_COUNTRY_COUNT, $condition = array('groupName' => $new_groupName, 'country' => $userCountry), $is_single = false, $is_like = array(), $or_like = array(), $order_by = array(), $selected_rows = 'groupCountryId');

                            if ($groupCountryCount == 0) {

                                $sql_groupCountryCount = "INSERT INTO " . GROUP_COUNTRY_COUNT . " (groupName, country)  VALUES ('$new_groupName', '$userCountry')";

                                $this->db->query($sql_groupCountryCount);
                            }
                        }
                    }

                    if ($keyword_status == 'is_update') {

                        $sql_keywordCountryCount = "UPDATE " . KEYWORD_COUNTRY_COUNT . " SET total = total + 1  WHERE keyword = '$new_keyword' and country = '$userCountry'";
                        $this->db->query($sql_keywordCountryCount);
                    }

                    if (@$userGender == 'male') {

                        if ($keyword_status == 'is_update') {
                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET male = male + 1  WHERE keyword = '$new_keyword'";
                            $this->db->query($sql_keyword);
                        }

                        if ($group_status == 'is_update') {
                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET male = male + 1  WHERE groupName = '$new_groupName'";
                            $this->db->query($sql_groupName);
                        }
                    }

                    if (@$userGender == 'female') {

                        if ($keyword_status == 'is_update') {
                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET female = female + 1  WHERE keyword = '$new_keyword'";
                            $this->db->query($sql_keyword);
                        }

                        if ($group_status == 'is_update') {
                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET female = female + 1  WHERE groupName = '$new_groupName'";
                            $this->db->query($sql_groupName);
                        }
                    }

                    if (@$userGender != 'male' && @$userGender != 'female') {

                        if ($keyword_status == 'is_update') {
                            $sql_keyword = "UPDATE " . KEYWORD_MASTER . " SET other = other + 1  WHERE keyword = '$new_keyword'";
                            $this->db->query($sql_keyword);
                        }

                        if ($group_status == 'is_update') {
                            $sql_groupName = "UPDATE " . GROUP_MASTER . " SET other = other + 1  WHERE groupName = '$new_groupName'";
                            $this->db->query($sql_groupName);
                        }
                    }

                    // new edited end

                    //update record
                    $condition = array('userId' => $getUserData['userId']);
                    $dataArr   = array('groupName' => $groupImpldStr, 'keyword' => $keywordImpldStr, 'allDataInString' => $allDataInString);
                    $is_insert = false;

                    $isUpdated = ManageData(USER, $condition, $dataArr, $is_insert);


                    if ($isUpdated == 1) {
                        $isUpdatedTotal++;
                    }
                }
                // Code for add history of enrichment 

                $condition  = array();
                $dataArr    = array('enrichCronStatusId' => $enrichCronStatusId, 'enrichData' => json_encode($csvDataArr), 'userId' => $getUserData['userId']);
                $is_insert  = true;
                ManageData(ENRICHMENT_HISTORY_DATA, $condition, $dataArr, $is_insert);

                return $isUpdatedTotal;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /* @param string $csvFile Path to the CSV file
     * @return string Delimiter
     */
    public function detectDelimiter($csvFile)
    {
        $delimiters = array(
            ';'  => 0,
            ','  => 0,
            "\t" => 0,
            "|"  => 0,
        );

        $handle    = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }

    public function lineStart($file)
    {
        $position = ftell($file);
        while (fgetc($file) != "\n") {
            fseek($file, --$position);
            if ($position == 0) {
                break;
            }
        }
        return $position;
    }
}
