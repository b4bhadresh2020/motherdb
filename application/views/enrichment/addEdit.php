<?php 
    
    $qry = "SELECT DISTINCT(groupName) FROM group_master";
    $groupNames = GetDatabyqry($qry);

    $qry = "SELECT DISTINCT(keyword) FROM keyword_master";
    $keywords = GetDatabyqry($qry);

    $countries = getCountry();
?>
<style type="text/css">
    hr{
        margin-top: 4px;
        margin-bottom: 10px;
        border-top: 1px solid #000;
    }

    .vertical-line{
        height: 62px;
        border-left: 1px solid;
    }
</style>

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Enrichment CSV</h1>
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
                                    <form method="post" action="<?php echo base_url('enrichment/addEdit'); ?>" id="csv_form" enctype="multipart/form-data" autocomplete="off">

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

                                            <a href="<?php echo base_url();?>download/enrichment_demo.csv" download class = "btn btn-primary" style="float: right;">Sample CSV</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <hr />
                                                    <label>Searching against</label>
                                                    <hr />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <select class="form-control" id="search_against_groupName">
                                                        <option value = "">Select Group</option>
                                                        <?php foreach (@$groupNames as $gn) { ?>
                                                            <option value = "<?php echo $gn['groupName']; ?>" <?php if(@$_GET['groupName'] == $gn['groupName']){ echo 'selected'; } ?> ><?php echo $gn['groupName']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <select class="form-control" id="search_against_keyword">
                                                        <option value = "">Select Keyword</option>
                                                        <?php foreach (@$keywords as $ky) { ?>
                                                            <option value = "<?php echo $ky['keyword']; ?>" <?php if(@$_GET['keyword'] == $ky['keyword']){ echo 'selected'; } ?> ><?php echo $ky['keyword']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <select class="form-control" id="search_against_country">
                                                        <option value="">Select Country</option>
                                                        <?php 
                                                            foreach ($countries as $country) { ?>
                                                                <option value="<?php echo $country['country']; ?>"> <?php echo $country['country']; ?></option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <hr />
                                                    <label>Result Identificaiton (This is where you can find your result)</label>
                                                    <hr />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Group Name*</label>
                                                    <input type="text" class="form-control" id="groupName" name="groupName">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Keyword *</label>
                                                    <input type="text" class="form-control" id="keyword" name="keyword">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <hr />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Identifier in Enrichment File</label>
                                                    <hr />
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <label>Look in database</label>
                                                    <hr />
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Legal</label>
                                                    <hr />
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="text" placeholder="Column Number" class="form-control" id="colNumber_0">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="fieldsName" data-select-id="0" value="emailId" data-check-text="Email" style="margin-top: 15px;"> Email
                                                </div>
                                            </div>
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" data-select-id="0" value="emailId" data-check-text="Email" style="margin-top: 15px;" id="lookingFor_0"> Email
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" value="ip" data-check-text="IP" style="margin-top: 15px;"> IP
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="text" placeholder="Column Number" class="form-control" id="colNumber_1">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="fieldsName" data-select-id="1" value="phone" data-check-text="Phone"  style="margin-top: 15px;"> Phone
                                                </div>
                                            </div>
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" data-select-id="1" value="phone" data-check-text="Phone"  style="margin-top: 15px;" id="lookingFor_1"> Phone
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" value="participated" data-check-text="Participated" style="margin-top: 15px;"> Participated
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="text" placeholder="Column Number" class="form-control" id="colNumber_2">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="fieldsName" data-select-id="2" value="firstName" data-check-text="First Name" style="margin-top: 15px;"> 
                                                    First Name
                                                </div>
                                            </div>
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" data-select-id="2" value="firstName" data-check-text="First Name" style="margin-top: 15px;" id="lookingFor_2"> 
                                                    First Name
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="text" placeholder="Column Number" class="form-control" id="colNumber_3">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="fieldsName" data-select-id="3" value="lastName" data-check-text="Last Name" style="margin-top: 15px;"> 
                                                    Last Name
                                                </div>
                                            </div>
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" data-select-id="3" value="lastName" data-check-text="Last Name" style="margin-top: 15px;" id="lookingFor_3"> 
                                                    Last Name
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="text" placeholder="Column Number" class="form-control" id="colNumber_4">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <input type="checkbox" class="fieldsName" data-select-id="4" value="address" data-check-text="Address" style="margin-top: 15px;"> 
                                                    Address
                                                </div>
                                            </div>
                                            <div class="col-lg-3 vertical-line">
                                                <div class="form-group">
                                                    <input type="checkbox" class="lookingFor" data-select-id="4" value="address" data-check-text="Address" style="margin-top: 15px;" id="lookingFor_4"> 
                                                    Address
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                        <a href="<?php echo base_url('enrichment/manage'); ?>" class="btn btn-default" >Reset</a>
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
    $this->load->view('enrichment/addEdit_script');
?>

