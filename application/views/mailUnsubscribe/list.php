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
                <form class="form-horizontal" method="post" action="<?php echo base_url('mailUnsubscribe/getUnsubscriberData/'); ?>">             
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
                                                            <option value="2">Transmitvia</option>                                                        
                                                            <option value="4">Ongage</option>                                                        
                                                            <option value="5">Sendgrid</option>                                                        
                                                            <option value="6">Sendinblue</option>                                                        
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
                                                    <select class="form-control" name="list" id="list" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input class="form-control" type="date" id="deliveryDate" name="deliveryDate" value="<?php echo isset($deliveryDate)?$deliveryDate:date('Y-m-d');?>" required>
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
                                        
                                    </table>
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
                                <div id="popupSucErrMsg"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Provider Name *</label>
                                    <select class="form-control" name="provider" id="popupProvider">
                                        <option value="0">Select Provider</option>                                                        
                                        <option value="1">Aweber</option>                                                        
                                        <option value="2">Transmitvia</option>                                                        
                                        <option value="4">Ongage</option>                                                        
                                        <option value="5">Sendgrid</option>                                                        
                                        <option value="6">Sendinblue</option>                                                        
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
                                    <select class="form-control" name="list[]" id="popupList" multiple="multiple" required>                                        
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
<?php $this->load->view('mailUnsubscribe/list_script'); ?>

