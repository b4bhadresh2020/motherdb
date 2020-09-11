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
                            <h1>Add BlackList</h1>
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
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>First Name </label>
                                                    <input type="text" class="form-control" name="firstName" value="<?php echo @$firstName;  ?>" >
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Last Name </label>
                                                    <input type="text" class="form-control" name="lastName" value="<?php echo @$lastName;  ?>" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Phone *</label>
                                                    <input type="number" class="form-control" name="phone" value="<?php echo @$phone;  ?>" >
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Email </label>
                                                    <input type="text" class="form-control" name="emailId" value="<?php echo @$emailId;  ?>" >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Gender </label>
                                                    <select class="form-control" name = "gender">
                                                        <option value="">Select Gender</option>
                                                        <option value = "male" <?php echo (@$gender == 'male') ? 'select' : ''; ?> >Male</option>
                                                        <option value = "female" <?php echo (@$gender == 'female') ? 'select' : ''; ?> >Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Country </label>
                                                    <select class="form-control" name="country">
                                                        <option value="">Select Country</option>
                                                        <?php 
                                                            foreach ($countries as $country) { ?>
                                                                <option value="<?php echo $country['country']; ?>" <?php echo (@$country == $country['country']) ? 'selected' : ''; ?> > <?php echo $country['country']; ?></option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <a href="<?php echo base_url('blacklist/manage'); ?>" class="btn btn-default" >Cancel</a>
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