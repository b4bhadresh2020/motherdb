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
                providerList +="<option value='1'>Australia-camilla</option>";
                providerList +="<option value='2'>Australia - Kare</option>";
                providerList +="<option value='3'>Canada - Camilla</option>";
                providerList +="<option value='4'>Canada - Kare</option>";
                providerList +="<option value='5'>Sweden - Camilla</option>";
                providerList +="<option value='6'>Sweden - Kare</option>";
                providerList +="<option value='7'>Norway - Camilla</option>";
                providerList +="<option value='8'>Norway - Kare</option>";
                providerList +="<option value='9'>Finland  - Camilla</option>";
                providerList +="<option value='10'>Finland  - Kare</option>";
            }else{
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