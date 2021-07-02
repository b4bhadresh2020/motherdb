<?php 
$aweberList = getProviderList(AWEBER);
$transmitviaList = getProviderList(TRANSMITVIA);
$constantContactList = getProviderList(CONSTANTCONTACT);
$ongageList = getProviderList(ONGAGE);
$sendgridList = getProviderList(SENDGRID);
$sendInBlueList = getProviderList(SENDINBLUE);
$sendPulseList = getProviderList(SENDPULSE);
$mailerliteList = getProviderList(MAILERLITE);
$mailjetList = getProviderList(MAILJET);
$convertkitList = getProviderList(CONVERTKIT);
$marketingPlatformList = getProviderList(MARKETING_PLATFORM);
$ontraportList = getProviderList(ONTRAPORT);
$activeCampaignList = getProviderList(ACTIVE_CAMPAIGN);
$expertSenderList = getProviderList(EXPERT_SENDER);
$cleverReachList = getProviderList(CLEVER_REACH);

$mailProviders = array(    
    'egoi' => 'E-goi',
);

// Add aweber list in mail provider
foreach($aweberList as $list){
    $mailProviders[$list['id']] = $list['listname']." (Aweber)";
}

// Add transmitvia list in mail provider
foreach($transmitviaList as $list){
    $mailProviders[$list['id']] = $list['listname']." (Transmitvia)";
}

// Add constant contact list in mail provider
foreach($constantContactList as $list){
    $mailProviders[$list['id']] = $list['listname']." (CC)";
}

// Add Ongage list in mail provider
foreach($ongageList as $list){
    $mailProviders[$list['id']] = $list['listname']." (Ongage)";
}

// Add Sendgrid list in mail provider
foreach($sendgridList as $list){
    $mailProviders[$list['id']] = $list['listname']." (Sendgrid)";
}

// Add Sendinblue list in mail provider
foreach($sendInBlueList as $list){
    $mailProviders[$list['id']] = $list['listname']." (Sendinblue)";
}

// Add Sendpulse list in mail provider
foreach($sendPulseList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Sendpulse)";
}

// Add Mailerlite list in mail provider
foreach($mailerliteList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Mailerlite)";
}

// Add Mailjet list in mail provider
foreach($mailjetList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Mailjet)";
}

// Add Convertkit list in mail provider
foreach($convertkitList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Convertkit)";
}

// Add Marketing Platform list in mail provider
foreach($marketingPlatformList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Marketing Platform)";
}

// Add Ontraport list in mail provider
foreach($ontraportList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Ontraport)";
}

// Add Active Campaign list in mail provider
foreach($activeCampaignList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Active Campaign)";
}

// Add expert sender list in mail provider
foreach($expertSenderList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Expert Sender)";
}

// Add Clever Reach list in mail provider
foreach($cleverReachList as $list){
    $mailProviders[$list['id']] = $list['listname']. " (Clever Reach)";
}
?>
<style>
    .btn-group{
        display: block !important;
    }
    .multiselect{
        min-width: 100%;
        padding: 10px;
        background: #fff;
        color: #000;
        text-align: left;
    }
    .caret{
        float: right;
        margin-top: 7px;
        border-top: 7px solid;
    }
