<?php
    
    $countries = getCountry();
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
    $activeCampaignList = getProviderList(ACTIVE_CAMPAIGN);

    $mailProviders = array(
        'egoi' => 'E-goi'
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
    foreach($sendPulseList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Sendpulse)";
    }

    // Add Mailerlite list in mail provider
    foreach($mailerliteList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Mailerlite)";
    }

    // Add Mailjet list in mail provider
     foreach($mailjetList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Mailjet)";
    }

    // Add Convertkit list in mail provider
     foreach($convertkitList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Convertkit)";
    }

    // Add Marketing Platform list in mail provider
    foreach($marketingPlatformList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Marketing Platform)";
    }

    // Add Active Campaign list in mail provider
    foreach($activeCampaignList as $list){
        $mailProviders[$list['id']] = $list['listname']. " (Active Campaign)";
    }

    $identifiers = array(
        'emailId' => 'Email',
        'phone'  => 'Phone'
    );

    if(isset($mailProvider) && !empty($mailProvider)){
        $mailProvider = json_decode($mailProvider);
    }else{
        $mailProvider = array();
    }

    if(isset($delay) && !empty($delay)){
        $delays = json_decode($delay,true);
    }else{
        $delays = array();
    }

    if(isset($isDuplicate) && !empty($isDuplicate)){
        $isDuplicate = json_decode($isDuplicate,true);
    }else{
        $isDuplicate = array();
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
                            <h1>Live Delivery</h1>
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
                                    <form method="post">

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>

                                        <?php if (@$suc_msg || @$error_msg) { 

                                            if (@$suc_msg) {
                                                $class = 'alert alert-success';
                                                $msg = $suc_msg;
                                            }else{
                                                $class = 'alert alert-danger';
                                                $msg = $error_msg;
                                            }

                                        ?>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class = '<?php echo $class; ?>' ><?php echo $msg; ?></div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Country * (Where to store data)</label>
                                                    <select name="country" class="form-control">
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $ctry) { ?>
                                                            <option value="<?php echo $ctry['country']; ?>" <?php if(@$country == $ctry['country']){ echo 'selected'; } ?> ><?php echo $ctry['country']; ?></option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Mail Provider *</label>
                                                    <select name="mailProvider[]" class="form-control " id="mailProvider" multiple="multiple">
                                                        <?php foreach ($mailProviders as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" <?php if(in_array($key,@$mailProvider)){ echo 'selected'; } ?>><?php echo $value; ?></option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Identifier * (To compare in our database)</label>
                                                    <select id="identifier" name="identifier" class="form-control">
                                                        <option value="">Select Identifier</option>
                                                        <?php foreach ($identifiers as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" <?php if(@$identifier == $key){ echo 'selected'; } ?> ><?php echo $value; ?></option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <span>Here is where the new data will be stored:</span>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Group Name *</label>
                                                    <input type="text" class="form-control"  name="groupName" value="<?php echo @$groupName; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Keyword *</label>
                                                    <input type="text" class="form-control"  name="keyword" value="<?php echo @$keyword; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Data Source *</label>
                                                    <input type="text" class="form-control"  name="dataSource" value="<?php echo @$dataSource; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>If user in these groups</label>
                                                    <input type="text" class="form-control"  name="ifUserInThisGroups" value="<?php echo @$ifUserInThisGroups; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Add the user in this group</label>
                                                    <input type="text" class="form-control"  name="addTheUserInThisGroup" value="<?php echo @$addTheUserInThisGroup; ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Enable to verify email id</label>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input id="checkEmail" type="checkbox" name="checkEmail" value="1" <?php echo (@$checkEmail)?"checked":""?>/>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Enable to check phone validation</label>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input id="checkPhone" type="checkbox" name="checkPhone" value="1" <?php echo (@$checkPhone)?"checked":""?>/>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>                                           
                                        </div>

                                        <div class="row providerBlockHeader" style="display:<?php echo counts($delays)?"block":"none"?>">
                                            <div class="col-md-3">
                                                <label>Aweber List Name</label>
                                            </div>                                                   
                                            <div class="col-md-3">
                                                <label>Delay (Day)</label>
                                            </div>                                                   
                                            <div class="col-md-3">
                                                <label>Only Add Duplicates</label>
                                            </div>                                                   
                                        </div>  
                                        <div class="providerBlock">    
                                            <?php if(counts($delays) > 0) {
                                                foreach($delays as $delayProvider => $delayDay){?>
                                                    <div id="<?php echo $delayProvider;?>" class="row provider_<?php echo $delayProvider; ?>" style="margin-top:5px;">
                                                        <div class="col-md-3">
                                                            <label class="pname"><?php echo $mailProviders[$delayProvider];?></label>
                                                        </div>                                                   
                                                        <div class="col-md-3">
                                                            <input type="number" name="delay[<?php echo $delayProvider;?>]" class="delay form-control" value="<?php echo $delayDay;?>"/>
                                                        </div>                                                   
                                                        <div class="col-md-1">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="isDuplicate[<?php echo $delayProvider;?>]" class="duplicate" value="1" <?php echo array_key_exists($delayProvider,$isDuplicate)?"checked":""?>/>
                                                                </label>
                                                            </div>    
                                                        </div>                                                   
                                                    </div>
                                            <?php } } ?>    
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <a href="<?php echo base_url('liveDelivery/manage'); ?>" class="btn btn-default" >Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>                
            </section>
        </div>
    </div>
</div>
<div class="newProvider" style="display:none">
    <div class="row" style="margin-top:5px;">
         <div class="col-md-3">
            <label class="pname"></label>
         </div>                                                   
         <div class="col-md-3">
            <input type="number" name="" class="delay form-control"/>
         </div>      
         <div class="col-md-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="" class="duplicate" value="1"/>
                </label>
            </div>    
        </div>                                             
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        $('#mailProvider').multiselect({
          includeSelectAllOption: true,
        });

        $('#mailProvider').on("change",function(){     
            var selectedProviders = $(this).val();
            if(selectedProviders !== null){
                var currentIndex = selectedProviders.length - 1;
            }else{
                var currentIndex = 0;
            }            
            jQuery('#mailProvider option:selected').each(function(index){                
                var value = $(this).val();
                var text = $(this).text();
                if($(".providerBlock").find(".provider_"+value).length == 0){                    
                    var newRecord = $(".newProvider .row").clone(); 
                    newRecord.addClass("provider_"+value).attr("id",value);  
                    newRecord.find(".pname").text(text);              
                    newRecord.find(".delay").attr("name","delay["+value+"]").val(currentIndex);              
                    newRecord.find(".duplicate").attr("name","isDuplicate["+value+"]").val(currentIndex);              
                    $(".providerBlock").append(newRecord);  
                }
            });   

            $(".providerBlock .row").each(function() {
                var providerID = $(this).attr("id");
                if($.inArray(providerID,selectedProviders) == -1){
                    $(this).remove();
                }
            });   
        });

        $(document).on("change","#identifier",function(){
            console.log("called");
            if($(this).val() == "phone"){
                $("#checkPhone").prop('checked', true);
            }else{
                $("#checkPhone").prop('checked', false);
            }
        });
    });
</script>

