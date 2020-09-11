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
                                                <th>File Name</th>
                                                <th>Percentage</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $i++;

                                                        //get file name
                                                        $filePath = $curEntry['filePath'];
                                                        $explodeFileArr = explode('/', $filePath);
                                                        $fileName = array_pop($explodeFileArr);

                                                        //get file percentage
                                                        // $condition = array("groupName REGEXP" => '[[:<:]]'.$curEntry['groupName'].'[[:>:]]');
                                                        // $totalGroupCount = GetAllRecordCount(USER,$condition);

                                                        // comment below line because of wrong percentage display.
                                                        //$groupName_con = $curEntry['groupName'];

                                                        //$totalGroupCount_sql = "SELECT sum(male) + sum(female) + sum(other) as total from ".GROUP_MASTER." WHERE groupName = '$groupName_con'";

                                                        //$totalGroupCount = $this->db->query($totalGroupCount_sql)->row_array();

                                                        //$percentage = ($totalGroupCount['total']/$curEntry['totalRecords']) * 100;
                                                        $percentage = ($curEntry['totalUpdatedRecords']/$curEntry['totalRecords']) * 100;
                                                        $percentage = reformat_number_format($percentage);

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $fileName; ?></td>
                                                        <!-- <td><?php //echo $percentage . '% ('.number_format($totalGroupCount['total']).')'; ?></td> -->
                                                        <td><?php echo $curEntry['totalUpdatedRecords'].", ".round($percentage,2) . '%'; ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url('userList/manage/0?groupName='.$curEntry['groupName']); ?>" class="btn btn-info btn-xs" title = "View"><i class="ti-eye"></i></a>
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

