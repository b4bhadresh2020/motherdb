<?php

$liveDeliveriesGroups = [];

$condition = array();
$is_single = FALSE;
$getApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array("country", "ASC"), array('liveDeliveryId' => 'desc')), 'country,apikey,groupName,keyword,mailProvider,live_status,dataSourceType');
foreach ($getApiKeys as $key => $liveDelivery) {
    $liveDeliveriesGroups[$liveDelivery['country']][] = $liveDelivery;
}
?>

<style type="text/css">
    #goToTop {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: #878787;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 10px;
    }

    #goToTop:hover {
        background-color: #55555585;
    }
</style>

<button onclick="topScrollFunction()" id="goToTop" title="Go to top"><i class="fa fa-chevron-up"></i></button>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">
                    <div class="card alert">
                        <div class="card-header">
                            <h4>Filters</h4>
                        </div>
                        <div class="card-body">
                            <div class="horizontal-form-elements">
                                <form class="form-horizontal" method="get" action="<?php echo base_url('liveDeliveryStat/manage/0'); ?>">
                                    <div class="row">
                                        <div class="col-lg-5">

                                            <div id="error_msg"></div>

                                            <div class="form-group custom-select">
                                                <label>Select Apikey (Group-Keyword)</label>
                                                <select name="apikey" id="apikey" class="form-control selectpicker">

                                                    <?php foreach ($liveDeliveriesGroups as $country =>  $apikeyGroup) { ?>
                                                        <optgroup label="<?php echo $country; ?>">
                                                            <?php foreach ($apikeyGroup as $value) {
                                                                if ($value['live_status'] == 1 || $value['dataSourceType'] == 1) { // dataSourceType 0:motherdb, 1: inboxgame, 2:facebook lead
                                                                    $liveDeliveryStatusHighlight = "green";
                                                                } else if ($value['live_status'] == 2) {
                                                                    $liveDeliveryStatusHighlight = "orange";
                                                                } else {
                                                                    $liveDeliveryStatusHighlight = "red";
                                                                }
                                                            ?>
                                                                <option value="<?php echo $value['apikey']; ?>" <?php if ($value['apikey'] == @$_GET['apikey']) {
                                                                                                                    echo 'selected';
                                                                                                                } ?> style="color:<?php echo $liveDeliveryStatusHighlight; ?>"><?php echo $value['groupName'] . '-' . $value['keyword'] . ' (' . $value['apikey'] . ')'; ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php  } ?>
                                                </select>
                                                <input type="hidden" id="hiddenDataSourceType" />
                                            </div>

                                            <?php $filter = array(
                                                'all'   => 'All',
                                                'td'    => 'Today',
                                                'yd'    => 'Yesterday',
                                                'lSvnD'   => 'Last 7 Days (Including Today)',
                                                'lThrtyD' => 'Last 30 Days (Including Today)',
                                                'wTd'   => 'Week to Date',
                                                'mTd'   => 'Month to Date',
                                                'qTd'   => 'Quarter to Date',
                                                'yTd'   => 'Year to Date',
                                                'pw'    => 'Previous Week',
                                                'pm'    => 'Previous Month',
                                                'pq'    => 'Previous Quarter',
                                                'py'    => 'Previous Year',
                                                'cd'    => 'Custom Date'
                                            ); ?>

                                            <div class="form-group">
                                                <label>Select Different Filter Option</label>
                                                <select name="chooseFilter" id="chooseFilter" class="form-control">

                                                    <?php foreach ($filter as $key => $value) { ?>

                                                        <option value="<?php echo $key; ?>" <?php if ($key == @$_GET['chooseFilter']) {
                                                                                                echo 'selected';
                                                                                            } ?>><?php echo $value; ?></option>

                                                    <?php } ?>

                                                </select>
                                            </div>

                                            <div class="row" id="startEndDateDiv" style="display: none;">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input class="form-control" type="datetime-local" name="startDate" id="startDate" value="<?php echo @$_GET['startDate']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input class="form-control" type="datetime-local" name="endDate" id="endDate" value="<?php echo @$_GET['endDate']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $sucFailTypeArr =  array('0' => 'Success', '1' => 'Duplicate', '2' => 'Blacklisted', '3' => 'Server Issue', '4' => 'Api Key Is Not Active', '5' => 'Email Is Required', '6' => 'Phone Is Required', '7' => 'Email Is Blank', '8' => 'Phone Is Blank', '9' => 'Invalid Email Format', '10' => 'Invalid Phone', '11' => 'Invalid Gender', '12' => 'Telia MX Block', '13' => 'Luukku Mx Block', '14' => 'PP MX Block', '15' => 'User Already Unsubscribed', '16' => 'Yahoo MX Block', '17' => 'Icloud MX Block', '18' => 'GMX MX Block', '19' => 'Duplicate Old', '20' => 'Blacklisted IP', '21' => 'Protonmail MX Block', '22' => 'Reject By Inboxgame and Facebook Lead', '23' => 'Reject By Integromat Lead', '24' => 'Age Is Required'); ?>

                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Select Result Type</label>
                                                        <select name="chooseSucFailRes" id="chooseSucFailRes" class="form-control">

                                                            <option value="" <?php if (@$_GET['chooseSucFailRes'] == '') {
                                                                                    echo 'selected';
                                                                                } ?>>All</option>
                                                            <?php
                                                            foreach ($sucFailTypeArr as $key => $value) { ?>
                                                                <option value="<?php echo $key; ?>" <?php if (@$_GET['chooseSucFailRes'] == "$key") {
                                                                                                        echo 'selected';
                                                                                                    } ?>><?php echo $value; ?></option>
                                                            <?php } ?>




                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Search</label>
                                                        <input class="form-control" type="text" name="globleSearch" id="globleSearch" value="<?php echo @$_GET['globleSearch']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <input type="submit" id="btn-submit" value="Submit" class="form-control btn btn-dark">
                                                </div>
                                                <div class="col-lg-3">
                                                    <input type="submit" name="reset" value="Reset" class="form-control btn btn-default">
                                                </div>
                                                <?php
                                                $apikey = "";
                                                $chooseFilter = "";
                                                $chooseSucFailRes = "";
                                                $globleSearch = "";
                                                $startDate = "";
                                                $endDate = "";

                                                if (@$_GET['apikey']) {
                                                    $apikey = $_GET['apikey'];
                                                }

                                                if (@$_GET['chooseFilter']) {
                                                    $chooseFilter = $_GET['chooseFilter'];
                                                }

                                                if (@$_GET['chooseSucFailRes'] != "") {
                                                    $chooseSucFailRes = $_GET['chooseSucFailRes'];
                                                }

                                                if (@$_GET['globleSearch']) {
                                                    $globleSearch = $_GET['globleSearch'];
                                                }

                                                if (@$_GET['startDate']) {
                                                    $startDate = $_GET['startDate'];
                                                }

                                                if (@$_GET['endDate']) {
                                                    $endDate = $_GET['endDate'];
                                                }
                                                ?>
                                                <div class="col-lg-4">
                                                    <a href="<?php echo base_url('liveDeliveryStat/exportCsv?apikey=' . $apikey . '&chooseFilter=' . $chooseFilter . '&chooseSucFailRes=' . $chooseSucFailRes . '&globleSearch=' . $globleSearch . '&startDate=' . $startDate . '&endDate=' . $endDate); ?>" class="form-control btn btn-warning">Export CSV</a>
                                                </div>

                                            </div>

                                        </div>

                                        <?php

                                        $totalCount = $countsArr['successCount'] + $countsArr['failureCount'];

                                        $successCountPercenatage = 0;
                                        $failureCountPercenatage = 0;
                                        $checkEmailsCountPercenatage = 0;

                                        if ($totalCount > 0) {
                                            $successCountPercenatage = reformat_number_format(($countsArr['successCount'] / $totalCount) * 100);
                                            $failureCountPercenatage = reformat_number_format(($countsArr['failureCount'] / $totalCount) * 100);
                                            $checkEmailsCountPercenatage = reformat_number_format(($countsArr['checkEmailCount'] / $totalCount) * 100);
                                        }


                                        ?>
                                        <div class="col-lg-2">
                                            <div class="card bg-info" style="background: #218dbd;">
                                                <div class="stat-widget-six">
                                                    <div class="stat-icon p-15">
                                                        <i class="ti-direction-alt"></i>
                                                    </div>
                                                    <div class="stat-content p-t-12 p-b-12">
                                                        <div class="text-left dib">
                                                            <div class="stat-heading">Total</div>
                                                            <div class="stat-text"><?php echo $totalCount; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-lg-2">
                                            <div class="card bg-success" style="background: #0c6750cc;">
                                                <div class="stat-widget-six">
                                                    <div class="stat-icon p-15">
                                                        <i class="ti-stats-up"></i>
                                                    </div>
                                                    <div class="stat-content p-t-12 p-b-12">
                                                        <div class="text-left dib">
                                                            <div class="stat-heading">Success</div>
                                                            <div class="stat-text"><?php echo $countsArr['successCount'] . ' (' . $successCountPercenatage . ' %)'; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="card bg-danger" style="background: #e44535;">
                                                <div class="stat-widget-six">
                                                    <div class="stat-icon p-15">
                                                        <i class="ti-stats-down"></i>
                                                    </div>
                                                    <div class="stat-content p-t-12 p-b-12">
                                                        <div class="text-left dib">
                                                            <div class="stat-heading">Failure</div>
                                                            <div class="stat-text"><?php echo $countsArr['failureCount'] . ' ( ' . $failureCountPercenatage . ' %)'; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="card bg-info" style="background: #218dbd;">
                                                <div class="stat-widget-six">
                                                    <div class="stat-icon p-15">
                                                        <i class="ti-direction-alt"></i>
                                                    </div>
                                                    <div class="stat-content p-t-12 p-b-12">
                                                        <div class="text-left dib">
                                                            <div class="stat-heading">Email verified</div>
                                                            <div class="stat-text"><?php echo $countsArr['checkEmailCount'] . ' ( ' . $checkEmailsCountPercenatage . ' %)'; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /# card -->
                </div>

            </div>

        </div>
    </div>
</div>

<div class="content-wrap" style="margin-top: 0px !important;">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <div class="col-md-4">
                        <div id="total_chart" style="width: 100%; height: 350px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="gender_chart" style="width: 100%; height: 350px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="age_chart" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                    <div class="col-md-4">
                        <div id="rejection_chart" style="width: 100%; height: 350px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="city_chart" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
                <div class="row" style="display: none;">
                    <!-- /# column -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Rejection Detail</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rejection Type</th>
                                                <th>Count</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $totalRejectCount = $countsArr['failureCount'];
                                            $duplicateCount = $rejectDetailCountsArr['duplicateCount'];
                                            $blacklistCount = $rejectDetailCountsArr['blacklistCount'];
                                            $serverIssueCount = $rejectDetailCountsArr['serverIssue'];
                                            $apiKeyIsNotActiveCount = $rejectDetailCountsArr['apiKeyIsNotActive'];
                                            $emailIsRequiredCount = $rejectDetailCountsArr['emailIsRequired'];
                                            $phoneIsRequiredCount = $rejectDetailCountsArr['phoneIsRequired'];
                                            $emailIsBlankCount = $rejectDetailCountsArr['emailIsBlank'];
                                            $phoneIsBlankCount = $rejectDetailCountsArr['phoneIsBlank'];
                                            $invalidEmailFormatCount = $rejectDetailCountsArr['invalidEmailFormat'];
                                            $invalidPhoneCount = $rejectDetailCountsArr['invalidPhone'];
                                            $invalidGenderCount = $rejectDetailCountsArr['invalidGender'];
                                            $teliaMxBlockCount = $rejectDetailCountsArr['teliaMxBlock'];
                                            $luukkuMxBlockCount = $rejectDetailCountsArr['luukkuMxBlock'];
                                            $ppMxBlockCount = $rejectDetailCountsArr['ppMxBlock'];
                                            $alreadyUnsubscribedCount = $rejectDetailCountsArr['alreadyUnsubscribed'];
                                            $yahooMxBlockCount = $rejectDetailCountsArr['yahooMxBlock'];
                                            $icloudMxBlockCount = $rejectDetailCountsArr['icloudMxBlock'];
                                            $gmxMxBlockCount = $rejectDetailCountsArr['gmxMxBlock'];
                                            $duplicateOldCount = $rejectDetailCountsArr['duplicateOld'];
                                            $blacklistIPCount = $rejectDetailCountsArr['blacklistIP'];
                                            $protonmailMxBlockCount = $rejectDetailCountsArr['protonmailMxBlock'];
                                            $rejectByInboxgameCount = $rejectDetailCountsArr['rejectByInboxgame'];
                                            $rejectByIntegromatCount = $rejectDetailCountsArr['rejectByIntegromat'];
                                            $ageIsRequiredCount = $rejectDetailCountsArr['ageIsRequired'];
                                            $hotmailMxBlockCount = $rejectDetailCountsArr['hotmailMxBlock'];
                                            $outlookMxBlockCount = $rejectDetailCountsArr['outlookMxBlock'];
                                            $liveMxBlockCount = $rejectDetailCountsArr['liveMxBlock'];
                                            $msnMxBlockCount = $rejectDetailCountsArr['msnMxBlock'];


                                            if ($totalRejectCount > 0) {

                                                $duplicatePer = ($duplicateCount / $totalRejectCount) * 100;
                                                $duplicatePer = reformat_number_format($duplicatePer);

                                                $blacklistPer = ($blacklistCount / $totalRejectCount) * 100;
                                                $blacklistPer = reformat_number_format($blacklistPer);

                                                $serverIssuePer = ($serverIssueCount / $totalRejectCount) * 100;
                                                $serverIssuePer = reformat_number_format($serverIssuePer);

                                                $apiKeyIsNotActivePer = ($apiKeyIsNotActiveCount / $totalRejectCount) * 100;
                                                $apiKeyIsNotActivePer = reformat_number_format($apiKeyIsNotActivePer);

                                                $emailIsRequiredPer = ($emailIsRequiredCount / $totalRejectCount) * 100;
                                                $emailIsRequiredPer = reformat_number_format($emailIsRequiredPer);

                                                $phoneIsRequiredPer = ($phoneIsRequiredCount / $totalRejectCount) * 100;
                                                $phoneIsRequiredPer = reformat_number_format($phoneIsRequiredPer);

                                                $emailIsBlankPer = ($emailIsBlankCount / $totalRejectCount) * 100;
                                                $emailIsBlankPer = reformat_number_format($emailIsBlankPer);

                                                $phoneIsBlankPer = ($phoneIsBlankCount / $totalRejectCount) * 100;
                                                $phoneIsBlankPer = reformat_number_format($phoneIsBlankPer);

                                                $invalidEmailFormatPer = ($invalidEmailFormatCount / $totalRejectCount) * 100;
                                                $invalidEmailFormatPer = reformat_number_format($invalidEmailFormatPer);

                                                $invalidPhonePer = ($invalidPhoneCount / $totalRejectCount) * 100;
                                                $invalidPhonePer = reformat_number_format($invalidPhonePer);

                                                $invalidGenderPer = ($invalidGenderCount / $totalRejectCount) * 100;
                                                $invalidGenderPer = reformat_number_format($invalidGenderPer);

                                                $teliaMxBlockPer = ($teliaMxBlockCount / $totalRejectCount) * 100;
                                                $teliaMxBlockPer = reformat_number_format($teliaMxBlockPer);

                                                $luukkuMxBlockPer = ($luukkuMxBlockCount / $totalRejectCount) * 100;
                                                $luukkuMxBlockPer = reformat_number_format($luukkuMxBlockPer);

                                                $ppMxBlockPer = ($ppMxBlockCount / $totalRejectCount) * 100;
                                                $ppMxBlockPer = reformat_number_format($ppMxBlockPer);

                                                $alreadyUnsubscribedPer = ($alreadyUnsubscribedCount / $totalRejectCount) * 100;
                                                $alreadyUnsubscribedPer = reformat_number_format($alreadyUnsubscribedPer);

                                                $yahooMxBlockPer = ($yahooMxBlockCount / $totalRejectCount) * 100;
                                                $yahooMxBlockPer = reformat_number_format($yahooMxBlockPer);

                                                $icloudMxBlockPer = ($icloudMxBlockCount / $totalRejectCount) * 100;
                                                $icloudMxBlockPer = reformat_number_format($icloudMxBlockPer);

                                                $gmxMxBlockPer = ($gmxMxBlockCount / $totalRejectCount) * 100;
                                                $gmxMxBlockPer = reformat_number_format($gmxMxBlockPer);

                                                $duplicateOldPer = ($duplicateOldCount / $totalRejectCount) * 100;
                                                $duplicateOldPer = reformat_number_format($duplicateOldPer);

                                                $blacklistIPPer = ($blacklistIPCount / $totalRejectCount) * 100;
                                                $blacklistIPPer = reformat_number_format($blacklistIPPer);

                                                $protonmailMxBlockPer = ($protonmailMxBlockCount / $totalRejectCount) * 100;
                                                $protonmailMxBlockPer = reformat_number_format($protonmailMxBlockPer);

                                                $rejectByInboxgamePer = ($rejectByInboxgameCount / $totalRejectCount) * 100;
                                                $rejectByInboxgamePer = reformat_number_format($rejectByInboxgamePer);

                                                $rejectByIntegromatPer = ($rejectByIntegromatCount / $totalRejectCount) * 100;
                                                $rejectByIntegromatPer = reformat_number_format($rejectByIntegromatPer);

                                                $ageIsRequiredPer = ($ageIsRequiredCount / $totalRejectCount) * 100;
                                                $ageIsRequiredPer = reformat_number_format($ageIsRequiredPer);

                                                $hotmailMxBlockPer = ($hotmailMxBlockCount / $totalRejectCount) * 100;
                                                $hotmailMxBlockPer = reformat_number_format($hotmailMxBlockPer);

                                                $outlookMxBlockPer = ($outlookMxBlockCount / $totalRejectCount) * 100;
                                                $outlookMxBlockPer = reformat_number_format($outlookMxBlockPer);

                                                $liveMxBlockPer = ($liveMxBlockCount / $totalRejectCount) * 100;
                                                $liveMxBlockPer = reformat_number_format($liveMxBlockPer);

                                                $msnMxBlockPer = ($msnMxBlockCount / $totalRejectCount) * 100;
                                                $msnMxBlockPer = reformat_number_format($msnMxBlockPer);
                                            } else {

                                                $duplicatePer = 0;
                                                $blacklistPer = 0;
                                                $serverIssuePer = 0;
                                                $apiKeyIsNotActivePer = 0;
                                                $emailIsRequiredPer = 0;
                                                $phoneIsRequiredPer = 0;
                                                $emailIsBlankPer = 0;
                                                $phoneIsBlankPer = 0;
                                                $invalidEmailFormatPer = 0;
                                                $invalidPhonePer = 0;
                                                $invalidGenderPer = 0;
                                                $teliaMxBlockPer = 0;
                                                $luukkuMxBlockPer = 0;
                                                $ppMxBlockPer = 0;
                                                $alreadyUnsubscribedPer = 0;
                                                $yahooMxBlockPer = 0;
                                                $icloudMxBlockPer = 0;
                                                $gmxMxBlockPer = 0;
                                                $duplicateOldPer = 0;
                                                $blacklistIPPer = 0;
                                                $protonmailMxBlockPer = 0;
                                                $rejectByInboxgamePer = 0;
                                                $rejectByIntegromatPer = 0;
                                                $ageIsRequiredPer = 0;
                                                $hotmailMxBlockPer = 0;
                                                $outlookMxBlockPer = 0;
                                                $liveMxBlockPer = 0;
                                                $msnMxBlockPer = 0;
                                            }


                                            ?>
                                            <tr>
                                                <td>1</td>
                                                <td>Duplicate</td>
                                                <td><?php echo $duplicateCount; ?></td>
                                                <td><?php echo $duplicatePer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Blacklisted</td>
                                                <td><?php echo $blacklistCount; ?></td>
                                                <td><?php echo $blacklistPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Inactive API Key</td>
                                                <td><?php echo $apiKeyIsNotActiveCount; ?></td>
                                                <td><?php echo $apiKeyIsNotActivePer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Required Email</td>
                                                <td><?php echo $emailIsRequiredCount; ?></td>
                                                <td><?php echo $emailIsRequiredPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Required Phone</td>
                                                <td><?php echo $phoneIsRequiredCount; ?></td>
                                                <td><?php echo $phoneIsRequiredPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Blank Email</td>
                                                <td><?php echo $emailIsBlankCount; ?></td>
                                                <td><?php echo $emailIsBlankPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>Blank Phone</td>
                                                <td><?php echo $phoneIsBlankCount; ?></td>
                                                <td><?php echo $phoneIsBlankPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Invalid Email Format</td>
                                                <td><?php echo $invalidEmailFormatCount; ?></td>
                                                <td><?php echo $invalidEmailFormatPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>Invalid Phone Format</td>
                                                <td><?php echo $invalidPhoneCount; ?></td>
                                                <td><?php echo $invalidPhonePer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>Invalid Gender</td>
                                                <td><?php echo $invalidGenderCount; ?></td>
                                                <td><?php echo $invalidGenderPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td>Server Issue</td>
                                                <td><?php echo $serverIssueCount; ?></td>
                                                <td><?php echo $serverIssuePer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>12</td>
                                                <td>Telia MX Block</td>
                                                <td><?php echo $teliaMxBlockCount; ?></td>
                                                <td><?php echo $teliaMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>13</td>
                                                <td>Luukku MX Block</td>
                                                <td><?php echo $luukkuMxBlockCount; ?></td>
                                                <td><?php echo $luukkuMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>14</td>
                                                <td>PP MX Block</td>
                                                <td><?php echo $ppMxBlockCount; ?></td>
                                                <td><?php echo $ppMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>15</td>
                                                <td>User Already Unsubscribed Before</td>
                                                <td><?php echo $alreadyUnsubscribedCount; ?></td>
                                                <td><?php echo $alreadyUnsubscribedPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>16</td>
                                                <td>Yahoo MX Block</td>
                                                <td><?php echo $yahooMxBlockCount; ?></td>
                                                <td><?php echo $yahooMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>17</td>
                                                <td>Icloud MX Block</td>
                                                <td><?php echo $icloudMxBlockCount; ?></td>
                                                <td><?php echo $icloudMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>18</td>
                                                <td>GMX MX Block</td>
                                                <td><?php echo $gmxMxBlockCount; ?></td>
                                                <td><?php echo $gmxMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>19</td>
                                                <td>Duplicate Old</td>
                                                <td><?php echo $duplicateOldCount; ?></td>
                                                <td><?php echo $duplicateOldPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>20</td>
                                                <td>Blacklisted IP</td>
                                                <td><?php echo $blacklistIPCount; ?></td>
                                                <td><?php echo $blacklistIPPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>21</td>
                                                <td>Protonmail MX Block</td>
                                                <td><?php echo $protonmailMxBlockCount; ?></td>
                                                <td><?php echo $protonmailMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>22</td>
                                                <td>Reject By Inboxgame and Facebook Lead</td>
                                                <td><?php echo $rejectByInboxgameCount; ?></td>
                                                <td><?php echo $rejectByInboxgamePer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>23</td>
                                                <td>Reject By Integromat Lead</td>
                                                <td><?php echo $rejectByIntegromatCount; ?></td>
                                                <td><?php echo $rejectByIntegromatPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>24</td>
                                                <td>Required Age</td>
                                                <td><?php echo $ageIsRequiredCount; ?></td>
                                                <td><?php echo $ageIsRequiredPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>25</td>
                                                <td>Hotmail MX Block</td>
                                                <td><?php echo $hotmailMxBlockCount; ?></td>
                                                <td><?php echo $hotmailMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>26</td>
                                                <td>Protonmail MX Block</td>
                                                <td><?php echo $outlookMxBlockCount; ?></td>
                                                <td><?php echo $outlookMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>27</td>
                                                <td>Protonmail MX Block</td>
                                                <td><?php echo $liveMxBlockCount; ?></td>
                                                <td><?php echo $liveMxBlockPer . ' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>28</td>
                                                <td>Protonmail MX Block</td>
                                                <td><?php echo $msnMxBlockCount; ?></td>
                                                <td><?php echo $msnMxBlockPer . ' %'; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /# row -->

            </section>
        </div>
    </div>
</div>



<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Live Delivery List </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- live Delivery List -->
                                <div class="table-responsive" id="liveDeliveryList" style="display: none;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Msg</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Postcode</th>
                                                <th>City</th>
                                                <th>Country</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Birthday</th>
                                                <th>IP</th>
                                                <th>optin url</th>
                                                <th>optin date</th>
                                                <th>Source</th>
                                                <th>Tag</th>
                                                <th>Created Date</th>
                                                <!-- <th>Group Name</th>
                                                <th>Keyword</th> -->

                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>
                                        <tbody id="live_delivery_stat_data">
                                            <!-- table will load here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- inboxgame FacebookLead List -->
                                <div class="table-responsive" id="inboxgameFacebookLeadList" style="display: none;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Msg</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Signup Date</th>
                                                <th>timestamp</th>
                                                <th>Source</th>
                                                <th>IP</th>
                                                <th>Country</th>
                                                <th>Created Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inboxgame_facebooklead__stat_data">
                                            <!-- table will load here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /# row -->

            </section>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you want to delete this Entry?</strong></h5>

                    <a href="javascript:;" onclick="javascript:proceedDeleteEntry();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#deletePopup').modal('hide');" class="btn btn-primary">No</a>

                </div>

            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>/assets/js/bootstrap-select.js"></script>
<script src="<?php echo base_url(); ?>/assets/js/chart/loader.js"></script>
<?php $this->load->view('liveDeliveryStat/live_delivery_stat_data_script'); ?>
<?php $this->load->view('liveDeliveryStat/live_delivery_stat_chart_script'); ?>