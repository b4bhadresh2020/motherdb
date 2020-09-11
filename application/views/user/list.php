<?php 
    /*$qry = "SELECT DISTINCT(city) FROM user";
    $cities = GetDatabyqry($qry);*/

    $qry = "SELECT DISTINCT(groupName) FROM group_master";
    $groupNames = GetDatabyqry($qry);

    $qry = "SELECT DISTINCT(keyword) FROM keyword_master";
    $keywords = GetDatabyqry($qry);

    $countries = getCountry();

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
                                <form class="form-horizontal" method="get" action="<?php echo base_url('userList/manage/0'); ?>">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" placeholder="Global Search" name="global" value = "<?php echo @$_GET['global']; ?>" >
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <select class="form-control" name="gender">
                                                                    <option value ="">Select Gender</option>
                                                                    <option value ="male" <?= @$_GET['gender'] == 'male' ? 'selected' : '' ?> >Male</option>
                                                                    <option value ="female" <?= @$_GET['gender'] == 'female' ? 'selected' : '' ?> >Female</option>
                                                                </select>
                                                            </div>        
                                                        </div>

                                                        <!-- <div class="col-lg-4">
                                                            <div class="form-group">
                                                                <select class="form-control" name="city">
                                                                    <option value = "">Select City</option>
                                                                    <?php foreach (@$cities as $ct) { ?>
                                                                        <option value = "<?php echo $ct['city']; ?>" <?php if(@$_GET['city'] == $ct['city']){ echo 'selected'; } ?> ><?php echo $ct['city']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>        
                                                        </div> -->

                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <select name="country" class="form-control">
                                                                    <option value="">Select Country</option>
                                                                    <?php foreach ($countries as $country) { ?>
                                                                        <option value="<?php echo $country['country']; ?>" <?php if(@$_GET['country'] == $country['country']){ echo 'selected'; } ?> ><?php echo $country['country']; ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <select class="form-control" name="groupName">
                                                                    <option value = "">Select Group</option>
                                                                    <?php foreach (@$groupNames as $gn) { ?>
                                                                        <option value = "<?php echo $gn['groupName']; ?>" <?php if(@$_GET['groupName'] == $gn['groupName']){ echo 'selected'; } ?> ><?php echo $gn['groupName']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>        
                                                        </div>

                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <select class="form-control" name="keyword">
                                                                    <option value = "">Select Keyword</option>
                                                                    <?php foreach (@$keywords as $ky) { ?>
                                                                        <option value = "<?php echo $ky['keyword']; ?>" <?php if(@$_GET['keyword'] == $ky['keyword']){ echo 'selected'; } ?> ><?php echo $ky['keyword']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>        
                                                        </div>

                                                    </div> 
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <input class="form-control" placeholder="Min Age" type="number" name="minAge" value = "<?php echo @$_GET['minAge']; ?>"  >
                                                            </div>        
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <input class="form-control" placeholder="Max Age" type="number" name="maxAge" value = "<?php echo @$_GET['maxAge']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>Start Date</label>
                                                                <input class="form-control" type="date" name="startDate" value = "<?php echo @$_GET['startDate']; ?>"  >
                                                            </div>        
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>End Date</label>
                                                                <input class="form-control" type="date" name="endDate" value = "<?php echo @$_GET['endDate']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <input type="submit" value="Submit" class="form-control btn btn-dark" >
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <input type="submit" name="reset" value="Reset" class="form-control btn btn-default" >
                                                        </div>

                                                        <?php 

                                                            $global = 0;
                                                            $gender = 0;
                                                            $city = 0;
                                                            $country = 0;
                                                            $groupName = 0;
                                                            $keyword = 0;
                                                            $minAge = 0;
                                                            $maxAge = 0;
                                                            $startDate = 0;
                                                            $endDate = 0;

                                                            if (@$_GET['global']) {
                                                                $global = $_GET['global'];
                                                            }

                                                            if (@$_GET['gender']) {
                                                                $gender = $_GET['gender'];
                                                            }

                                                            if (@$_GET['city']) {
                                                                $city = $_GET['city'];
                                                            }

                                                            if (@$_GET['country']) {
                                                                $country = $_GET['country'];
                                                            }

                                                            if (@$_GET['groupName']) {
                                                                $groupName = $_GET['groupName'];
                                                            }

                                                            if (@$_GET['keyword']) {
                                                                $keyword = $_GET['keyword'];
                                                            }

                                                            if (@$_GET['minAge']) {
                                                                $minAge = $_GET['minAge'];
                                                            }

                                                            if (@$_GET['maxAge']) {
                                                                $maxAge = $_GET['maxAge'];
                                                            }

                                                            if (@$_GET['startDate']) {
                                                                $startDate = $_GET['startDate'];
                                                            }

                                                            if (@$_GET['endDate']) {
                                                                $endDate = $_GET['endDate'];
                                                            }
                                                        ?>

                                                        <div class="col-lg-4">
                                                            <a href="<?php echo base_url('userList/exportCsv/'.$global.'/'.$gender.'/'.$city.'/'.$country.'/'.$groupName.'/'.$keyword.'/'.$minAge.'/'.$maxAge.'/'.$startDate.'/'.$endDate); ?>" class="form-control btn btn-warning" >Export CSV</a>
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
            <p style="text-align: center;">Total Entires : <?php echo $dataCount; ?> / <?php echo $totalUserCount; ?></p>
            <!-- /# row -->
            <section id="main-content">

                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <h4>User List </h4>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Postcode</th>
                                                <th>City</th>
                                                <th>Country</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Birthday</th>
                                                <th>IP</th>
                                                <th>Participated</th>
                                                <th>Campaign Source</th>
                                                <th>Group Name</th>
                                                <th>Keyword</th>
                                                <!-- <th>Custom</th> -->
                                                <th>Black List</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 0 + $start;
                                                foreach ($listArr as $curEntry) {
                                                    $userId = $curEntry["userId"];
                                                    $i++;

                                                    if ($curEntry['birthdateDay'] != 0 && $curEntry['birthdateMonth'] != 0 && $curEntry['birthdateYear'] != 0) {
                                                        
                                                        $concatenateBday = $curEntry['birthdateDay'] . '-' . $curEntry['birthdateMonth']. '-' . $curEntry['birthdateYear'];    
                                                        $birthdate = date('d-m-Y',strtotime($concatenateBday));
                                                    }else{
                                                        $birthdate = '';
                                                    }
                                                    
                                                    $otherLable = '';
                                                    if ($curEntry['otherLable'] != '') {
                                                        $otherLable = json_decode($curEntry['otherLable'],TRUE);
                                                    }

                                                    $other = '';
                                                    if ($curEntry['other'] != '') {
                                                        $other = json_decode($curEntry['other'],TRUE);
                                                    }                                                    

                                                    ?>
                                                    <tr>
                                                        
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $curEntry["firstName"]; ?></td>
                                                        <td><?php echo $curEntry["lastName"]; ?></td>
                                                        <td><?php echo $curEntry["emailId"]; ?></td>
                                                        <td><?php echo $curEntry["address"]; ?></td>
                                                        <td><?php echo $curEntry["postCode"]; ?></td>
                                                        <td><?php echo $curEntry["city"]; ?></td>
                                                        <td><?php echo $curEntry["country"]; ?></td>
                                                        <td><?php echo $curEntry["phone"]; ?></td>
                                                        <td><?php echo $curEntry["gender"]; ?></td>
                                                        <td><?php echo $birthdate; ?></td>
                                                        <td><?php echo $curEntry["ip"]; ?></td>
                                                        <td><?php echo $curEntry["participated"]; ?></td>
                                                        <td><?php echo $curEntry["campaignSource"]; ?></td>
                                                        <td><?php echo $curEntry["groupName"]; ?></td>
                                                        <td><?php echo $curEntry["keyword"]; ?></td>

                                                        <!-- <?php if ($otherLable != '' && count($otherLable) > 0) { ?>
                                                            
                                                            <td>
                                                                <table border="1">
                                                                    <tr>
                                                                        <?php foreach ($otherLable as $value) { ?>
                                                                            <th><?= $value; ?></th>    
                                                                        <?php } ?>
                                                                        
                                                                    </tr>
                                                                    <tr>
                                                                        <?php foreach ($other as $value) { ?>
                                                                            <th><?= $value; ?></th>    
                                                                        <?php } ?>
                                                                    </tr>
                                                                </table>
                                                            </td>

                                                        <?php }else{ ?>

                                                            <td></td>

                                                        <?php } ?> -->
                                                        
                                                        <td style="text-align: center;">
                                                            <!-- <a href="javascript:;" class="btn btn-danger btn-xs" data-deleteUrl = "<?php echo site_url("userList/delete/" . @$userId."/".$start); ?>" onclick = "javascript:deleteEntry(this);" title = "Delete"><i class="ti-close"></i></a> -->
                                                            <a href="javascript:;" class="btn btn-info btn-xs" data-unlink-url = "<?php echo site_url("userList/unlink/" . $userId."/".$start); ?>" onclick = "javascript:unlinkEntry(this);" title = "Add in Black List"><i class="ti-unlink"></i></a>
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


<div class="modal fade" id="deletePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you want Delete this Entry?</strong></h5>
                    
                    <a href="javascript:;" onclick="javascript:proceedDeleteEntry();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#deletePopup').modal('hide');" class="btn btn-primary">No</a>

                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unlinkPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 30%;top: 15%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="x_panel" style="border: none;">
                    <h5 style="margin-bottom:10px;"><strong>Do you really want to add this User to Black List ?</strong></h5>
                    
                    <a href="javascript:;" onclick="javascript:proceedUnlinkEntry();" class="btn btn-success btn-delete">Yes</a>
                    <a href="javascript:;" onclick="$('#unlinkPopup').modal('hide');" class="btn btn-primary">No</a>

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

    function unlinkEntry(curObj) {
        unlinkUrl = $(curObj).attr("data-unlink-url");
        $('#unlinkPopup').modal('show');
    }
    function proceedUnlinkEntry() {
        window.location.href = unlinkUrl;
    }

</script>