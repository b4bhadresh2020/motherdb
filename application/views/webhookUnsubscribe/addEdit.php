<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Webhook Unsubscribe Settings</h1>
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
                    <div class="col-lg-8">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="sucErrMsg">
                                                <?php if($this->session->flashdata('success')) { ?>
                                                    <p class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></p>
                                                <?php } else if ($this->session->flashdata('error')) { ?>
                                                    <p class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="<?php echo base_url().'webhook_unsubscribe/addEdit';?>" method="post">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table table-bordered text-center">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width:20% !important">Main Provider</th>
                                                            <th class="text-center" style="width:10% !important">Mailjet</th>
                                                            <!-- <th class="text-center" style="width:10% !important">Marketing Platform</th> -->
                                                            <th class="text-center" style="width:10% !important">Ontraport</th>
                                                            <th class="text-center" style="width:10% !important">Active Campaign</th>
                                                            <th class="text-center" style="width:10% !important">Clever Reach</th>
                                                            <th class="text-center" style="width:10% !important">Omnisend</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $mainProvider = [
                                                        '9' => 'Mailjet',
                                                        '11' => 'Marketing Platform',
                                                        '12' => 'Ontraport',
                                                        '13' => 'Active Campaign',
                                                        '15' => 'Clever Reach',
                                                        '16' => 'Omnisend',
                                                    ];
                                                    foreach($unsubscribeSettings as $key => $setting){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $mainProvider[$setting['main_provider']];?></td>
                                                            <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[9]" value="1" <?=(!empty($setting['mailjet']) && $setting['mailjet'] == 1) ? 'checked' : ''?>>
                                                            </td>
                                                            <!-- <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[11]" value="1" <?=(!empty($setting['marketing_platform']) && $setting['marketing_platform'] == 1) ? 'checked' : ''?>>
                                                            </td> -->
                                                            <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[12]" value="1" <?=(!empty($setting['ontraport']) && $setting['ontraport'] == 1) ? 'checked' : ''?>>
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[13]" value="1" <?=(!empty($setting['active_campaign']) && $setting['active_campaign'] == 1) ? 'checked' : ''?>>
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[15]" value="1" <?=(!empty($setting['clever_reach']) && $setting['clever_reach'] == 1) ? 'checked' : ''?>>
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="<?=$setting['main_provider']?>[16]" value="1" <?=(!empty($setting['omnisend']) && $setting['omnisend'] == 1) ? 'checked' : ''?>>
                                                            </td>
                                                        </tr>  
                                                        <?php
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success" style="margin-top:15px;">Submit</button>
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