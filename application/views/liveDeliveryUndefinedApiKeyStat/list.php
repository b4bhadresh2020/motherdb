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
                                <form class="form-horizontal" method="get" action="<?php echo base_url('liveDeliveryUndefinedApiKeyStat/manage/0'); ?>">
                                    <div class="row">
                                        <div class="col-lg-5">

                                            <div id="error_msg"></div>
                                           
                                            <?php $filter = array(
                                                'all'   =>'All',
                                                'td'    => 'Today',
                                                'yd'    => 'Yesterday',
                                                'lSvnD'   => 'Last 7 Days (Including Today)',
                                                'lThrtyD' => 'Last 30 Days (Including Today)',
                                                'wTd'   => 'Week to Date',
                                                'mTd'   => 'Month to Date',
                                                'qTd'   => 'Quarter to Date',
                                                'yTd'   => 'Year to Date',
                                                'pw'    => 'Previous Week',
                                                'pm'    => 'Previous Month',
                                                'pq'    => 'Previous Quarter',
                                                'py'    => 'Previous Year',
                                                'cd'    => 'Custom Date'
                                            ); ?>
                                            
                                            <div class="form-group">
                                                <label>Select Different Filter Option</label>
                                                <select name="chooseFilter" id="chooseFilter" class="form-control" >

                                                    <?php foreach ($filter as $key => $value) { ?>
                                                        
                                                        <option value="<?php echo $key; ?>" <?php if($key == @$_GET['chooseFilter']){ echo 'selected'; } ?> ><?php echo $value; ?></option>

                                                    <?php } ?>

                                                </select>
                                            </div>
                                            
                                            <div class="row" id="startEndDateDiv" style="display: none;">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input class="form-control" type="date" name="startDate" id="startDate" value = "<?php echo @$_GET['startDate']; ?>"  >
                                                    </div>     
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input class="form-control" type="date" name="endDate" id="endDate" value = "<?php echo @$_GET['endDate']; ?>">
                                                    </div>    
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <input type="submit" id="btn-submit" value="Submit" class="form-control btn btn-dark" >
                                                </div>
                                                <div class="col-lg-3">
                                                    <input type="submit"  name="reset" value="Reset" class="form-control btn btn-default" >
                                                </div>
                                            </div>
                                                
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="card bg-warning">
                                                <div class="stat-widget-six">
                                                    <div class="stat-icon p-15">
                                                        <i class="ti-stats-up"></i>
                                                    </div>
                                                    <div class="stat-content p-t-12 p-b-12">
                                                        <div class="text-left dib">
                                                            <div class="stat-heading">Total Count</div>
                                                            <div class="stat-text"><?php echo $totalRejectCount; ?></div>
                                                        </div>
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

        </div>
    </div>
