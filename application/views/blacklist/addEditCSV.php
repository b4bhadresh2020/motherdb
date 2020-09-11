<?php $fieldsName = array(
    'firstName' => 'Firstname',
    'lastName'  => 'Lastname',
    'emailId'   => 'Email',
    'phone'     => 'Phone',
    'gender'    => 'Gender',
); 

?>


<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Black List CSV</h1>
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
                    <div class="col-lg-6">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="sucErrMsg"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-pink btn-outline m-b-10 m-l-5" onclick="$('#uploadCsv').click();">Upload CSV</button>
                                        <input type="file" name="uploadCsv" id="uploadCsv" style="display: none;">
                                        <label id="uploadLable"></label>
                                        <!-- <label>(Max Size 20MB)</label> -->

                                        <a href="<?php echo base_url();?>download/demo.csv" download class = "btn btn-primary" style="float: right;">Sample CSV</a>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Column Number</label>
                                                <input type="text" class="form-control colNumber" name="colNumber[]">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Field Name</label>
                                                <select class="form-control fieldsName" data-select-id = "0" id="fieldsName_0" name = "fieldsName[]">
                                                    <?php foreach ($fieldsName as $key => $value) { ?>
                                                            <option value = "<?php echo $key; ?>"><?php echo $value; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    
                                    <div id="addExtraRowDiv"></div>

                                    <div class="row">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-dark m-b-10 m-l-5" title="Add More" id="addExraField"><i class="ti-plus"></i></button>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-success" id="btn_insert">Submit</button>
                                    <a href="<?php echo base_url('blacklist/upload'); ?>" class="btn btn-default" >Reset</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </section>
        </div>
    </div>
</div>

<div class="modal fade" id="approvedRejectPop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Are you sure, you want to add this?</strong></h5>
                    <div class="row">
                        <div id="confirmTable"></div>
                    </div>
                    
                    <!-- <a href="javascript:;" onclick="$('#csv_form').submit();" class="btn btn-success btn-approved-reject">Yes</a> -->
                    <div id="btn_div">
                        <a href="javascript:;" onclick="javascript:upload_csv();" class="btn btn-success btn-approved-reject">Yes</a>
                        <a href="javascript:;" onclick="$('#approvedRejectPop').modal('hide');" class="btn btn-primary">No</a>
                    </div>
                    <div class="progress" id="progressBar">
                        <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" id = "progressbarStriped">
                            <spane id = "percentage_span"></spane> Completed 
                        </div>
                        <div> Do not refresh the page</div>
                    </div>
                    <div id="countButton" style="display: none;">
                        <label id="total_enrty_label"></label>
                        <input type="button" id="okPopupBtn" value="Ok" class="btn btn-info" onclick="reloadCurrentPage()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
    $this->load->view('blacklist/addEditCSV_script');
?>

