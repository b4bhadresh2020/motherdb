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
                                <form class="form-horizontal" method="get" action="<?php echo base_url('blacklist/manage'); ?>">
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
                                <div class="row">
                                    <div class="col-lg-10">
                                        <h4>Blacklist Users</h4>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo base_url('blacklist/addEdit'); ?>" class="btn btn-dark" style="margin-bottom: 5px; float: right; " ><i class="fa fa-plus" aria-hidden="true"></i> Add</a>    
                                    </div> 
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>Phone</th>
                                                <th>Email Id</th>
                                                <!-- <th>Country</th> -->
                                                <th>Gender</th>
                                                <!-- <th>Date</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    
                                                    $i++;

                                                    $userName = $curEntry['firstName'].' '.$curEntry['lastName'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $userName; ?></td>
                                                    <td><?php echo $curEntry['phone']; ?></td>
                                                    <td><?php echo $curEntry['emailId']; ?></td>
                                                    <!-- <td><?php echo $curEntry['country']; ?></td> -->
                                                    <td><?php echo $curEntry['gender']; ?></td>
                                                    <!-- <td><?php echo date('d-m-Y H:i:s', strtotime($curEntry['createdDate'])); ?></td> -->
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

