<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Synchronise</h1>
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
                                    <?php 
                                        $isSyncCronRunning = getConfigVal('isSyncCronRunning'); 
                                    ?>

                                    <button type="button" class="btn btn-success btn_sync" id="btn_sync_0" data-btn-val='0' style="display: <?php if($isSyncCronRunning == 1) { echo 'None';  } ?>" data-change-val = '1' >Sync</button>
                                    <button type="button" class="btn btn-success btn_sync" id="btn_sync_1" data-btn-val='1' data-change-val = '0' style="display: <?php if($isSyncCronRunning == 0) { echo 'None';  } ?>">Synchronizing</button>
                                    <a href="<?php echo base_url(); ?>" class="btn btn-default" >Back</a>
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
                    <h5 style="margin-bottom:10px;"><strong>Are you sure, you want to change the sync status?</strong></h5>
                    <div>
                        <a href="javascript:;" onclick="javascript:proceedToSyncData();" class="btn btn-success">Yes</a>
                        <a href="javascript:;" onclick="$('#approvedRejectPop').modal('hide');" class="btn btn-primary">No</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->load->view('synchronise/list_script');
?>

