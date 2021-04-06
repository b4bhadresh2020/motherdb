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
                                        <h4>List of Employee</h4>
                                        <a href="javascript:;" class="btn btn-warning" data-employeeId = '' onclick = "javascript:addEmployee(this);" title = "Add" style="float: right;"><i class="fa fa-user"></i> New Employee</a>
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
                                                <th>Full Name</th>
                                                <th>Status</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(isset($listArr) && count($listArr)){
                                                    foreach ($listArr as $key => $employee) {
                                            ?>
                                            <tr>                                                        
                                                <td><?= $key+1 ?></td>
                                                <td><?= $employee["adminUname"] ?></td>
                                                <td><?= $employee["fullname"] ?></td>
                                                <td><?= ($employee["isInActive"] == 0 )?"Active":"Deactive" ?></td>
                                                <td><?= ($employee["role"])?"Employee":"Admin" ?></td>
                                                <td>
                                                <?php if($key != 0) { ?>   
                                                    <a href="javascript:;" class="btn btn-warning" data-employeeId = '<?php echo $employee["adminId"]; ?>' onclick = "javascript:loadEmployeeData(this);" title = "Edit"><i class="fa fa-edit"></i></a>                                                 
                                                    <button class="btn btn-danger deletedata" data-employeeId="<?php echo @$employee['adminId']?>"><i class="fa fa-trash"></i></button>  
                                                <?php } ?>                                          
                                                </td>
                                            </tr>
                                            <?php            
                                                    }
                                                }
                                            ?>
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
<div class="modal fade" id="employeePopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <form name="updateForm" method="post" action="<?php echo site_url('/employee/addEdit')?>">                
                    <div class="x_panel" style="border: none;">
                        <h5><strong>Employee Information</strong></h5>
                        <input type="hidden" name="adminId" id="adminId"/>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" id="adminUname" name="adminUname" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                                </div>
                            </div>                                                                                                       
                        </div> 
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select class="form-control" name="isInActive" id="isInActive" required>
                                        <option value="0">Active</option>                                                        
                                        <option value="1">Deactive</option>                                                        
                                    </select>
                                </div>
                            </div>                            
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Role *</label>
                                    <select class="form-control" name="role" id="role" required>
                                        <option value="0">Admin</option>                                                        
                                        <option value="1">Employee</option>                                                        
                                    </select>
                                </div>
                            </div>
                        </div>                        
                        <div class="row passwordBlock">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Password*</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="submit" class="btn btn-success">Submit</button>
                                <a href="javascript:;" onclick="$('#employeePopup').modal('hide');" class="btn btn-primary">Close</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('employee/list_script'); ?>
