<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>Cronjob File Provider Status List </h4>
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
                                                <th>Country</th>
                                                <th>Total Records</th>
                                                <th>Inserted Records</th>
                                                <th>Sent Records</th>
                                                <th>Provider Name</th>
                                                <th>Provider List</th>
                                                <th>From Date</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Perday Records</th>
                                                <th>Status</th>
                                                <th>Action</th>
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
                                                        $cronStatus = "Deactive";
                                                        if ($curEntry['status'] == 1) {
                                                            $cronStatus = "Active";
                                                        }else if($curEntry['status'] == 2){
                                                            $cronStatus = "Completed";
                                                        }
                                                    ?>
                                                    <tr>                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $fileName; ?></td>
                                                        <td><?php echo $curEntry['country']; ?></td>
                                                        <td><?php echo $curEntry['totalRecords']; ?></td>
                                                        <td><?php echo $curEntry['totalInsertedRecords']; ?></td>
                                                        <td><?php echo $curEntry['totalSentRecords']; ?></td>
                                                        <td><?php echo getProviderName($curEntry['providerName']); ?></td>
                                                        <td><?php
                                                         if($curEntry['providerName'] ==  AWEBER ){
                                                            echo getAweverProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  TRANSMITVIA ){
                                                            echo getTransmitviaProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  ONGAGE ){
                                                            echo getOngageProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  SENDGRID ){
                                                            echo getSendgridProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  SENDINBLUE ){
                                                            echo getSendInBlueProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  SENDPULSE ){
                                                            echo getSendPulseProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  MAILERLITE ){
                                                            echo getMailerliteProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  MAILJET ){
                                                            echo getMailjetProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  CONVERTKIT ){
                                                            echo getConvertkitProviderListName($curEntry['providerList']);
                                                         }else if($curEntry['providerName'] ==  MARKETING_PLATFORM ){
                                                            echo getMarketingPlatformProviderListName($curEntry['providerList']);
                                                         }
                                                         ?></td>
                                                        <td><?php echo date("d-m-Y",strtotime($curEntry['fromDate'])); ?></td>
                                                        <td><?php echo $curEntry['startTime']; ?></td>
                                                        <td><?php echo $curEntry['endTime']; ?></td>
                                                        <td><?php echo $curEntry['perDayRecord']; ?></td>
                                                        <td><?php echo $cronStatus; ?></td>
                                                        <td>
                                                        <?php if(@$curEntry['status'] != 2):?>
                                                        <button class="btn <?php echo ($curEntry['status'])?"btn-danger":"btn-success"?> updatedata" data-provider="<?php echo @$curEntry['id']?>" data-status="<?php echo @$curEntry['status']?>"><?php echo ($curEntry['status'])?"Deactive":"Active"?></button>
                                                        <?php endif ?>
                                                        <?php if(@$curEntry['totalSentRecords'] > 0):?>
                                                        <a href="javascript:;" class="btn btn-info" data-providerId = '<?php echo $curEntry["id"]; ?>' onclick = "javascript:loadHistoryData(this);" title = "History">History</a>
                                                        <?php endif ?>
                                                        <?php if(@$curEntry['status'] != 2):?>
                                                        <a href="javascript:;" class="btn btn-warning" data-providerId = '<?php echo $curEntry["id"]; ?>' onclick = "javascript:loadProviderData(this);" title = "Edit"><i class="fa fa-edit"></i></a>
                                                        <?php endif ?>
                                                        <?php if(@$curEntry['totalSentRecords'] == 0):?>
                                                        <button class="btn btn-danger deletedata" data-provider="<?php echo @$curEntry['id']?>"><i class="fa fa-trash"></i></button>
                                                        <?php endif ?>                                                        
                                                        <a href="<?php echo site_url('/CronjobProviderStat/historyData/0?id='.$curEntry['id'])?>" class="btn btn-default" title = "history">User Data</a>                                                        
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
<div class="modal fade" id="clickHistoryDataPopupForProvider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5><strong>History of send data to provider</strong></h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="historyDataTable"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="javascript:;" onclick="$('#clickHistoryDataPopupForProvider').modal('hide');" class="btn btn-primary" style="margin-top:10px; ">Close</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editDataPopupForProvider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <form name="updateForm" method="post" action="<?php echo site_url('/cronjobProviderStat/updateProviderData')?>">                
                    <div class="x_panel" style="border: none;">
                        <h5><strong>Update Provider Information</strong></h5>
                        <input type="hidden" name="id" id="providerId"/>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Provider Name *</label>
                                    <select class="form-control" name="providerName" id="providerName">
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
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Provider List *</label>
                                    <select class="form-control" name="providerList" id="providerList">
                                        <option value="">Select Provider List</option>                                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Per Day Limit *</label>
                                    <input type="text" class="form-control" id="perDayRecord" name="perDayRecord">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>From Date *</label>
                                    <input type="date" class="form-control" id="fromDate" name="fromDate">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Start Time *</label>
                                    <input type="time" class="form-control" id="startTime" name="startTime">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>End Time *</label>
                                    <input type="time" class="form-control" id="endTime" name="endTime">
                                </div>
                            </div>                                                                              
                        </div>    
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="submit" class="btn btn-success">Update</button>
                                <a href="javascript:;" onclick="$('#editDataPopupForProvider').modal('hide');" class="btn btn-primary">Close</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('cronjobProviderStat/list_script'); ?>
