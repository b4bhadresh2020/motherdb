<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo (@$headerTitle)?"$headerTitle : ".getConfigVal('siteTitle'):getConfigVal('siteTitle');?></title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <!-- <link rel="shortcut icon" href="<?php echo base_url();?>image/logo/Logo_Badger_admin.png"> -->
      <!-- Styles -->
    <link href="<?php echo base_url();?>assets/css/lib/chartist/chartist.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/themify-icons.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="<?php echo base_url();?>assets/css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="<?php echo base_url();?>assets/css/lib/weather-icons.css" rel="stylesheet" />
    <link href="<?php echo base_url();?>assets/css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/unix.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/dashboard.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/maleFemalePercentage.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/countryWiseKeywordPercentage.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/jquery-confirm.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/bootstrap-multiselect.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/custom.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/alertify.css" rel="stylesheet">
    

    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/fonts/fontawesome-webfont-fa.css" /> -->
    <script src="<?php echo base_url();?>assets/js/lib/jquery.min.js"></script>
</head>
<style type="text/css">
    .sidebar .nano-content > ul li.active > a.color-green::before {
        background: #4CAF50;
    }
    .sidebar .nano-content > ul > li.active > a{
        background: #245e79;
    }
    .sidebar .nano-content > ul > li.active > a.color-green{
        background: #3f5d18;
    }
