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
                                        <h4>Batch Campaign SMS Stat </h4>
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
                                                <th>Export Type</th>
                                                <th>SMS Providers<br><sub>(Provider/Sent/Delivered)</sub></th>
                                                <th>CSV Type</th>
                                                <th>Country</th>
                                                <th>Total Send</th>
                                                <th>Number of Clicks</th>
                                                <th>Clicks %</th>
                                                <th>Unsubscribed</th>
                                                <th>Unsubscribed in %</th>
                                                <th>Date/Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        
                                                        $campaignId = $curEntry["campaignId"];
                                                        $i++;

                                                        $total = $curEntry['total'];

                                                        //active part
                                                        $activeCount = $curEntry["clicks"];
                                                        $activePercentage = 0;
                                                        if ($activeCount > 0) {
                                                            $activePercentage = ($activeCount/$total) * 100;
                                                        }

                                                        $color = '#080';
                                                        if ($activePercentage < 9) {
                                                            $color = '#F00';
                                                        }

                                                        $activePercentage = reformat_number_format($activePercentage).' %';

                                                        //unsubscribe part
                                                        $unsubscribedCount = $curEntry["unsubscribed"];
                                                        $unsubscribedPercentage = 0;
                                                        if ($unsubscribedCount > 0) {
                                                            $unsubscribedPercentage = ($unsubscribedCount/$total) * 100;
                                                        }
                                                        $unsubscribedPercentage = reformat_number_format($unsubscribedPercentage).' %';

                                                        if($curEntry["csvType"] == 0){
                                                            $csvType = "Normal CSV";
                                                        }else if($curEntry["csvType"] == 1){
                                                            $csvType = "With Merge Tag";
                                                        }else if($curEntry["csvType"] == 2){
                                                            $csvType = "Without Merge Tag";
                                                        }

                                                        $providers = array();
                                                        if($curEntry["smsProvider"] != ""){
                                                            $providers = json_decode($curEntry["smsProvider"],true);
                                                        }
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["campaignName"]; ?></td>
                                                        <td><?php echo $curEntry["exportType"]; ?></td>
                                                        <td style="text-align:right">
                                                            <?php 
                                                                if(count($providers) > 0){
                                                                    foreach($providers as $providerName => $providerData){
                                                            ?>
                                                            <span class="badge badge-light"><?php echo $providerName; ?></span>
                                                            <span class="badge badge-primary"><?php echo isset($providerData['sent'])?$providerData['sent']:''; ?></span>
                                                            <span class="badge badge-success"><?php echo isset($providerData['delivered'])?$providerData['delivered']:''; ?></span>
                                                            <br>
                                                            <?php            
                                                                    }
                                                                }
                                                            
                                                            ?>
                                                            <?php if($curEntry["exportType"] == "sms"){?>
                                                                <a href="javascript:;" class="btn btn-info btn-xs" style="padding:0;line-height:1" data-batchCampaignId = '<?php echo $curEntry["batchCampaignId"]; ?>' onclick = "javascript:loadMoreData(this);" title = "More Data"><i class="ti-eye"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $csvType; ?></td>
                                                        <td><?php echo $curEntry["country"]; ?></td>
                                                        <td><?php echo $total; ?></td>
                                                        <td><?php echo $activeCount; ?></td>
                                                        <td style = "color: <?php echo $color; ?>"><?php echo $activePercentage; ?></td>
                                                        <td><?php echo $unsubscribedCount; ?></td>
                                                        <td><?php echo $unsubscribedPercentage; ?></td>
                                                        <td><?php echo date('d-m-Y H:i:s', strtotime($curEntry["createdDate"])); ?></td>
                                                        
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

<div class="modal fade" id="clickMoreDataPopupForProvider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5><strong>Some More Data Of Provider</strong></h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="clickMoreDataTable"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="javascript:;" onclick="$('#clickMoreDataPopupForProvider').modal('hide');" class="btn btn-primary" style="margin-top:10px; ">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var BASE_URL = "<?php echo base_url(); ?>";
    
    function deleteEntry(curObj) {
        deleteUrl = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = deleteUrl;
    }

    function loadMoreData(curObj) {
        var batchCampaignId = JSON.parse($(curObj).attr("data-batchCampaignId"));
        $.ajax({
            url : BASE_URL + 'batch_campaign_sms_stat/getProviderMoreData',
            type : 'post',
            data:{
                batchCampaignId:batchCampaignId
            },
            success:function(response){
                $('#clickMoreDataTable').html(response);
            }
        });

        $('#clickMoreDataPopupForProvider').modal('show');
    }

</script>