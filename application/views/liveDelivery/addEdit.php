<?php
    
    $countries = getCountry();
    $aweberList = getProviderList(AWEBER);
    $transmitviaList = getProviderList(TRANSMITVIA);
    $constantContactList = getProviderList(CONSTANTCONTACT);
    $ongageList = getProviderList(ONGAGE);
    $sendgridList = getProviderList(SENDGRID);

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
        $delays = (array)json_decode($delay);
    }else{
        $delays = array();
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
                                                    <select name="identifier" class="form-control">
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
                                        </div>

                                        <div class="row providerBlockHeader" style="display:<?php echo counts($delays)?"block":"none"?>">
                                            <div class="col-md-3">
                                                <label>Aweber List Name</label>
                                            </div>                                                   
                                            <div class="col-md-3">
                                                <label>Delay (Day)</label>
                                            </div>                                                   
                                        </div>  
                                        <div class="providerBlock">    
                                            <?php if(counts($delays) > 0) {
                                                foreach($delays as $delayProvider => $delayDay){?>
                                                    <div id="<?php echo $delayProvider;?>" class="row provider_<?php echo $delayProvider; ?>" style="margin-top:5px;">
                                                        <div class="col-md-3">
                                                            <label class="pname"><?php echo $mailProviders[$delayProvider];?></label>
                                                        </div>                                                   
                                                        <div class="col-md-1">
                                                            <input type="number" name="delay[<?php echo $delayProvider;?>]" class="delay form-control" value="<?php echo $delayDay;?>"/>
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
         <div class="col-md-1">
            <input type="number" name="" class="delay form-control"/>
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
    });
</script>

