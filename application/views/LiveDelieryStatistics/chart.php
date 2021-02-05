<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<style>
    .btn-group{
        display: block !important;
    }
    .multiselect{
        min-width: 100%;
        padding: 10px;
        background: #fff;
        color: #000;
        text-align: left;
    }
    .caret{
        float: right;
        margin-top: 7px;
        border-top: 7px solid;
    }
</style>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Live Delivery Statistics</h1>
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
                                            <div id="sucErrMsg"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label>API Key  (Group-Keyword) *</label>
                                                <select class="form-control" name="country" id="select_apikey">
                                                    <option value="">Select Api Key</option>
                                                        <?php 
                                                        foreach ($apikeys as $apikey) {                                              
                                                        ?>
                                                            <option value="<?php echo $apikey['apikey']; ?>"> <?php echo $apikey['groupName'].'-'.$apikey['keyword'].' ('.$apikey['apikey'].')'; ?></option>
                                                        <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Date</label>
                                                <input class="form-control" type="date" id="deliveryDate" name="deliveryDate" value="<?php echo date('Y-m-d');?>">
                                            </div>  
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                    <a href="<?php echo base_url('repost/addEdit'); ?>" class="btn btn-default" >Reset</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>Chart</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="no-result">
                                    <div>
                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                        <div class="msg">No data Found</div>
                                    </div>
                                </div>
                                <div style="width:75%;">
                                    <canvas id="canvas"></canvas>
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
    $this->load->view('LiveDelieryStatistics/chartScript');
?>

