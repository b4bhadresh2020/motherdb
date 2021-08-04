<script type="text/javascript">

	var BASE_URL = '<?php echo base_url(); ?>';

    $(document).ready(function(){
        $(document).on("click",".updatedata",function(){
            var providerId = $(this).attr("data-provider");
            var status = $(this).attr("data-status");
            $.ajax({
                url : BASE_URL + 'cronjobProviderStat/updateStatus/',
                type : 'post',
                data:{
                    id:providerId,
                    status:status
                },
                success:function(response){
                   location.reload();
                }
            });            
        });

        $(document).on("click",".deletedata",function(){
            var providerId = $(this).attr("data-provider");
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure to delete !',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url : BASE_URL + 'cronjobProviderStat/delete/',
                            type : 'post',
                            data:{
                                id:providerId
                            },
                            success:function(response){
                            location.reload();
                            }
                        });
                    },
                    cancel: function () {
                    }
                }
            });              
        });  

        $(document).on("change","#providerName",function(){
            var providerId = $(this).val();
            var providerList = "";
            if(providerId == 1){
                providerList +="<option value='1'>Velkomstgaven.com (Norway)</option>";
                providerList +="<option value='2'>Gratispresent.se (Sweden)</option>";
                providerList +="<option value='3'>Velkomstgaven.dk (Denmark)</option>";
                providerList +="<option value='4'>Freecasinodeal.com/no  (Norway)</option>";
                providerList +="<option value='5'>Freecasinodeal.com/fi (Finland)</option>";
                providerList +="<option value='6'>Freecasinodeal.com</option>";
                providerList +="<option value='7'>FI - Katariinasmail</option>";
                providerList +="<option value='8'>NO - Signesmail</option>";
                providerList +="<option value='9'>SE - Frejasmail</option>";
                providerList +="<option value='10'>CA - Getspinn</option>";
                providerList +="<option value='11'>NO - Getspinn</option>";
                providerList +="<option value='12'>NZ - Getspinn</option>";
                providerList +="<option value='13'>Freecasinodeal.com/nz (New Zealand)</option>";
                providerList +="<option value='14'>DK - Signesmail</option>";
                providerList +="<option value='15'>DK - abbie</option>";
                providerList +="<option value='16'>FI - abbie</option>";
                providerList +="<option value='17'>NO - abbie</option>";
                providerList +="<option value='18'>SE - abbie</option>";
                providerList +="<option value='19'>FreeCasinodeal/ca (Canada)</option>";
                providerList +="<option value='20'>FelinaFinans/se</option>";
                providerList +="<option value='21'>New_gratispresent</option>";
                providerList +="<option value='22'>New_velkomstgaven_dk</option>";
                providerList +="<option value='23'>New_velkomstgaven_com</option>";
                providerList +="<option value='24'>New_velkomstgaven1_com</option>";
                providerList +="<option value='25'>New_unelmalaina</option>";
                providerList +="<option value='26'>Freecasinodeal/NZ (olivia)</option>";
                providerList +="<option value='27'>Freecasinodeal/CA (sofia)</option>";
                providerList +="<option value='28'>Freecasinodeal/NO (emma)</option>";
                providerList +="<option value='29'>Freecasinodeal/FI (aida)</option>";
                providerList +="<option value='30'>Frejasmail1/SE</option>";
                providerList +="<option value='31'>Frejasmail2/SE</option>";
                providerList +="<option value='32'>Signesmail1/DK</option>";
                providerList +="<option value='33'>Katariinasmail1/FI</option>";
                providerList +="<option value='34'>Signesmail1/NO</option>";
                providerList +="<option value='35'>Signesmail2/NO</option>";
                providerList +="<option value='36'>Abbiesmail1/CA</option>";
                providerList +="<option value='37'>Abbiesmail2/CA</option>";
                providerList +="<option value='38'>Ashleysmail/NZ</option>";
                providerList +="<option value='39'>Ashleysmail1/NZ</option>";
                providerList +="<option value='40'>Signesmail/DK</option>";
                providerList +="<option value='41'>Velkomstgaven/NO</option>";
                providerList +="<option value='42'>Velkomstgaven1/NO</option>";
                providerList +="<option value='43'>Gratispresent/SE</option>";
                providerList +="<option value='44'>Gratispresent1/SE</option>";
                providerList +="<option value='45'>FelinaFinans/SE</option>";
                providerList +="<option value='46'>FelinaFinans1/SE</option>";
                providerList +="<option value='47'>FelinaFinansmail/SE</option>";
                providerList +="<option value='48'>Unelmalaina/FI</option>";
                providerList +="<option value='49'>Unelmalaina1/FI</option>";
                providerList +="<option value='50'>Velkomstgaven/DK</option>";
                providerList +="<option value='51'>Velkomstgaven1/DK</option>";
                providerList +="<option value='52'>Getspinn1/CA</option>";
                providerList +="<option value='53'>Getspinnmail/CA</option>";
                providerList +="<option value='54'>Freecamail/CA</option>";
            }else if(providerId == 2){
                providerList +="<option value='1'>NO - deveroper</option>";
                providerList +="<option value='2'>SE - deveroper - Loan</option>";
                providerList +="<option value='3'>SE - deveroper</option>";
                providerList +="<option value='4'>FI - deveroper</option>";
                providerList +="<option value='5'>NO Casino - eonoc</option>";
                providerList +="<option value='6'>FI Casino - eonoc</option>";
                providerList +="<option value='7'>FI - eacademyzone</option>";
                providerList +="<option value='8'>NO - eacademyzone</option>";
                providerList +="<option value='9'>SE - eacademyzone</option>";
                providerList +="<option value='10'>SE - Loan - eacademyzone</option>";
                providerList +="<option value='11'>Global Casino Dollars - divinecareca</option>";
                providerList +="<option value='12'>Global Casino EUR - divinecareca</option>";
                providerList +="<option value='13'>NO Casino - divinecareca</option>";
                providerList +="<option value='14'>FI - ElasticEmail</option>";
                providerList +="<option value='15'>SE - ElasticEmail</option>";
                providerList +="<option value='16'>NO - ElasticEmail</option>";
                providerList +="<option value='17'>NO - SparkPost</option>";
                providerList +="<option value='18'>SE - SparkPost</option>";
                providerList +="<option value='19'>FI - SparkPost</option>";
                providerList +="<option value='20'>NO - Amazon</option>";
                providerList +="<option value='21'>SE - Amazon</option>";
                providerList +="<option value='22'>FI - Amazon</option>";
            }else if(providerId == 4){
                providerList +="<option value='1'>SE - Test</option>";
                providerList +="<option value='2'>DK - Test</option>";
                providerList +="<option value='3'>Canada - Camilla</option>";
                providerList +="<option value='4'>Canada - Kare</option>";
                providerList +="<option value='5'>Sweden - Camilla</option>";
                providerList +="<option value='6'>Sweden - Kare</option>";
                providerList +="<option value='7'>Norway - Camilla</option>";
                providerList +="<option value='8'>Norway - Kare</option>";
                providerList +="<option value='9'>Finland  - Camilla</option>";
                providerList +="<option value='10'>Finland  - Kare</option>";
                providerList +="<option value='11'>New Zealand  - Camilla</option>";
                providerList +="<option value='12'>NO - Test</option>";
                providerList +="<option value='13'>Denmark  - Kare</option>";
                providerList +="<option value='14'>Denmark  - Camilla</option>";
                providerList +="<option value='15'>FI - Test</option>";
            }else if(providerId == 5){
                providerList +="<option value='1'>CA</option>";
            }else if(providerId == 6){
                providerList +="<option value='1'>NO</option>";
                providerList +="<option value='2'>CA</option>";
                providerList +="<option value='3'>NZ</option>";
                providerList +="<option value='4'>SE</option>";
            } else if(providerId == 7){
                providerList +="<option value='1'>NO</option>";
                providerList +="<option value='2'>CA</option>";
                providerList +="<option value='3'>SE</option>";
            } else if(providerId == 8){
                providerList +="<option value='1'>DK-Velkomstgaven</option>";
                providerList +="<option value='2'>NO-Velkomstgaven.com</option>";
                providerList +="<option value='3'>NO-Velkomstgaven.com1</option>";
                providerList +="<option value='4'>SE-Gratispresent</option>";
            } else if(providerId == 9){
                providerList +="<option value='1'>Velkomstgaven/DK</option>";
                providerList +="<option value='2'>Gratispresent/SE</option>";
                // providerList +="<option value='3'>Velkomstgaven/NOR</option>";
            } else if(providerId == 10){
                providerList +="<option value='1'>Camilla/DK</option>";
                providerList +="<option value='2'>Camilla/SE</option>";
                providerList +="<option value='3'>Camilla/NO</option>";
                providerList +="<option value='4'>Camilla/FI</option>";
                providerList +="<option value='5'>Camilla/CA</option>";
                providerList +="<option value='6'>Camilla/NZ</option>";
                providerList +="<option value='7'>Velkomstgaven/NOR</option>";
                providerList +="<option value='8'>Gratispresent/SE</option>";
                providerList +="<option value='9'>Velkomstgaven1/NOR</option>";
                providerList +="<option value='10'>Unelmalaina/FI</option>";
                providerList +="<option value='11'>Velkomstgaven/DK</option>";
            } else if(providerId == 11){
                // providerList +="<option value='1'>SE-Gratispresent</option>";
                // providerList +="<option value='2'>NO-Velkomstgaven</option>";
                // providerList +="<option value='3'>DK-Velkomstgaven</option>";
                // providerList +="<option value='4'>FI-Unelmalaina</option>";
                // providerList +="<option value='5'>FreeCasinoDeal-CA</option>";
                // providerList +="<option value='6'>FreeCasinoDeal-FI</option>";
                // providerList +="<option value='7'>FreeCasinoDeal-NO</option>";
                // providerList +="<option value='8'>FreeCasinoDeal-NZ</option>";  
                // providerList +="<option value='9'>NO-Velkomstgaven1</option>";             
            } else if(providerId == 12){
                providerList +="<option value='1'>Gratispresentmail.se</option>";
                providerList +="<option value='2'>Freecasinodeal1/no</option>";
                providerList +="<option value='3'>Freecasinodeal1/fi</option>";
                providerList +="<option value='4'>Velkomstgavenmail.dk</option>";
                providerList +="<option value='5'>Freecasinodeal1/ca</option>";
                providerList +="<option value='6'>Freecasinodeal1/nz</option>";            
            } else if(providerId == 13){
                providerList +="<option value='1'>Velkomstgaven/NOR</option>";
                providerList +="<option value='2'>GratisPresent/SE</option>";       
                providerList +="<option value='3'>Frejasmail/SE</option>";
                providerList +="<option value='4'>Unelmalaina/FI</option>";
                providerList +="<option value='5'>Signesmail/NOR</option>";
                providerList +="<option value='6'>Katariinasmail/FI</option>";
                providerList +="<option value='7'>Velkomstgaven/DK</option>";
                providerList +="<option value='8'>Signesmail/DK</option>";
            } else if(providerId == 14){
                providerList +="<option value='1'>camilla/abbiesmail2.com/CA</option>";       
                providerList +="<option value='2'>camilla/ashleysmail1.com/NZ</option>";       
                providerList +="<option value='3'>camilla/felinafinans.se/SE</option>";       
                providerList +="<option value='4'>camilla/frejasmail2.se/SE</option>";       
                providerList +="<option value='5'>camilla/katariinasmail1.com/FI</option>";       
                providerList +="<option value='6'>camilla/signesmail1.dk/DK</option>";       
                providerList +="<option value='7'>camilla/signesmail2.com/NO</option>"; 
                providerList +="<option value='8'>Kaare/NO-FreeCasinodeal</option>";       
                providerList +="<option value='9'>Kaare/FI-FreeCasinodeal</option>";       
                providerList +="<option value='10'>Kaare/CA-FreeCasinodeal</option>";       
                providerList +="<option value='11'>Kaare/NZ-FreeCasinodeal</option>";       
                providerList +="<option value='12'>Kaare/CA-GetSpinn</option>";       
                providerList +="<option value='13'>Kaare/NZ-GetSpinn</option>";       
                providerList +="<option value='14'>Kaare/NO-GetSpinn</option>";       
                providerList +="<option value='15'>Kaare/gratispresentmail.se/SE</option>";       
                providerList +="<option value='16'>Kaare/unelmalainamail.fi/Unelmalaina</option>";       
                providerList +="<option value='17'>Kaare/Velkomstgaven-NO</option>";       
                providerList +="<option value='18'>Kaare/DK-Velkomstgaven</option>";       
            } else if(providerId == 15){
                providerList +="<option value='1'>Velkomstgaven/DK</option>";
                providerList +="<option value='2'>Cathrinesmail/CA</option>";
                providerList +="<option value='3'>Cathrinesmail/DK</option>";
                providerList +="<option value='4'>Cathrinesmail/FI</option>";
                providerList +="<option value='5'>Cathrinesmail/NO</option>";
                providerList +="<option value='6'>Cathrinesmail/NZ</option>";
                providerList +="<option value='7'>Cathrinesmail/SE</option>";
                providerList +="<option value='8'>Velkomstgaven/NO</option>";
                providerList +="<option value='9'>Gratispresent/SE</option>";
                providerList +="<option value='10'>Unelmalaina/FI</option>";  
            } else if(providerId == 16){
                providerList +="<option value='1'>SE-Gratispresent</option>";
                providerList +="<option value='2'>NO-Velkomstgaven</option>";
                providerList +="<option value='3'>FI-Unelmalaina</option>";
                providerList +="<option value='4'>DK-Velkomstgaven</option>";
            } else{
                providerList +="<option value='0'>Select Provider List</option>";
            }

            $("#providerList").html(providerList);
        });
    });

    function loadHistoryData(curObj) {
        var providerId = JSON.parse($(curObj).attr("data-providerId"));
        $.ajax({
            url : BASE_URL + 'cronjobProviderStat/getProviderHistoryData',
            type : 'post',
            data:{
                providerId:providerId
            },
            success:function(response){
                $('#historyDataTable').html(response);
            }
        });

        $('#clickHistoryDataPopupForProvider').modal('show');
    }  

    function loadProviderData(curObj) {
        var providerId = JSON.parse($(curObj).attr("data-providerId"));
        $.ajax({
            url : BASE_URL + 'cronjobProviderStat/getProviderData',
            type : 'post',
            data:{
                providerId:providerId
            },
            success:function(response){
                var providerData = JSON.parse(response);
                $("#providerId").val(providerData.id);
                $("#providerName").val(providerData.providerName).change();
                $("#providerList").val(providerData.providerList);
                $("#perDayRecord").val(providerData.perDayRecord);
                $("#fromDate").val(providerData.fromDate);
                $("#startTime").val(providerData.startTime);
                $("#endTime").val(providerData.endTime);
            }
        });

        $('#editDataPopupForProvider').modal('show');
    }  
</script>    