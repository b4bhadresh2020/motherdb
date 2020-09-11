<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Key</th>
                <th style="text-align: left;">Date Time</th>
            </tr>
        </thead>
        <tbody>

            <?php if(count($redirect_click_data) > 0){

                $i = 0;

                foreach ($redirect_click_data as $curEntry) {
                    
                    $i++;

                    $uniqueKey = $curEntry["uniqueKey"];
                    $clickDateTime = date('d-M-Y H:i:s',strtotime($curEntry['createdDate']));

                    ?> 

                    <tr>

                        <td><?php echo $i; ?></td>
                        <td><?php echo $uniqueKey; ?></td>
                        <td style="text-align: left;"><?php echo $clickDateTime; ?></td>

                    </tr>

                <?php }

            }else{ ?>

                <tr>
                    <td colspan="3" style="text-align:center;">This link is not active yet</td>
                </tr>

            <?php } ?>
            
        </tbody>
    </table>
</div>