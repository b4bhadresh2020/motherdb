
<div class="col-lg-12">
    <div class="card alert">
        <div class="card-body">
            <div class="table-responsive">
                <?php foreach($countriesStat as $country => $stat) { ?>
                    <table class="table table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="<?= count($statFileds )+1; ?>">Total Leads Sweden : <?= $country ?></th>
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
                                    
                                    <td><?= $stat['total'][$filed] ?></td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Avg per day</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    
                                    <td><?= ($day != 0) ? ceil(($stat['total'][$filed]/$day)) : 0; ?>%</td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Succes</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    
                                    <td><?= $stat['success'][$filed] ?></td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Avg per day</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    
                                    <td><?= ($day != 0) ? ceil(($stat['success'][$filed]/$day)) : 0; ?>%</td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Failure in %</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    
                                    <td><?= ($stat['fail'][$filed] != 0) ? ceil(($stat['fail'][$filed]*100)/$stat['total'][$filed]) : 0; ?>%</td>
                                    
                                <?php }  ?>
                            </tr>
                            <tr>
                                <td><b>Total Leads Accepted by ESP</b></td>
                                <?php foreach($statFileds as $filed => $day){ ?>
                                    
                                    <td><?= $stat['duplicate'][$filed] ?></td>
                                    
                                <?php }  ?>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
                