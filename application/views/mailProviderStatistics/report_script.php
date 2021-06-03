<script type="text/javascript">
    var BASE_URL = "<?php echo base_url(); ?>";
    var selectedProvider = "<?php echo @$provider ?>";
    var selectedList     = "<?php echo @$list ?>";

    if(selectedProvider !=""){
        $("#provider").val(selectedProvider);
        listData(selectedProvider);
    }

    if(selectedList !=""){
        setTimeout(() => {
            $("#list").val(selectedList);
        }, 1000);
    }

    $(document).on("change","#provider",function(){
        var provider = $(this).val();
        if(provider){
            listData(provider);
        }else{
            $('#sucErrMsg').text('Please select Provider').addClass('alert alert-danger');
        }
    });

    function listData(provider){
        $.ajax({
            url : BASE_URL + 'mailProviderStatistics/getProviderList',
            type:"post",
            data:{ provider:provider},
            success:function(response){
                var liveDiliveries = JSON.parse(response);

                $("#list").html("<option value='0'> Select List Name </option>");
                $.each(liveDiliveries,function(index,value){
                    $("#list").append("<option value='"+value.id+"'>"+value.listname+"</option>");
                });
            }

        });
    }
</script>