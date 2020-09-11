<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Live Delivery List </h4>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo base_url();?>download/api_documentation.pdf" download class="btn btn-primary">API Documentation</a>
                                        <a href="<?php echo base_url('liveDelivery/addEdit'); ?>" class="btn btn-dark" style="float: right;" ><i class="fa fa-plus" aria-hidden="true"></i> Add</a>    
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Api Key</th>
                                                <th>Group Name</th>
                                                <th>Keyword</th>
                                                <th>Live Delivery URL</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $liveDeliveryId = $curEntry["liveDeliveryId"];
                                                        $i++;
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry['apikey']; ?></td>
                                                        <td><?php echo $curEntry['groupName']; ?></td>
                                                        <td><?php echo $curEntry['keyword']; ?></td>
                                                        <td style="word-break: break-all;"><?php echo $curEntry['liveDeliveryUrl']; ?></td>
                                                        <td>
                                                            <?php if($curEntry['isInActive'] == 0){ ?>

                                                                <a class="btn btn-success btn-sm unblock_0_<?php echo $liveDeliveryId;?>" href="javascript:;" onclick="javascript:changeStatus('1','<?php echo $liveDeliveryId;?>','Inactive','0');" title="Click to Inactive">Active</a>
                                                                <a class="btn btn-danger btn-sm block_1_<?php echo $liveDeliveryId;?>" href="javascript:;" onclick="javascript:changeStatus('0','<?php echo $liveDeliveryId;?>','Active','0');" title="Click to Active" style="display: none;" >Inactive</a>

                                                            <?php } else { ?>

                                                                <a class="btn btn-success btn-sm unblock_0_<?php echo $liveDeliveryId;?>" href="javascript:;" onclick="javascript:changeStatus('1','<?php echo $liveDeliveryId;?>','Inactive','0');" title="Click to Inactive" style="display: none;">Active</a>
                                                                <a class="btn btn-danger btn-sm block_1_<?php echo $liveDeliveryId;?>" href="javascript:;" onclick="javascript:changeStatus('0','<?php echo $liveDeliveryId;?>','Active','0');" title="Click to Active"  >Inactive</a>

                                                            <?php  } ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url('liveDelivery/addEdit/'. $liveDeliveryId); ?>" class="btn btn-info btn-xs" title = "Edit"><i class="ti-pencil"></i></a>
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


<div class="modal fade" id="changeStatusPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you really want to make <span id="status-title"></span> this URL?</strong></h5>
                    <br >
                    <input type="hidden" id="statusValue" />
                    <input type="hidden" id="idValue" />
                    <a href="javascript:;" onclick="javascript:proceedChangeStatus();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#changeStatusPopup').modal('hide');" class="btn btn-primary">No</a>

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


    function changeStatus(status,id,title) {

        $("#status-title").text(title);
        $("#statusValue").val(status);
        $("#idValue").val(id);
        $('#changeStatusPopup').modal('show');
    }

    /*
       @proceedChangeStatus
       -> onclick active and Inctive campagin     
    */
        function proceedChangeStatus() {
            var status   = $("#statusValue").val();
            var id       = $("#idValue").val();

            // ajax call here
            $.ajax({
                type:"post",
                url:"<?php echo base_url('liveDelivery/changeActiveInActiveStatus');?>",
                data:{
                    status : status,
                    id : id,
                },
                success:function(){
                    if(status == 0){
                        $(".unblock_"+status+"_"+id).show();
                        $(".block_1_"+id).hide();
                    }
                    else {
                        $(".block_"+status+"_"+id).show();
                        $(".unblock_0_"+id).hide();
                    }
                    $('#changeStatusPopup').modal('hide');
                }   
            });
        }

</script>