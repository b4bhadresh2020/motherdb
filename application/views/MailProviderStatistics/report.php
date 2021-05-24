<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Mail Delivery Statistics</h1>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
                <div class="col-lg-4 p-l-0 title-margin-left">
                    <div class="page-header">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="<?php echo base_url(); ?>">Dashboard</a></li>
                                <li class="active"><?php echo $headerTitle; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
            </div>
            <!-- /# row -->
            <section id="main-content"> 
                <form class="form-horizontal" method="post" action="<?php echo base_url('mailProviderStatistics/getMailProviderData/'); ?>">             
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label>Provider *</label>
                                                        <select class="form-control" name="provider" id="provider">
                                                            <option value="0">Select Provider</option>                                                        
                                                            <option value="1">Aweber</option>                                                        
                                                            <option value="2">Transmitvia</option>                                                        
                                                            <option value="4">Ongage</option>                                                        
                                                            <option value="5">Sendgrid</option>                                                        
                                                            <option value="6">Sendinblue</option> 
                                                            <option value="7">Sendpulse</option> 
                                                            <option value="8">Mailerlite</option>
                                                            <option value="9">Mailjet</option> 
                                                            <option value="10">Convertkit</option>
                                                            <option value="11">Marketing Platform</option>
                                                            <option value="13">Active Campaign</option>                                                      
                                                        </select>
                                                    </div>
                                                </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>List Name *</label>
                                                    <select class="form-control" name="list" id="list" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input class="form-control" type="date" id="deliveryDate" name="deliveryDate" value="<?php echo isset($deliveryDate)?$deliveryDate:date('Y-m-d');?>" required>
                                                </div>  
                                            </div>
                                        </div>                                    
                                        <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Report Base On Provider Response</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="table-header-font-small">
                                                <th>#</th>
                                                <th>APIKEY</th>
                                                <th>Group Name</th>
                                                <th>Keyword</th>
                                                <th>Success</th>
                                                <th>Subscriber Exist</th>
                                                <th>Auth Fail</th>
                                                <th>Bad Request</th>
                                                <th>Blacklist</th>
                                                <th>Host Rejected</th>
                                                <th>Manual Rejected</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(isset($liveDeliveryStastic) && count($liveDeliveryStastic)){                     
                                                    foreach ($liveDeliveryStastic as $key => $providerStatistic) {
                                                        $total = $providerStatistic['success'] + $providerStatistic['subscriber_exist'] + $providerStatistic['auth_fail'] + $providerStatistic['bad_fail'] + $providerStatistic['blacklisted'] + $providerStatistic['host'] + $providerStatistic['manual'];
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $providerStatistic['apikey'] ?></td>
                                                <td><?php echo $providerStatistic['groupName'] ?></td>
                                                <td><?php echo $providerStatistic['keyword'] ?></td>  
                                                <td class="green-bg text-center"><?php echo $providerStatistic['success']?></td>
                                                <td class="yellow-bg text-center"><?php echo $providerStatistic['subscriber_exist']?></td>
                                                <td class="red-bg text-center"><?php echo $providerStatistic['auth_fail']?></td>
                                                <td class="blue-bg text-center"><?php echo $providerStatistic['bad_fail']?></td>
                                                <td class="black-bg text-center"><?php echo $providerStatistic['blacklisted']?></td>
                                                <td class="sky-bg text-center"><?php echo $providerStatistic['host']?></td>
                                                <td class="text-center"><?php echo $providerStatistic['manual']?></td>
                                                <td><?php echo $total ?></td>
                                            </tr>
                                            <?php }} ?>
                                        </tbody>
                                    </table>
                                </div>                               
                            </div>
                        </div>
                    </div>
                </div>               
            </section>
        </div>
    </div>
</div>
<?php $this->load->view('mailProviderStatistics/report_script'); ?>

