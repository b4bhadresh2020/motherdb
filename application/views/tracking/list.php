<?php 
    $condition = array();
    $is_single = FALSE;
    $campaignList = GetAllRecord(CAMPAIGN,$condition,$is_single);
    $generalBatchList = GetAllRecord(GENERAL_BATCH,$condition,$is_single);
    $batchNameList = GetAllRecord(BATCH,$condition,$is_single);


?>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-12">
                    <div class="card alert">
                        <div class="card-header">
                            <h4>Filters</h4>
                        </div>
                        <div class="card-body">
                            <div class="horizontal-form-elements">
                                <form class="form-horizontal" method="get" action="<?php echo base_url('tracking/manage/0'); ?>">
                                    <div class="row">
                                        <div class="col-lg-8">

                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <select class="form-control" name="campaignName">
                                                                <option value = "">Select Campaign Name</option>
                                                                <?php foreach (@$campaignList as $cl) { ?>
                                                                    <option value = "<?php echo $cl['campaignId']; ?>" <?php if(@$_GET['campaignName'] == $cl['campaignId']){ echo 'selected'; } ?> ><?php echo $cl['campaignName']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>        
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <select class="form-control" name="generalBatchName">
                                                                <option value = "">Select General Batch / Segment Name</option>
                                                                <?php foreach (@$generalBatchList as $gbnl) { ?>
                                                                    <option value = "<?php echo $gbnl['generalBatchId']; ?>" <?php if(@$_GET['generalBatchName'] == $gbnl['generalBatchId']){ echo 'selected'; } ?> ><?php echo $gbnl['generalBatchName']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>        
                                                    </div>       
        

                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <select class="form-control" name="batchName">
                                                                <option value = "">Select Batch / Segment Name</option>
                                                                <?php foreach (@$batchNameList as $bnl) { ?>
                                                                    <option value = "<?php echo $bnl['batchId']; ?>" <?php if(@$_GET['batchName'] == $bnl['batchId']){ echo 'selected'; } ?> ><?php echo $bnl['batchName']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>        
                                                    </div>       
                                                </div>
                                            </div>
                                            
                                                
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <input type="text" class="form-control" placeholder="Search by key" name="uniqueKey" value = "<?php echo @trim($_GET['uniqueKey']); ?>" >
                                                </div>
                                                <div class="col-lg-4">
                                                    <input type="number" class="form-control" placeholder="Search by Phone" name="phone" value = "<?php echo @trim($_GET['phone']); ?>" >
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="submit" class="form-control btn btn-dark" >
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="submit" name="reset" value="Reset" class="form-control btn btn-default" >
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /# card -->
                </div>

            </div>
            <!-- /# row -->
            <section id="main-content">
                <div class="row">
                    
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <h4>User List </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Campaign Name</th>
                                                <th>General Batch / General Segment Name</th>
                                                <th>Batch / Segment Name</th>
                                                <th>User Name</th>
                                                <th>Phone Number</th>
                                                <th>Unique Key</th>
                                                <th>Active</th>
                                                <th>Total Clicks</th>
                                                <th style="text-align: center;">Detail</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    $uniqueKey = $curEntry["uniqueKey"];
                                                    $i++;

                                                    $isActive = 'No';

                                                    if ($curEntry['isActive'] == 1) {
                                                        $isActive = 'Yes';
                                                    }

                                            ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $curEntry['campaignName']; ?></td>
                                                    <td><?php echo $curEntry['generalBatchName']; ?></td>
                                                    <td><?php echo $curEntry['batchName']; ?></td>
                                                    <td><?php echo $curEntry['userName']; ?></td>
                                                    <td><?php echo $curEntry['phone']; ?></td>
                                                    <td><?php echo $curEntry['uniqueKey']; ?></td>
                                                    <td><?php echo $isActive; ?></td>
                                                    <td><?php echo $curEntry['total_clicks']; ?></td>
                                                    <td style="text-align: center;">
                                                        <a href="javascript:;" class="btn btn-info btn-xs" data-unique-key = "<?php echo $uniqueKey; ?>" onclick = "javascript:loadMoreData(this);" title = "More Data"><i class="ti-eye"></i></a>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6" style="padding-top: 25px;">
                                        <div class="datatable_pageinfo"><?php echo @$pageinfo; ?></div>
                                    </div>
                                    <div class="col-lg-6">
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



<div class="modal fade" id="clickMoreDataPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5><strong>Some More Data Of Current Click URL</strong></h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="clickMoreDataTable"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="javascript:;" onclick="$('#clickMoreDataPopup').modal('hide');" class="btn btn-primary" style="margin-top:10px; ">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

    var BASE_URL = "<?php echo base_url(); ?>";

    function loadMoreData(curObj) {
        var uniqueKey = $(curObj).attr("data-unique-key");
        
        $.ajax({
            url : BASE_URL + 'tracking/getUrlMoreData',
            type : 'post',
            data:{
                uniqueKey:uniqueKey
            },
            success:function(response){
                $('#clickMoreDataTable').html(response);
            }
        });

        $('#clickMoreDataPopup').modal('show');
    }

</script>
