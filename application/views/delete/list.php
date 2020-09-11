<?php 
    $qry = "SELECT DISTINCT(groupName) FROM group_master";
    $groupNames = GetDatabyqry($qry);

    $qry = "SELECT DISTINCT(keyword) FROM keyword_master";
    $keywords = GetDatabyqry($qry);

    $countries = getCountry();

?>

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Delete</h1>
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

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select name="country" class="form-control" id="country">
                                                    <option value="">Select country</option>
                                                    <?php foreach ($countries as $country) { ?>
                                                        <option value="<?php echo $country['country']; ?>" ><?php echo $country['country']; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Group Name</label>
                                                <select name="groupName" class="form-control" id="groupName">
                                                    <option value="">Select Group</option>
                                                    <?php foreach ($groupNames as $groupName) { ?>
                                                        <option value="<?php echo $groupName['groupName']; ?>" ><?php echo $groupName['groupName']; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Keyword</label>
                                                <select name="keyword" class="form-control" id="keyword">
                                                    <option value="">Select Keyword</option>
                                                    <?php foreach ($keywords as $keyword) { ?>
                                                        <option value="<?php echo $keyword['keyword']; ?>" ><?php echo $keyword['keyword']; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" id="btn_delete" class="btn btn-danger" >Submit</button>
                                    <a href="<?php echo base_url('delete/manage'); ?>" class="btn btn-default" >Reset</a>
                                    
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
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 10%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Are you sure, you want to delete it?</strong></h5>
                    <div>
                        <a href="javascript:;" onclick="javascript:proceedToDeleteData();" class="btn btn-success">Yes</a>
                        <a href="javascript:;" onclick="$('#approvedRejectPop').modal('hide');" class="btn btn-primary">No</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('delete/delete_script'); ?>

