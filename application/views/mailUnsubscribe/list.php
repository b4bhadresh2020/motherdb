<style>
    .multiselect-dropdown .btn-group {
        width: 100%;
    }
    .multiselect-dropdown .multiselect.dropdown-toggle {
        width: 100%;
        text-align: left;       
    }
    .multiselect-dropdown .multiselect.dropdown-toggle  .multiselect-selected-text {
        max-width: 100%;
        display: inline-block;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .multiselect-dropdown .multiselect.dropdown-toggle .caret {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%)
    }
    .multiselect-dropdown .multiselect-container {
        width: 100%;
        overflow-x: hidden;
    }
    .multiselect-dropdown .multiselect-container>li>a>label {
        white-space: pre-line;
    }
</style>
<?php 
$countries = getCountry();
?>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Mail Unsubscriber List</h1>
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
                <form class="form-horizontal" method="get" action="<?php echo base_url('mailUnsubscribe/getUnsubscriberData/0'); ?>">             
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Provider *</label>
                                                        <select class="form-control" name="provider" id="provider">
                                                            <option value="0">Select Provider</option>                                                        
                                                            <option value="1">Aweber</option>                                                        
                                                            <!-- <option value="2">Transmitvia</option>                                                        
                                                            <option value="4">Ongage</option>                                                        
                                                            <option value="5">Sendgrid</option>                                                        
                                                            <option value="6">Sendinblue</option>  -->                                                       
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Country *</label>
                                                    <select id="country" name="country" class="form-control">
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $ctry) { ?>
                                                            <option value="<?php echo $ctry['country']; ?>"><?php echo $ctry['country']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>                                            
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>List Name *</label>
                                                    <select class="form-control" name="list" id="list">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input class="form-control" type="date" id="deliveryDate" name="deliveryDate" value="<?php echo isset($deliveryDate)?$deliveryDate:''?>">
                                                </div>  
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select id="status" name="status" class="form-control">
                                                        <option value="0">All</option>
                                                        <option value="1" <?php echo isset($status) && ($status == 1) ? "selected":'' ?>>Success</option>
                                                        <option value="2" <?php echo isset($status) && ($status == 2) ? "selected":'' ?>>Failed</option>
                                                    </select>    
                                                </div>  
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Email Id</label>
                                                    <input class="form-control" type="text" name="email" value="<?php echo isset($email)?$email:''?>">
                                                </div>  
                                            </div>
                                        </div>                                    
                                        <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnsubscriber">
                                            Add New Unsubscriber
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Unsubscriber User List</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                    <thead>
                                            <tr class="table-header-font-small">
                                                <th>#</th>
                                                <th>Email</th>
                                                <th>Unsubscribe Date</th>
                                                <th>Provider</th>
                                                <th>Country</th>
                                                <th>List</th>
                                                <th>Status</th>
                                                <th>Response</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(isset($listArr) && count($listArr)){                     
                                                    foreach ($listArr as $key => $unsubscriber) {
                                                        $providerName = "";
                                                        if($unsubscriber["provider"] == AWEBER){
                                                            $providerName = "Aweber";
                                                        }else if($unsubscriber["provider"] == ONGAGE){
                                                            $providerName = "Ongage";
                                                        }
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $unsubscriber['email']; ?></td>
                                                <td><?php echo $unsubscriber['created_at']; ?></td>
                                                <td><?php echo $providerName; ?></td>
                                                <td><?php echo $unsubscriber['country']; ?></td>
                                                <td><?php echo $unsubscriber['listname']; ?></td>
                                                <td><?php echo ($unsubscriber['status'] == 1)?"Success":"Failed"; ?></td>
                                                <td><?php echo $unsubscriber['response']; ?></td>
                                            </tr>
                                            <?php }} ?>
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
            </section>
        </div>
    </div>
</div>
<div class="modal fade" id="addUnsubscriber" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <form id="unsubscribeForm" name="unsubscribeForm" method="post" action="#">                
                    <div class="x_panel" style="border: none;">
                        <h5 style="margin-bottom: 20px;"><strong>Add Unsubscriber Information</strong></h5>
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="popupSucErrMsg" class="alert btn-danger" role="alert" style="display: none;color:#ffffff"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Provider Name *</label>
                                    <select class="form-control" name="provider" id="popupProvider">
                                        <option value="0">Select Provider</option>                                                        
                                        <option value="1">Aweber</option>                                                        
                                        <!-- <option value="2">Transmitvia</option>                                                        
                                        <option value="4">Ongage</option>                                                        
                                        <option value="5">Sendgrid</option>                                                        
                                        <option value="6">Sendinblue</option> -->                                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Country *</label>
                                    <select id="popupCountry" name="country" class="form-control">
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $ctry) { ?>
                                            <option value="<?php echo $ctry['country']; ?>"><?php echo $ctry['country']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group multiselect-dropdown">
                                    <label style="width:100%">List Name *</label>
                                    <select class="form-control" name="list[]" id="popupList" multiple="multiple">                                        
                                    </select>
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>                                                                                                         
                        </div>                            
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" name="submit" class="btn btn-danger unsubscribe">Unsubscribe</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="responsePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h5 style="margin-bottom: 20px;"><strong>Unsubscriber List Information</strong></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="alert-success-label" style="margin-bottom: 20px;"><strong>Successfully Unsubscriber List</strong></h5>
                        <div class="alert alert-success" role="alert"></div>
                        <h5 class="alert-danger-label" style="margin-bottom: 20px;"><strong>Failed Unsubscriber List</strong></h5>
                        <div class="alert alert-danger" role="alert"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('mailUnsubscribe/list_script'); ?>

