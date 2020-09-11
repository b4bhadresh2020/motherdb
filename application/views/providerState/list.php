<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card alert">
                            <div class="card-header">
                                <h4>Filters</h4>
                            </div>
                            <div class="card-body">
                                <div class="horizontal-form-elements">
                                    <form class="form-horizontal" method="get" action="<?php echo base_url($formUrl); ?>">
                                        <div class="row">
                                            <div class="col-lg-8">                                                
                                                <div class="row">                                                    
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>Date From</label>
                                                            <input class="form-control" type="date" name="deliveryDate" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                                        </div>        
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label>API List</label>
                                                            <select name="apikey" class="form-control">
                                                                <option value="">Select API Key</option>
                                                                <?php foreach ($apiKeys as $apikey) { ?>
                                                                    <option value="<?php echo $apikey['apikey']; ?>" <?php if(@$_GET['apikey'] == $apikey['apikey']){ echo 'selected'; } ?> ><?php echo $apikey['groupName'].'-'.$apikey['keyword'].' ('.$apikey['apikey'].')'; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <label>&nbsp;</label>
                                                        <input type="submit" value="Submit" class="form-control btn btn-dark" >
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <label>&nbsp;</label>
                                                        <input type="submit" name="reset" value="Reset" class="form-control btn btn-default" >
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
                <div class="row">
                    <!-- /# column -->
                    <?php                     
                        $totalLeads = array();
                    ?>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h4> Email List Stats <?php echo $currentProviderName;?></h4>
                                    </div>
                                    <div class="col-lg-1" style="background-color: #FFAE42;text-align: center;padding-top: 10px;border-radius: 5px;margin-left: 5px;float:right">
                                        <label>Live Repost</label>
                                    </div>
                                    <div class="col-lg-1" style="background-color: #4CAF50;text-align: center;padding-top: 10px;border-radius: 5px;float:right">
                                        <label>Live Delivery</label>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="card-body">                                
                                <div class="table-responsive" style="margin-bottom: 15px;margin-top:10px">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:20% !important"><?php echo $currentProviderName;?></th>
                                                <?php
                                                    foreach($weekDays as $day){
                                                ?>
                                                <th class="text-center"><?php echo date("d-m-Y",strtotime($day)); ?><br><?php echo date("D",strtotime($day)); ?></th>
                                                <?php        
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($providerStatusInfo as $providerListName => $statusDateInfo){ 
                                                $isLiveDelivery = 0;
                                                $isLiveRepost = 0;
                                                $apiKeywords = "";

                                                if(in_array($providerListName,$liveDeliveryProvider)){
                                                    $isLiveDelivery = 1; 
                                                }
                                                if(in_array($providerListName,$liveRepostProvider)){
                                                    $isLiveRepost = 1; 
                                                }                                                
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="javascript:void(0);"><?php echo $providerListName; ?></a>
                                                </td>
                                                <?php foreach($statusDateInfo as $curdate => $data){ 
                                                    if(!array_key_exists($curdate,$totalLeads)){
                                                        $totalLeads[$curdate] = ($data['livedelivery_success']['total']+$data['liverepost_success']['total']);    
                                                    }else{
                                                        $totalLeads[$curdate] += ($data['livedelivery_success']['total']+$data['liverepost_success']['total']);  
                                                    }
                                                ?>
                                                    <?php if($isLiveDelivery && $isLiveRepost){
                                                        $liveDeliveryTooltip = "";    
                                                        $liveRepostTooltip = "";  
                                                        foreach($data['livedelivery_success'] as $keyword => $keywordTotal){
                                                            if($keyword != "total"){
                                                                if($liveDeliveryTooltip == ""){
                                                                    $liveDeliveryTooltip = $keyword." - ".$keywordTotal;
                                                                }else{
                                                                    $liveDeliveryTooltip .= "<br>".$keyword." - ".$keywordTotal;
                                                                }
                                                            }                                                             
                                                        }
                                                        foreach($data['liverepost_success'] as $keyword => $keywordTotal){
                                                            if($keyword != "total"){
                                                                if($liveRepostTooltip == ""){
                                                                    $liveRepostTooltip = $keyword." - ".$keywordTotal;
                                                                }else{
                                                                    $liveRepostTooltip .= "<br>".$keyword." - ".$keywordTotal;
                                                                }
                                                            }     
                                                        }  
                                                    ?>
                                                    <td>                                                        
                                                        <table class="table">
                                                            <tr>
                                                                <td style="border: none;background-color:#4CAF50">
                                                                <div data-toggle="tooltip" data-placement="top" title="<?php echo !empty($liveDeliveryTooltip)? $liveDeliveryTooltip : ""; ?>">
                                                                <?php echo !empty($data['livedelivery_success']['total']) ? $data['livedelivery_success']['total'] : "&nbsp"; ?>
                                                                </div></td>
                                                                <td style="border: none;background-color:#FFAE42"><div data-toggle="tooltip" data-placement="top" title="<?php echo !empty($liveRepostTooltip)? $liveRepostTooltip : ""; ?>"><?php echo !empty($data['liverepost_success']['total']) ? $data['liverepost_success']['total'] : '&nbsp'; ?>
                                                                </div></td>
                                                            </tr>
                                                        </table>                                                        
                                                    </td>
                                                    <?php } else if($isLiveDelivery) {
                                                        $liveDeliveryTooltip = "";    
                                                        foreach($data['livedelivery_success'] as $keyword => $keywordTotal){
                                                            if($keyword != "total"){
                                                                if($liveDeliveryTooltip == ""){
                                                                    $liveDeliveryTooltip = $keyword." - ".$keywordTotal;
                                                                }else{
                                                                    $liveDeliveryTooltip .= "<br>".$keyword." - ".$keywordTotal;
                                                                }
                                                            }     
                                                        }                                                          
                                                    ?>
                                                        <td style="background-color:#4CAF50">
                                                        <div data-toggle="tooltip" data-placement="top" title="<?php echo !empty($liveDeliveryTooltip)? $liveDeliveryTooltip : ""; ?>">
                                                        <?php echo !empty($data['livedelivery_success']['total']) ? $data['livedelivery_success']['total'] : '&nbsp'; ?>
                                                        </div>
                                                        </td>
                                                    <?php } else if($isLiveRepost) {
                                                        $liveRepostTooltip = "";
                                                        foreach($data['liverepost_success'] as $keyword => $keywordTotal){
                                                            if($keyword != "total"){
                                                                if($liveRepostTooltip == ""){
                                                                    $liveRepostTooltip = $keyword." - ".$keywordTotal;
                                                                }else{
                                                                    $liveRepostTooltip .= "".$keyword." - ".$keywordTotal;
                                                                }
                                                            }     
                                                        }    
                                                    ?>
                                                        <td style="background-color:#FFAE42"><div data-toggle="tooltip" data-placement="top" title="<?php echo !empty($liveRepostTooltip)? $liveRepostTooltip : ""; ?>"><?php echo !empty($data['liverepost_success']['total']) ? $data['liverepost_success']['total'] : '&nbsp'; ?></div></td>
                                                    <?php } else { ?>
                                                        <td><?php echo !empty($data['livedelivery_success']['total']) ? $data['livedelivery_success']['total'] : '&nbsp'; ?></td>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tr>
                                            <?php } ?>
                                            <tr style="background-color: lightgrey;">
                                                <td>Total</td>
                                                <?php foreach($totalLeads as $total){ ?>
                                                    <td><?php echo !empty($total) ? $total : '&nbsp'; ?></td>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
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