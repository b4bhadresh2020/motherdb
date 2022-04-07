<script type="text/javascript">
    var BASE_URL = "<?php echo base_url(); ?>";
    var selectedProvider = "<?php echo @$provider ?>";
    var selectedcountry = "<?php echo @$country ?>";
    var selectedList     = "<?php echo @$list ?>";    

    if(selectedProvider !=""){
        $("#provider").val(selectedProvider);
    }

    if(selectedcountry !=""){
        $("#country").val(selectedcountry);
        listData(selectedProvider,selectedcountry);
    }

    if(selectedList !=""){
        setTimeout(() => {
            $("#list").val(selectedList);
        }, 1000);
    }

    $(document).ready(function(){
        $('#popupList').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: 'All'
        });
        $(".multiselect-dropdown .btn-group").css({"width":"100%"});
        $(".multiselect-dropdown .btn-group .multiselect").css({"width":"100%"});
    });

    $(document).on("change","#provider",function(){
        var country = $("#country").val();
        var provider = $(this).val();
        if(country && provider != 0){
            $('#sucErrMsg').text("").hide();
            listData(provider,country);
        }
    }); 

    $(document).on("change","#country",function(){
        var country = $(this).val();
        var provider = $("#provider").val();
        if(country && provider != 0){
            $('#sucErrMsg').text("").hide();
            listData(provider,country);
        }
        // else if(provider == 0){
        //     $('#sucErrMsg').text('Please select provider').addClass('alert alert-danger');
        // }
        else if(country == ""){
            $('#sucErrMsg').text('Please select country').addClass('alert alert-danger');
        }
    });    

    function listData(provider,country){
        $.ajax({
            url : BASE_URL + 'mailUnsubscribe/getProviderList',
            type:"post",
            data:{ provider:provider,country:country},
            success:function(response){
                var liveDiliveries = JSON.parse(response);
                $("#list").html("<option value='0'> All Select </option>");
                $.each(liveDiliveries,function(index,value){
                    $("#list").append("<option value='"+value.id+"'>"+value.displayname+"</option>");
                });                
            }

        });
    }
    $(document).on("change","#popupProvider",function(){
        var country = $("#popupCountry").val();
        var provider = $(this).val();
        if(country && provider != 0){
            popupListData(provider,country);
        }
        // if(provider != 0) {
        //     $('.custom-list-dropdown').show();
        // } else {
        //     $('.custom-list-dropdown').hide();
        // }
    }); 

    $(document).on("change","#popupCountry",function(){
        var country = $(this).val();
        var provider = $("#popupProvider").val();
        if(country && provider != 0){
            popupListData(provider,country);
            $('.custom-list-dropdown').show();
        } else {
            $('.custom-list-dropdown').hide();
        }
        // else if(provider == 0){
        //     $('#popupSucErrMsg').text('Please select provider').addClass('alert alert-danger');
        // }
        // else if(country == ""){
        //     $('#popupSucErrMsg').text('Please select country').addClass('alert alert-danger');
        // }
    });    

    function popupListData(provider,country){
        $.ajax({
            url : BASE_URL + 'mailUnsubscribe/getProviderList',
            type:"post",
            data:{ provider:provider,country:country},
            success:function(response){
                var liveDiliveries = JSON.parse(response);
                $("#popupList").html('');
                $.each(liveDiliveries,function(index,value){
                    $("#popupList").append("<option value='"+value.id+"'>"+value.displayname+"</option>");
                });                
                $("#popupList").multiselect("destroy");
                $('#popupList').multiselect({
                    includeSelectAllOption: true,
                    nonSelectedText: 'All'
                });
            }
        });
    }

    $(document).on("click",".unsubscribe",function(){
        // if($("#popupProvider").val() == 0){
        //     $("#popupSucErrMsg").html("Please select provider").show();
        // }
        // else if($("#popupCountry").val() == ""){
        //     $("#popupSucErrMsg").html("Please select country").show();
        // }
        $(".alert-success,.alert-info").html('');   
        $(".alert-success-label,.alert-success").show();  
        $(".alert-info-label,.alert-info").show();

        if($("#email").val() == ""){
            $("#popupSucErrMsg").html("Please enter email address").show();
        }else{
            $(".unsubscribe").prop('disabled', true);
            $(".page-loader").show();
            $.ajax({
                url : BASE_URL + 'mailUnsubscribe/unsubscribe',
                type:"post",
                data:$("#unsubscribeForm").serialize(),
                success:function(response){
                    $(".page-loader").hide();
                    $("#popupSucErrMsg").html("").hide();
                    // $("#unsubscribeForm").trigger("reset");
                    $("#popupList option").remove();
                    $('#popupList').multiselect('rebuild');

                    var data = JSON.parse(response);
                    if(data.queueList != ""){
                        $(".alert-success").html(data.queueList);
                    }else{
                        $(".alert-success-label,.alert-success").hide();
                    }                    
                    if(data.alreadyUnsubscribeList != ""){
                        $(".alert-info").html(data.alreadyUnsubscribeList);
                    } else {
                        $(".alert-info-label,.alert-info").hide();
                    }

                    $("#addUnsubscriber").modal('hide');
                    $("#responsePopup").modal('show');

                }
            });
        }
    });

    $(document).on("click",".close-responsePopup",function(){
        $('#responsePopup').modal('hide');
        $("#addUnsubscriber").modal('show');
        $(".unsubscribe").prop('disabled', false);
        $("#email").val('');
    });
</script>