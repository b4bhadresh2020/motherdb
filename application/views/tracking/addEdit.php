<style>
/* Customize the label (the container) */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 15px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
<script src="<?php echo base_url();?>assets/js/lib/bootstrap3-typeahead.min.js"></script>

<?php 
    $allCountryCodes = getAllCountryCode(); 
    $allSmsApiProvider = getAllSmsApiProvider();
?>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Create / Export Click Url </h1>
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
                    <div class="col-lg-10">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="post" action="<?php echo $form_action; ?>" id = "tracking-form">

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>

                                        <?php if (@$suc_msg || @$error_msg) { 

                                            if (@$suc_msg) {
                                                $class = 'alert alert-success';
                                                $SucErrMsg = $suc_msg;
                                            }else{
                                                $class = 'alert alert-danger';
                                                $SucErrMsg = $error_msg;
                                            }

                                        ?>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class = '<?php echo $class; ?>' id = "postMsg" ><?php echo @$SucErrMsg; ?></div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                        <div class="row section-box">
                                            <div class="col-md-12">
                                                <h4 style="margin-bottom: 15px;">Filter Section: Following fields are responsible for getting data from our database</h4>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label>Country *</label>
                                                            <select class="form-control" name="country" id="country">
                                                                <option value="">Select Country</option>
                                                                <?php 
                                                                    foreach ($countries as $country) { ?>
                                                                        <option value="<?php echo $country['country']; ?>" <?php if(@$country == $country['country']){ echo 'selected'; } ?> > <?php echo $country['country']; ?></option>
                                                                <?php 
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label>Group Name</label>
                                                            <select class="form-control" name="groupName" id="groupName">
                                                                <option value = ''>All</option>
                                                                <?php foreach (@$groups as $value) { ?>
                                                                    <option value = "<?php echo $value['groupName']; ?>" <?php echo @$groupName == $value['groupName'] ? 'selected' : ''; ?> ><?php echo $value['groupName']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>                                                    
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Number of SMS </label>
                                                            <input class="form-control" type="number" name="numberOfSms" value="<?php echo @$numberOfSms; ?>" id="numberOfSms">
                                                        </div>   
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label>Min Age</label>
                                                            <input class="form-control" type="number" name="minAge" value="<?php echo @$minAge; ?>" id="minAge">
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label>Max Age</label>
                                                            <input class="form-control" type="number" name="maxAge" value="<?php echo @$maxAge; ?>" id="maxAge">
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label>Gender</label>
                                                            <select class="form-control" name="gender" id="gender">
                                                                <option value ="" <?php echo @$gender == '' ? 'selected' : ''; ?> >Both</option>
                                                                <option value ="male" <?php echo @$gender == 'male' ? 'selected' : ''; ?> >Male</option>
                                                                <option value ="female" <?php echo @$gender == 'female' ? 'selected' : ''; ?> >Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Active / Non-active</label>
                                                            <select class="form-control" name="type" id="type">
                                                                <option value ="" <?php echo @$type == '' ? 'selected' : ''; ?> >All</option>
                                                                <option value ="1" <?php echo @$type == '1' ? 'selected' : ''; ?> >Active</option>
                                                                <option value ="0" <?php echo @$type == '0' ? 'selected' : ''; ?> >Non Active</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Keyword</label>
                                                            <select class="form-control" name="keyword" id="keyword">
                                                                <option value = "">Select Keyword</option>
                                                                <?php foreach ($keywords as $ky) { ?>
                                                                    <option value = "<?php echo $ky['keyword']; ?>" <?php if(@$keyword == $ky['keyword']){ echo 'selected'; } ?> ><?php echo $ky['keyword']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>   
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Except Days <span title="This will avoid all the users that have received message on the given days.">(<i class="ti-help"></i>)</span></label>
                                                            <input class="form-control" type="number" name="exceptDays" value="<?php echo @$exceptDays; ?>" id="exceptDays" >
                                                        </div>   
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <label>Super Clickers</label>
                                                            <select class="form-control" name="superClickers" id="superClickers">
                                                                <option value ="" <?php echo @$type == '' ? 'selected' : ''; ?> >All</option>
                                                                <option value ="1" <?php echo @$type == '1' ? 'selected' : ''; ?> >1-3</option>
                                                                <option value ="2" <?php echo @$type == '2' ? 'selected' : ''; ?> >3-5</option>
                                                                <option value ="3" <?php echo @$type == '3' ? 'selected' : ''; ?> >5-10</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row section-box">
                                            <div class="col-md-12">
                                                <h4 style="margin-bottom: 15px;">Campaign Section: Following section is to apply <b>campaign</b> into the data that is filtered by above filter section <button class="btn btn-sm btn-primary slim-btn" type="button" data-toggle="modal" data-target="#campaign-explanation">Explain me</button></h4> 
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label>Campaign *</label>
                                                            <select class="form-control" name="campaignName" id="campaignName">
                                                                <option value = ''>Select Campaign</option>
                                                                <?php foreach (@$campaigns as $key => $value) { ?>
                                                                    <option value = "<?php echo $value['campaignId']; ?>" <?php echo @$campaignName == $value['campaignId'] ? 'selected' : ''; ?> data-is-new = "<?php echo $value['isNew']; ?>" ><?php echo $value['campaignName']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div> 
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label>Phone Numbers filteration</label>
                                                            <select class="form-control" name="phone" id=phone>
                                                                <option value ="" <?php echo @$phone == '' ? 'selected' : ''; ?>>All Numbers</option>
                                                                <option value ="1" <?php echo @$phone == '1' ? 'selected' : ''; ?> >Numbers that have not received the campaign before</option>
                                                                <option value ="0" <?php echo @$phone == '0' ? 'selected' : ''; ?> >Numbers that have received campaign before</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Models -->
                                            <div id="campaign-explanation" class="modal fade" role="dialog">
                                                <div class="modal-dialog dialog-with-scroll">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                                                            <h4 class="modal-title">How campaign works:</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>You can select your desired campaign, either new or old. but the important part is to understand <u>"Phone number filteration"</u>. Here I have tried to explain it by screenshots</p>

                                                            <p>For sake of simplicity, let us suppose that we are targetting <u>"Gambling"</u> campaign. Now there are two possibilities: 1) Campaign is new 2) Campaign is old</p>

                                                            <p><u>Possibility 1) Campaign is new</u><br>If campaign is new, then no user will be assiciated with that campaign. and all data that has been filtered by filter section will be applied for this campaign. At that time, you will see that "Phone numbers filteration" is disabled. because it is only applicable for old campaigns</p>

                                                            <p><u>Possibility 2) Campaign is old</u><br> For example, suppose there are three users who filtered by the filter section.</p>
                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Name</th>
                                                                        <th>Campaign</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Kare</td>
                                                                        <td>Gambling</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td>Hitesh</td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td>Alpesh</td>
                                                                        <td></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <p>As you can see that in the above filtered data, only first user "kare" is associated with Gambling campaign.</p>

                                                            <p>Now there are three options, for "Phone numbers filteration"</p>
                                                            <p><u>Option 1) All numbers</u><br>so if you select this, you will get all three users, and remaining two users will also associated with the gambling campaign. so the database will look like below:</p>

                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Name</th>
                                                                        <th>Campaign</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Kare</td>
                                                                        <td>Gambling</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td>Hitesh</td>
                                                                        <td>Gambling</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td>Alpesh</td>
                                                                        <td>Gambling</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <p><u>Option 2) Numbers that have not received campaign before</u><br>If you select this, then system will only target users that are not associated with gambling campaign. For our example, it will be "Hitesh and Alpesh":</p>

                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Name</th>
                                                                        <th>Campaign</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td>Hitesh</td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td>Alpesh</td>
                                                                        <td></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <p><u>Option 3) Numbers that have received campaign before</u><br>If you select this, then system will only target users that are already associated with gambling campaign. For our example, it will be "Kare":</p>

                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Name</th>
                                                                        <th>Campaign</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>Kare</td>
                                                                        <td>Gambling</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            <hr>

                                                            <p><b>Now important Q&A:</b></p>
                                                            <P><b>How you can send SMS to the users again?</b></p>
                                                            <p>- If you wish to send SMS to the same users from "gambling" campaign, you can select it from dropdown and choose "Phone numbers filteration => Numbers that have received campaign before".</p>

                                                            <hr>
                                                            <P><b>Where I can create campaign?</b></p>
                                                            <p>- Side menu ->  Create/Edit Campaign Name -> Press "+Add".</p>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>                                                

                                         <div class="row section-box">
                                            <div class="col-md-12">
                                                <h4 style="margin-bottom: 15px;">Group Clickers Section: Following section is to apply <b>group clickers</b> into the data that is filtered by above filter section <button class="btn btn-sm btn-primary slim-btn" type="button" data-toggle="modal" data-target="#group-clickers-explanation">Explain me</button></h4>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="radiobox">
                                                            <label>
                                                                <input type="radio" name="csvType" value="0" <?php echo @$csvType == 0 ? 'checked' : ''; ?> checked="checked"> Normal CSV
                                                            </label>
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="radiobox">
                                                            <label>
                                                                <input type="radio" name="csvType" value="1" <?php echo @$csvType == 1 ? 'checked' : ''; ?> > With Merge Tag
                                                            </label>
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="radiobox">
                                                            <label>
                                                                <input type="radio" name="csvType" value="2" <?php echo @$csvType == 2 ? 'checked' : ''; ?>> Without Merge Tag
                                                            </label>
                                                        </div>        
                                                    </div>
                                                </div>

                                                <!-- 
                                                    - Below is hidden field for trackclicker and Export/send to clickers
                                                    - Value of this input hidden box will change on clicking of red or green button in js
                                                 -->

                                                 <input type="hidden" id="trackclicker_and_export_to_clickers" value="0" />

                                                <hr style="border-top: 1px solid #757575;">

                                                <div class="row" id="smsDiv" style="display: <?php echo @$csvType == 1 || @$csvType == 2 ? 'block' : 'none' ; ?>;">
                                                    <div class="col-lg-12">
                                                        <div class="row" style="margin-bottom: 15px;">
                                                            <div class="col-lg-6">
                                                                <button type="button" class="btn btn-success" data-toggle-target="new-group-clickers" id="green_btn_track_clickers">Track Clickers</button>
                                                                <p>Note that, Here you can also select old group clickers. but it will not filter data based on group clickers you selected</p>
                                                                <p>When to use it? - </p>
                                                                <ul class="disc-style">
                                                                    <li>If you want to create fresh new group clickers</li>
                                                                    <li>If you want to select old group clickers but do not want to filter users. that means you can assign old group clickers to new users</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <button type="button" class="btn btn-danger" data-toggle-target="old-group-clickers" id="red_btn_export_send_to_clikers" >Export / Send to Clickers</button>
                                                                <p>Note that, Here whatever group clicker you will select, the data will be filtered based on that.</p>
                                                                <p>When to use it? - </p>
                                                                <ul class="disc-style">
                                                                    <li>If you want to send SMS to the same users who are related to this group clickers.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="row" id="new-group-clickers" data-toggle-group="clickers-option">
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>Group Clickers Name (Batch) *</label>
                                                                    <input type="text" class="type_ahead_batch_name form-control" id="batchName" name="batchName" autocomplete="off" />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>General Group Clickers Name (General Batch)</label>
                                                                    <input type="text" class="type_ahead_general_batch_name form-control" id="generalBatchName" name="generalBatchName" autocomplete="off" />
                                                                </div>   
                                                            </div>
                                                            <div class="col-lg-6">
                                                                    &nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="row" id="old-group-clickers" data-toggle-group="clickers-option">
                                                            <div class="col-lg-6">
                                                                &nbsp;
                                                            </div>

                                                            <!-- we dont know which is batch or which is general batch so concate with $ (name and table name) -->
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>Group clickers filteration * <button class="btn btn-sm btn-primary slim-btn" type="button" data-toggle="modal" data-target="#group-clickers-filteration-exp">Explain me</button></label>
                                                                    <select class="form-control" name="batchAndGeneralBatchName" id="batchAndGeneralBatchName">
                                                                        <option value = ''>Select Group Clickers</option>
                                                                        <?php foreach ($batchAndGeneralBatchArr as $value) { ?>
                                                                            <option value = "<?php echo $value['batchAndGeneralBatchIdentity'].'_&_'.$value['batchAndGeneralBatchName']; ?>"><?php echo $value['batchAndGeneralBatchName'];?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>

                                                                <!-- Models -->
                                                                <div id="group-clickers-filteration-exp" class="modal fade" role="dialog">
                                                                    <div class="modal-dialog dialog-with-scroll">
                                                                        <!-- Modal content-->
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                                                                                <h4 class="modal-title">What is Group clickers filteration:</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>This option will be disabled if campaign is new</p>
                                                                                <p>If you want to filter users by Group clickers, then you can select your descired clicker name from list. Note that, You can find both "Group clickers" and "General Group clickers" into the list</p>
                                                                                <p>When you select old clickers, you cannot choose to add new Group clickers id into this process</p>
                                                                            </div>

                                                                            

                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Campaign filteration <button class="btn btn-sm btn-primary slim-btn" type="button" data-toggle="modal" data-target="#group-clickers-camp-filteration-exp">Explain me</button></label>
                                                                    <select class="form-control" name="reTrackingCamapignFilter" id="reTrackingCamapignFilter" >
                                                                        <option value = '1'>Send to all users</option>
                                                                        <option value = '2'>Send to users that has received selected campaign before (But that campaign should be old)</option>
                                                                    </select>

                                                                    <!-- Models -->
                                                                    <div id="group-clickers-camp-filteration-exp" class="modal fade" role="dialog">
                                                                        <div class="modal-dialog dialog-with-scroll">
                                                                            <!-- Modal content-->
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                                                                                    <h4 class="modal-title">What is Campaign filteration:</h4>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>There are two options</p>
                                                                                    <p><u>1) Send to all users</u></p>
                                                                                    <p>If you select this, system will consider all the users who are associated with "Group clickers" you selected.</p>
                                                                                    <p><u>2) Send to users that has received selected campaign before (But that campaign should be old)</u></p>
                                                                                    <p>If you select this, System will filter all users based on both A) Group clickers and B) the campaign you selected.</p>
                                                                                </div>

                                                                                

                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </div>
                                                        </div>

                                                        <hr style="border-top: 1px solid #757575;">

                                                        <div class="row" style="margin-top: 15px;">
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>Message ( {url}, {unsubscribe_url} are requiered) *</label>
                                                                    <textarea class="form-control" name="msg" rows="5" id="msg"></textarea><p><span id="msg_remaining">160 characters remaining / </span> <span id="messages_count"> 1 sms</span></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>Redirect URL (True URL) *</label>
                                                                    <input type="text" class="form-control" id="redirectUrl" name="redirectUrl" >
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Models -->
                                                <div id="group-clickers-explanation" class="modal fade" role="dialog">
                                                    <div class="modal-dialog dialog-with-scroll">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
                                                                <h4 class="modal-title">How group clickers works:</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>As you can see there are three options</p>
                                                                <ul class="disc-style">
                                                                    <li>1) Normal CSV</li>
                                                                    <li>2) With Merge tag</li>
                                                                    <li>3) Without Merge tag</li>
                                                                </ul>
                                                                <p><u>1) Normal CSV:</u><br>This option will only export the filtered data as CSV.</p>
                                                                <p><u>2) With Merge Tag & 3) Without Merge Tag:</u><br>When you select this option, you can able to see new panel underneath. There you can configure <u>"Group clickers and General Group clickers"</u>, and also define message for SMS.</p>
                                                                <hr>
                                                                <p><b>Now important Q&A:</b></p>
                                                                <P><b>What is "Group clickers"?</b></p>
                                                                <p>- What ever data you filter from filter section, those data tagged by Group clicker that you define. For example, if you select/write <b>"casino clickers"</b>, then data will be look like this:</p>

                                                                <table class="table table-striped table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Name</th>
                                                                            <th>Group clickers</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>1</td>
                                                                            <td>Kare</td>
                                                                            <td>casino clickers</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>2</td>
                                                                            <td>Hitesh</td>
                                                                            <td>casino clickers</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>3</td>
                                                                            <td>Alpesh</td>
                                                                            <td>casino clickers</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <p>Note that, it is spelling sensitive, so that we have implemented auto-complete feature to avoid spelling mistake</p>

                                                                <hr>
                                                                <p><b>What is "General Group clickers"?</b></p>
                                                                <p>- It works similar to group clickers. It is <u>"non mandatory"</u> field. Filtered data also tagged by general group clickers. For example, if you select/write <b>"casino clickers"</b> in group clickers, and <b>"clickers"</b> in general grouop clickers, then data will be look like this:</p>

                                                                <table class="table table-striped table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Name</th>
                                                                            <th>Group clickers</th>
                                                                            <th>General Group clickers</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>1</td>
                                                                            <td>Kare</td>
                                                                            <td>casino clickers</td>
                                                                            <td>clickers</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>2</td>
                                                                            <td>Hitesh</td>
                                                                            <td>casino clickers</td>
                                                                            <td>clickers</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>3</td>
                                                                            <td>Alpesh</td>
                                                                            <td>casino clickers</td>
                                                                            <td>clickers</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <p>Note that, it is spelling sensitive, so that we have implemented auto-complete feature to avoid spelling mistake</p>

                                                                <hr>
                                                                <p><b>Why there are two fields like "Group clickers" and "General Group clickers"? What is purpose of "General Group clickers"?</b></p>
                                                                <p>- Reasons that we need to add extra field as "General group clickers" are as follows:</p>
                                                                <ul class="disc-style">
                                                                    <li>It is not possible to tell system that what is general clickers without giving it specification.</li>
                                                                    <li>Adding additional field will make system to filter and modify data faster</li>
                                                                    <li>To avoid spelling related mistakes</li>
                                                                    <li>To calculate stats faster</li>
                                                                </ul>
                                                            </div>

                                                            

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row section-box">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Domain Name </label>
                                                    <select class="form-control" name="domain" id="domain">
                                                        <option value="http://hoi3.com/">hoi3.com</option>
                                                        <option value="http://kliknu.me/">kliknu.me</option>
                                                        <option value="http://klikk.be/">klikk.be</option>
                                                        <option value="http://klikkaa.me/">klikkaa.me</option>
                                                        <option value="http://cas1no.me/">cas1no.me</option>
                                                        <option value="http://klicka.co/">klicka.co</option>
                                                        <option value="http://bitl.io/">bitl.io</option>
                                                        <option value="http://freesp.me/">freesp.me</option>
                                                        <option value="http://sp1nn.me/">sp1nn.me</option>
                                                        <!-- <option value="http://clik.tips/">clik.tips</option> -->
                                                        <option value="http://viply.vip/">viply.vip</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Unsubscribe Domain Name </label>
                                                    <select class="form-control" name="unsubscribeDomain" id="unsubscribeDomain">
                                                        <option value="">--Select--</option>
                                                        <option value="http://hoi3.com/">hoi3.com</option>
                                                        <option value="http://kliknu.me/">kliknu.me</option>
                                                        <option value="http://klikk.be/">klikk.be</option>
                                                        <option value="http://klikkaa.me/">klikkaa.me</option>
                                                        <option value="http://cas1no.me/">cas1no.me</option>
                                                        <option value="http://klicka.co/">klicka.co</option>
                                                        <option value="http://bitl.io/">bitl.io</option>
                                                        <option value="http://freesp.me/">freesp.me</option>
                                                        <option value="http://sp1nn.me/">sp1nn.me</option>
                                                        <!-- <option value="http://clik.tips/">clik.tips</option> -->
                                                        <option value="http://viply.vip/">viply.vip</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="addCountryCodeToBatch" value="1" id="addCountryCodeToBatch" <?php echo @$mergeTag == 1 ? 'checked' : '' ;?> > Add country code to batch (if you select this, then exported file will have country code applied.)
                                                    </label>
                                                </div>        
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-success" id="btn_export">Export</button>
                                                <a href="<?php echo base_url('tracking/addEdit'); ?>" class="btn btn-default" >Reset</a>
                                                <button type="button" id="send_sms_motherdb" class="btn btn-warning">Send SMS Motherdb</button>
                                                <button type="button" id="test_single_sms" onclick="open_test_sms_modal();" class="btn btn-info">Test Single SMS</button>
                                            </div>
                                        </div>
                                        

                                        <div class="row section-box" style="margin-top:25px; display: none;" id="send-sms-motherdb-part">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h4 style="margin-bottom: 15px;">SMS Integration Section: Following section is to send sms directly from motherdb <!-- <button class="btn btn-sm btn-primary slim-btn" type="button" data-toggle="modal" data-target="#group-clickers-explanation">Explain me</button> --></h4>    
                                                </div>
                                                
                                            </div>                                            
                                            <hr style="border-top: 1px solid #757575;">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <h4>Broadcast</h4>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="radiobox">
                                                        <label>
                                                            <input type="radio" name="send_sms_broadcast_type" data-toggle-target="send-now-part" value="1" > Send Now
                                                        </label>
                                                    </div>        
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="radiobox">
                                                        <label>
                                                            <input type="radio" name="send_sms_broadcast_type" value="2" data-toggle-target="specific-time-part"> Specific Time
                                                        </label>
                                                    </div>        
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="radiobox">
                                                        <label>
                                                            <input type="radio" name="send_sms_broadcast_type" value="3" data-toggle-target="split-sms-part" > Split SMS
                                                        </label>
                                                    </div>        
                                                </div>
                                            </div>
                                            <hr style="border-top: 1px solid #757575;">
                                            <div class="row" id="send-now-part" data-toggle-group="broadcast-radio-option">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Sender Id *</label>
                                                        <input type="text" class="form-control" name="send_now_sender_id" id="send_now_sender_id" />
                                                    </div> 

                                                    <div class="form-group">
                                                        <select name="send_now_service_provider" class="form-control" id="send_now_service_provider">
                                                            <option value = ''>Select Provider</option>
                                                            <?php foreach ($allSmsApiProvider as $key => $value) { ?>
                                                                <option value = <?php echo $key; ?> ><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>    
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="specific-time-part" data-toggle-group="broadcast-radio-option">
                                                <div class="col-lg-6"> 
                                                    <div class="form-group">
                                                        <label>Sender Id *</label>
                                                        <input type="text" class="form-control" id="specific_time_sender_id" name="specific_time_sender_id" />
                                                    </div> 
                                                    <div class="form-group">
                                                        <select name="specific_time_service_provider" class="form-control" id="specific_time_service_provider">
                                                            <option value = ''>Select Provider</option>
                                                            <?php foreach ($allSmsApiProvider as $key => $value) { ?>
                                                                <option value = <?php echo $key; ?> ><?php echo $value; ?></option>
                                                            <?php } ?>
                                                        </select>    
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Specific Time</label>
                                                        <input type="datetime-local" name="broadcast_specific_time" class="form-control" id="broadcast_specific_time"> 
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="split-sms-part" data-toggle-group="broadcast-radio-option">
                                                <div class="col-lg-4"> 
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label>Split Parts</label>
                                                                <input type="number" id="broadcast_split_number" class="form-control" min="1" > 
                                                                <input type="button" name="btn_split_part" id="btn_split_part" class="btn btn-info" value="Make Parts" style="margin-top: 10px;">
                                                            </div>        
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8"> 
                                                    <div id="split_parts_div">
                                                        <!-- dynamic spit part will add here -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="throttleBlock" class="throttleBlock" style="display:none">
                                                <hr style="border-top: 1px solid #757575;">
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <label class="container">
                                                            Throttle sending    
                                                            <input name="throttle" id="throttle" type="checkbox">
                                                            <span class="checkmark"></span>                          
                                                        </label>
                                                    </div> 
                                                    <div class="col-lg-2 sendoverBlock" style="display:none">
                                                        <div class="form-group">
                                                            <label>Spread send over </label>
                                                            <select class="form-control" name="sendover" id="sendover">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                            </select>
                                                        </div>
                                                    </div>                                               
                                                </div>
                                                <div class="row">
                                                    
                                                </div>
                                            </div>    
                                            <input type="button" name="set_broadcast_live" id="set_broadcast_live" value="Set SMS Broadcast Live" class="btn btn-success">
                                        </div>

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


<!-- Models -->
<div class="modal fade" id="testSmsPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document">
        <div class="modal-content">
            <div class="modal-body" style="background: #dcdcdc;">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Send test sms to a single number</strong></h5>

                    <div id="test_sms_err_suc_msg"></div>
                    
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <select id="test_popup_prefix" class="form-control">
                                            <option value = ''>Select Prefix</option>
                                            <?php foreach ($allCountryCodes as $key => $value) { ?>
                                                <option value = <?php echo $value; ?> ><?php echo '+'.$value; ?></option>
                                            <?php } ?>
                                        </select>    
                                    </div>    
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="number" id="test_popup_mobile_number" placeholder="Mobile Number *" name="test_popup_mobile_number" class="form-control" />    
                                    </div>    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="test_popup_sender_id" id="test_popup_sender_id" placeholder="Sender Id *" />
                                    </div>         
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <select id="test_popup_service_provider" class="form-control">
                                            <option value = ''>Select Provider *</option>
                                            <?php foreach ($allSmsApiProvider as $key => $value) { ?>
                                                <option value = <?php echo $key; ?> ><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>    
                                    </div>        
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Message ( {url}, {unsubscribe_url} are requiered) *</label>
                                        <textarea class="form-control" id="test_msg" name="test_msg" rows="5" placeholder="Test Message *"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <a href="javascript:;" onclick="javascript:proceedSendTestSms();" class="btn btn-success btn-delete">Send</a>
                    <a href="javascript:;" onclick="$('#testSmsPopup').modal('hide');" class="btn btn-primary">Cancel</a>

                </div>
                
            </div>
        </div>
    </div>
</div>

<?php
    $this->load->view('tracking/addEdit_script');
?>

