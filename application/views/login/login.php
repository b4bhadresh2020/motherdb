<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin :: Login</title>

    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="<?php echo base_url();?>image/logo/Logo_Badger_admin.png">
    <!-- Retina iPad Touch Icon-->
    <!-- <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff"> -->
    <!-- Retina iPhone Touch Icon-->
    <!-- <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff"> -->
    <!-- Standard iPad Touch Icon-->
    <!-- <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff"> -->
    <!-- Standard iPhone Touch Icon-->
    <!-- <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff"> -->

    <!-- Styles -->
    <link href="<?php echo base_url();?>assets/css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/themify-icons.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/lib/unix.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-primary">

    <div class="unix-login">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="<?php echo base_url();?>"><span><?php echo getConfigVal('siteTitle');?></span></a>
                        </div>
                        <div class="login-form">
                            <h4>Administrator Login</h4>
                            <?php
                                if(@$error_msg) {
                            ?>    
                            <div class="alert alert-danger"><?php echo @$error_msg; ?></div>
                            <?php 
                                }
                            ?>
                            <form method="post">
                                <div class="form-group">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" placeholder="User Name" name="adminLoginUname" autocomplete="off" value="admin">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Password" name="adminLoginPassword" value="admin">
                                </div>
                                <div class="form-group">
                                    <label>Code</label>
                                    <input type="text" class="form-control" placeholder="Code" name="gCode" autocomplete="off" value="<?php echo $gCode; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">Sign in</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>