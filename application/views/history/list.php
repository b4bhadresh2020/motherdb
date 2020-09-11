<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">

                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">

                             <div class="row">
                                <div class="col-lg-6">
                                    <div id="sucErrMsg"></div>
                                </div>
                            </div>

                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4><?php echo $pageTitle; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="horizontal-form-elements">
                                    <form class="form-horizontal" method="get" action="<?php echo base_url('history/manage/0'); ?>">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <div class="row">

                                                            <div class="col-lg-3">
                                                                <div class="form-group">
                                                                    <select name="isImported" class="form-control" id="isImported">
                                                                        <option value="">Select Type</option>
                                                                        <option value="1" <?php if(@$_GET['isImported'] == '1'){ echo 'selected'; } ?> >Import</option>
                                                                        <option value="0" <?php if(@$_GET['isImported'] == '0'){ echo 'selected'; } ?> >Export</option>

                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3">
                                                                <div class="form-group">
                                                                    <select class="form-control" name="fileModuleType" id="fileModuleType">
                                                                        <option value ="">Select Module</option>
                                                                    </select>
                                                                </div>
                                                                <input type="hidden" id="fileModuleTypeVal" value = "<?php echo @$_GET['fileModuleType']; ?>">        
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <div class="col-lg-4">
                                                                        <input type="submit" value="Submit" class="form-control btn btn-dark" >
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <input type="submit" name="reset" value="Reset" class="form-control btn btn-default" >
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div> 
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>File Name</th>
                                                <!-- <th>File Module</th> -->
                                                <th>Total Entries</th>
                                                <!-- <th>Type</th> -->
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $historyId = $curEntry["historyId"];
                                                        $i++;

                                                        $isShowDelete = 0;

                                                        if ($curEntry['isImported'] == 1) {
                                                            $type = 'Import';

                                                            if ($curEntry['fileModuleType'] == 'user' || $curEntry['fileModuleType'] == 'enrichment') {
                                                                $isShowDelete = 1;
                                                            }
                                                        }else{
                                                            $type = 'Export';
                                                        }

                                                        

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry['fileName']; ?></td>
                                                        <!-- <td><?php echo ucfirst($curEntry['fileModuleType']); ?></td> -->
                                                        <td><?php echo $curEntry['totalCount']; ?></td>
                                                        <!-- <td><?php echo $type; ?></td> -->
                                                        <td><?php echo date('d-m-Y H:i:s',strtotime($curEntry['createdDate'])); ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo $curEntry['redirectUrl']; ?>" class="btn btn-info btn-xs" title = "View"><i class="ti-eye"></i></a>

                                                            <a class="btn btn-danger btn-xs" data-deleteUrl="<?php echo site_url("history/delete/" . @$historyId) ?>" href="javascript:;" onclick="javascript:deleteEntry(this);" title="Delete"><i class="ti-close"></i></a>
                                                            
                                                            <!-- <?php if($isShowDelete == 1){ ?>

                                                                <button type="button" data-delete-url = "<?php echo $curEntry['redirectUrl']; ?>" onclick = "javascript:deleteFileEntry(this)" title="Delete" class="btn btn-danger btn-xs"><i class="ti-close"></i></button>

                                                            <?php } ?> -->
                                                            
                                                        </td>

                                                    </tr>
                                            <?php } ?>
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
                <!-- /# row -->

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
                        <a href="javascript:;" onclick="javascript:proceedToDeleteFileData();" class="btn btn-success">Yes</a>
                        <a href="javascript:;" onclick="$('#approvedRejectPop').modal('hide');" class="btn btn-primary">No</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deletePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you want Delete this Entry?</strong></h5>
                    
                    <a href="javascript:;" onclick="javascript:proceedDeleteEntry();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#deletePopup').modal('hide');" class="btn btn-primary">No</a>

                </div>
                
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('history/history_list_script'); ?>