</style>
<body>
    <?php 
        $color_blue = '#245e79'; 
        $color_green = '#3f5d18'; 
    ?>
    <div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
        <div class="nano">
            <div class="nano-content">
                <ul>
                    
                    <?php if(is_admin()) { ?>
                    <li class="label">Main</li>
                    <li class="<?php echo (@$load_page == 'dashboard')?"active":"";?>" title = "dashboard"><a href="<?php echo base_url('adminHome');?>"><i class="ti-home"></i> Dashboard </a>
                    </li>

                    <li class="label">User</li>
                   
                    <li class="<?php echo (@$load_page == 'user')?"active":"";?>" title = "Upload User CSV" ><a href="<?php echo base_url('user/manage');?>" ><i class="ti-files"></i> Upload User CSV </a> </li>

                    <li class="<?php echo (@$load_page == 'userlist')?"active":"";?>" title = "User List" ><a href="<?php echo base_url('userList/manage');?>"><i class="ti-user" ></i>User List </a> </li> 

                    <li class="<?php echo (@$load_page == 'employee')?"active":"";?>" title = "Employee" ><a href="<?php echo base_url('employee/manage');?>"><i class="ti-user" ></i>Employee List </a> </li> 

                    <li class="label">Enrichment</li>
                   
                    <li class="<?php echo (@$load_page == 'enrichment')?"active":"";?>" title = "Upload Enrichment CSV" ><a href="<?php echo base_url('enrichment/manage');?>" ><i class="ti-files"></i> Upload Enrichment CSV </a></li>

                    <li class="label">Salus</li>

                    <li class="<?php echo (@$load_page == 'saluslist')?"active":"";?>" title = "Salus List" ><a href="<?php echo base_url('salusList/manage');?>"><i class="ti-user" ></i>Salus List </a> </li>

                    <li class="label">Campaigns</li>
                   
                    <li class="<?php echo (@$load_page == 'campaign')?"active":"";?>" title = "campaign" ><a href="<?php echo base_url('campaign/manage');?>" ><i class="ti-announcement"></i> Create/Edit Campaign Name </a></li>


                    <li class="label">Click URL</li>
                        
                        <li class="<?php echo (@$load_page == 'createTracking')?"active":"";?>" title = "Create Links"><a href="<?php echo base_url('tracking/addEdit'); ?>" ><i class="ti-pencil-alt"></i> Create SMS Batch (Clickurl)</a></li>
                        <li class="<?php echo (@$load_page == 'csv_history')?"active":"";?>" title = "Csv History List"><a href="<?php echo base_url('csv_history/manage'); ?>" ><i class="ti-files"></i>CSV History </a></li>

                        <?php 
                            $is_hide_batch_user_data = getConfigVal('is_hide_batch_user_data'); 
                            if ($is_hide_batch_user_data == 'false') { ?>

                                <li class="<?php echo (@$load_page == 'trackingList')?"active":"";?>" title = "Review Links" ><a href="<?php echo base_url('tracking/manage'); ?>" ><i class="ti-eye"></i> Campaign User List Detail</a></li>        
                                
                            <?php } ?>

                        

                        <li class="<?php echo (@$load_page == 'campaign_sms_stat')?"active":"";?>" title = "Campaign sms stat" ><a href="<?php echo base_url('campaign_sms_stat/manage'); ?>" class="color-green" ><i class="ti-eye"></i> Campaign List SMS Stat</a></li>
                        <li class="<?php echo (@$load_page == 'batch_campaign_sms_stat')?"active":"";?>" title = "Campaign sms provider stat" ><a href="<?php echo base_url('batch_campaign_sms_stat/manage'); ?>" class="color-green" ><i class="ti-eye"></i> Campaign SMS Provider Stat</a></li>
                        <li class="<?php echo (@$load_page == 'batchstat')?"active":"";?>" title = "Group Clickers (batch)" ><a href="<?php echo base_url('batchstat/manage'); ?>" class="color-green" ><i class="ti-eye"></i> Group Clickers Stat (Batch)</a></li>
                        <li class="<?php echo (@$load_page == 'general_batch_stat')?"active":"";?>" title = "General Batch/Segement Stat" ><a href="<?php echo base_url('general_batch_stat/manage'); ?>" class="color-green" ><i class="ti-eye"></i> General Group Clickers Stat (Batch)</a></li>


                    <?php 
                        //get last record from live delivery undefine key data
                        $condition = array();
                        $is_single = TRUE;
                        $lastRecordData = GetAllRecord(LIVE_DELIVERY_UNDEFINED_KEY_DATA,$condition,$is_single,array(),array(),array(array('liveDeliveryUndefinedApiKeyDataId' => 'DESC')),'createdDate');

                        if (count($lastRecordData) > 0) {
                            $createdDate = $lastRecordData['createdDate'];
                            $currrentDate = date('Y-m-d H:i:s');
                            $hours = getDateTimeDiffInHours($createdDate,$currrentDate);
                        }else{
                            $hours = -1;
                        }

                    ?>

                    <li class="label">Live Delivery Section</li>
                   
                        <li class="<?php echo (@$load_page == 'liveDelivery')?"active":"";?>" title = "Live Delivery" ><a href="<?php echo base_url('liveDelivery/manage');?>" ><i class="ti-announcement"></i> Live Delivery </a></li>
                        <li class="<?php echo (@$load_page == 'liveDeliveryStat')?"active":"";?>" title = "Live Delivery Stat" ><a href="<?php echo base_url('liveDeliveryStat/manage');?>" class="color-green" ><i class="ti-announcement"></i> Live Delivery Stat </a></li>
                        <li class="<?php echo (@$load_page == 'liveDeliveryUndefinedApiKeyStat')?"active":"";?>" title = "Live Delivery Undefined APIkey Stat" ><a href="<?php echo base_url('liveDeliveryUndefinedApiKeyStat/manage');?>" <?php if($hours <= 24 && $hours >= 0){ echo 'class="color-red"'; }else{ echo 'class="color-green"'; } ?> ><i class="ti-announcement"></i> Live Delivery Undefined API Key Stat </a></li>
                        <li class="<?php echo (@$load_page == 'mailProviderStatistics')?"active":"";?>" title = "Live Provider Statistics" ><a href="<?php echo base_url('mailProviderStatistics');?>" class="color-green" ><i class="ti-announcement"></i> Live Provider Statistics </a></li>

                    <?php } ?>
                    
                    <li class="label">Email Unsubscribe</li>
                    
                        <li class="<?php echo (@$load_page == 'mailUnsubscribe')?"active":"";?>" title = "Email Unsubscribe" ><a href="<?php echo base_url('mailUnsubscribe'); ?>" ><i class="ti-timer"></i> Email Unsubscribe </a></li>
                    
                    <?php if(is_admin()) { ?>
                    <li class="label">Cronjob Status </li>
                        
                        <li class="<?php echo (@$load_page == 'cronStat')?"active":"";?>" title = "Cron Status" ><a href="<?php echo base_url('cronjobStat/manage'); ?>" ><i class="ti-timer"></i> User Cronjob </a></li>
                        <li class="<?php echo (@$load_page == 'enrichCronStat')?"active":"";?>" title = "Enrichment Cron Status" ><a href="<?php echo base_url('enrichmentCronjobStat/manage'); ?>" ><i class="ti-timer"></i>Enrichment Cronjob </a></li>
                        <li class="<?php echo (@$load_page == 'blacklistCronStat')?"active":"";?>" title = "Blacklist Cron Status" ><a href="<?php echo base_url('blacklistCronjobStat/manage'); ?>" ><i class="ti-timer"></i>Blacklist Cronjob </a></li>
                        <li class="<?php echo (@$load_page == 'cronjobProviderStat')?"active":"";?>" title = "Cron Provider Status" ><a href="<?php echo base_url('cronjobProviderStat/manage'); ?>" ><i class="ti-timer"></i> User Provider Cronjob </a></li>

                    <li class="label">Email Queue</li>

                        <li class="<?php echo (@$load_page == 'aweberQueue')?"active":"";?>" title = "Aweber Queue List" ><a href="<?php echo base_url('aweberQueue/manage'); ?>" ><i class="ti-timer"></i> Aweber Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'ongageQueue')?"active":"";?>" title = "Ongage Queue List" ><a href="<?php echo base_url('ongageQueue/manage'); ?>" ><i class="ti-timer"></i> Ongage Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'transmitviaQueue')?"active":"";?>" title = "Transmitvia Queue List" ><a href="<?php echo base_url('transmitviaQueue/manage'); ?>"><i class="ti-timer"></i> Transmitvia Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'constantContactQueue')?"active":"";?>" title = "ConstantContact Queue List" ><a href="<?php echo base_url('constantContactQueue/manage'); ?>"><i class="ti-timer"></i> Constant Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'sendgridQueue')?"active":"";?>" title = "Sendgrid Queue List" ><a href="<?php echo base_url('sendgridQueue/manage'); ?>"><i class="ti-timer"></i> Sendgrid Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'sendinblueQueue')?"active":"";?>" title = "Sendinblue Queue List" ><a href="<?php echo base_url('sendinblueQueue/manage'); ?>"><i class="ti-timer"></i> Sendinblue Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'sendpulseQueue')?"active":"";?>" title = "Sendpulse Queue List" ><a href="<?php echo base_url('SendpulseQueue/manage'); ?>"><i class="ti-timer"></i> Sendpulse Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'mailerliteQueue')?"active":"";?>" title = "Mailerlite Queue List" ><a href="<?php echo base_url('MailerliteQueue/manage'); ?>"><i class="ti-timer"></i> Mailerlite Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'mailjetQueue')?"active":"";?>" title = "Mailjet Queue List" ><a href="<?php echo base_url('MailjetQueue/manage'); ?>"><i class="ti-timer"></i> Mailjet Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'convertkitQueue')?"active":"";?>" title = "Convertkit Queue List" ><a href="<?php echo base_url('ConvertkitQueue/manage'); ?>"><i class="ti-timer"></i> Convertkit Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'marketingPlatformQueue')?"active":"";?>" title = "Marketing Platform Queue List" ><a href="<?php echo base_url('marketingPlatformQueue/manage'); ?>"><i class="ti-timer"></i> Marketing Platform Queue List </a></li>
                        <li class="<?php echo (@$load_page == 'activeCampaignQueue')?"active":"";?>" title = "Active Campaign Queue List" ><a href="<?php echo base_url('activeCampaignQueue/manage'); ?>"><i class="ti-timer"></i> Active Campaign Queue List </a></li>

                    <li class="label">Email Provider State </li>

                        <li class="<?php echo (@$load_page == 'aweber')?"active":"";?>" title = "Aweber State" ><a href="<?php echo base_url('providerState/aweber'); ?>" ><i class="ti-timer"></i> Aweber Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'transmitvia')?"active":"";?>" title = "Transmitvia State" ><a href="<?php echo base_url('providerState/transmitvia'); ?>" ><i class="ti-timer"></i> Transmitvia Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'constantcontact')?"active":"";?>" title = "Constant Contact State" ><a href="<?php echo base_url('providerState/constantcontact'); ?>" ><i class="ti-timer"></i> Constant Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'ongage')?"active":"";?>" title = "Ongage State" ><a href="<?php echo base_url('providerState/ongage'); ?>" ><i class="ti-timer"></i> Ongage Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'sendgrid')?"active":"";?>" title = "Sendgrid State" ><a href="<?php echo base_url('providerState/sendgrid'); ?>" ><i class="ti-timer"></i> Sendgrid Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'sendinblue')?"active":"";?>" title = "Sendinblue State" ><a href="<?php echo base_url('providerState/sendinblue'); ?>" ><i class="ti-timer"></i> Sendinblue Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'sendpulse')?"active":"";?>" title = "Sendpulse State" ><a href="<?php echo base_url('providerState/sendpulse'); ?>" ><i class="ti-timer"></i> Sendpulse Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'mailerlite')?"active":"";?>" title = "Mailerlite State" ><a href="<?php echo base_url('providerState/mailerlite'); ?>" ><i class="ti-timer"></i> Mailerlite Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'mailjet')?"active":"";?>" title = "Mailjet State" ><a href="<?php echo base_url('providerState/mailjet'); ?>" ><i class="ti-timer"></i> Mailjet Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'convertkit')?"active":"";?>" title = "Convertkit State" ><a href="<?php echo base_url('providerState/convertkit'); ?>" ><i class="ti-timer"></i> Convertkit Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'marketingPlatform')?"active":"";?>" title = "Marketing Platform State" ><a href="<?php echo base_url('providerState/marketingPlatform'); ?>" ><i class="ti-timer"></i> Marketing Platform Email Stats </a></li>
                        <li class="<?php echo (@$load_page == 'activeCampaign')?"active":"";?>" title = "Active Campaign State" ><a href="<?php echo base_url('providerState/activeCampaign'); ?>" ><i class="ti-timer"></i> Active Campaign Email Stats </a></li>

                    <li class="label">Stats </li>
                        
                        <li class="<?php echo (@$load_page == 'keywordPercentage')?"active":"";?>" title = "Keyword Percentage" ><a href="<?php echo base_url('keywordPercentage/manage'); ?>" class="color-green" ><i class="ti-stats-up"></i> Keyword % </a></li>
                        <li class="<?php echo (@$load_page == 'enrichResultPercentage')?"active":"";?>" title = "Result Percentage" ><a href="<?php echo base_url('enrichResultPercentage/manage'); ?>" class="color-green" ><i class="ti-stats-up"></i> Enrichment Result % </a></li>
                        <li class="<?php echo (@$load_page == 'countryWiseKeywordPercentage')?"active":"";?>" title = "Country Wisr Keyword Percentage" ><a href="<?php echo base_url('countryWiseKeywordPercentage/manage'); ?>" class="color-green"><i class="ti-stats-up"></i> Country Wise % By Keyword</a></li>
                        <li class="<?php echo (@$load_page == 'maleFemalePercentage')?"active":"";?>" title = "Male and Female Percentage" ><a href="<?php echo base_url('MaleFemalePercentage/manage'); ?>" class="color-green" ><i class="ti-stats-up"></i> Male / Female % </a></li>
                        
                    

                    <li class="label">History</li>
                        
                    <li class="<?php echo (@$load_page == 'history')?"active":"";?>" title = "History" ><a href="<?php echo base_url('history/manage'); ?>" ><i class="ti-book"></i> History</a></li>

                    <li class="label">Unsubscriber</li>
                        
                    <li class="<?php echo (@$load_page == 'unsubsriberList')?"active":"";?>" title = "Unsubscriber Users" ><a href="<?php echo base_url('unsubscribe/manage'); ?>" ><i class="ti-unlink"></i> Unsubscriber List</a></li>
                        

                    <li class="label">Black List</li>
                    <li class="<?php echo (@$load_page == 'blacklist')?"active":"";?>" title = "Blacklist Users" ><a href="<?php echo base_url('blacklist/manage'); ?>"><i class="ti-unlink"></i> Blacklist Users</a></li>
                    <li class="<?php echo (@$load_page == 'blackListUpload')?"active":"";?>" title = "Upload Black List CSV" ><a href="<?php echo base_url('blacklist/upload');?>"><i class="ti-files"></i> Upload Blacklist CSV </a></li>

                    <li class="label">Synchronise Section</li>
                    <li class="<?php echo (@$load_page == 'sync')?"active":"";?>" title="Synchronise"><a href="<?php echo base_url('synchronise/manage');?>"><i class="ti-timer"></i> Synchronise </a></li> 
                    
                    <li class="label">Delete Section</li>
                    <li class="<?php echo (@$load_page == 'delete')?"active":"";?>" title="Delete"><a href="<?php echo base_url('delete/manage');?>" ><i class="ti-trash"></i> Delete </a></li> 

                    <li class="label">Repost</li>
                    <li class="<?php echo (@$load_page == 'repost')?"active":"";?>"  title="Repost"><a href="<?php echo base_url('repost/addEdit');?>" ><i class="ti-export"></i> Repost</a></li>

                    <li class="label">System</li>
                    <li title="Logout"><a href="<?php echo base_url('adminHome/logout');?>"><i class="ti-power-off"></i> Logout</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- /# sidebar -->


    <div class="header">
        <div class="pull-left">
            <!-- <div class="logo"><a href="<?php echo base_url('adminHome');?>"><span><img src="<?php echo base_url();?>image/logo/Logo_Badger_admin.png" alt="" width="150"/></span></a></div> -->
            <div class="hamburger sidebar-toggle is-active">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </div>
        <div class="pull-right p-r-15">
            <ul>
                
                <li class="header-icon dib"><!-- <img class="avatar-img" src="assets/images/avatar/1.jpg" alt="" />  --><span class="user-avatar">
                    <?php 
                        $condition = array(
                            'adminId' => $this->session->userdata('adminId')
                        );
                        $getAdminUser = GetAllRecord(ADMINMASTER,$condition,true);
                        echo $getAdminUser['adminUname'];
                    ?> <i class="ti-angle-down f-s-10"></i></span>
                    <div class="drop-down dropdown-profile">
                        
                        <div class="dropdown-content-body">
                            <ul>
                                <li><a href="<?php echo base_url('changePassword');?>"><i class="ti-lock"></i> <span>Change Password</span></a></li>
                                <?php if(is_admin()){ ?>
                                <li><a href="<?php echo base_url('siteConfig');?>"><i class="ti-settings"></i> <span>Setting</span></a></li>
                                <?php } ?>
                                <li><a href="<?php echo base_url('logout');?>"><i class="ti-power-off"></i> <span>Logout</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            if ($('.active').length) {
                var active_height = $('.active').offset().top;
            }else{
                var active_height = 0;
            }
            
            if (active_height <= 710) {
                $(".nano").nanoScroller({ scrollTop: 0 });    
            }else if (active_height >= 710 && active_height < 1230){
                $(".nano").nanoScroller({ scrollTop: 390 });
            }else{
                $(".nano").nanoScroller({ scrollTop: active_height });
            }
        });
    </script>