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
        }else if(provider == 0){
            $('#sucErrMsg').text('Please select provider').addClass('alert alert-danger');
        }else if(country == ""){
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
                $("#list").html("<option value='0'> Select List Name </option>");
                $.each(liveDiliveries,function(index,value){
                    $("#list").append("<option value='"+value.id+"'>"+value.listname+"</option>");
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
    }); 

    $(document).on("change","#popupCountry",function(){
        var country = $(this).val();
        var provider = $("#popupProvider").val();
        if(country && provider != 0){
            popupListData(provider,country);
        }else if(provider == 0){
            $('#popupSucErrMsg').text('Please select provider').addClass('alert alert-danger');
        }else if(country == ""){
            $('#popupSucErrMsg').text('Please select country').addClass('alert alert-danger');
        }
    });    

    function popupListData(provider,country){
        $.ajax({
            url : BASE_URL + 'mailUnsubscribe/getProviderList',
            type:"post",
            data:{ provider:provider,country:country},
            success:function(response){
                var liveDiliveries = JSON.parse(response);
                $.each(liveDiliveries,function(index,value){
                    $("#popupList").append("<option value='"+value.id+"'>"+value.listname+"</option>");
                });                
                $("#popupList").multiselect("destroy");
                $('#popupList').multiselect({
                    includeSelectAllOption: true,
                });
            }
        });
    }

    $(document).on("click",".unsubscribe",function(){
        if($("#popupProvider").val() == 0){
            $("#popupSucErrMsg").html("Please select provider").show();
        }else if($("#popupCountry").val() == ""){
            $("#popupSucErrMsg").html("Please select country").show();
        }else if($("#popupList :selected").length == 0){
            $("#popupSucErrMsg").html("Please select list").show();
        }else if($("#email").val() == ""){
            $("#popupSucErrMsg").html("Please enter email address").show();
        }else{
            $(".unsubscribe").prop('disabled', true);
            $.ajax({
                url : BASE_URL + 'mailUnsubscribe/unsubscribe',
                type:"post",
                data:$("#unsubscribeForm").serialize(),
                success:function(response){
                    $("#popupSucErrMsg").html("").hide();
                    $("#unsubscribeForm").trigger("reset");
                    $("#popupList option").remove();
                    $('#popupList').multiselect('rebuild');

                    var data = JSON.parse(response);
                    
                    if(data.successList != ""){
                        $(".alert-success").html(data.successList);
                    }else{
                        $(".alert-success-label,.alert-success").hide();
                    }
                    if(data.failList != ""){
                        $(".alert-danger").html(data.failList);
                    }else{
                        $(".alert-danger-label,.alert-danger").hide();
                    }

                    $("#addUnsubscriber").modal('hide');
                    $("#responsePopup").modal('show');

                }
            });
        }
    });
</script>