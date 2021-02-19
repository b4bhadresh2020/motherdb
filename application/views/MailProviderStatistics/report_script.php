<script type="text/javascript">
    var BASE_URL = "<?php echo base_url(); ?>";

    $(document).on("change","#providerName",function(){
        var provider = $(this).val();
        if(provider){
            $.ajax({
                url : BASE_URL + 'mailProviderStatistics/getProviderList',
                type:"post",
                data:{ provider:provider},
                success:function(response){
                    var liveDiliveries = JSON.parse(response);

                    $("#listName").html("<option value='0'> Select List Name </option>");
                    $.each(liveDiliveries,function(index,value){
                        $("#listName").append("<option value='"+value.id+"'>"+value.listname+"</option>");
                    });
                }

            })
        }else{
            $('#sucErrMsg').text('Please select Provider').addClass('alert alert-danger');
        }
    });
</script>