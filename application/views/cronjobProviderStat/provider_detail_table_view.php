<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Provider Name</th>
                <th>Provider List</th>
                <th>Send Date</th>
                <th>Total</th>                
            </tr>
        </thead>
        <tbody>

            <?php if(count($historyProviderData) > 0){

                $i = 0;

                foreach ($historyProviderData as $providerInfo) {
                    $i++;
                    ?> 
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo getProviderName($providerInfo['providerName']); ?></td>
                        <td><?php echo ($providerInfo['providerName'] ==1 )?getAweverProviderListName($providerInfo['providerList']):getTransmitviaProviderListName($providerInfo['providerList']); ?></td>
                        <td><?php echo date("d-m-Y",strtotime($providerInfo['sendDate'])); ?></td>
                        <td><?php echo $providerInfo['totalSend']; ?></td>
                    </tr>

                <?php }

            }else{ ?>

                <tr>
                    <td colspan="5" style="text-align:center;">No Information Available</td>
                </tr>

            <?php } ?>
            
        </tbody>
    </table>
</div>