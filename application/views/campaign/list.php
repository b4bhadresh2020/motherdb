<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Campaign List </h4>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo base_url('campaign/addEdit'); ?>" class="btn btn-dark" style="float: right;" ><i class="fa fa-plus" aria-hidden="true"></i> Add</a>    
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Campaign Name</th>
                                                <th>Country</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $campaignId = $curEntry["campaignId"];
                                                        $i++;
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["campaignName"].' ( '. date('d-m-Y H:i:s',strtotime($curEntry['createdDate'])) ." ) " ; ?></td>
                                                        <td><?php echo strtoupper($curEntry["country"]); ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url('campaign/addEdit/'. $campaignId); ?>" class="btn btn-info btn-xs" title = "Edit"><i class="ti-pencil"></i></a>
                                                            <a class="btn btn-danger btn-xs" data-deleteUrl="<?php echo site_url("campaign/delete/" . @$campaignId) ?>" href="javascript:;" onclick="javascript:deleteEntry(this);" title="Delete"><i class="ti-close"></i></a>
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


<script type="text/javascript">
    
    function deleteEntry(curObj) {
        deleteUrl = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = deleteUrl;
    }

</script>