<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>SMS Provider</th>
                <th>Total</th>
                <th>Total (%)</th>
                <th>Sent</th>
                <th>Sent (%)</th>                
                <th>Delivered</th>                
                <th>Delivered (%)</th>  
                <th>Click</th>                
                <th>Click (%)</th>                
            </tr>
        </thead>
        <tbody>

            <?php if(count($providerData) > 0){

                $i = 0;

                foreach ($providerData as $provider => $providerInfo) {
                    $i++;
                    ?> 
                    <tr>

                        <td><?php echo $i; ?></td>
                        <td><?php echo $provider; ?></td>
                        <td><?php echo $providerInfo['total']; ?></td>
                        <td><?php echo round($providerInfo['total_per'],2); ?></td>
                        <td><?php echo $providerInfo['sent']; ?></td>
                        <td><?php echo round($providerInfo['sent_per'],2); ?></td>
                        <td><?php echo $providerInfo['delivered']; ?></td>
                        <td><?php echo round($providerInfo['delivered_per'],2); ?></td>
                        <td><?php echo $providerInfo['click']; ?></td>
                        <td><?php echo round($providerInfo['click_per'],2); ?></td>

                    </tr>

                <?php }

            }else{ ?>

                <tr>
                    <td colspan="3" style="text-align:center;">No Information Available</td>
                </tr>

            <?php } ?>
            
        </tbody>
    </table>
</div>