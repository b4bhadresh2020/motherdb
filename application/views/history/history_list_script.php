<script type="text/javascript">
    
    function deleteEntry(curObj) {
        delete_url = $(curObj).attr("data-deleteUrl");
        $('#deletePopup').modal('show');
    }
    function proceedDeleteEntry() {
        window.location.href = delete_url;
    }

</script>

<script type="text/javascript">

	var BASE_URL = '<?php echo base_url(); ?>';
	var totalRecordCount = 0;
	var deleteUrl = '';


	$(document).ready(function(){

		var isImported = $('#isImported').val();
		var userSelected = '';
		var enrichmentSelected = '';
		var blacklistSelected = '';
		var withMergeSelected = '';
		var withoutMergeSelected = '';

		var fileModuleTypeVal = $('#fileModuleTypeVal').val();

		if (fileModuleTypeVal == 'user') {
			userSelected = 'selected';
		}else if(fileModuleTypeVal == 'enrichment'){
			enrichmentSelected = 'selected';
		}else if(fileModuleTypeVal == 'blacklist'){
			blacklistSelected = 'selected';
		}else if(fileModuleTypeVal == 'with_merge'){
			withMergeSelected = 'selected';
		}else if(fileModuleTypeVal == 'without_merge'){
			withoutMergeSelected = 'selected';
		}

		if (isImported == '') {
			var html = "<option value =''>Select Module</option>";
		}else if (isImported == 1) {
			var html = "<option value =''>Select Module</option>";
				html += "<option value = 'user' "+userSelected+">User</option>";
				html += "<option value = 'enrichment' "+enrichmentSelected+">Enrichment</option>";
				html += "<option value = 'blacklist' "+blacklistSelected+">Blacklist</option>";
		}else if(isImported == 0){
			var html = "<option value =''>Select Module</option>";
				html += "<option value = 'user' "+userSelected+">User</option>";
				html += "<option value = 'with_merge' "+withMergeSelected+">SMS Data With Merge</option>";
				html += "<option value = 'without_merge' "+withoutMergeSelected+">SMS Data Without Merge</option>";
		}

		$('#fileModuleType').html(html);

		$('#isImported').change(function(){
			var isImported = $(this).val();

			if (isImported == '') {
				var html = "<option value =''>Select Module</option>";
			}else if (isImported == 1) {
				var html = "<option value =''>Select Module</option>";
					html += "<option value = 'user'>User</option>";
					html += "<option value = 'enrichment'>Enrichment</option>";
					html += "<option value = 'blacklist'>Blacklist</option>";
			}else if(isImported == 0){
				var html = "<option value =''>Select Module</option>";
					html += "<option value = 'user'>User</option>";
					html += "<option value = 'with_merge'>SMS Data With Merge</option>";
					html += "<option value = 'without_merge'>SMS Data Without Merge</option>";
			}

			$('#fileModuleType').html(html);
		});
	});

    function deleteFileEntry(curObj) {
    	
    	$('#sucErrMsg').text('').removeClass('alert alert-danger alert-success alert-primary');
        deleteUrl = $(curObj).attr("data-delete-url");
        $('#approvedRejectPop').modal('show');
    }

    function proceedToDeleteFileData(){

    	var detail = getUrlVars(deleteUrl);
		$('#approvedRejectPop').modal('hide');

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


	// Read a page's GET URL variables and return them as an associative array.
	function getUrlVars(getUrl) {

	    var vars = {}, hash;
	    var hashes = getUrl.slice(getUrl.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	        hash = hashes[i].split('=');

	        if (hash[0] != '') {
	        	vars[hash[0]] = hash[1];	
	        }
	    }
	    return vars;
	}

</script>