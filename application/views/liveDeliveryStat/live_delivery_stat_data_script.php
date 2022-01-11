<script type="text/javascript">

    function deleteEntry(curObj) {
        deleteUrl = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = deleteUrl;
    }

    $(document).ready(function(){

        if ($('#chooseFilter').val() == 'cd') {
            $('#startEndDateDiv').show();
        }else{
            $('#startEndDateDiv').hide();
        }

        $('#chooseFilter').change(function(){
            
            if ($(this).val() == 'cd') {
                $('#startDate').val('');
                $('#endDate').val('');
                $('#startEndDateDiv').show();
            
            }else{
            
                $('#startEndDateDiv').hide();
            
            }
        });

        $('#btn-submit').click(function(){

            var chooseFilter = $('#chooseFilter').val();
            var startDate    = $('#startDate').val();
            var endDate      = $('#endDate').val();

            if (chooseFilter == 'cd') {

                if (startDate == '') {

                    $('#error_msg').text('Please select start date time').addClass('alert alert-danger');
                    $('#startDate').focus();
                    return false;

                }else if(endDate == ''){

                    $('#error_msg').text('Please select end date time').addClass('alert alert-danger');
                    $('#endDate').focus();
                    return false;

                }else if(startDate > endDate){

                    $('#error_msg').text('Start date time must be smaller than end date time').addClass('alert alert-danger');
                    $('#startDate').focus();
                    return false;

                }
            }
        });
    });

</script>

<script type="text/javascript">

    var BASE_URL = '<?php echo base_url(); ?>';
    var start = 0;
    var perPage = 20;
    var backToTopValue = 2000;
    var hasNoMoreData = false;

    var apikey = $('#apikey').val();
    var chooseFilter = $('#chooseFilter').val();
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();
    var chooseSucFailRes = $('#chooseSucFailRes').val();
    var globleSearch = $('#globleSearch').val();
    var dataSourceType = $('#hiddenDataSourceType').val();

    var filterData = {
        apikey:apikey,
        chooseFilter:chooseFilter,
        startDate:startDate,
        endDate:endDate,
        chooseSucFailRes:chooseSucFailRes,
        globleSearch:globleSearch,
        dataSourceType: dataSourceType,
    };

    $(document).ready(function(){
        getDataSourceType(apikey);
        setTimeout(function(){
            var dataSourceType = $('#hiddenDataSourceType').val(); 
            loadCampaignData(dataSourceType);
            
            $(window).scroll(function() {

                if($(window).scrollTop() + $(window).height() >= $(document).height() && hasNoMoreData == false){

                    start = start + perPage;
                    loadCampaignData(dataSourceType);
                }

                scrollFunction();
            });
        }, 1500);
    });

    function getDataSourceType(apikey){
        $.ajax({
            url : BASE_URL + 'liveDeliveryStat/getDataSourceType',
            type: 'post',
            data: {
                apikey: apikey
            },
            success: function(data) {
                var result = JSON.parse(data);
                var dataSourceType = result.dataSourceType;
                $('#hiddenDataSourceType').val(dataSourceType);

                // display live delivery stat/ inboxgame_fblead stat
                if(dataSourceType == 1) {
                    $('#inboxgameFacebookLeadList').show();
                } else {
                    $('#liveDeliveryList').show();
                }
            }
        });
    }

    function loadCampaignData(dataSourceType){
        
        //get data by ajax
        $.ajax({
            url : BASE_URL+'liveDeliveryStat/get_live_delivery_stat_data/'+ start,
            type : 'post',
            data : {
                getData:filterData
            },
            success:function(data){
                if(dataSourceType == 1) {
                    if (data != '') {
                        if(start == 0) {
                            $('#inboxgame_facebooklead__stat_data').html(data);
                        } else {
                            $('#inboxgame_facebooklead__stat_data').append(data);
                        }
                        hasNoMoreData = false;
                    } else {
                        hasNoMoreData = true;
                            $('#inboxgame_facebooklead__stat_data').append('<tr><td colspan="5" style="text-align: center;">No More Data</td></tr>');
                        if(start == 0) {
                            $('#inboxgame_facebooklead__stat_data').html('<tr><td colspan="5"> No Data Found</td></tr>');
                        }
                    }
                } else {
                    if (data != '') {
                        if (start == 0) {
                            $('#live_delivery_stat_data').html(data); 
                        }else{
                            $('#live_delivery_stat_data').append(data);   
                        }
                        hasNoMoreData = false;

                    }else{

                        hasNoMoreData = true;
                        $('#live_delivery_stat_data').append('<tr><td colspan = "19" style="text-align:center;">No More Data</td></tr>'); 

                        if (start == 0) {
                            $('#live_delivery_stat_data').html('<tr><td colspan = "19">No Data Found</td></tr>');   
                        }
                    }
                }
                
            }
        });


    }


    function scrollFunction() {

        if (document.body.scrollTop > backToTopValue || document.documentElement.scrollTop > backToTopValue) {
            $('#goToTop').show();
        } else {
            $('#goToTop').hide();
        }
    }

    function topScrollFunction() {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    }

</script>
