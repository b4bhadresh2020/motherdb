<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4><?php echo $pageTitle; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Keyword</th>
                                                <th>Percentage</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($keyword['listArr'] as $curEntry) {
                                                        $i++;

                                                        //get keyword percentage
                                                        $keyword_con = $curEntry['keyword'];

                                                        $totalKeywordCount_sql = "SELECT sum(male) + sum(female) + sum(other) as total from ".KEYWORD_MASTER." WHERE keyword = '$keyword_con'";

                                                        $totalKeywordCount = $this->db->query($totalKeywordCount_sql)->row_array();

                                                        $dataCount = GetAllRecordCount(USER);
                                                        
                                                        if ($dataCount > 0) {
                                                            $percentage = ($totalKeywordCount['total']/$dataCount) * 100;    
                                                        }else{
                                                            $percentage = 0;
                                                        }
                                                        $percentage = reformat_number_format($percentage);

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry['keyword']; ?></td>
                                                        <td><?php echo $percentage . '% ('.number_format($totalKeywordCount['total']).')'; ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url('userList/manage/0?keyword='.$curEntry['keyword']); ?>" class="btn btn-info btn-xs" title = "View"><i class="ti-eye"></i></a>
                                                        </td>

                                                    </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 " style="padding-top: 25px;">
                                        <div class="datatable_pageinfo"><?php echo @$keyword['pageinfo']; ?></div>
                                    </div>
                                    <div class="col-lg-6 ">
                                        <div class="paginate_links pull-right"><?php echo @$keyword['links']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <!-- <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Batch Stat </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Batch Name</th>
                                                <th>Total</th>
                                                <th>Active</th>
                                                <th>Active Percentage</th>
                                                <th>Unsubscribed</th>
                                                <th>Unsubscribed Percentage</th>
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($batchstat['listArr'] as $curEntry) {
                                                        $batchId = $curEntry["batchId"];
                                                        $i++;

                                                        //get active url count
                                                        $condition = array('batchId' => $batchId,'isActive >' => 0 );
                                                        $activeCount = GetAllRecordCount(BATCH_USER,$condition);
                                                        $activePercentage = 0;
                                                        if ($activeCount > 0) {
                                                            $activePercentage = ($activeCount/$curEntry["total_count"]) * 100;
                                                        }
                                                        $activePercentage = reformat_number_format($activePercentage).'%';

                                                        //get unsubscribe url count
                                                        $condition = array('batchId' => $batchId,'isUnsubscribed >' => 0 );
                                                        $isUnsubscribedCount = GetAllRecordCount(BATCH_USER,$condition);
                                                        $isUnsubscribedPercentage = 0;
                                                        if ($isUnsubscribedCount > 0) {
                                                            $isUnsubscribedPercentage = ($isUnsubscribedCount/$curEntry["total_count"]) * 100;
                                                        }
                                                        $isUnsubscribedPercentage = reformat_number_format($isUnsubscribedPercentage).'%';
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["batchName"]; ?></td>
                                                        <td><?php echo number_format($curEntry["total_count"]); ?></td>
                                                        <td><?php echo number_format($activeCount); ?></td>
                                                        <td><?php echo $activePercentage; ?></td>
                                                        <td><?php echo number_format($isUnsubscribedCount); ?></td>
                                                        <td><?php echo $isUnsubscribedPercentage; ?></td>

                                                    </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 " style="padding-top: 25px;">
                                        <div class="datatable_pageinfo"><?php echo @$batchstat['pageinfo']; ?></div>
                                    </div>
                                    <div class="col-lg-6 ">
                                        <div class="paginate_links pull-right"><?php echo @$batchstat['links']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->






                    
                </div>
                <!-- /# row -->

            </section>
        </div>
    </div>
</div>

