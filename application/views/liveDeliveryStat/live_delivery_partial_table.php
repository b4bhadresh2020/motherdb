<?php

$i = 0 + $start;
foreach ($listArr as $curEntry) {
    if (@$curEntry['dataSourceType'] == 1 || @$curEntry['dataSourceType'] == 2) {
        $i++;
        $isEmailChecked = $curEntry["isEmailChecked"];

        if ($curEntry['isFail'] == 1) {
            $status = 'Failure';
        } else {
            $status = 'Success';
        }

        $sucFailMsgIndexArr = array(0 => 'Success', 1 => 'Duplicate', 2 => 'Blacklisted', 3 => 'Server Issue', 4 => 'Api Key Is Not Active', 5 => 'Email Is Required', 6 => 'Phone Is Required', 7 => 'Email Is Blank', 8 => 'Phone Is Blank', 9 => 'Invalid Email Format', 10 => 'Invalid Phone', 11 => 'Invalid Gender', 12 => 'Telia MX Block', 13 => 'Luukku MX Block', 14 => 'PP MX Block', 15 => 'User Already Unsubscribed Before', 16 => 'Yahoo MX Block', 17 => 'Icloud MX Block',  18 => 'GMX MX Block', 19 => 'Duplicate Old', 20 => 'Blacklisted IP', 21 => 'Protonmail MX Block', 22 => 'Reject By Inboxgame and Facebook Lead', 23 => 'Reject By Integromat Lead', 24 => 'Age Is Required');
?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo $sucFailMsgIndexArr[$curEntry['sucFailMsgIndex']]; ?></td>
            <td><?= !empty($curEntry['name']) ? $curEntry['name'] : '<div class="text-center">-</div>' ?></td>
            <td style="color:<?= ($isEmailChecked) ? "green" : "" ?>"><?php echo $curEntry['emailId']; ?></td>
            <td><?= !empty($curEntry['signupDate']) ? $curEntry['signupDate'] : '<div class="text-center">-</div>' ?></td>
            <td><?= !empty($curEntry['timestamp']) ? $curEntry['timestamp'] : '<div class="text-center">-</div>' ?></td>
            <td><?= !empty($curEntry['apiSource']) ? $curEntry['apiSource'] : '<div class="text-center">-</div>' ?></td>
            <td><?= !empty($curEntry['ip']) ? $curEntry['ip'] : '<div class="text-center">-</div>' ?></td>
            <td><?= !empty($curEntry['apiCountry']) ? $curEntry['apiCountry'] : '<div class="text-center">-</div>' ?></td>
            <td><?php echo date('Y-m-d H:i:s', strtotime($curEntry['createdDate'])); ?></td>
        </tr>
    <?php
    } else {
        $liveDeliveryDataId = $curEntry["liveDeliveryDataId"];
        $isEmailChecked = $curEntry["isEmailChecked"];
        $i++;

        if ($curEntry['birthdateDay'] != 0 && $curEntry['birthdateMonth'] != 0 && $curEntry['birthdateYear'] != 0) {

            $concatenateBday = $curEntry['birthdateDay'] . '-' . $curEntry['birthdateMonth'] . '-' . $curEntry['birthdateYear'];
            $birthdate = date('d-m-Y', strtotime($concatenateBday));
        } else {
            $birthdate = '';
        }

        if ($curEntry['isFail'] == 1) {
            $status = 'Failure';
        } else {
            $status = 'Success';
        }

        $sucFailMsgIndexArr = array(0 => 'Success', 1 => 'Duplicate', 2 => 'Blacklisted', 3 => 'Server Issue', 4 => 'Api Key Is Not Active', 5 => 'Email Is Required', 6 => 'Phone Is Required', 7 => 'Email Is Blank', 8 => 'Phone Is Blank', 9 => 'Invalid Email Format', 10 => 'Invalid Phone', 11 => 'Invalid Gender', 12 => 'Telia MX Block', 13 => 'Luukku MX Block', 14 => 'PP MX Block', 15 => 'User Already Unsubscribed Before', 16 => 'Yahoo MX Block', 17 => 'Icloud MX Block',  18 => 'GMX MX Block', 19 => 'Duplicate Old', 20 => 'Blacklisted IP', 21 => 'Protonmail MX Block', 22 => 'Reject By Inboxgame and Facebook Lead', 23 => 'Reject By Integromat Lead', 24 => 'Age Is Required', 25 => 'Hotmail MX Block', 26 => 'Outlook MX Block', 27 => 'Live MX Block', 28 => 'MSN MX Block');
    ?>
        <tr>

            <td><?php echo $i; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo $sucFailMsgIndexArr[$curEntry['sucFailMsgIndex']]; ?></td>
            <td><?php echo $curEntry['firstName']; ?></td>
            <td><?php echo $curEntry['lastName']; ?></td>
            <td style="color:<?= ($isEmailChecked) ? "green" : "" ?>"><?php echo $curEntry['emailId']; ?></td>
            <td><?php echo $curEntry['address']; ?></td>
            <td><?php echo $curEntry['postCode']; ?></td>
            <td><?php echo $curEntry['city']; ?></td>
            <td><?php echo $curEntry['country']; ?></td>
            <td><?php echo $curEntry['phone']; ?></td>
            <td><?php echo $curEntry['gender']; ?></td>
            <td><?php echo $birthdate; ?></td>
            <td><?php echo $curEntry['ip']; ?></td>
            <td><?php echo $curEntry['optinurl']; ?></td>
            <td><?php echo $curEntry['optindate']; ?></td>
            <td><?php echo $curEntry['source']; ?></td>
            <td><?php echo $curEntry['tag']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', strtotime($curEntry['createdDate'])); ?></td>
            <!-- <td><?php echo $curEntry['groupName']; ?></td>
            <td><?php echo $curEntry['keyword']; ?></td> -->


            <!-- <td style="text-align: center;">
                <a class="btn btn-danger btn-xs" data-deleteUrl="<?php echo site_url("liveDeliveryStat/delete/" . @$liveDeliveryDataId) ?>" href="javascript:;" onclick="javascript:deleteEntry(this);" title="Delete"><i class="ti-close"></i></a>
            </td> -->

        </tr>
<?php }
}
?>