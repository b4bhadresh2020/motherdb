<div class="content-wrap">
    <div class="main" style="min-height: 865px;">
        <div class="container-fluid">
            <div class="row" style="display:none;">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Hello, <span>Welcome Here</span></h1>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
                <div class="col-lg-4 p-l-0 title-margin-left">
                    <div class="page-header">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li class="active"><?php echo $pageTitle;?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
            </div>
            <!-- /# row -->
            <section id="main-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card alert">
                                <div class="card-header">
                                    <h4>General Setting</h4>
                                    <div class="card-header-right-icon" style="display:none;">
                                        <ul>
                                            <li class="card-close" data-dismiss="alert"><i class="ti-close"></i></li>
                                            <li class="doc-link"><a href="#"><i class="ti-link"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="form-group">
                                            <?php
                                                if(@$error_msg) {
                                            ?>
                                                <div class="alert alert-danger"><?php echo @$error_msg; ?></div>
                                            <?php
                                                }

                                                if(@$suc_msg) {
                                            ?>
                                                <div class="alert alert-success"><?php echo @$suc_msg; ?></div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                        <form method="post" action="">
                                            
                                            <div class="form-group">
                                                <p class="text-muted m-b-15 f-s-12">Website Title</p>
                                                <input type="text" name="siteTitle" class="form-control input-rounded" placeholder="Website Title" value="<?php echo @$siteTitle;?>">
                                            </div>
                                             
                                            <input type="submit" class="btn btn-default" value="submit" name="submit">
                                        </form>

                                        
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
