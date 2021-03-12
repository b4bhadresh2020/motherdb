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
                                    <div class="col-lg-6">
                                        <h4>Enrichment Cronjob File Status List </h4>
                                    </div>
                                    <!-- <div class="col-lg-6" style="text-align: right;">
                                        <h5>Cronjob Status : <?php echo $cronjobRunningStatus; ?></h5>
                                    </div> -->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>File Name</th>
                                                <th>Total Records</th>
                                                <th>Updated Records</th>
                                                <th>Percentage</th>
                                                <th>File Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $i++;

                                                        //file name
                                                        $filePath = $curEntry['filePath'];
                                                        $explodeFileArr = explode('/', $filePath);
                                                        $fileName = array_pop($explodeFileArr);

                                                        //file status
                                                        $fileStatus = "Not yet started";
                                                        if ($curEntry['isFileRunning'] == 1) {
                                                            $fileStatus = "Running";
                                                        }else if($curEntry['isFileRunning'] == 2){
                                                            $fileStatus = "Completed";
                                                        }

                                                        $percentage = ($curEntry['totalUpdatedRecords']/$curEntry['totalRecords']) * 100;
                                                        $percentage = number_format((float)$percentage, 8, '.', '');

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $fileName; ?></td>
                                                        <td><?php echo $curEntry['totalRecords']; ?></td>
                                                        <td><?php echo $curEntry['totalUpdatedRecords']; ?></td>
                                                        <td><?php echo $percentage.'%'; ?></td>
                                                        <td><?php echo $fileStatus; ?></td>

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
