<script type="text/javascript">
	var BASE_URL = '<?php echo base_url(); ?>';
</script>

<script type="text/javascript">

	var totalRecordCount = 0;

	$(document).ready(function(){

		$('#sucErrMsg').text('').removeClass('alert alert-danger alert-success alert-primary');

		$('#btn_delete').click(function(){

			var country = $('#country').val();
			var groupName = $('#groupName').val();
			var keyword = $('#keyword').val();

			if (country == '' && groupName == '' && keyword == '') {

				$('#sucErrMsg').text('Country OR Group Name OR Keyword is required').addClass('alert alert-danger');
				return false;

			}else{
				$('#sucErrMsg').text('').removeClass('alert alert-danger');
				$('#approvedRejectPop').modal('show');
			}
		});


	});


	function proceedToDeleteData(){

		$('#approvedRejectPop').modal('hide');

		var country = $('#country').val();
		var groupName = $('#groupName').val();
		var keyword = $('#keyword').val();

		var detail = {
			country : country,
			groupName : groupName,
			keyword : keyword
		}

		$.ajax({

			url : BASE_URL + 'delete/getDeleteDataCount',	
			type : 'post',
			data : detail,
			success : function(result){
				
				var result = JSON.parse(result);
				
				if (result.err == 1) {

					$('#sucErrMsg').text(result.msg).addClass('alert alert-danger');
					return false;

				}else if (result.err == 0) {

					totalRecordCount = result.count;
					$('#sucErrMsg').text("Please do not refresh the page. " + totalRecordCount + ' records are remiaing.' ).addClass('alert alert-info');			

					recursivelyRemoveFunction(detail);

				}else{

					$('#sucErrMsg').text("Something went wrong. Please try again later.").addClass('alert alert-danger');
					reloadPage();
					return false;
				}
				
			}
		});
	}


	function recursivelyRemoveFunction(detail){
		
		$.ajax({
			url : BASE_URL + 'delete/deleteDataRecursively',	
			type : 'post',
			data : detail,
			success : function(result){

				var result = JSON.parse(result);
				if (result.err == 1) {

					$('#sucErrMsg').text(result.msg).addClass('alert alert-danger');
					return false;

				}else if(result.err == 2){

					$('#sucErrMsg').text(result.msg).addClass('alert alert-success');
					reloadPage();
					return false;	

				}else if(result.err == 0){

					totalRecordCount = totalRecordCount-result.count;
					$('#sucErrMsg').text("Please do not refresh the page. " + totalRecordCount + ' records are remiaing.' ).addClass('alert alert-info');	
					recursivelyRemoveFunction(detail);
				}else{

					$('#sucErrMsg').text("Something went wrong. Please try again later.").addClass('alert alert-danger');
					reloadPage();
                    return false;
				}
			}
		});
	}


	function reloadPage(){
		var timeoutVar = setTimeout(function(){ 
            clearTimeout(timeoutVar);
            location.reload();
        }, 2000);
	}
</script>