</div>

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
                                        <h4>Rejection In Detail</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rejection Type</th>
                                                <th>Count</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 

                                            $undefinedApiKeyCount = $rejectDetailCountsArr['undefinedApiKey'];
                                            $blankApiKeyCount = $rejectDetailCountsArr['blankApiKey'];
                                            $invalidApiKeyCount = $rejectDetailCountsArr['invalidApiKey'];

                                            if ($totalRejectCount > 0) {
                                                
                                                $undefinedApiKeyPer = ($undefinedApiKeyCount / $totalRejectCount) * 100;
                                                $undefinedApiKeyPer = reformat_number_format($undefinedApiKeyPer);

                                                $blankApiKeyPer = ($blankApiKeyCount / $totalRejectCount) * 100;
                                                $blankApiKeyPer = reformat_number_format($blankApiKeyPer);

                                                $invalidApiKeyPer = ($invalidApiKeyCount / $totalRejectCount) * 100;
                                                $invalidApiKeyPer = reformat_number_format($invalidApiKeyPer);

                                            }else{

                                                $undefinedApiKeyPer = 0;
                                                $blankApiKeyPer = 0;
                                                $invalidApiKeyPer = 0;
                                            }
                                            

                                            ?>
                                            <tr>
                                                <td>1</td>
                                                <td>Undefined API Key</td>
                                                <td><?php echo $undefinedApiKeyCount; ?></td>
                                                <td><?php echo $undefinedApiKeyPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Blank API Key</td>
                                                <td><?php echo $blankApiKeyCount; ?></td>
                                                <td><?php echo $blankApiKeyPer.' %'; ?></td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Inactive API Key</td>
                                                <td><?php echo $invalidApiKeyCount; ?></td>
                                                <td><?php echo $invalidApiKeyPer.' %'; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                    <div class="col-lg-10">
                                        <h4>Live Delivery List </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Msg</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Postcode</th>
                                                <th>City</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Birthday</th>
                                                <th>IP</th>
                                                <th>optin url</th>
                                                <th>optin date</th>
                                                <th>Created Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $liveDeliveryUndefinedApiKeyDataId = $curEntry["liveDeliveryUndefinedApiKeyDataId"];
                                                        $i++;

                                                        if ($curEntry['birthdateDay'] != 0 && $curEntry['birthdateMonth'] != 0 && $curEntry['birthdateYear'] != 0) {
                                                        
                                                            $concatenateBday = $curEntry['birthdateDay'] . '-' . $curEntry['birthdateMonth']. '-' . $curEntry['birthdateYear'];    
                                                            $birthdate = date('d-m-Y',strtotime($concatenateBday));
                                                        }else{
                                                            $birthdate = '';
                                                        }

                                                        $sucFailMsgIndexArr = array(1 => 'Undefined Api Key', 2 => 'Blank Api Key', 3 => 'Invalid Api Key');
                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $sucFailMsgIndexArr[$curEntry['sucFailMsgIndex']]; ?></td>
                                                        <td><?php echo $curEntry['firstName']; ?></td>
                                                        <td><?php echo $curEntry['lastName']; ?></td>
                                                        <td><?php echo $curEntry['emailId']; ?></td>
                                                        <td><?php echo $curEntry['address']; ?></td>
                                                        <td><?php echo $curEntry['postCode']; ?></td>
                                                        <td><?php echo $curEntry['city']; ?></td>
                                                        <td><?php echo $curEntry['phone']; ?></td>
                                                        <td><?php echo $curEntry['gender']; ?></td>
                                                        <td><?php echo $birthdate; ?></td>
                                                        <td><?php echo $curEntry['ip']; ?></td>
                                                        <td><?php echo $curEntry['optinurl']; ?></td>
                                                        <td><?php echo $curEntry['optindate']; ?></td>
                                                        <td><?php echo date('Y-m-d H:i:s',strtotime($curEntry['createdDate'])); ?></td>
                                                        

                                                        <!-- <td style="text-align: center;">
                                                            <a class="btn btn-danger btn-xs" data-deleteUrl="<?php echo site_url("liveDeliveryStat/delete/" . @$liveDeliveryDataId) ?>" href="javascript:;" onclick="javascript:deleteEntry(this);" title="Delete"><i class="ti-close"></i></a>
                                                        </td> -->

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

<div class="modal fade" id="deletePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you want to delete this Entry?</strong></h5>
                    
                    <a href="javascript:;" onclick="javascript:proceedDeleteEntry();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#deletePopup').modal('hide');" class="btn btn-primary">No</a>

                </div>
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function deleteEntry(curObj) {
        deleteUrl = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = deleteUrl;
    }

    $(document).ready(function(){

        if ($('#chooseFilter').val() == 'cd') {
            $('#startEndDateDiv').show();
        }else{
            $('#startEndDateDiv').hide();
        }

        $('#chooseFilter').change(function(){
            
            if ($(this).val() == 'cd') {
                $('#startDate').val('');
                $('#endDate').val('');
                $('#startEndDateDiv').show();
            
            }else{
            
                $('#startEndDateDiv').hide();
            
            }
        });

        $('#btn-submit').click(function(){

            var chooseFilter = $('#chooseFilter').val();
            var startDate    = $('#startDate').val();
            var endDate      = $('#endDate').val();

            if (chooseFilter == 'cd') {

                if (startDate == '') {

                    $('#error_msg').text('Please select start date').addClass('alert alert-danger');
                    $('#startDate').focus();
                    return false;

                }else if(endDate == ''){

                    $('#error_msg').text('Please select end date').addClass('alert alert-danger');
                    $('#endDate').focus();
                    return false;

                }else if(startDate > endDate){

                    $('#error_msg').text('Start date must be smaller than end date').addClass('alert alert-danger');
                    $('#startDate').focus();
                    return false;

                }
            }
        });
    });

</script>