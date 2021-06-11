<script type="text/javascript">
	var BASEPATH = "<?php echo base_url(); ?>";
	var userApiDataLength = 0;

	$(document).ready(function() {

		$('#select_mailproviders').multiselect({
			includeSelectAllOption: true,
		});

		$('#repostScheduleForm').on('submit', function(e) {
			e.preventDefault();
			var select_apikey = $('#select_apikey').val();
			var select_mailproviders = $('#select_mailproviders').val();
			var deliveryStartDate = $('#deliveryStartDate').val();
			var deliveryEndDate =  $('#deliveryEndDate').val();
			var deliveryStartTime = $('#deliveryStartTime').val();
			var deliveryEndTime = $('#deliveryEndTime').val();
			var liveDeliveryStatus = $('#liveDeliveryStatus').val();

			if (select_apikey == '') {
				$('#sucErrMsg').text('Please select API Key').addClass('alert alert-danger');
			} else if (select_mailproviders == null) {
				$('#sucErrMsg').text('Please select Mail Providers').addClass('alert alert-danger');
			} else if(deliveryStartDate == '') {
				$('#sucErrMsg').text('Please select Start Date').addClass('alert alert-danger');
			} else if(deliveryEndDate == '') {
				$('#sucErrMsg').text('Please select End Date').addClass('alert alert-danger');
			}else if(deliveryStartTime == '') {
				$('#sucErrMsg').text('Please select Start Time').addClass('alert alert-danger');
			}else if(deliveryEndTime == '') {
				$('#sucErrMsg').text('Please select End Time').addClass('alert alert-danger');
			}else if(liveDeliveryStatus == '') {
				$('#sucErrMsg').text('Please select Data Status').addClass('alert alert-danger');
			} else {
				$('#sucErrMsg').text('').removeClass('alert alert-danger');
				//get data
				$.ajax({
					url: BASEPATH + 'RepostSchedule/addRepostSchedule',
					type: 'post',
					data: $(this).serialize(),
					success: function(response) {
						var response = JSON.parse(response);
						console.log(response);
						if (response.status == 'success') {
							$('#sucErrMsg').text(response.msg).addClass('alert alert-success');
						} else {
							$('#sucErrMsg').text(response.msg).addClass('alert alert-danger');
						}
						setTimeout(function(){
							location.reload();
						},2000);
					}
				});
			}
		});

		$("#select_apikey").change(function() {
			var selectProvider = $(this).find(':selected').attr("data-provider");
			var selectProviderVal = $(this).find(':selected').attr("data-providerid");
			var providers = selectProviderVal.split(",");
			$("#select_mailproviders").val(providers);
			$("#select_mailproviders").multiselect("refresh");
			$("#providerName").text(selectProvider);

		});
	});

	function loadrepostSchedule(curObj) {
		var repostScheduleId = JSON.parse($(curObj).attr("data-repostScheduleId"));
		$.ajax({
			url: BASEPATH + 'repostSchedule/getRepostSchedule',
			type : 'post',
			data: {
				repostScheduleId: repostScheduleId
			},
			success: function(response){
				var repostScheduleData = JSON.parse(response);
				$("#repostScheduleId").val(repostScheduleData.id);
				$('#editDeliveryStartTime').val(repostScheduleData.deliveryStartTime);
				$('#editDeliveryEndTime').val(repostScheduleData.deliveryEndTime);
				$('#editperDayRecord').val(repostScheduleData.perDayRecord);
			}
		});
		$('#editDataPopupRepostSchedule').modal('show');
	}
</script>