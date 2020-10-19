<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <a href="<?php echo site_url('/cronjobProviderStat/manage')?>"><b>Back to cron list</b></a>
                                </div>
                            </div>
                            <div class="card-header">
                                <h4>Filters</h4>
                            </div>
                            <div class="card-body">
                                <div class="horizontal-form-elements">                                    
                                    <form class="form-horizontal" method="get" action="<?php echo base_url('CronjobProviderStat/historyData/0'); ?>">
                                        <div class="row">
                                            <div class="col-lg-8"> 
                                                <input type="hidden" name='id' value="<?php echo $_GET['id'];?>">                                               
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Email Address</label>
                                                            <input class="form-control" placeholder="Email ID" type="email" name="email" value = "<?php echo @$_GET['email']; ?>"  >
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Send Date</label>
                                                            <input class="form-control" type="date" name="sendDate" value = "<?php echo @$_GET['sendDate']; ?>"  >
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
                                        <h4>Aweber Queue User List </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email ID</th>
                                                <th>Provider Name</th>
                                                <th>Delivery Datetime</th>
                                                <th>Status</th>
                                                <th>Response</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $i++;    
                                                        $providerName = "";
                                                        switch ($curEntry['providerName']) {
                                                            case 1:
                                                                $providerName = 'Aweber';
                                                                $response = $curEntry['aweberResponse'];
                                                                break;
                                                            case 2:
                                                                $providerName = 'Transmitvia';
                                                                $response = $curEntry['transmitviaResponse'];
                                                                break;
                                                            case 3:
                                                                $providerName = 'Constant contact';
                                                                $response = '';
                                                                break;        
                                                            case 4:
                                                                $providerName = 'Ongage';
                                                                $response = $curEntry['ongageResponse'];
                                                                break;
                                                            case 5:                                                                
                                                                $providerName = 'Sendgrid';
                                                                $response = $curEntry['sendgridResponse'];
                                                                break;
                                                        }
                                                        if($curEntry['status'] == 0){
                                                            $response_staus = "Pending";
                                                            $response_messgae = "-";
                                                        }else{
                                                            $decodeResponse = json_decode($response,true);
                                                            $response_staus = "Send Success";
                                                            if($decodeResponse['result'] == "success"){
                                                                $response_messgae = "ID - ".$decodeResponse['data']['id'];
                                                            }else{
                                                                $response_messgae = $decodeResponse['error']['msg'];
                                                            }
                                                        }                                                    
                                                    ?>
                                                    <tr>                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry['firstName']; ?></td>
                                                        <td><?php echo $curEntry['lastName']; ?></td>
                                                        <td><?php echo $curEntry['emailId']; ?></td>
                                                        <td><?php echo $providerName; ?></td>
                                                        <td><?php echo ($curEntry['sendDate']!="") ? date("d-m-Y H:i:s",strtotime($curEntry['sendDate'])):''; ?></td>
                                                        <td><?php echo $response_staus; ?></td>                                  
                                                        <td><?php echo $response_messgae; ?></td>                                  
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