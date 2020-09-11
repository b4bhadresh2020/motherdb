<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <?php

                    $totalCount = count($onlyCountry);   
                    for ($i=0; $i < $totalCount; $i++) { 
                        $onlyCountryEXP = explode(',', $onlyCountry[$i]);

            

                        if ($i % 6 == 0) { ?>
                            <div class="row">
                        <?php } ?>
                                <div class="col-lg-2">
                                    <div class="card">
                                        <div class="stat-widget-eight">
                                            <div class="stat-header">
                                                <div class="header-title pull-left">
                                                    <?php echo $onlyCountryEXP[0]; ?>
                                                    <span class="country_total">Total: <?php echo number_format($onlyCountryEXP[1]); ?></span>        
                                                </div>

                                            </div>

                                            <?php 
                                                foreach ($countryKeywordPers[$onlyCountryEXP[0]] as $key => $value) { ?>
                                                    <?php 
                                                        $valueEXP = explode(',', $value);
                                                    ?>

                                                    <div class="clearfix"></div>
                                                    <div class="stat-content">
                                                        <div class="pull-left">
                                                            <span class="stat-digit"> <?php echo $key; ?> </span>
                                                        </div>
                                                        <div class="pull-right">
                                                            <span class="progress-stats">
                                                                <div style="text-align: right;"><?php echo $valueEXP[0]; ?>%</div>
                                                                <div style="text-align: right;">(<?php echo number_format($valueEXP[1])?>)</div>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary w-<?php echo ceil($valueEXP[0]/5) * 5; ?>" role="progressbar" aria-valuenow="<?php echo ceil($valueEXP[0]/5) * 5; ?>" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div> 
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                        <?php if(($i+1) % 6 == 0 || ($i+1) == $totalCount){ ?>
                            </div>
                        <?php } ?>     
                            
                    <?php } 
                ?>

            </section>
        </div>
    </div>
</div>

