<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <h4>Filters</h4>
                            </div>
                            <div class="card-body">
                                <div class="horizontal-form-elements">
                                    <form class="form-horizontal" method="get" action="<?php echo base_url('ongageQueue/manage/0'); ?>">
                                        <div class="row">
                                            <div class="col-lg-8">                                                
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Email Address</label>
                                                            <input class="form-control" placeholder="Email ID" type="email" name="email" value = "<?php echo @$_GET['email']; ?>"  >
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Delivery Date</label>
                                                            <input class="form-control" type="date" name="deliveryDate" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Ongage Listname</label>
                                                            <select name="providerId" class="form-control">
                                                                <option value="">Select Ongage List</option>
                                                                <?php foreach ($ongageList as $list) { ?>
                                                                    <option value="<?php echo $list['id']; ?>" <?php if(@$_GET['providerId'] == $list['id']){ echo 'selected'; } ?> ><?php echo $list['listname']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>  
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control">
                                                                <option value="-1" <?php if(@$_GET['status'] == "-1"){ echo 'selected'; } ?>>Select Status</option>
                                                                <option value="0" <?php if(@$_GET['status'] == 0){ echo 'selected'; } ?> ><?php echo "Pending" ?></option>
                                                                <option value="1" <?php if(@$_GET['status'] == 1){ echo 'selected'; } ?> ><?php echo "Send Success" ?></option>
                                                            </select>
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
                                        <h4>Ongage Queue User List </h4>
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
                                                <th>Provider List</th>
                                                <th>Requset Datetime</th>
                                                <th>Delivery Datetime</th>
                                                <th>Status</th>
                                                <th>Ongage Response</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                        $i++;    
                                                        if($curEntry['status'] == 0){
                                                            $response_staus = "Pending";
                                                            $response_messgae = "-";
                                                        }else{
                                                            $decodeResponse = json_decode($curEntry['response'],true);
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
                                                        <td><?php echo $curEntry['providerListName']; ?></td>
                                                        <td><?php echo date("d-m-Y H:i:s",$curEntry['currentTimestamp']); ?></td>
                                                        <td><?php echo date("d-m-Y H:i:s",$curEntry['deliveryTimestamp']); ?></td>     
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