</style>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Send data to email marketing</h1>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
                <div class="col-lg-4 p-l-0 title-margin-left">
                    <div class="page-header">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="<?php echo base_url(); ?>">Dashboard</a></li>
                                <li class="active"><?php echo $headerTitle; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
            </div>
            <!-- /# row -->
            <section id="main-content">
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="post" id="repostScheduleForm" action="<?php echo base_url('repostSchedule/addRepostSchedule'); ?>">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="form-group custom-select">
                                                    <label>API Key  (Group-Keyword) *</label>
                                                    <select class="form-control selectpicker" name="apiKey" id="select_apikey">
                                                        <option value="">Select Api Key</option>
                                                        <?php foreach ($apikeys as $country => $apikeyGroup){?>
                                                            <optgroup label="<?php echo $country; ?>"> 
                                                                <?php 
                                                                    foreach ($apikeyGroup as $apikey) {     
                                                                    $providers = json_decode($apikey['mailProvider']);
                                                                    $providersName = array();
                                                                    foreach($providers as $provider){
                                                                        $providersName[] = $mailProviders[$provider];
                                                                    }
                                                                    $providersIdString = implode(",",$providers);
                                                                    $providersNameString = implode(", ",$providersName);  
                                                                    
                                                                    if($apikey['live_status'] == 1){
                                                                        $liveDeliveryStatusHighlight = "green";
                                                                    }else if($apikey['live_status'] == 2){
                                                                        $liveDeliveryStatusHighlight = "orange";
                                                                    }else{
                                                                        $liveDeliveryStatusHighlight = "red";
                                                                    } 
                                                                    
                                                                ?>
                                                                <option value="<?php echo $apikey['apikey']; ?>" data-provider="<?php echo $providersNameString; ?>" data-providerid="<?php echo $providersIdString; ?>" style="color:<?php echo $liveDeliveryStatusHighlight; ?>"> <?php echo $apikey['groupName'].'-'.$apikey['keyword'].' ('.$apikey['apikey'].')'; ?></option>
                                                                <?php } ?>
                                                            </optgroup> 
                                                        <?php  } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Mail Provider *</label>
                                                    <select class="form-control" name="providers[]" id="select_mailproviders" multiple="multiple">
                                                        <?php 
                                                            foreach ($mailProviders as $providerKey => $providerName) {                                
                                                        ?>
                                                        <option value="<?php echo $providerKey; ?>"> <?php echo $providerName ?></option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Start Date</label>
                                                    <input class="form-control" type="date" id="deliveryStartDate" name="deliveryStartDate" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>End Date</label>
                                                    <input class="form-control" type="date" id="deliveryEndDate" name="deliveryEndDate" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Start Time</label>
                                                    <input class="form-control" type="time" id="deliveryStartTime" name="deliveryStartTime" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>End Time</label>
                                                    <input class="form-control" type="time" id="deliveryEndTime" name="deliveryEndTime" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Per Day Limit *</label>
                                                    <input type="number" class="form-control" id="perDayRecord" name="perDayRecord">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group custom-select">
                                                    <label>Live Delivery Status</label>
                                                    <select class="form-control selectpicker" name="liveDeliveryStatus" id="liveDeliveryStatus">
                                                        <option value="">Select status</option>
                                                        <option value="2">All</option>
                                                        <option value="0">Success</option>
                                                        <option value="1">Duplicates</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <a href="<?php echo base_url('repostSchedule/addEdit'); ?>" class="btn btn-default" >Reset</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                 <!-- /# row -->
            <section id="main-content">
                <div class="row">
                    
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <h4>Repost Schedule List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>API Key</th>
                                                <th>Total Records</th>
                                                <th>Send Records</th>
                                                <th>Provider</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Perday Records</th>
                                                <th>Live Delivery Status</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    $i++;
                                                    $repostScheduleId = $curEntry['id'];
                                                    $status = '';
                                                    $class = '';
                                                   

                                                    if ($curEntry['status'] == '0') {
                                                        $status = 'Deactive'; 
                                                        $action = 'Active';
                                                        $class = "btn btn-success";
                                                        $url = 'updateStatus/'.$curEntry["id"].'/1';                                                   
                                                    }else if($curEntry['status'] == '1'){
                                                        $status = 'Active';
                                                        $action = 'Deactive';
                                                        $class = "btn btn-danger"; 
                                                        $url = 'updateStatus/'.$curEntry["id"].'/0';
                                                    }else if($curEntry['status'] == '2'){
                                                        $status = 'Completed';
                                                        $action = 'Complete';
                                                        $class = "btn btn-primary";
                                                        $url = '#'; 
                                                    }

                                                    if ($curEntry['liveDeliveryStatus'] == '0') {
                                                        $liveDeliveryStatus = "Success";
                                                    }else if($curEntry['liveDeliveryStatus'] == '1'){
                                                        $liveDeliveryStatus = "Duplicates";
                                                    }else{
                                                        $liveDeliveryStatus = "All";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["apiKey"]; ?></td>
                                                        <td><?php echo $curEntry["totalRecord"]; ?></td>
                                                        <td>
                                                            <span class="send-count"><?php echo $curEntry["totalSendRecords"];?></span>
                                                            <span class="error-count"><?php echo $curEntry['totalErrorRcords'];?></span>
                                                        </td>
                                                        <td><?php echo $curEntry["providers"]; ?></td>
                                                        <td><?php echo date('d-m-Y',strtotime($curEntry["deliveryStartDate"])); ?></td>
                                                        <td><?php echo date('d-m-Y',strtotime($curEntry["deliveryEndDate"])); ?></td>
                                                        <td><?php echo date('H:i',strtotime($curEntry["deliveryStartTime"])); ?></td>
                                                        <td><?php echo date('H:i',strtotime($curEntry["deliveryEndTime"])); ?></td>
                                                        <td><?php echo $curEntry["perDayRecord"]; ?></td>
                                                        <td><?php echo $liveDeliveryStatus; ?></td>
                                                        <td><?php echo $status; ?></td>
                                                        <td>
                                                            <?php if($curEntry['status'] == 0) { ?>  
                                                                <a href="javascript:void(0);" class="btn btn-primary" data-repostScheduleId = '<?php echo $repostScheduleId; ?>' onclick="javascript:loadrepostSchedule(this);">edit</a>
                                                            <?php } ?>
                                                            <a href="<?php echo $url; ?>" class="<?php echo $class; ?>"><?php echo $action; ?></a>
                                                        </td>
                                                    </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 " style="padding-top: 25px;">
                                        <div class="datatable_pageinfo"><?php echo @$pageinfo; ?></div>
                                    </div>
                                    <div class="col-lg-6 ">
                                        <div class="paginate_links pull-right"><?php echo @$links; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /# row -->

            </section>
            </section>
        </div>
    </div>
</div>

<!-- START:: Edit repost schedule -DAB -->
<div class="modal fade" id="editDataPopupRepostSchedule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <form name="updateForm" method="post" action="<?php echo site_url('/repostSchedule/updateRepostScheduleData')?>">                
                    <div class="x_panel" style="border: none;">
                        <h5><strong>Update Repost Schedule Information</strong></h5>
                        <input type="hidden" name="id" id="repostScheduleId"/>
                        <div class="row">                           
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Start Time *</label>
                                    <input type="time" class="form-control" id="editDeliveryStartTime" name="deliveryStartTime">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>End Time *</label>
                                    <input type="time" class="form-control" id="editDeliveryEndTime" name="deliveryEndTime">
                                </div>
                            </div>  
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Per Day Limit *</label>
                                    <input type="number" class="form-control" id="editperDayRecord" name="perDayRecord">
                                </div>
                            </div>                                                                            
                        </div>    
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="submit" class="btn btn-success">Update</button>
                                <a href="javascript:;" onclick="$('#editDataPopupRepostSchedule').modal('hide');" class="btn btn-primary">Close</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END:: Edit repost schedule -DAB -->
<?php
    $this->load->view('repostSchedule/addEdit_script');
?>
<script src="<?php echo base_url();?>/assets/js/bootstrap-select.js"></script>

