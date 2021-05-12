<script type="text/javascript">
	var BASEPATH = "<?php echo base_url(); ?>";
	var userApiDataLength = 0;

	$(document).ready(function() {

		$('#select_mailproviders').multiselect({
			includeSelectAllOption: true,
		});

		$('#btn_insert').click(function() {

			var select_apikey = $('#select_apikey').val();
			var select_mailproviders = $('#select_mailproviders').val();

			if (select_apikey == '') {
				$('#sucErrMsg').text('Please select API Key').addClass('alert alert-danger');
			} else if (select_mailproviders == null) {
				$('#sucErrMsg').text('Please select Mail Providers').addClass('alert alert-danger');
			} else {
				$('#sucErrMsg').text('').removeClass('alert alert-danger');
				//get data
				$.ajax({
					url: BASEPATH + 'repost/getApiKeyData',
					type: 'post',
					dataType: 'json',
					data: {
						apikey: select_apikey
					},
					success: function(response) {

						if (response.err == 1) {
							$('#sucErrMsg').text(response.msg).addClass('alert alert-danger');
						} else if (response.err == 0) {
							var apiData = response.apiData;
							var groupName = response.groupName;
							var keyword = response.keyword;

							userApiDataLength = apiData.length;
							//Below comment line for live delivery providers
							//$providers = JSON.parse(response.provider);

							//Below provider is selected provider from dropdown.
							$providers = select_mailproviders;
							$.each($providers, function(index, provider) {
								if (provider == 'egoi') {
									sendEmailToEgoi(apiData);
								} else {
									// Here we check provider and call method.
									getProvider(provider, apiData, groupName, keyword);
								}
							});
						}
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

	function getProvider(provider, apiData, groupName, keyword) {
		var providerId = 0;
		$.ajax({
			url: BASEPATH + 'repost/getProvider',
			type: 'post',
			data: {
				id: provider
			},
			success: function(response) {
				providerId = response;
				if (providerId == 1) {
					sendEmailToAweber(apiData, provider, groupName, keyword);
				} else if (providerId == 2) {
					sendEmailToTransmitvia(apiData, provider, groupName, keyword);
				} else if (providerId == 3) {
					sendEmailToConstantContact(apiData, provider, groupName, keyword);
				} else if (providerId == 4) {
					sendEmailToOngage(apiData, provider, groupName, keyword);
				} else if (providerId == 5) {
					sendEmailToSendgrid(apiData, provider, groupName, keyword);
				} else if (providerId == 6) {
					sendEmailToSendInBlue(apiData, provider, groupName, keyword);
				} else if (providerId == 7) {
					sendEmailToSendpulse(apiData, provider, groupName, keyword);
				}
			}
		});
	}

	var sendEmailToEgoiCount = 0;
	var sendEmailToAweberCount = 0;
	var sendEmailToConstantContactCount = 0;
	var sendEmailToTransmitviaCount = 0;
	var sendEmailToOngageCount = 0;
	var sendEmailToSendgridCount = 0;
	var sendEmailToSendInBlueCount = 0;
	var sendEmailToSendpulseCount = 0;

	function sendEmailToEgoi(apiData) {
		var apiDataDetail = apiData[sendEmailToEgoiCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToEgoi',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToEgoiCount++;
				if (sendEmailToEgoiCount < apiData.length) {
					sendEmailToEgoi(apiData);
				}

			}
		});
	}

	function sendEmailToAweber(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToAweberCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToAweber',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToAweberCount++;
				if (sendEmailToAweberCount < apiData.length) {
					sendEmailToAweber(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToConstantContact(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToConstantContactCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToConstantContact',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToConstantContactCount++;
				if (sendEmailToConstantContactCount < apiData.length) {
					sendEmailToConstantContact(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToTransmitvia(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToTransmitviaCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToTransmitvia',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToTransmitviaCount++;
				if (sendEmailToTransmitviaCount < apiData.length) {
					sendEmailToTransmitvia(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToOngage(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToOngageCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToOngage',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToOngageCount++;
				if (sendEmailToOngageCount < apiData.length) {
					sendEmailToOngage(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToSendgrid(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToSendgridCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToSendgrid',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToSendgridCount++;
				if (sendEmailToSendgridCount < apiData.length) {
					sendEmailToSendgrid(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToSendInBlue(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToSendInBlueCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToSendInBlue',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToSendInBlueCount++;
				if (sendEmailToSendInBlueCount < apiData.length) {
					sendEmailToSendInBlue(apiData, provider, groupName, keyword);
				}

			}
		});
	}

	function sendEmailToSendpulse(apiData, provider, groupName, keyword) {
		var apiDataDetail = apiData[sendEmailToSendpulseCount];

		$.ajax({
			url: BASEPATH + 'repost/addDataToSendpulse',
			type: 'post',
			data: {
				apiDataDetail: apiDataDetail,
				provider: provider,
				groupName: groupName,
				keyword: keyword
			},
			success: function(response) {

				userApiDataLength--;
				if (userApiDataLength == 0) {

					$('#sucErrMsg').text("Contacts has been added successfully !!!").addClass('alert alert-success');

					var timeoutVar = setTimeout(function() {
						clearTimeout(timeoutVar);
						location.reload();
					}, 2000);

				} else {
					$('#sucErrMsg').text('Approx ' + userApiDataLength + ' Records Left').addClass('alert alert-info');
				}

				sendEmailToSendpulseCount++;
				if (sendEmailToSendpulseCount < apiData.length) {
					sendEmailToSendpulse(apiData, provider, groupName, keyword);
				}

			}
		});
	}
</script>