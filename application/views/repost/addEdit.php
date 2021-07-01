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

// Add Expert Sender list in mail provider
foreach($expertSenderList as $list) {
    $mailProviders[$list['id']] = $list['listname']. " (Expert Sender)";
}

// Add Clever Reach list in mail provider
foreach($cleverReachList as $list) {
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
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="sucErrMsg"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="form-group custom-select">
                                                <label>API Key  (Group-Keyword) *</label>
                                                <select class="form-control selectpicker" name="country" id="select_apikey">
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
                                                <select class="form-control" name="mailprovider[]" id="select_mailproviders" multiple="multiple">
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
                                    
                                    <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                    <a href="<?php echo base_url('repost/addEdit'); ?>" class="btn btn-default" >Reset</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </section>
        </div>
    </div>
</div>

<?php
    $this->load->view('repost/addEdit_script');
?>
<script src="<?php echo base_url();?>/assets/js/bootstrap-select.js"></script>

