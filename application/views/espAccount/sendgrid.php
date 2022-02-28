<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName"><?php echo $headerTitle; ?></h1>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>account List</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                    <thead>
                                            <tr class="table-header-font-small">
                                                <th>#</th>
                                                <th>Email</th>
                                                <th style="width: 40%;">Password</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(isset($listArr) && count($listArr)){                     
                                                    foreach ($listArr as $key => $account) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php
                                                                $page = $this->uri->segment(3); 
                                                                if(empty($page)) {
                                                                    $page = 0;
                                                                }
                                                                echo $page + $key + 1;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $account['email_id']; ?></td>
                                                        <td style="width:40%;">
                                                            <div class="pass-main">
                                                                <input type="password" class="password" value="<?php echo $account['email_password']; ?>" disabled/>
                                                                <i class="ti ti-eye show-pass-icon" title="show password"></i>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="toggle-button-cover custom-toggle">
                                                                <div class="button-cover">
                                                                    <div class="button b2" id="switch-btn">
                                                                        <input type="checkbox" class="checkbox" <?=($account['status'] == 1 ? 'checked':'')?> value="<?=$account['id']?>" data-esp="5"/>
                                                                        <div class="knobs"></div>
                                                                        <div class="layer"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php }} ?>
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
            </section>
        </div>
    </div>
</div>
<?php
    $this->load->view('espAccount/espAccountScript');
?>