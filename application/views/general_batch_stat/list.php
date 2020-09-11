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
                                        <h4>General Group Clickers List (General Batch) </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>General Group Clickers Name (General Batch)</th>
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
                                                        
                                                        $generalBatchId = $curEntry["generalBatchId"];
                                                        $i++;

                                                        $total = $curEntry['total'];

                                                        //active part
                                                        $activeCount = $curEntry["active"];
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
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["generalBatchName"]; ?></td>
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


<script type="text/javascript">
    
    function deleteEntry(curObj) {
        deleteUrl = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = deleteUrl;
    }

</script>