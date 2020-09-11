<?php 
    $qry = "SELECT DISTINCT(domainName) FROM loan_master";
    $domainNames = GetDatabyqry($qry);

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
                                <form class="form-horizontal" method="get" action="<?php echo base_url('salusList/manage/0'); ?>">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <select class="form-control" name="domainName">
                                                            <option value = "">Select Domain</option>
                                                            <?php foreach (@$domainNames as $domain) { ?>
                                                                <option value = "<?php echo $domain['domainName']; ?>" <?php if(@$_GET['domainName'] == $domain['domainName']){ echo 'selected'; } ?> ><?php echo $domain['domainName']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>        
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <select class="form-control" name="status">
                                                            <option value = "">Select Status</option>
                                                            <option value="0" <?php if(@$_GET['status'] == '0'){ echo 'selected'; } ?> >Original</option>
                                                            <option value="1" <?php if(@$_GET['status'] == '1'){ echo 'selected'; } ?> >Duplicate</option>
                                                            <option value="2" <?php if(@$_GET['status'] == '2'){ echo 'selected'; } ?> >Lowquality</option>
                                                        </select>
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
                                <h4>Salus List </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Loan Amount</th>
                                                <th>Loan Period</th>
                                                <th>Domain</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    $i++;

                                                    $status = '';

                                                    if ($curEntry['status'] == '0') {
                                                        $status = 'Original';                                                        
                                                    }else if($curEntry['status'] == '1'){
                                                        $status = 'Duplicate';
                                                    }else if($curEntry['status'] == '2'){
                                                        $status = 'Lowquality';
                                                    }

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["emailId"]; ?></td>
                                                        <td><?php echo $curEntry["phone"]; ?></td>
                                                        <td><?php echo $curEntry["loanAmount"]; ?></td>
                                                        <td><?php echo $curEntry["loanPeriod"]; ?></td>
                                                        <td><?php echo $curEntry["domainName"]; ?></td>
                                                        <td><?php echo $status; ?></td>
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

