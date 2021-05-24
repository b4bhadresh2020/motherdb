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
                    $providerListName = "";
                    if($providerInfo['providerName'] == AWEBER){
                        $providerListName = getAweverProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == TRANSMITVIA){
                        $providerListName = getTransmitviaProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == ONGAGE){
                        $providerListName = getOngageProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == SENDGRID){
                        $providerListName = getSendgridProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == SENDINBLUE){
                        $providerListName = getSendInBlueProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == SENDPULSE){
                        $providerListName = getSendPulseProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == MAILERLITE){
                        $providerListName = getMailerliteProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == MAILJET){
                        $providerListName = getMailjetProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == CONVERTKIT){
                        $providerListName = getConvertkitProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == MARKETING_PLATFORM){
                        $providerListName = getMarketingPlatformProviderListName($providerInfo['providerList']);
                    }else if($providerInfo['providerName'] == ONTRAPORT){
                        $providerListName = getOntraportProviderListName($providerInfo['providerList']);
                    }
                    ?> 
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo getProviderName($providerInfo['providerName']); ?></td>
                        <td><?php echo $providerListName; ?></td>
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