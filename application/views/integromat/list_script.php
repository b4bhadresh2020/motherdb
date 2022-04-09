<script type="text/javascript">

	var BASE_URL = '<?php echo base_url(); ?>';   
     
    $(document).on("click",".deletedata",function(){
        var id = $(this).attr("data-id");
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to revoke from live delivery and delete !',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url : BASE_URL + 'integromat/delete/',
                        type : 'post',
                        data:{
                            id:id
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

    function loadHookData(curObj) {
        var id = JSON.parse($(curObj).attr("data-id"));
        $.ajax({
            url : BASE_URL + 'integromat/getHookData',
            type : 'post',
            data:{
                id:id
            },
            success:function(response){
                var hookData = JSON.parse(response);
                $("#id").val(hookData.id);
                $("#hook_name").val(hookData.hook_name);
                $("#hook_url").val(hookData.hook_url);
            }
        });

        $('#hookPopup').modal('show');
    }  

    function addHook(curObj){        
        $('#hookPopup').modal('show');
    }

    $(document).ready(function(){
        $succFailMsg = "<?php echo $this->session->flashdata("message")?>";
        if($succFailMsg != ""){
            alertify.success($succFailMsg);
        }
    })
</script>    