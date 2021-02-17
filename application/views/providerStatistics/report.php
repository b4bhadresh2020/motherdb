<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Live Delivery Statistics</h1>
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
                <form class="form-horizontal" method="post" action="<?php echo base_url('providerStatistics/getMailProviderData/'); ?>">             
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="form-group">
                                                    <label>API Key  (Group-Keyword) *</label>
                                                    <select class="form-control" name="apikey" id="select_apikey" required>
                                                        <option value="">Select Api Key</option>
                                                            <?php 
                                                            foreach ($apikeys as $apikey) {                                                                                                             
                                                            ?>
                                                                <option value="<?php echo $apikey['apikey']; ?>" <?php echo (@$selectedApikey == $apikey['apikey'])?"selected":""?>> <?php echo $apikey['groupName'].'-'.$apikey['keyword'].' ('.$apikey['apikey'].')'; ?></option>
                                                            <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
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
                <?php if(isset($selectedApikey)){?>                                                       
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>Statistics</h4>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="card bg-success" style="background: #03a9f4;">
                                                    <div class="stat-widget-six">
                                                        <div class="stat-icon p-15">
                                                            <i class="ti-stats-down"></i>
                                                        </div>
                                                        <div class="stat-content p-t-12 p-b-12">
                                                            <div class="text-left dib">
                                                                <div class="stat-heading">Total</div>
                                                                <div class="stat-text"><?php echo isset($liveDeliveryStastic[$selectedApikey]['total'])?$liveDeliveryStastic[$selectedApikey]['total']:0;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="card bg-success" style="background: #357a38;">
                                                    <div class="stat-widget-six">
                                                        <div class="stat-icon p-15">
                                                            <i class="ti-stats-down"></i>
                                                        </div>
                                                        <div class="stat-content p-t-12 p-b-12">
                                                            <div class="text-left dib">
                                                                <div class="stat-heading">Non-Duplicate</div>
                                                                <div class="stat-text"><?php echo isset($liveDeliveryStastic[$selectedApikey]['success'])?$liveDeliveryStastic[$selectedApikey]['success']:0;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="card bg-warning" style="background: #ff9800;">
                                                    <div class="stat-widget-six">
                                                        <div class="stat-icon p-15">
                                                            <i class="ti-stats-down"></i>
                                                        </div>
                                                        <div class="stat-content p-t-12 p-b-12">
                                                            <div class="text-left dib">
                                                                <div class="stat-heading">Duplicate</div>
                                                                <div class="stat-text"><?php echo isset($liveDeliveryStastic[$selectedApikey]['duplicate'])?$liveDeliveryStastic[$selectedApikey]['duplicate']:0;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="card bg-danger">
                                                    <div class="stat-widget-six">
                                                        <div class="stat-icon p-15">
                                                            <i class="ti-stats-down"></i>
                                                        </div>
                                                        <div class="stat-content p-t-12 p-b-12">
                                                            <div class="text-left dib">
                                                                <div class="stat-heading">Failed</div>
                                                                <div class="stat-text"><?php echo isset($liveDeliveryStastic[$selectedApikey]['failed'])?$liveDeliveryStastic[$selectedApikey]['failed']:0;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="margin-top: 50px;">
                                <h4>Report Base On Provider Response</h4>
                                <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Provider</th>
                                                    <th>Provider List Name</th>
                                                    <th>Date</th>
                                                    <th>Filter</th>
                                                    <th>Success</th>
                                                    <th>Subscriber Exist</th>
                                                    <th>Auth Fail</th>
                                                    <th>Bad Request</th>
                                                    <th>Blacklist</th>
                                                    <th>Host Rejected</th>
                                                    <th>Request/Response</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    if(isset($liveDeliveryStastic)){                     
                                                    foreach ($liveDeliveryStastic[$selectedApikey]['providerDetail'] as $key => $providerStatistic) {
                                                        if($providerStatistic['isDuplicate']){
                                                            $totalRequest = $liveDeliveryStastic[$selectedApikey]['duplicate'];
                                                        }else{
                                                            $totalRequest = $liveDeliveryStastic[$selectedApikey]['success'] + $liveDeliveryStastic[$selectedApikey]['duplicate'];
                                                        }
                                                        $totalServeRequest = $providerStatistic['success']+ $providerStatistic['subscriber_exist']+$providerStatistic['auth_fail']+$providerStatistic['bad_fail']+$providerStatistic['blacklisted']+$providerStatistic['host'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $key + 1; ?></td>
                                                    <td><?php echo $providerStatistic['provider_name'] ?></td>
                                                    <td><?php echo $providerStatistic['listname'] ?></td>
                                                    <td>
                                                    <?php 
                                                        if($providerStatistic['delay']){
                                                            echo date('d-m-Y', strtotime($deliveryDate. ' + '.$providerStatistic['delay'].' days'));
                                                        }else{
                                                            echo date('d-m-Y', strtotime($deliveryDate));
                                                        }
                                                    ?>
                                                    </td>
                                                    <td><?php echo ($providerStatistic['isDuplicate'])?"Duplicate Only":"All"?></td>
                                                    <td><?php echo $providerStatistic['success']?></td>
                                                    <td><?php echo $providerStatistic['subscriber_exist']?></td>
                                                    <td><?php echo $providerStatistic['auth_fail']?></td>
                                                    <td><?php echo $providerStatistic['bad_fail']?></td>
                                                    <td><?php echo $providerStatistic['blacklisted']?></td>
                                                    <td><?php echo $providerStatistic['host']?></td>
                                                    <td><?php echo $totalRequest.' / '.$totalServeRequest?></td>
                                                </tr>
                                                <?php }} ?>
                                            </tbody>
                                        </table>
                                    </div>                               
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</div>