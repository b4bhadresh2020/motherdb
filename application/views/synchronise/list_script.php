<script type="text/javascript">
	var BASE_URL = "<?php echo base_url(); ?>";
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.btn_sync').click(function(){

			btn_val = $(this).attr('data-btn-val');
			btn_change_val = $(this).attr('data-change-val');

			$('#approvedRejectPop').modal('show');
				
		});
	});

	function proceedToSyncData(){

		$('#approvedRejectPop').modal('hide');
		$('#btn_sync_' + btn_val).hide();
		$('#btn_sync_' + btn_change_val).show();

		$.ajax({
			url : BASE_URL + 'synchronise/changeSyncStatus',
			type : 'post',
			data : {
				changeVal : btn_change_val
			},
			success:function(response){

			}
		});

	}
</script>