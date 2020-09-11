<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">

                <!-- <div class="col-lg-12">
                    <div class="card alert">
                        <div class="card-header">
                            <h4>Filters</h4>
                        </div>
                        <div class="card-body">
                            <div class="horizontal-form-elements">
                                <form class="form-horizontal" method="get" action="<?php echo base_url('unsubscribe/manage'); ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="global" value="<?php echo @$_GET['global'];  ?>" placeholder="Global Search">
                                                        </div>       
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
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>
            <!-- /# row -->
            <section id="main-content">
                <div class="row">
                    
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h4>History CSV List </h4>
                                    </div>
                                    <div class="col-lg-6" style="text-align: right;">
                                        <h4>Csv file that not used before yet is highlighted in red</h4>
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
                                                <th>Created Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        
                                                        $fileNameId = $curEntry["fileNameId"];
                                                        $i++;

                                                        $isUsed = $curEntry['isUsed'];

                                                    ?>
                                                    <tr style = "<?php  if ($isUsed == 0) { echo 'background-color: #eacac8;'; } ?>" >
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["fileName"]; ?></td>
                                                        <td><?php echo date('d-m-Y H:i:s', strtotime($curEntry["createdDate"])); ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="<?php echo base_url('csv_history/export_csv/'. $fileNameId); ?>" class="btn btn-default btn-xs" title = "Download" onClick="javascript:remove_bg_color(this);" download><i class="ti-download"></i></a>
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

<script type="text/javascript">
    function remove_bg_color(curobj){
        $(curobj).closest('tr').css("background-color","#fff");
    }
</script>
