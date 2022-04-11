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
                                        <h4>List of Integromat Hooks</h4>
                                        <a href="javascript:;" class="btn btn-warning" data-id = '' onclick = "javascript:addHook(this);" title = "Add" style="float: right;"><i class="fa fa-user"></i> New Hook</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Hook Name</th>
                                                <th>Hook Url</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(isset($listArr) && count($listArr)){
                                                    foreach ($listArr as $key => $hook) {
                                            ?>
                                            <tr>                                                        
                                                <td><?= $key+1 ?></td>
                                                <td><?= $hook["hook_name"] ?></td>
                                                <td><?= $hook["hook_url"] ?></td>
                                                <td>
                                                    <a href="javascript:;" class="btn btn-warning" data-id = '<?php echo $hook["id"]; ?>' onclick = "javascript:loadHookData(this);" title = "Edit"><i class="fa fa-edit"></i></a>                                                 
                                                    <button class="btn btn-danger deletedata" data-id="<?php echo @$hook['id']?>"><i class="fa fa-trash"></i></button>  
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
<div class="modal fade" id="hookPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog msg-delete-box" role="document" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-body">
                <form name="updateForm" method="post" action="<?php echo site_url('/integromat/addEdit')?>">                
                    <div class="x_panel" style="border: none;">
                        <h5><strong>Hook Information</strong></h5>
                        <input type="hidden" name="id" id="id"/>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Hook Name</label>
                                    <input type="text" class="form-control" id="hook_name" name="hook_name" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Hook Url</label>
                                    <input type="text" class="form-control" id="hook_url" name="hook_url" required>
                                </div>
                            </div>                                                                                                       
                        </div>                 
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="submit" class="btn btn-success">Submit</button>
                                <a href="javascript:;" onclick="$('#hookPopup').modal('hide');" class="btn btn-primary">Close</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('integromat/list_script'); ?>
