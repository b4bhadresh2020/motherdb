<?php
  $countries = getCountry();
?>

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Campaign</h1>
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
                    <div class="col-lg-6">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="post">

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div id="sucErrMsg"></div>
                                            </div>
                                        </div>

                                        <?php if (@$suc_msg || @$error_msg) { 

                                            if (@$suc_msg) {
                                                $class = 'alert alert-success';
                                                $msg = $suc_msg;
                                            }else{
                                                $class = 'alert alert-danger';
                                                $msg = $error_msg;
                                            }

                                        ?>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class = '<?php echo $class; ?>' ><?php echo $msg; ?></div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Campaign Name *</label>
                                                    <input type="text" class="form-control" name="campaignName" value="<?php echo @$campaignName;  ?>" >
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Country *</label>
                                                    <select name="country" class="form-control">
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $countryName) { ?>
                                                            <option value="<?php echo $countryName['country']; ?>" <?php if(@$country == $countryName['country']){ echo 'selected'; } ?> ><?php echo $countryName['country']; ?></option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <a href="<?php echo base_url('campaign/manage'); ?>" class="btn btn-default" >Cancel</a>
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


