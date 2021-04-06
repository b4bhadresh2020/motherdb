<script type="text/javascript">

	var BASE_URL = '<?php echo base_url(); ?>';   
     
    $(document).on("click",".deletedata",function(){
        var employeeId = $(this).attr("data-employeeId");
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to delete !',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url : BASE_URL + 'employee/delete/',
                        type : 'post',
                        data:{
                            employeeId:employeeId
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

    function loadEmployeeData(curObj) {
        var employeeId = JSON.parse($(curObj).attr("data-employeeId"));
        $.ajax({
            url : BASE_URL + 'employee/getEmployeeData',
            type : 'post',
            data:{
                employeeId:employeeId
            },
            success:function(response){
                var employeeData = JSON.parse(response);
                $("#adminId").val(employeeData.adminId);
                $("#adminUname").val(employeeData.adminUname);
                $("#fullname").val(employeeData.fullname);
                $("#isInActive").val(employeeData.isInActive);
                $("#role").val(employeeData.role);
                $("#password").removeAttr("required");    
                $(".passwordBlock").hide();            
            }
        });

        $('#employeePopup').modal('show');
    }  

    function addEmployee(curObj){        
        $('#employeePopup').modal('show');
    }

    $(document).ready(function(){
        $succFailMsg = "<?php echo $this->session->flashdata("message")?>";
        if($succFailMsg != ""){
            alertify.success($succFailMsg);
        }
    })
</script>    