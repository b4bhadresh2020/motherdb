<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <a href="<?php echo site_url('/espAccount/'.$functionName); ?>"><b>Back to <?=$provider?> Account list</b></a>
                                </div>
                            </div>
                            <div class="card-header">
                                <h4>Filters</h4>
                            </div>
                            <div class="card-body">
                                <div class="horizontal-form-elements">                                    
                                    <form class="form-horizontal" method="get" action="<?php echo base_url('espAccount/statusLogData/0'); ?>">
                                        <div class="row">
                                            <div class="col-lg-8"> 
                                                <input type="hidden" name='esp' value="<?php echo $esp;?>">
                                                <input type="hidden" name='accountId' value="<?php echo $accountId;?>">                                                
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Date</label>
                                                            <input class="form-control" type="date" name="created_at" value = "<?php echo @$_GET['created_at']; ?>"  >
                                                        </div>        
                                                    </div>                                                                                                        
                                                    <div class="col-lg-1">
                                                        <label>&nbsp;</label>
                                                        <input type="submit" value="Submit" class="form-control btn btn-dark" >
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <label>&nbsp;</label>
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
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4><?=$headerTitle?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Email</th>
                                                <th>IP</th>
                                                <th>Status</th>
                                                <th>Datetime</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $i++;
                                                        $status = "";
                                                        if($curEntry['status'] == 0) {
                                                            $status = "<span class='text-red'>OFF</span>";
                                                        } else {
                                                            $status = "<span class='text-green'>ON</span>";
                                                        }                                             
                                                    ?>
                                                    <tr>                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry['email_id']; ?></td>
                                                        <td><?php echo $curEntry['ip']; ?></td>
                                                        <td><?php echo $status; ?></td>
                                                        <td><?php echo $curEntry['created_at']; ?></td>
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