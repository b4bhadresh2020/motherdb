<?php 
    
    $condition = array('isInActive' => 0);
    $is_single = FALSE;
    $getApiKeys = GetAllRecord(LIVE_DELIVERY,$condition,$is_single,array(),array(),array(array('liveDeliveryId' => 'DESC')),'apikey,groupName,keyword');
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

                                            <div class="form-group">
                                                <label>Select Apikey (Group-Keyword)</label>
                                                <select name="apikey" id="apikey" class="form-control" >

                                                    <?php foreach ($getApiKeys as $value) { ?>
                                                        
                                                        <option value="<?php echo $value['apikey']; ?>" <?php if($value['apikey'] == @$_GET['apikey']){ echo 'selected'; } ?> ><?php echo $value['groupName'].'-'.$value['keyword'].' ('.$value['apikey'].')'; ?></option>

                                                    <?php } ?>

                                                </select>
                                            </div>
                                           
                                            <?php $filter = array(
                                                'all'   =>'All',
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
                                                <select name="chooseFilter" id="chooseFilter" class="form-control" >

                                                    <?php foreach ($filter as $key => $value) { ?>
                                                        
                                                        <option value="<?php echo $key; ?>" <?php if($key == @$_GET['chooseFilter']){ echo 'selected'; } ?> ><?php echo $value; ?></option>

                                                    <?php } ?>

                                                </select>
                                            </div>
                                            
                                            <div class="row" id="startEndDateDiv" style="display: none;">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input class="form-control" type="datetime-local" name="startDate" id="startDate" value = "<?php echo @$_GET['startDate']; ?>"  >
                                                    </div>     
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input class="form-control" type="datetime-local" name="endDate" id="endDate" value = "<?php echo @$_GET['endDate']; ?>">
                                                    </div>    
                                                </div>
                                            </div>
                                            <?php $sucFailTypeArr =  array('0' => 'Success', '1' => 'Duplicate', '2' => 'Blacklisted', '3' => 'Server Issue','4' => 'Api Key Is Not Active', '5' => 'Email Is Required', '6' => 'Phone Is Required', '7' => 'Email Is Blank', '8' => 'Phone Is Blank', '9' => 'Invalid Email Format', '10' => 'Invalid Phone', '11' => 'Invalid Gender'); ?>
                                            
                                            <div class="row" >
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Select Result Type</label> 
                                                        <select name="chooseSucFailRes" id="chooseSucFailRes" class="form-control" >

                                                            <option value="" <?php if(@$_GET['chooseSucFailRes'] == ''){ echo 'selected'; } ?> >All</option>
                                                            <?php
                                                                foreach ($sucFailTypeArr as $key => $value) {?>
                                                                    <option value="<?php echo $key; ?>" <?php if(@$_GET['chooseSucFailRes'] == "$key"){ echo 'selected'; } ?> ><?php echo $value; ?></option>            
                                                                <?php } ?>

                                                            
                                                            

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Search</label>
                                                        <input class="form-control" type="text" name="globleSearch" id="globleSearch" value = "<?php echo @$_GET['globleSearch']; ?>">
                                                    </div>    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <input type="submit" id="btn-submit" value="Submit" class="form-control btn btn-dark" >
                                                </div>
                                                <div class="col-lg-3">
                                                    <input type="submit"  name="reset" value="Reset" class="form-control btn btn-default" >
                                                </div>
                                            </div>
                                                
                                        </div>

                                        <?php 

                                            $totalCount = $countsArr['successCount'] + $countsArr['failureCount'];

                                            $successCountPercenatage = 0;
                                            $failureCountPercenatage = 0;
                                            if ($totalCount > 0) {
                                                $successCountPercenatage = reformat_number_format(($countsArr['successCount'] / $totalCount) * 100);
                                                $failureCountPercenatage = reformat_number_format(($countsArr['failureCount'] / $totalCount) * 100);
                                            }
                                            

                                        ?>
                                        <div class="col-lg-2">
                                            <div class="card bg-info" style="background: #218dbd;"  >
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
                                                            <div class="stat-text"><?php echo $countsArr['successCount'].' ('.$successCountPercenatage.' %)'; ?></div>
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
                                                            <div class="stat-text"><?php echo $countsArr['failureCount'].' ( '.$failureCountPercenatage.' %)'; ?></div>
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

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
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

                                            }else{

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
                                            }
                                            

                                            ?>
                                            <tr>
                                                <td>1</td>
                                                <td>Duplicate</td>
                                                <td><?php echo $duplicateCount; ?></td>
                                                <td><?php echo $duplicatePer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Blacklisted</td>
                                                <td><?php echo $blacklistCount; ?></td>
                                                <td><?php echo $blacklistPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Inactive API Key</td>
                                                <td><?php echo $apiKeyIsNotActiveCount; ?></td>
                                                <td><?php echo $apiKeyIsNotActivePer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Required Email</td>
                                                <td><?php echo $emailIsRequiredCount; ?></td>
                                                <td><?php echo $emailIsRequiredPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Required Phone</td>
                                                <td><?php echo $phoneIsRequiredCount; ?></td>
                                                <td><?php echo $phoneIsRequiredPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>Blank Email</td>
                                                <td><?php echo $emailIsBlankCount; ?></td>
                                                <td><?php echo $emailIsBlankPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>Blank Phone</td>
                                                <td><?php echo $phoneIsBlankCount; ?></td>
                                                <td><?php echo $phoneIsBlankPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>Invalid Email Format</td>
                                                <td><?php echo $invalidEmailFormatCount; ?></td>
                                                <td><?php echo $invalidEmailFormatPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>Invalid Phone Format</td>
                                                <td><?php echo $invalidPhoneCount; ?></td>
                                                <td><?php echo $invalidPhonePer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>Invalid Gender</td>
                                                <td><?php echo $invalidGenderCount; ?></td>
                                                <td><?php echo $invalidGenderPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>11</td>
                                                <td>Server Issue</td>
                                                <td><?php echo $serverIssueCount; ?></td>
                                                <td><?php echo $serverIssuePer.' %'; ?></td>
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
                                <div class="table-responsive">
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

<?php $this->load->view('liveDeliveryStat/live_delivery_stat_data_script'); ?>
