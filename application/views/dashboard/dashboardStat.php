
<div class="col-lg-12">
    <div class="card alert">
        <div class="card-body">
            <div class="table-responsive">
                <?php
                    $currentMonth = strtolower(date('F'));
                    foreach($StatData as $country => $stat) { ?>
                    <table class="table table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="<?= count($statFileds )+1; ?>">Total Leads : <?= $country ?></th>
                            </tr>
                            <tr>
                                <th>Stat</th>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <th>
                                        <?php
                                            if($filed == 'current_month'){
                                                $fileds = "This month (".date('F').")";
                                            }else if($filed == 'current_week'){
                                                $fileds = "Last 7 Days";
                                            }else{
                                                $fileds = ucfirst(str_replace("_"," ",$filed));
                                            }
                                            echo $fileds;
                                        ?>
                                        
                                    </th>
                                <?php }  ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Total</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['total'][$filed];
                                        ?>
                                    </td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Avg per day</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            if($day != 0) {
                                                echo ceil(($stat['total'][$filed]/$day));
                                            } else  {
                                                echo 0;
                                            } 
                                        ?>
                                    </td>
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Succes</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['success'][$filed];
                                        ?>
                                    </td>
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Avg per day</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            if($day != 0) {
                                                echo ceil(($stat['success'][$filed]/$day));
                                            } else {
                                                echo 0;
                                            }  
                                        ?>
                                    </td>
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Failure in %</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            if($stat['fail'][$filed] != 0) {
                                                echo ceil(($stat['fail'][$filed]*100)/$stat['total'][$filed]);
                                            } else {
                                                echo 0;
                                            }
                                        ?>%
                                    </td>
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Total Leads Accepted by ESP</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['duplicate'][$filed];
                                        ?>
                                    </td>
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b><?= $country ?> Facebook Lead Ads</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['fb_lead_ads'][$filed];
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td><b><?= $country ?> Facebook Hosted Ads</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['fb_hosted_ads'][$filed];
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td><b>Total Facebook</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo $stat['total_fb'][$filed];
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td><b>Total Facebook lead ads integromat</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    <td>
                                        <?php 
                                            if($filed == 'current_week') {
                                                $filed = "lastSevenDay";
                                            } else if ($filed == 'current_month') {
                                                $filed = $currentMonth;
                                            } else {
                                                $filed = $filed;
                                            }
                                            echo isset($stat['total_fb_hosted_ads_integromat'][$filed])?$stat['total_fb_hosted_ads_integromat'][$filed]:0;
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
                