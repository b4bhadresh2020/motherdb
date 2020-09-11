<script type="text/javascript">
	var BASE_URL = "<?php echo base_url(); ?>";

	$(document).ready(function(){

		//auto search function for batchname
		$('input.type_ahead_batch_name').typeahead({
	        source:  function (query, process) {
	        return $.get('auto_search_batch', { query: query }, function (data) {
	                data = $.parseJSON(data);
	                return process(data);
	            });
	        }
	    });


	    //auto search function for general_batch_name
		$('input.type_ahead_general_batch_name').typeahead({
	        source:  function (query, process) {
	        return $.get('auto_search_general_batch', { query: query }, function (data) {
	                data = $.parseJSON(data);
	                return process(data);
	            });
	        }
	    });

		$('#country').on('change',function(){
			var country = $(this).val();

			$.ajax({
				url  : BASE_URL + 'tracking/getCampaign',
				type : 'post',
				dataType: "json",
				data : {
					country:country
				},
				success:function(data){
					var campaigns = data.campaigns;
					
					var dataHtml = "";
					    dataHtml += "<option value = ''>Select Campaign</option>";
					for (var i = 0; i < campaigns.length; i++) {
						dataHtml += "<option value = '" + campaigns[i].campaignId + "' data-is-new = '" + campaigns[i].isNew + "' >" + campaigns[i].campaignName + "</option>";
					}

					$('#campaignName').html(dataHtml);
				} 
			});
		});

		$('input[name=csvType]').change(function(){

			var csvType = $( 'input[name=csvType]:checked' ).val();

			if (csvType == 1 || csvType == 2) {
				$('#smsDiv').show();
			}else{
				$('#smsDiv').hide();
			}

			
		});



		$('#country').on('change', function(){

			var conCountry = this.value;

			$.ajax({
				url  : BASE_URL + 'tracking/getGroupByCountry',
				type : 'post',
				dataType: "json",
				data : {
					conCountry:conCountry
				},
				success:function(data){
					var groupData = data.groupData;

					var dataHtml = "";
					    dataHtml += "<option value = ''>All</option>";
					for (var i = 0; i < groupData.length; i++) {
						dataHtml += "<option value = '" + groupData[i].groupName + "' >" + groupData[i].groupName + "</option>";
					}
					$('#groupName').html(dataHtml);
				} 
			});

		});

		$('#phone').prop('disabled',true);
		$('#campaignName').change(function(){
			
			var isNew = $(this).find(':selected').attr('data-is-new');
			if (isNew == 0) {
				$('#phone').prop('disabled',false);
			}else{
				$('#phone').prop('disabled',true);
			}

			$('#phone').val('');
		});

		$('#green_btn_track_clickers').click(function(){
			$('#trackclicker_and_export_to_clickers').val('1'); //dont change it
		});

		$('#red_btn_export_send_to_clikers').click(function(){
			$('#trackclicker_and_export_to_clickers').val('2'); //dont change it
		});

		$('input[type=radio][name=send_sms_broadcast_type]').change(function() {
			if (this.value == 1) {
				$("#throttleBlock").hide();
			}
			else{
				$("#throttleBlock").show();
			}
		});

		$("#throttle").change(function(){
			if($(this).is(':checked')){
				$(".sendoverBlock").show();
			}else{
				$(".sendoverBlock").hide();
			}
		});
			

		// data toggle script added by hitesh
		$("[data-toggle-target]").click(function(){

			var val = $(this).attr("data-toggle-target");
			var target = $("#"+val);
			var targetGroup = $("[data-toggle-group="+target.attr("data-toggle-group")+"]");
			targetGroup.hide();
			target.show();
		})

		/*
			To export csv, processed data
		*/
		$('#btn_export').click(function(){
			
			var country = $('#country').val();
			var domain = $('#domain').val();
			var unsubscribeDomain = $('#unsubscribeDomain').val();
			var groupName = $('#groupName').val();
			var numberOfSms = $('#numberOfSms').val();
			var minAge = $('#minAge').val();
			var maxAge = $('#maxAge').val();
			var gender = $('#gender').val();
			var type = $('#type').val();
			var keyword = $('#keyword').val();
			var exceptDays = $('#exceptDays').val();
			var superClickers = $('#superClickers').val();
			var campaignName = $('#campaignName').val();
			var phone = $('#phone').val();
			var msg = $('#msg').val();
			var redirectUrl = $('#redirectUrl').val();
			var addCountryCodeToBatch = '';
			if ($('#addCountryCodeToBatch').is(":checked")) {
			  	var addCountryCodeToBatch = $('#addCountryCodeToBatch').val();
			}
			var csvType = $('input[name=csvType]:checked').val();

			var trackclicker_and_export_to_clickers = $('#trackclicker_and_export_to_clickers').val();
			
			if (country == '') {

				$('#sucErrMsg').text('Please select Country').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else if (domain == '') {

				$('#sucErrMsg').text('Please select Domain').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else if(campaignName == ''){

				$('#sucErrMsg').text('Please select Campaign Name').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else {

				if (csvType == 1 || csvType == 2) {

					if (trackclicker_and_export_to_clickers == 1) {

						var batchName = $('#batchName').val();
						var generalBatchName = $('#generalBatchName').val();

						if (batchName == '') {

							$('#sucErrMsg').text('Please Enter Group Clickers (Batch)').addClass('alert alert-danger');
							scroll_to_top();
							return false;

						}else{

							var msg_validation = check_msg_validation(msg);
							if (msg_validation.err == 1) {

								$('#sucErrMsg').text(msg_validation.error_msg).addClass('alert alert-danger');
								scroll_to_top();
								return false;	

							}else{

								var redirectUrl_validation = check_redirect_url_validation(redirectUrl);
								if (redirectUrl_validation.err == 1) {

									$('#sucErrMsg').text(redirectUrl_validation.error_msg).addClass('alert alert-danger');
									scroll_to_top();
									return false;	

								}else{	

									$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
									var track_clicker_post_data = {

											country : country,
											groupName : groupName,
											numberOfSms : numberOfSms,
											minAge : minAge,
											maxAge : maxAge,
											gender : gender,
											type : type,
											keyword : keyword,
											exceptDays : exceptDays,
											superClickers : superClickers,
											campaignName : campaignName,
											phone : phone,
											msg : msg,
											redirectUrl : redirectUrl,
											addCountryCodeToBatch : addCountryCodeToBatch,
											batchName : batchName,
											generalBatchName : generalBatchName,
											csvType:csvType,
											domain:domain,
											unsubscribeDomain:unsubscribeDomain,
									}
									
									//now get total count
									$.ajax({

										url : BASE_URL + 'tracking/getTrackClickerDataCount',
										data : track_clicker_post_data,
										type : 'post',
										success : function(track_clicker_response){
											
											var track_clicker_arr = JSON.parse(track_clicker_response);
											/*console.log('track_clicker_arr', track_clicker_arr);*/
											var track_clicker_total_count = track_clicker_arr.totalCount;

											if (track_clicker_total_count > 0) {

												//make export button disabled
												make_export_button_disabled();

												var process_data = $.extend({}, track_clicker_post_data, track_clicker_arr);

												$('#sucErrMsg').text("Total " + track_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
												scroll_to_top();

												make_process_for_user_data(process_data);
												

											}else{
												$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}

										}
									});
								}
							}
						}

					}else if(trackclicker_and_export_to_clickers == 2){

						var batchAndGeneralBatchName = $('#batchAndGeneralBatchName').val();
						var reTrackingCamapignFilter = $('#reTrackingCamapignFilter').val();

						if (batchAndGeneralBatchName == '') {

							$('#sucErrMsg').text('Please Select option in Group clickers filteration ').addClass('alert alert-danger');
							scroll_to_top();
							return false;

						}else{

							var msg_validation = check_msg_validation(msg);
							if (msg_validation.err == 1) {

								$('#sucErrMsg').text(msg_validation.error_msg).addClass('alert alert-danger');
								scroll_to_top();
								return false;	

							}else{

								var redirectUrl_validation = check_redirect_url_validation(redirectUrl);
								if (redirectUrl_validation.err == 1) {

									$('#sucErrMsg').text(redirectUrl_validation.error_msg).addClass('alert alert-danger');
									scroll_to_top();
									return false;	

								}else{	

									$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
									var re_track_clicker_post_data = {

											country : country,
											groupName : groupName,
											numberOfSms : numberOfSms,
											minAge : minAge,
											maxAge : maxAge,
											gender : gender,
											type : type,
											keyword : keyword,
											exceptDays : exceptDays,
											superClickers : superClickers,
											campaignName : campaignName,
											phone : phone,
											msg : msg,
											redirectUrl : redirectUrl,
											addCountryCodeToBatch : addCountryCodeToBatch,
											batchAndGeneralBatchName:batchAndGeneralBatchName,
											reTrackingCamapignFilter:reTrackingCamapignFilter,
											csvType:csvType,
											domain:domain,
											unsubscribeDomain:unsubscribeDomain,
									}
									
									//now get total count
									$.ajax({

										url : BASE_URL + 're_tracking/getReTrackClickerDataCount',
										data : re_track_clicker_post_data,
										type : 'post',
										success : function(re_track_clicker_response){
											
											var re_track_clicker_arr = JSON.parse(re_track_clicker_response);
											/*console.log('re_track_clicker_arr', re_track_clicker_arr);*/
											var re_track_clicker_total_count = re_track_clicker_arr.totalCount;

											if (re_track_clicker_total_count > 0) {

												//make export button disabled
												make_export_button_disabled();

												var re_track_process_data = $.extend({}, re_track_clicker_post_data,re_track_clicker_arr);

												$('#sucErrMsg').text("Total " + re_track_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
												scroll_to_top();

												make_re_track_process_for_user_data(re_track_process_data);
												

											}else{
												$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}

										}
									});
								}
							}
						}

					}else{
						$('#sucErrMsg').text("Please select appropriate option those are Greeen button (Track clickers) OR Red button (Export / Send to Clickers) ").addClass('alert alert-danger');
						scroll_to_top();
						return false;
					}	

				}else{

					$('form#tracking-form').submit();
				}

				
			}	

		});	
		
	})
	
	
	function scroll_to_top(){
		$("html, body").animate({ scrollTop: 0 }, "slow");
	}

	function redirect_to_csv_history(){

		setTimeout(function(){ 
			window.location.replace(BASE_URL + "csv_history/manage");
		}, 2000);
		
	}

	function make_export_button_disabled(){
		$('#btn_export').attr('disabled','disabled').text('Processing');
	}



	function check_msg_validation(msg){

		if ($.trim(msg) == '') {
			return {
				err : 1,
				error_msg : "The Message field is required"
			}
		}else{

			var url_sample = "{url}";
			var unsubscribe_url_sample = "{unsubscribe_url}";

			if(msg.indexOf(url_sample) != -1){
				if (msg.indexOf(unsubscribe_url_sample) != -1) {
					return {
						err : 0
					}
				}else{
					return {
						err : 1,
						error_msg : "{unsubscribe_url} is required in message field"
					}	
				}
			}else{
				return {
					err : 1,
					error_msg : "{url} is required in message field"
				}   
			}
		}
	}	

	function check_redirect_url_validation(redirectUrl){

		if ($.trim(redirectUrl) == '') {
			return {
				err : 1,
				error_msg : "The Redirect URL field is required"
			}
		}else{
		    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(redirectUrl)) {
		      	return {
		      		err:0
		      	};
		    } else {
		      	return {
		      		err : 1,
		      		error_msg : 'Invalid Redirect URL. Example of URL = "https://www.example.com" OR "http://www.example.com"'
		      	};
		    } 
		}
	}

	var process_start = 0;
	var process_per_page = 150;
	var intervalCountTotal = 0;
	var is_set_except_days_for_process_user_data = 0;
	function make_process_for_user_data(process_data){

		var numberOfSms = process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < process_per_page) {
			process_per_page = numberOfSms;
		}
		
		var start_perpage_obj = {
			process_start:process_start,
			process_per_page:process_per_page,
			is_set_except_days:is_set_except_days_for_process_user_data
		};
		var user_process_data = $.extend({},process_data,start_perpage_obj);
		var total_process_data_count = user_process_data.totalCount;

		console.log('user_process_data',user_process_data);
		$.ajax({
			url : BASE_URL + 'tracking/process_user_data',
			type : 'post',
			data : user_process_data,
			success:function(response){

				var process_response = JSON.parse(response);
				var intervalCount = process_response.intervalCount;
				var is_set_except_days = process_response.is_set_except_days;

				intervalCountTotal += intervalCount;
				console.log('intervalCountTotal',intervalCountTotal);
				var remaining_records = total_process_data_count - intervalCountTotal;
				console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (intervalCountTotal < total_process_data_count && intervalCount > 0) {
					process_start = process_start + process_per_page;
					if (remaining_records < process_per_page) {
						process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						process_start = 0;
						is_set_except_days_for_process_user_data = 1;
					}
					make_process_for_user_data(process_data);
				}else{
					$('#sucErrMsg').text("Your csv file is ready to download").addClass('alert alert-success');
					scroll_to_top();
					redirect_to_csv_history();
				}

			}
		});
		
	}


	var re_track_process_start = 0;
	var re_track_process_per_page = 150;
	var re_track_intervalCountTotal = 0;
	var is_set_except_days_for_re_track_process_user_data = 0;
	function make_re_track_process_for_user_data(re_track_process_data){
		
		var numberOfSms = re_track_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < re_track_process_per_page) {
			re_track_process_per_page = numberOfSms;
		}

		var start_perpage_obj = {
			re_track_process_start:re_track_process_start,
			re_track_process_per_page:re_track_process_per_page,
			is_set_except_days:is_set_except_days_for_re_track_process_user_data
		};
		var re_track_user_process_data = $.extend({},re_track_process_data,start_perpage_obj);
		var re_track_total_process_data_count = re_track_user_process_data.totalCount;

		console.log('re_track_user_process_data',re_track_user_process_data);
		
		$.ajax({
			url : BASE_URL + 're_tracking/re_track_process_user_data',
			type : 'post',
			data : re_track_user_process_data,
			success:function(response){

				var re_track_process_response = JSON.parse(response);
				//console.log('intervalCount',re_track_process_response.intervalCount);
				var intervalCount = re_track_process_response.intervalCount;
				var is_set_except_days = re_track_process_response.is_set_except_days;

				re_track_intervalCountTotal += intervalCount;
				var remaining_records = re_track_total_process_data_count - re_track_intervalCountTotal;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (re_track_intervalCountTotal < re_track_total_process_data_count && intervalCount > 0) {
					re_track_process_start = re_track_process_start + re_track_process_per_page;
					/*console.log('re_track_process_start',re_track_process_start);*/
					if (remaining_records < re_track_process_per_page) {
						re_track_process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						re_track_process_start = 0;
						is_set_except_days_for_re_track_process_user_data = 1;
					}
					make_re_track_process_for_user_data(re_track_process_data);
				}else{
					$('#sucErrMsg').text("Your csv file is ready to download").addClass('alert alert-success');
					scroll_to_top();
					redirect_to_csv_history();
				}

			}
		});
		
	}


	/*
		send sms motherdb part
	*/

	$(document).ready(function(){

		/*msg counting code start*/
		var $msg_remaining = $('#msg_remaining'),
		    $messages = $msg_remaining.next();

		$('textarea[name="msg"]').keyup(function(){

		    var chars = this.value.length,
		        messages = Math.ceil(chars / 160),
		        msg_remaining = messages * 160 - (chars % (messages * 160) || messages * 160);

		    $msg_remaining.text(msg_remaining + ' characters remaining / ');

		    if (messages > 1) {
		    	$messages.text(messages + ' sms').css("color","red");
		    }else{
		    	$messages.text(messages + ' sms').css('color','');
		    }
		});
		/*msg counting code end*/

		//$('#send_sms_motherdb').toggle();
		$('#send_sms_motherdb').click(function(){
			$('#send-sms-motherdb-part').toggle();
		});

		$('#btn_split_part').click(function(){

			var split_parts = $("#broadcast_split_number").val();
			var allSmsApiProvider = <?php echo json_encode(getAllSmsApiProvider()); ?>;
			
			if (split_parts > 0) {
				$('#split_parts_div').text('');
				
				var htmlView = '';
				for (var i = 0; i < split_parts; i++) {

					htmlView += '<div class="row">';
						htmlView += '<div class="col-lg-2">';
                            htmlView += '<div class="form-group">';
                                htmlView += '<label style="margin-top: 40px;">Part ' + (i + 1) + ' </label>';
                            htmlView += '</div>';
                        htmlView += '</div>';
            			htmlView += '<div class="col-lg-3">';
                            htmlView += '<div class="form-group">';
                                htmlView += '<label>Sender Id *</label>';
                                htmlView += '<input type="text" name="broadcast_split_part_sender_id_'+i+'" class="form-control broadcast_split_part_sender_id">';
                            htmlView += '</div>';
                        htmlView += '</div>';
                        htmlView += '<div class="col-lg-3">';
                            htmlView += '<div class="form-group">';
                                htmlView += '<label>Specific Time *</label>';
                                htmlView += '<input type="datetime-local" name="broadcast_split_part_time_'+i+'" class="form-control broadcast_split_part_time" > ';
                            htmlView += '</div>';
                        htmlView += '</div>';
                        htmlView += '<div class="col-lg-3">';
                            htmlView += '<div class="form-group">';
                                htmlView += '<label>Sms Provider *</label>';
                                htmlView += '<select name="broadcast_split_part_service_provider_'+i+'" class="form-control broadcast_split_part_service_provider">';
                                    htmlView += '<option value = "">Select Provider</option>';
                                    $.each(allSmsApiProvider,function(service_provider_key, service_provider_value){
									  	htmlView += '<option value = "' + service_provider_key + '">' + service_provider_value + '</option>';
									});
                                htmlView += '</select>';
                            htmlView += '</div>';
                        htmlView += '</div>';
                    htmlView += '</div>';
				}
				$('#split_parts_div').html(htmlView);
			}
		});

		$('#set_broadcast_live').click(function(){

			var country = $('#country').val();
			var domain = $('#domain').val();
			var unsubscribeDomain = $('#unsubscribeDomain').val();
			var groupName = $('#groupName').val();
			var numberOfSms = $('#numberOfSms').val();
			var minAge = $('#minAge').val();
			var maxAge = $('#maxAge').val();
			var gender = $('#gender').val();
			var type = $('#type').val();
			var keyword = $('#keyword').val();
			var exceptDays = $('#exceptDays').val();
			var superClickers = $('#superClickers').val();
			var campaignName = $('#campaignName').val();
			var phone = $('#phone').val();
			var msg = $('#msg').val();
			var redirectUrl = $('#redirectUrl').val();
			var addCountryCodeToBatch = '';
			var isThrottle = 0;
			var sendOverHour = 1;
			var previousProvider = '';
			if ($('#addCountryCodeToBatch').is(":checked")) {
			  	var addCountryCodeToBatch = $('#addCountryCodeToBatch').val();
			}

			if ($('#throttle').is(":checked")) {
				  isThrottle = 1;
				  sendOverHour = $("#sendover").val();
			}

			var csvType = $('input[name=csvType]:checked').val();

			var trackclicker_and_export_to_clickers = $('#trackclicker_and_export_to_clickers').val();
			console.log('trackclicker_and_export_to_clickers',trackclicker_and_export_to_clickers);
			//debugger;
			if (country == '') {

				$('#sucErrMsg').text('Please select Country').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else if (domain == '') {

				$('#sucErrMsg').text('Please select Domain').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else if(campaignName == ''){

				$('#sucErrMsg').text('Please select Campaign Name').addClass('alert alert-danger');
				scroll_to_top();
				return false;

			}else{

				if (csvType == 1 || csvType == 2) {

					if (trackclicker_and_export_to_clickers == 1) {

						var batchName = $('#batchName').val();
						var generalBatchName = $('#generalBatchName').val();

						if (batchName == '') {

							$('#sucErrMsg').text('Please Enter Group Clickers (Batch)').addClass('alert alert-danger');
							scroll_to_top();
							return false;

						}else{

							var msg_validation = check_msg_validation(msg);
							if (msg_validation.err == 1) {

								$('#sucErrMsg').text(msg_validation.error_msg).addClass('alert alert-danger');
								scroll_to_top();
								return false;	

							}else{

								var redirectUrl_validation = check_redirect_url_validation(redirectUrl);
								if (redirectUrl_validation.err == 1) {

									$('#sucErrMsg').text(redirectUrl_validation.error_msg).addClass('alert alert-danger');
									scroll_to_top();
									return false;	

								}else{	

									var broadcastType = $('input[name=send_sms_broadcast_type]:checked').val();

									if (broadcastType == 1) {

										var send_now_sender_id = $('#send_now_sender_id').val();
										var send_now_service_provider = $('#send_now_service_provider').val();

										if (send_now_sender_id == '') {

											$('#sucErrMsg').text('Please add sender id ').addClass('alert alert-danger');
											scroll_to_top();
											return false;	

										}else{

											var isValid = isValidSenderId(send_now_sender_id);

											if (isValid != 1) {
												$('#sucErrMsg').text('Invalid sender id. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}else{
												if (send_now_service_provider == '') {

													$('#sucErrMsg').text('Please select sms provider').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else{

													$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
													var broad_cast_post_data = {

															country : country,
															groupName : groupName,
															numberOfSms : numberOfSms,
															minAge : minAge,
															maxAge : maxAge,
															gender : gender,
															type : type,
															keyword : keyword,
															exceptDays : exceptDays,
															superClickers : superClickers,
															campaignName : campaignName,
															phone : phone,
															msg : msg,
															redirectUrl : redirectUrl,
															batchName : batchName,
															generalBatchName : generalBatchName,
															csvType:csvType,
															send_now_sender_id:send_now_sender_id,
															send_now_service_provider:send_now_service_provider,
															broadcastType:broadcastType,
															domain:domain,
															unsubscribeDomain:unsubscribeDomain,
															isThrottle:isThrottle,
															sendOverHour:sendOverHour,
															previousProvider:previousProvider
													}
													
													//now get total count
													$.ajax({

														url : BASE_URL + 'broadcast/getBroadcastTrackClickerDataCount',
														data : broad_cast_post_data,
														type : 'post',
														success : function(broadcast_track_clicker_response){
															
															var broadcast_track_clicker_arr = JSON.parse(broadcast_track_clicker_response);
															
															var broadcast_track_clicker_total_count = broadcast_track_clicker_arr.totalCount;

															console.log('broadcast_track_clicker_response', broadcast_track_clicker_response);
															console.log('broadcast_track_clicker_arr', broadcast_track_clicker_arr);
															console.log('broadcast_track_clicker_total_count', broadcast_track_clicker_total_count);

															if (broadcast_track_clicker_total_count > 0) {

																//make sms broad cast button disabled
																make_sms_broadcast_live_button_disabled();

																var broadcast_process_data = $.extend({}, broad_cast_post_data, broadcast_track_clicker_arr);

																$('#sucErrMsg').text("Total " + broadcast_track_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																scroll_to_top();

																make_process_for_send_now_broadcast_data(broadcast_process_data);

															}else{
																$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
																scroll_to_top();
																return false;
															}

														}
													});
												}
											}
										}

									}else if(broadcastType == 2){

										var specific_time_sender_id = $('#specific_time_sender_id').val();
										var specific_time_service_provider = $('#specific_time_service_provider').val();
										var broadcast_specific_time = $('#broadcast_specific_time').val();

										if (specific_time_sender_id == '') {

											$('#sucErrMsg').text('Please add sender id ').addClass('alert alert-danger');
											scroll_to_top();
											return false;	

										}else{
											var isValid = isValidSenderId(specific_time_sender_id);

											if (isValid != 1) {
												$('#sucErrMsg').text('Invalid sender id. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}else{
												if (specific_time_service_provider == '') {

													$('#sucErrMsg').text('Please select sms provider').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else if(broadcast_specific_time == ''){

													$('#sucErrMsg').text('Please select specific time').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else{

													var current_date = get_current_date_time();
													
													console.log(broadcast_specific_time);
													console.log(current_date);

													if (broadcast_specific_time < current_date) {

														$('#sucErrMsg').text('You can not select the past date').addClass('alert alert-danger');
														scroll_to_top();
														return false;
													}else{
														
														//check if specific time is not behind the current time
														$.ajax({
															
															url : BASE_URL + 'tracking/check_time_difference',
															data : {
																country : country,
																broadcast_specific_time : broadcast_specific_time
															},
															type : 'post',
															success : function(res){

																var time_diff_res = JSON.parse(res);
																if (time_diff_res.err == 1) {

																	$('#sucErrMsg').text('Date is passed in selected country.You can not select the past date.').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;

																}else if (time_diff_res.err == 2) {

																	$('#sucErrMsg').text('Please select time before 20:00').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;

																}else{

																	$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
																	var diff_in_sec = time_diff_res.diff_in_sec;
																	var broad_cast_specific_time_post_data = {

																			country : country,
																			groupName : groupName,
																			numberOfSms : numberOfSms,
																			minAge : minAge,
																			maxAge : maxAge,
																			gender : gender,
																			type : type,
																			keyword : keyword,
																			exceptDays : exceptDays,
																			superClickers : superClickers,
																			campaignName : campaignName,
																			phone : phone,
																			msg : msg,
																			redirectUrl : redirectUrl,
																			batchName : batchName,
																			generalBatchName : generalBatchName,
																			csvType:csvType,
																			specific_time_sender_id:specific_time_sender_id,
																			specific_time_service_provider:specific_time_service_provider,
																			broadcast_specific_time:broadcast_specific_time,
																			diff_in_sec:diff_in_sec,
																			broadcastType:broadcastType,
																			domain:domain,
																			unsubscribeDomain:unsubscribeDomain,
																			isThrottle:isThrottle,
																			sendOverHour:sendOverHour,
																			previousProvider:previousProvider
																	}
																	
																	//now get total count
																	$.ajax({

																		url : BASE_URL + 'broadcast/getBroadcastTrackClickerDataCount',
																		data : broad_cast_specific_time_post_data,
																		type : 'post',
																		success : function(broadcast_specific_time_track_clicker_response){
																			
																			var broadcast_specific_time_track_clicker_arr = JSON.parse(broadcast_specific_time_track_clicker_response);
																			
																			var broadcast_specific_time_track_clicker_total_count = broadcast_specific_time_track_clicker_arr.totalCount;

																			console.log('broadcast_specific_time_track_clicker_response', broadcast_specific_time_track_clicker_response);
																			console.log('broadcast_specific_time_track_clicker_arr', broadcast_specific_time_track_clicker_arr);
																			console.log('broadcast_specific_time_track_clicker_total_count', broadcast_specific_time_track_clicker_total_count);

																			if (broadcast_specific_time_track_clicker_total_count > 0) {

																				//make sms broad cast button disabled
																				make_sms_broadcast_live_button_disabled();

																				var broadcast_specific_time_process_data = $.extend({}, broad_cast_specific_time_post_data, broadcast_specific_time_track_clicker_arr);

																				$('#sucErrMsg').text("Total " + broadcast_specific_time_track_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																				scroll_to_top();

																				make_process_for_specific_time_broadcast_data(broadcast_specific_time_process_data);

																			}else{
																				$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
																				scroll_to_top();
																				return false;
																			}
																		}
																	});
																}
															}
														});
													}
												}
											}
										}

									

									}else if(broadcastType == 3){

										var broadcast_split_number = $('#broadcast_split_number').val();
										if (broadcast_split_number == '' || broadcast_split_number <= 0) {

											$('#sucErrMsg').text('Split Parts should not be blank and should be grater than 0').addClass('alert alert-danger');
											scroll_to_top();
											return false;

										}else{
											
											//get total records
											$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
											var get_broad_cast_splits_part_total = {

													country : country,
													groupName : groupName,
													numberOfSms : numberOfSms,
													minAge : minAge,
													maxAge : maxAge,
													gender : gender,
													type : type,
													keyword : keyword,
													exceptDays : exceptDays,
													superClickers : superClickers,
													campaignName : campaignName,
													phone : phone,
													msg : msg,
													redirectUrl : redirectUrl,
													batchName : batchName,
													generalBatchName : generalBatchName,
													csvType:csvType,
													broadcastType:broadcastType,
													domain:domain,
													unsubscribeDomain:unsubscribeDomain,
													isThrottle:isThrottle,
													sendOverHour:sendOverHour,
													previousProvider:previousProvider
											}
											

											//now get total count
											$.ajax({

												url : BASE_URL + 'broadcast/getBroadcastTrackClickerDataCount',
												data : get_broad_cast_splits_part_total,
												type : 'post',
												success : function(broadcast_splits_part_track_clicker_response){
													
													var broadcast_split_time_track_clicker_arr = JSON.parse(broadcast_splits_part_track_clicker_response);
													
													var broadcast_splits_time_track_clicker_total_count = broadcast_split_time_track_clicker_arr.totalCount;

													console.log('broadcast_splits_part_track_clicker_response', broadcast_splits_part_track_clicker_response);
													console.log('broadcast_split_time_track_clicker_arr', broadcast_split_time_track_clicker_arr);
													console.log('broadcast_splits_time_track_clicker_total_count', broadcast_splits_time_track_clicker_total_count);

													if (broadcast_splits_time_track_clicker_total_count > 0) {

														//total records / parts should always >= 1

														if (broadcast_splits_time_track_clicker_total_count / broadcast_split_number >= 1) {

															//check if all splits fields filled properly or not. for that we need to check length of all fields

															var split_sender_id_arr = [];
																
															$('.broadcast_split_part_sender_id').each(function(){
																if (this.value != '') {
																	split_sender_id_arr.push(this.value)
																}
															  
															});

															var split_specific_time_arr = [];
															$('.broadcast_split_part_time').each(function(){
																if (this.value != '') {
																	split_specific_time_arr.push(this.value)		
																}
															  
															});

															var split_service_provider_arr = [];
															$('.broadcast_split_part_service_provider').each(function(){
																if (this.value != '') {
																	split_service_provider_arr.push(this.value)		
																}
															  
															});
															
															var length_of_splits_sender_id = split_sender_id_arr.length;
															var length_of_splits_specific_time = split_specific_time_arr.length;
															var length_of_splits_service_provider = split_service_provider_arr.length;
															
															if (length_of_splits_sender_id == broadcast_split_number && length_of_splits_specific_time == broadcast_split_number && length_of_splits_service_provider == broadcast_split_number) {

																//check if sender ids are valid
																
																var is_all_sender_id_valid = 1;
																for (var i = 0; i < length_of_splits_sender_id; i++) {
																	var is_valid_sender_id = isValidSenderId(split_sender_id_arr[i]);

																	if (is_valid_sender_id != 1) {
																		is_all_sender_id_valid = 0; 
																		break;
																	}
																}


																if (is_all_sender_id_valid == 1) {

																	//check if all specific date/time is of future

																	var is_all_specific_time_valid = 1;
																	var current_date = get_current_date_time();
																	for (var i = 0; i < length_of_splits_specific_time; i++) {

																		if (split_specific_time_arr[i] < current_date) {
																			is_all_specific_time_valid = 0; 
																			break;
																		}
																	}
																	
																	if (is_all_specific_time_valid == 1) {

																		//now check and get time difference of all country

																		$.ajax({
																			url : BASE_URL + 'tracking/check_time_difference_in_arr',
																			type : 'post',
																			data : {
																				country : country,
																				split_specific_time_arr:split_specific_time_arr

																			},
																			success:function(check_time_response){

																				var check_time_response = JSON.parse(check_time_response);
																				console.log('time_diff',check_time_response);
																				console.log('response.err',check_time_response.err);
																				console.log('response.diff_in_sec_arr',check_time_response.diff_in_sec_arr);

																				if (check_time_response.err == 0) {

																					var diff_in_sec_arr = check_time_response.diff_in_sec_arr;

																					var split_divided_obj = {
																						total_num : broadcast_splits_time_track_clicker_total_count,
																						divided : broadcast_split_number
																					};
																					var get_split_divided_arr_val = get_split_divided_arr(split_divided_obj);

																					console.log('get_split_divided_arr_val',get_split_divided_arr_val);

																					var broadcast_splits_part_process_data = $.extend({split_sender_id_arr:split_sender_id_arr,
																						split_service_provider_arr:split_service_provider_arr,
																						diff_in_sec_arr:diff_in_sec_arr,get_split_divided_arr_val:get_split_divided_arr_val,split_specific_time_arr:split_specific_time_arr
																					}, get_broad_cast_splits_part_total, broadcast_split_time_track_clicker_arr);

																					console.log('broadcast_splits_part_process_data',broadcast_splits_part_process_data);

																					//make sms broad cast button disabled
																					make_sms_broadcast_live_button_disabled();

																					$('#sucErrMsg').text("Total " + broadcast_splits_time_track_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																					scroll_to_top();

																					make_process_for_split_parts_broadcast_data(broadcast_splits_part_process_data);

																				}else if(check_time_response.err == 2){
																					$('#sucErrMsg').text('Please select time before 20:00').addClass('alert alert-danger');
																					scroll_to_top();
																					return false;
																				}else{

																					$('#sucErrMsg').text('One or more date/time you have selected in specific time is already passed in selected country. Please select future date/time ').addClass('alert alert-danger');
																					scroll_to_top();
																					return false;
																				}
																			}
																		});

																		

																	}else{

																		$('#sucErrMsg').text('One or more date/time you have selected in specific time is already passed. Please select future date/time ').addClass('alert alert-danger');
																		scroll_to_top();
																		return false;
																	}

																}else{

																	$('#sucErrMsg').text('One or more sender id is not valid. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;
																}

															}else{

																$('#sucErrMsg').text("Data of all split part is requiered").addClass('alert alert-danger');
																scroll_to_top();
																return false;

															}

														}else{

															$('#sucErrMsg').text("No enough data to split equally. Data count is " + broadcast_splits_time_track_clicker_total_count + " and Split Parts is " + broadcast_split_number).addClass('alert alert-danger');
															scroll_to_top();
															return false;
														}

													}else{
														$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
														scroll_to_top();
														return false;
													}
												}
											});
										}
									}else{

										$('#sucErrMsg').text('Please select "send now" or "specific time" or "split sms" ').addClass('alert alert-danger');
										scroll_to_top();
										return false;	
									}

								}
							}
						}

					}else if(trackclicker_and_export_to_clickers == 2){
						console.log('in second part');

						var batchAndGeneralBatchName = $('#batchAndGeneralBatchName').val();
						var reTrackingCamapignFilter = $('#reTrackingCamapignFilter').val();

						if (batchAndGeneralBatchName == '') {

							$('#sucErrMsg').text('Please Select option in Group clickers filteration ').addClass('alert alert-danger');
							scroll_to_top();
							return false;

						}else{

							var msg_validation = check_msg_validation(msg);
							if (msg_validation.err == 1) {

								$('#sucErrMsg').text(msg_validation.error_msg).addClass('alert alert-danger');
								scroll_to_top();
								return false;	

							}else{

								var redirectUrl_validation = check_redirect_url_validation(redirectUrl);
								if (redirectUrl_validation.err == 1) {

									$('#sucErrMsg').text(redirectUrl_validation.error_msg).addClass('alert alert-danger');
									scroll_to_top();
									return false;	

								}else{	

									var broadcastType = $('input[name=send_sms_broadcast_type]:checked').val();

									if (broadcastType == 1) {

										var send_now_sender_id = $('#send_now_sender_id').val();
										var send_now_service_provider = $('#send_now_service_provider').val();

										if (send_now_sender_id == '') {

											$('#sucErrMsg').text('Please add sender id ').addClass('alert alert-danger');
											scroll_to_top();
											return false;	

										}else{

											var isValid = isValidSenderId(send_now_sender_id);

											if (isValid != 1) {
												$('#sucErrMsg').text('Invalid sender id. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}else{
												if (send_now_service_provider == '') {

													$('#sucErrMsg').text('Please select sms provider').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else{

													$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
													var re_track_clicker_broadcast_send_now_post_data = {

															country : country,
															groupName : groupName,
															numberOfSms : numberOfSms,
															minAge : minAge,
															maxAge : maxAge,
															gender : gender,
															type : type,
															keyword : keyword,
															exceptDays : exceptDays,
															superClickers : superClickers,
															campaignName : campaignName,
															phone : phone,
															msg : msg,
															redirectUrl : redirectUrl,
															addCountryCodeToBatch : addCountryCodeToBatch,
															batchAndGeneralBatchName:batchAndGeneralBatchName,
															reTrackingCamapignFilter:reTrackingCamapignFilter,
															csvType:csvType,
															send_now_sender_id:send_now_sender_id,
															send_now_service_provider:send_now_service_provider,
															broadcastType:broadcastType,
															domain:domain,
															unsubscribeDomain:unsubscribeDomain,
															isThrottle:isThrottle,
															sendOverHour:sendOverHour,
															previousProvider:previousProvider
													}

													console.log('before call ajax');
													console.log('re_track_clicker_broadcast_send_now_post_data',re_track_clicker_broadcast_send_now_post_data);

													//debugger;
													//now get total count
													$.ajax({

														url : BASE_URL + 're_tracking_broadcast/getReTrackBroadcastClickerDataCount',
														data : re_track_clicker_broadcast_send_now_post_data,
														type : 'post',
														success : function(re_track_broadcast_send_now_clicker_response){
															
															var re_track_broadcast_send_now_clicker_arr = JSON.parse(re_track_broadcast_send_now_clicker_response);
															console.log('re_track_broadcast_send_now_clicker_arr', re_track_broadcast_send_now_clicker_arr);
															var re_track_broadcast_send_now_clicker_total_count = re_track_broadcast_send_now_clicker_arr.totalCount;

															if (re_track_broadcast_send_now_clicker_total_count > 0) {

																//make export button disabled
																make_sms_broadcast_live_button_disabled();

																var re_track_broadcast_send_now_process_data = $.extend({}, re_track_clicker_broadcast_send_now_post_data,re_track_broadcast_send_now_clicker_arr);

																$('#sucErrMsg').text("Total " + re_track_broadcast_send_now_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																scroll_to_top();

																make_re_track_braodcast_send_now_process_for_user_data(re_track_broadcast_send_now_process_data);
																

															}else{
																$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
																scroll_to_top();
																return false;
															}

														}
													});
												}
											}
										}

									}else if(broadcastType == 2){

										var specific_time_sender_id = $('#specific_time_sender_id').val();
										var specific_time_service_provider = $('#specific_time_service_provider').val();
										var broadcast_specific_time = $('#broadcast_specific_time').val();

										if (specific_time_sender_id == '') {

											$('#sucErrMsg').text('Please add sender id ').addClass('alert alert-danger');
											scroll_to_top();
											return false;	

										}else{
											var isValid = isValidSenderId(specific_time_sender_id);

											if (isValid != 1) {
												$('#sucErrMsg').text('Invalid sender id. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
												scroll_to_top();
												return false;
											}else{
												if (specific_time_service_provider == '') {

													$('#sucErrMsg').text('Please select sms provider').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else if(broadcast_specific_time == ''){

													$('#sucErrMsg').text('Please select specific time').addClass('alert alert-danger');
													scroll_to_top();
													return false;

												}else{
													var current_date = get_current_date_time();

													console.log(broadcast_specific_time);
													console.log(current_date);

													if (broadcast_specific_time < current_date) {

														$('#sucErrMsg').text('You can not select the past date').addClass('alert alert-danger');
														scroll_to_top();
														return false;
													}else{

														//check if specific time is not behind the current time
														$.ajax({

															url : BASE_URL + 'tracking/check_time_difference',
															data : {
																country : country,
																broadcast_specific_time : broadcast_specific_time
															},
															type : 'post',
															success : function(res){

																var time_diff_res = JSON.parse(res);
																if (time_diff_res.err == 1) {

																	$('#sucErrMsg').text('Date is passed in selected country. You can not select the past date.').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;

																}else if (time_diff_res.err == 2) {

																	$('#sucErrMsg').text('Please select time before 20:00').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;

																}else{

																	$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');

																	var diff_in_sec = time_diff_res.diff_in_sec;
																	var re_track_clicker_broadcast_specific_time_post_data = {

																			country : country,
																			groupName : groupName,
																			numberOfSms : numberOfSms,
																			minAge : minAge,
																			maxAge : maxAge,
																			gender : gender,
																			type : type,
																			keyword : keyword,
																			exceptDays : exceptDays,
																			superClickers : superClickers,
																			campaignName : campaignName,
																			phone : phone,
																			msg : msg,
																			redirectUrl : redirectUrl,
																			addCountryCodeToBatch : addCountryCodeToBatch,
																			batchAndGeneralBatchName:batchAndGeneralBatchName,
																			reTrackingCamapignFilter:reTrackingCamapignFilter,
																			csvType:csvType,
																			specific_time_sender_id:specific_time_sender_id,
																			specific_time_service_provider:specific_time_service_provider,
																			broadcast_specific_time:broadcast_specific_time,
																			diff_in_sec:diff_in_sec,
																			broadcastType:broadcastType,
																			domain:domain,
																			unsubscribeDomain:unsubscribeDomain,
																			isThrottle:isThrottle,
																			sendOverHour:sendOverHour,
																			previousProvider:previousProvider
																	}
																	
																	//now get total count
																	$.ajax({

																		url : BASE_URL + 're_tracking_broadcast/getReTrackBroadcastClickerDataCount',
																		data : re_track_clicker_broadcast_specific_time_post_data,
																		type : 'post',
																		success : function(re_track_broadcast_specific_time_clicker_response){
																			
																			var re_track_broadcast_specific_time_clicker_arr = JSON.parse(re_track_broadcast_specific_time_clicker_response);
																			/*console.log('re_track_broadcast_specific_time_clicker_arr', re_track_broadcast_specific_time_clicker_arr);*/
																			var re_track_broadcast_specific_time_clicker_total_count = re_track_broadcast_specific_time_clicker_arr.totalCount;

																			if (re_track_broadcast_specific_time_clicker_total_count > 0) {

																				//make export button disabled
																				make_sms_broadcast_live_button_disabled();

																				var re_track_broadcast_specific_time_process_data = $.extend({}, re_track_clicker_broadcast_specific_time_post_data,re_track_broadcast_specific_time_clicker_arr);

																				$('#sucErrMsg').text("Total " + re_track_broadcast_specific_time_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																				scroll_to_top();

																				make_re_track_braodcast_specific_time_process_for_user_data(re_track_broadcast_specific_time_process_data);
																				

																			}else{
																				$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
																				scroll_to_top();
																				return false;
																			}
																		}
																	});
																}
															}
														});
													}
												}
											}
										}


									}else if(broadcastType == 3){

										var broadcast_split_number = $('#broadcast_split_number').val();
										if (broadcast_split_number == '' || broadcast_split_number <= 0) {

											$('#sucErrMsg').text('Split Parts should not be blank and should be grater than 0').addClass('alert alert-danger');
											scroll_to_top();
											return false;

										}else{

											//get total records
											$('#sucErrMsg').text("").removeClass('alert alert-success alert-danger');
											var re_track_clicker_broadcast_split_part_post_data = {

													country : country,
													groupName : groupName,
													numberOfSms : numberOfSms,
													minAge : minAge,
													maxAge : maxAge,
													gender : gender,
													type : type,
													keyword : keyword,
													exceptDays : exceptDays,
													superClickers : superClickers,
													campaignName : campaignName,
													phone : phone,
													msg : msg,
													redirectUrl : redirectUrl,
													addCountryCodeToBatch : addCountryCodeToBatch,
													batchAndGeneralBatchName:batchAndGeneralBatchName,
													reTrackingCamapignFilter:reTrackingCamapignFilter,
													csvType:csvType,
													broadcastType:broadcastType,
													domain:domain,
													unsubscribeDomain:unsubscribeDomain,
													isThrottle:isThrottle,
													sendOverHour:sendOverHour,
													previousProvider:previousProvider
											}

											//now get total count
											$.ajax({

												url : BASE_URL + 're_tracking_broadcast/getReTrackBroadcastClickerDataCount',
												data : re_track_clicker_broadcast_split_part_post_data,
												type : 'post',
												success : function(re_track_broadcast_split_part_clicker_response){
													
													var re_track_broadcast_split_part_clicker_arr = JSON.parse(re_track_broadcast_split_part_clicker_response);
													/*console.log('re_track_broadcast_split_part_clicker_arr', re_track_broadcast_split_part_clicker_arr);*/
													var re_track_broadcast_split_part_clicker_total_count = re_track_broadcast_split_part_clicker_arr.totalCount;

													if (re_track_broadcast_split_part_clicker_total_count > 0) {

														//total records / parts should always >= 1
														if (re_track_broadcast_split_part_clicker_total_count / broadcast_split_number >= 1) {

															//check if all splits fields filled properly or not. for that we need to check length of all fields

															var split_sender_id_arr = [];
															$('.broadcast_split_part_sender_id').each(function(){
																if (this.value != '') {
																	split_sender_id_arr.push(this.value)
																}
															  
															});

															var split_specific_time_arr = [];
															$('.broadcast_split_part_time').each(function(){
																if (this.value != '') {
																	split_specific_time_arr.push(this.value)		
																}
															  
															});

															var split_service_provider_arr = [];
															$('.broadcast_split_part_service_provider').each(function(){
																if (this.value != '') {
																	split_service_provider_arr.push(this.value)		
																}
															  
															});
															
															var length_of_splits_sender_id = split_sender_id_arr.length;
															var length_of_splits_specific_time = split_specific_time_arr.length;
															var length_of_splits_service_provider = split_service_provider_arr.length;

															if (length_of_splits_sender_id == broadcast_split_number && length_of_splits_specific_time == broadcast_split_number && length_of_splits_service_provider == broadcast_split_number) {

																//check if sender ids are valid
																
																var is_all_sender_id_valid = 1;
																for (var i = 0; i < length_of_splits_sender_id; i++) {
																	var is_valid_sender_id = isValidSenderId(split_sender_id_arr[i]);

																	if (is_valid_sender_id != 1) {
																		is_all_sender_id_valid = 0; 
																		break;
																	}
																}

																if (is_all_sender_id_valid == 1) {

																	//check if all specific date/time is of future

																	var is_all_specific_time_valid = 1;
																	var current_date = get_current_date_time();
																	for (var i = 0; i < length_of_splits_specific_time; i++) {

																		if (split_specific_time_arr[i] < current_date) {
																			is_all_specific_time_valid = 0; 
																			break;
																		}
																	}

																	if (is_all_specific_time_valid == 1) {

																		//now check and get time difference of all country

																		$.ajax({
																			url : BASE_URL + 'tracking/check_time_difference_in_arr',
																			type : 'post',
																			data : {
																				country : country,
																				split_specific_time_arr:split_specific_time_arr

																			},
																			success:function(check_time_response){

																				var check_time_response = JSON.parse(check_time_response);
																				console.log('time_diff',check_time_response);
																				console.log('response.err',check_time_response.err);
																				console.log('response.diff_in_sec_arr',check_time_response.diff_in_sec_arr);

																				if (check_time_response.err == 0) {

																					var diff_in_sec_arr = check_time_response.diff_in_sec_arr;

																					var split_divided_obj = {
																						total_num : re_track_broadcast_split_part_clicker_total_count,
																						divided : broadcast_split_number
																					};
																					var get_split_divided_arr_val = get_split_divided_arr(split_divided_obj);

																					console.log('get_split_divided_arr_val',get_split_divided_arr_val);

																					var re_track_broadcast_split_part_process_data = $.extend({split_sender_id_arr:split_sender_id_arr,
																						split_service_provider_arr:split_service_provider_arr,
																						diff_in_sec_arr:diff_in_sec_arr,get_split_divided_arr_val:get_split_divided_arr_val,split_specific_time_arr:split_specific_time_arr
																					}, re_track_clicker_broadcast_split_part_post_data, re_track_broadcast_split_part_clicker_arr);

																					console.log('re_track_broadcast_split_part_process_data',re_track_broadcast_split_part_process_data);

																					//make sms broad cast button disabled
																					make_sms_broadcast_live_button_disabled();

																					$('#sucErrMsg').text("Total " + re_track_broadcast_split_part_clicker_total_count + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
																					scroll_to_top();

																					make_re_track_broadcast_split_part_process_for_user_data(re_track_broadcast_split_part_process_data);


																				}else if(check_time_response.err == 2){
																					$('#sucErrMsg').text('Please select time before 20:00').addClass('alert alert-danger');
																					scroll_to_top();
																					return false;
																				}else{

																					$('#sucErrMsg').text('One or more date/time you have selected in specific time is already passed in selected country. Please select future date/time ').addClass('alert alert-danger');
																					scroll_to_top();
																					return false;
																				}
																			}
																		});

																	
																	}else{
																		$('#sucErrMsg').text('One or more date/time you have selected in specific time is already passed. Please select future date/time ').addClass('alert alert-danger');
																		scroll_to_top();
																		return false;
																	}

																}else{
																	$('#sucErrMsg').text('One or more sender id is not valid. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
																	scroll_to_top();
																	return false;
																}

															}else{

																$('#sucErrMsg').text("Data of all split part is requiered").addClass('alert alert-danger');
																scroll_to_top();
																return false;
															}


														}else{

															$('#sucErrMsg').text("No enough data to split equally. Data count is " + re_track_broadcast_split_part_clicker_total_count + " and Split Parts is " + broadcast_split_number).addClass('alert alert-danger');
															scroll_to_top();
															return false;
														}

													}else{
														$('#sucErrMsg').text("No data found").addClass('alert alert-danger');
														scroll_to_top();
														return false;
													}
												}
											});
										}

									}else{

										$('#sucErrMsg').text('Please select "send now" or "specific time" or "split sms" ').addClass('alert alert-danger');
										scroll_to_top();
										return false;	
									}

									
								}
							}
						}

					}else{
						$('#sucErrMsg').text("Please select appropriate option those are Greeen button (Track clickers) OR Red button (Export / Send to Clickers) ").addClass('alert alert-danger');
						scroll_to_top();
						return false;
					}	

				}else{

					$('#sucErrMsg').text("You need to select either 'with merge tag' or 'without merge tag (do not worry it will not create csv)'").addClass('alert alert-danger');
						scroll_to_top();
						return false;
				}
			
			}
		});

	});

	
	var broadcast_send_now_process_start = 0;
	var broadcast_send_now_process_per_page = 150;
	var broadcast_interval_count_total = 0;
	var is_set_except_days_for_send_now_process_user_data = 0;
	var batchCampaignId = 0;
	function make_process_for_send_now_broadcast_data(broadcast_process_data){

		var numberOfSms = broadcast_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < broadcast_send_now_process_per_page) {
			broadcast_send_now_process_per_page = numberOfSms;
		}
		
		var start_perpage_obj = {
			broadcast_send_now_process_start:broadcast_send_now_process_start,
			broadcast_send_now_process_per_page:broadcast_send_now_process_per_page,
			is_set_except_days:is_set_except_days_for_send_now_process_user_data,
			batchCampaignId:batchCampaignId
		};
		var user_process_data = $.extend({},broadcast_process_data,start_perpage_obj);
		var total_process_data_count = user_process_data.totalCount;

		//console.log('user_process_data',user_process_data);
		$.ajax({
			url : BASE_URL + 'broadcast/broadcast_process_send_now_user_data',
			type : 'post',
			data : user_process_data,
			success:function(response){

				var process_response = JSON.parse(response);
				var intervalCount = process_response.intervalCount;
				var is_set_except_days = process_response.is_set_except_days;

				// set batchCampaignID from response.
				batchCampaignId = process_response.batchCampaignId;

				broadcast_interval_count_total += intervalCount;
				//console.log('broadcast_interval_count_total',broadcast_interval_count_total);
				var remaining_records = total_process_data_count - broadcast_interval_count_total;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (broadcast_interval_count_total < total_process_data_count && intervalCount > 0) {
					broadcast_send_now_process_start = broadcast_send_now_process_start + broadcast_send_now_process_per_page;
					if (remaining_records < broadcast_send_now_process_per_page) {
						broadcast_send_now_process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						broadcast_send_now_process_start = 0;
						is_set_except_days_for_send_now_process_user_data = 1;
					}
					make_process_for_send_now_broadcast_data(broadcast_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					scroll_to_top();
					reload_current_page();
				}

			}
		});
			
	}


	var broadcast_specific_time_process_start = 0;
	var broadcast_specific_time_process_per_page = 150;
	var broadcast_specific_time_interval_count_total = 0;
	var is_set_except_days_for_specific_time_process_user_data = 0;
	var batchCampaignId = 0;
	var minuteCounter = 1;
	var previousProvider = '';
	function make_process_for_specific_time_broadcast_data(broadcast_specific_time_process_data){

		var numberOfSms = broadcast_specific_time_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < broadcast_specific_time_process_per_page) {
			broadcast_specific_time_process_per_page = numberOfSms;
		}
		
		var start_perpage_obj = {
			broadcast_specific_time_process_start:broadcast_specific_time_process_start,
			broadcast_specific_time_process_per_page:broadcast_specific_time_process_per_page,
			is_set_except_days:is_set_except_days_for_specific_time_process_user_data,
			batchCampaignId:batchCampaignId,
			minuteCounter:minuteCounter,
			previousProvider:previousProvider
		};
		var user_specific_time_process_data = $.extend({},broadcast_specific_time_process_data,start_perpage_obj);
		var total_specific_time_process_data_count = user_specific_time_process_data.totalCount;

		//console.log('user_specific_time_process_data',user_specific_time_process_data);
		$.ajax({
			url : BASE_URL + 'broadcast/broadcast_process_specific_time_user_data',
			type : 'post',
			data : user_specific_time_process_data,
			success:function(response){

				var process_response = JSON.parse(response);
				var intervalCount = process_response.intervalCount;
				var is_set_except_days = process_response.is_set_except_days;

				// set batchCampaignID from response.
				batchCampaignId = process_response.batchCampaignId;
				minuteCounter = process_response.minuteCounter;
				previousProvider = process_response.previousProvider;

				broadcast_specific_time_interval_count_total += intervalCount;
				//console.log('broadcast_specific_time_interval_count_total',broadcast_specific_time_interval_count_total);
				var remaining_records = total_specific_time_process_data_count - broadcast_specific_time_interval_count_total;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (broadcast_specific_time_interval_count_total < total_specific_time_process_data_count && intervalCount > 0) {
					broadcast_specific_time_process_start = broadcast_specific_time_process_start + broadcast_specific_time_process_per_page;
					if (remaining_records < broadcast_specific_time_process_per_page) {
						broadcast_specific_time_process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						broadcast_specific_time_process_start = 0;
						is_set_except_days_for_specific_time_process_user_data = 1;
					}
					make_process_for_specific_time_broadcast_data(broadcast_specific_time_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					//scroll_to_top();
					//reload_current_page();
				}

			}
		});
	}



	var broadcast_split_part_process_start = 0;
	var broadcast_split_part_process_per_page = 150;
	var broadcast_split_part_interval_count_total = 0;
	var dynamic_index_for_dynamic_arr = 0;  // split_sender_id_arr, split_service_provider_arr, diff_in_sec_arr...It will change
	var is_set_except_days_for_split_part_process_user_data = 0;
	var batchCampaignId = 0;
	var minuteCounter = 1;
	var previousProvider = '';

	function make_process_for_split_parts_broadcast_data(broadcast_splits_part_process_data){
		console.log('broadcast_splits_part_process_data',broadcast_splits_part_process_data);
		var numberOfSms = broadcast_splits_part_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < broadcast_split_part_process_per_page) {
			broadcast_split_part_process_per_page = numberOfSms;
		}
		
		var start_perpage_obj = {
			broadcast_split_part_process_start:broadcast_split_part_process_start,
			broadcast_split_part_process_per_page:broadcast_split_part_process_per_page,
			is_set_except_days:is_set_except_days_for_split_part_process_user_data,
			split_part_sender_id : broadcast_splits_part_process_data.split_sender_id_arr[dynamic_index_for_dynamic_arr],
			split_part_service_provider : broadcast_splits_part_process_data.split_service_provider_arr[dynamic_index_for_dynamic_arr],
			split_part_diff_in_sec : broadcast_splits_part_process_data.diff_in_sec_arr[dynamic_index_for_dynamic_arr],
			split_part_specific_date : broadcast_splits_part_process_data.split_specific_time_arr[dynamic_index_for_dynamic_arr],
			batchCampaignId:batchCampaignId,
			minuteCounter:minuteCounter,
			previousProvider:previousProvider
		};
		var user_split_part_process_data = $.extend({},broadcast_splits_part_process_data,start_perpage_obj);
		var total_split_part_process_data_count = user_split_part_process_data.totalCount;

		console.log('user_split_part_process_data',user_split_part_process_data);
		$.ajax({
			url : BASE_URL + 'broadcast/broadcast_process_split_part_user_data',
			type : 'post',
			data : user_split_part_process_data,
			success:function(response){

				var process_response = JSON.parse(response);
				var intervalCount = process_response.intervalCount;
				var is_set_except_days = process_response.is_set_except_days;
				
				// set batchCampaignID from response.
				batchCampaignId = process_response.batchCampaignId;
				minuteCounter = process_response.minuteCounter;
				previousProvider = process_response.previousProvider;

				broadcast_split_part_interval_count_total += intervalCount;
				//console.log('broadcast_split_part_interval_count_total',broadcast_split_part_interval_count_total);
				var remaining_records = total_split_part_process_data_count - broadcast_split_part_interval_count_total;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (broadcast_split_part_interval_count_total < total_split_part_process_data_count  && intervalCount > 0) {

					broadcast_split_part_process_start = broadcast_split_part_process_start + broadcast_split_part_process_per_page;
					if (remaining_records < broadcast_split_part_process_per_page) {
						broadcast_split_part_process_per_page = remaining_records;
					}

					var getNextHighestIndexOfGivenArr = getNextHighestIndex(user_split_part_process_data.get_split_divided_arr_val,broadcast_split_part_interval_count_total);

					if (getNextHighestIndexOfGivenArr >= 0) {
						dynamic_index_for_dynamic_arr = getNextHighestIndexOfGivenArr;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						broadcast_split_part_process_start = 0;
						is_set_except_days_for_split_part_process_user_data = 1;
					}

					make_process_for_split_parts_broadcast_data(broadcast_splits_part_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					scroll_to_top();
					reload_current_page();
				}

			}
		});
		
	}

	function getNextHighestIndex(arr, number) {

	  for (var i = 0; i < arr.length; i ++) {
	    if (arr[i] > number) {
	      return i;
	    }
	  }
	}
	

	var re_track_broadcast_send_now_process_start = 0;
	var re_track_broadcast_send_now_process_per_page = 150;
	var re_track_broadcast_send_now_intervalCountTotal = 0;
	var is_set_except_days_for_re_track_send_now_process_user_data = 0;
	var re_batchCampaignId = 0;
	function make_re_track_braodcast_send_now_process_for_user_data(re_track_broadcast_send_now_process_data){


		var numberOfSms = re_track_broadcast_send_now_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < re_track_broadcast_send_now_process_per_page) {
			re_track_broadcast_send_now_process_per_page = numberOfSms;
		}

		var start_perpage_obj = {
			re_track_broadcast_send_now_process_start:re_track_broadcast_send_now_process_start,
			re_track_broadcast_send_now_process_per_page:re_track_broadcast_send_now_process_per_page,
			is_set_except_days:is_set_except_days_for_re_track_send_now_process_user_data,
			batchCampaignId:re_batchCampaignId
		};
		var re_track_broadcast_send_now_user_process_data = $.extend({},re_track_broadcast_send_now_process_data,start_perpage_obj);
		var re_track_broadcast_send_now_total_process_data_count = re_track_broadcast_send_now_user_process_data.totalCount;

		console.log('re_track_broadcast_send_now_user_process_data',re_track_broadcast_send_now_user_process_data);
		
		$.ajax({
			url : BASE_URL + 're_tracking_broadcast/re_track_broadcast_send_now_process_user_data',
			type : 'post',
			data : re_track_broadcast_send_now_user_process_data,
			success:function(response){

				var re_track_broadcast_send_now_process_response = JSON.parse(response);
				var intervalCount = re_track_broadcast_send_now_process_response.intervalCount;
				var is_set_except_days = re_track_broadcast_send_now_process_response.is_set_except_days;

				// set batchCampaignID from response.
				re_batchCampaignId = re_track_broadcast_send_now_process_response.batchCampaignId;

				console.log('intervalCount',re_track_broadcast_send_now_process_response.intervalCount);
				re_track_broadcast_send_now_intervalCountTotal += intervalCount;
				var remaining_records = re_track_broadcast_send_now_total_process_data_count - re_track_broadcast_send_now_intervalCountTotal;
				console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (re_track_broadcast_send_now_intervalCountTotal < re_track_broadcast_send_now_total_process_data_count  && intervalCount > 0) {
					re_track_broadcast_send_now_process_start = re_track_broadcast_send_now_process_start + re_track_broadcast_send_now_process_per_page;
					console.log('re_track_broadcast_send_now_process_start',re_track_broadcast_send_now_process_start);
					if (remaining_records < re_track_broadcast_send_now_process_per_page) {
						re_track_broadcast_send_now_process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						re_track_broadcast_send_now_process_start = 0;
						is_set_except_days_for_re_track_send_now_process_user_data = 1;
					}
					make_re_track_braodcast_send_now_process_for_user_data(re_track_broadcast_send_now_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					scroll_to_top();
					reload_current_page();
				}

			}
		});
	}



	var re_track_broadcast_specific_time_process_start = 0;
	var re_track_broadcast_specific_time_process_per_page = 150;
	var re_track_broadcast_specific_time_intervalCountTotal = 0;
	var is_set_except_days_for_re_track_specific_time_process_user_data = 0;
	var re_batchCampaignId = 0;
	var re_minuteCounter = 1;
	var re_previousProvider = '';

	function make_re_track_braodcast_specific_time_process_for_user_data(re_track_broadcast_specific_time_process_data){

		var numberOfSms = re_track_broadcast_specific_time_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < re_track_broadcast_specific_time_process_per_page) {
			re_track_broadcast_specific_time_process_per_page = numberOfSms;
		}

		var start_perpage_obj = {
			re_track_broadcast_specific_time_process_start:re_track_broadcast_specific_time_process_start,
			re_track_broadcast_specific_time_process_per_page:re_track_broadcast_specific_time_process_per_page,
			is_set_except_days:is_set_except_days_for_re_track_specific_time_process_user_data,
			batchCampaignId:re_batchCampaignId,
			minuteCounter:re_minuteCounter,
			previousProvider:re_previousProvider
		};
		var re_track_broadcast_specific_time_user_process_data = $.extend({},re_track_broadcast_specific_time_process_data,start_perpage_obj);
		var re_track_broadcast_specific_time_total_process_data_count = re_track_broadcast_specific_time_user_process_data.totalCount;

		//console.log('re_track_broadcast_specific_time_user_process_data',re_track_broadcast_specific_time_user_process_data);
		
		$.ajax({
			url : BASE_URL + 're_tracking_broadcast/re_track_broadcast_specific_time_process_user_data',
			type : 'post',
			data : re_track_broadcast_specific_time_user_process_data,
			success:function(response){

				var re_track_broadcast_specific_time_process_response = JSON.parse(response);
				var intervalCount = re_track_broadcast_specific_time_process_response.intervalCount;
				var is_set_except_days = re_track_broadcast_specific_time_process_response.is_set_except_days;

				// set batchCampaignID from response.
				re_batchCampaignId = re_track_broadcast_specific_time_process_response.batchCampaignId;
				re_minuteCounter = re_track_broadcast_specific_time_process_response.minuteCounter;
				re_previousProvider = re_track_broadcast_specific_time_process_response.previousProvider;

				//console.log('intervalCount',re_track_broadcast_specific_time_process_response.intervalCount);
				re_track_broadcast_specific_time_intervalCountTotal += intervalCount;
				var remaining_records = re_track_broadcast_specific_time_total_process_data_count - re_track_broadcast_specific_time_intervalCountTotal;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (re_track_broadcast_specific_time_intervalCountTotal < re_track_broadcast_specific_time_total_process_data_count && intervalCount > 0) {
					re_track_broadcast_specific_time_process_start = re_track_broadcast_specific_time_process_start + re_track_broadcast_specific_time_process_per_page;
					/*console.log('re_track_broadcast_specific_time_process_start',re_track_broadcast_specific_time_process_start);*/
					if (remaining_records < re_track_broadcast_specific_time_process_per_page) {
						re_track_broadcast_specific_time_process_per_page = remaining_records;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						re_track_broadcast_specific_time_process_start = 0;
						is_set_except_days_for_re_track_specific_time_process_user_data = 1;
					}
					make_re_track_braodcast_specific_time_process_for_user_data(re_track_broadcast_specific_time_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					scroll_to_top();
					reload_current_page();
				}

			}
		});

	}


	var re_track_broadcast_split_part_process_start = 0;
	var re_track_broadcast_split_part_process_per_page = 150;
	var re_track_broadcast_split_part_interval_count_total = 0;
	var is_set_except_days_for_re_track_split_part_process_user_data = 0;
	var re_track_dynamic_index_for_dynamic_arr = 0;  // split_sender_id_arr, split_service_provider_arr, diff_in_sec_arr...It will change
	var re_batchCampaignId = 0;
	var re_minuteCounter = 1;
	var re_previousProvider = '';

	function make_re_track_broadcast_split_part_process_for_user_data(re_track_broadcast_split_part_process_data){

		console.log('re_track_broadcast_split_part_process_data',re_track_broadcast_split_part_process_data);
		var numberOfSms = re_track_broadcast_split_part_process_data.numberOfSms;

		if (numberOfSms > 0 && numberOfSms < re_track_broadcast_split_part_process_per_page) {
			re_track_broadcast_split_part_process_per_page = numberOfSms;
		}
		
		var start_perpage_obj = {
			re_track_broadcast_split_part_process_start:re_track_broadcast_split_part_process_start,
			re_track_broadcast_split_part_process_per_page:re_track_broadcast_split_part_process_per_page,
			is_set_except_days:is_set_except_days_for_re_track_split_part_process_user_data,
			split_part_sender_id : re_track_broadcast_split_part_process_data.split_sender_id_arr[re_track_dynamic_index_for_dynamic_arr],
			split_part_service_provider : re_track_broadcast_split_part_process_data.split_service_provider_arr[re_track_dynamic_index_for_dynamic_arr],
			split_part_diff_in_sec : re_track_broadcast_split_part_process_data.diff_in_sec_arr[re_track_dynamic_index_for_dynamic_arr],
			split_part_specific_date : re_track_broadcast_split_part_process_data.split_specific_time_arr[re_track_dynamic_index_for_dynamic_arr],
			batchCampaignId:re_batchCampaignId,
			minuteCounter:re_minuteCounter,			
			previousProvider:re_previousProvider			
		};
		var re_track_broadcast_user_split_part_process_data = $.extend({},re_track_broadcast_split_part_process_data,start_perpage_obj);
		var re_track_broadcast_total_split_part_process_data_count = re_track_broadcast_user_split_part_process_data.totalCount;

		console.log('re_track_broadcast_user_split_part_process_data',re_track_broadcast_user_split_part_process_data);
		$.ajax({
			url : BASE_URL + 're_tracking_broadcast/re_track_broadcast_process_split_part_user_data',
			type : 'post',
			data : re_track_broadcast_user_split_part_process_data,
			success:function(response){

				var process_response = JSON.parse(response);
				var intervalCount = process_response.intervalCount;
				var is_set_except_days = process_response.is_set_except_days;

				re_track_broadcast_split_part_interval_count_total += intervalCount;

				// set batchCampaignID from response.
				re_batchCampaignId = process_response.batchCampaignId;
				re_minuteCounter = process_response.minuteCounter;
				re_previousProvider = process_response.previousProvider;

				//console.log('re_track_broadcast_split_part_interval_count_total',re_track_broadcast_split_part_interval_count_total);
				var remaining_records = re_track_broadcast_total_split_part_process_data_count - re_track_broadcast_split_part_interval_count_total;
				//console.log('remaining_records',remaining_records);
				$('#sucErrMsg').text("Total " + remaining_records + " records are remaining to proceed. Please do not refresh").addClass('alert alert-success');
				scroll_to_top();

				if (re_track_broadcast_split_part_interval_count_total < re_track_broadcast_total_split_part_process_data_count && intervalCount > 0) {

					re_track_broadcast_split_part_process_start = re_track_broadcast_split_part_process_start + re_track_broadcast_split_part_process_per_page;
					if (remaining_records < re_track_broadcast_split_part_process_per_page) {
						re_track_broadcast_split_part_process_per_page = remaining_records;
					}

					var getNextHighestIndexOfGivenArr = getNextHighestIndex(re_track_broadcast_user_split_part_process_data.get_split_divided_arr_val,re_track_broadcast_split_part_interval_count_total);

					if (getNextHighestIndexOfGivenArr >= 0) {
						re_track_dynamic_index_for_dynamic_arr = getNextHighestIndexOfGivenArr;
					}

					if (is_set_except_days == 1) {   // because last_sms_date changes when it is filled
						re_track_broadcast_split_part_process_start = 0;
						is_set_except_days_for_re_track_split_part_process_user_data = 1;
					}

					make_re_track_broadcast_split_part_process_for_user_data(re_track_broadcast_split_part_process_data);
				}else{
					$('#sucErrMsg').text("Your SMS broadcast is live").addClass('alert alert-success');
					scroll_to_top();
					reload_current_page();
				}

			}
		});

	}

	
	function make_sms_broadcast_live_button_disabled(){
		$('#set_broadcast_live').attr('disabled','disabled').val('Processing');
	}

	function reload_current_page(){

		setTimeout(function(){ 
			location.reload();
		}, 2000);
		
	}


	function open_test_sms_modal(){

		$('#test_sms_err_suc_msg').text('').removeClass('alert alert-success alert-danger alert-warning');
		$('#test_popup_prefix').val('');
		$('#test_popup_service_provider').val('');
		$('#test_popup_mobile_number').val('');
		$('#test_msg').val('');
		$('#test_popup_sender_id').val('');
		$('#testSmsPopup').modal('show');
	}

	function proceedSendTestSms(){

		var test_popup_prefix = $('#test_popup_prefix').val();
		var test_popup_mobile_number = $.trim($('#test_popup_mobile_number').val());
		var test_popup_sender_id = $.trim($('#test_popup_sender_id').val());
		var test_popup_service_provider = $('#test_popup_service_provider').val();
		var test_msg = $.trim($('#test_msg').val());
		var domain = $.trim($('#domain').val());
		var unsubscribeDomain = $.trim($('#unsubscribeDomain').val());
			
		if (test_popup_prefix == '') {
			$('#test_sms_err_suc_msg').text('Please select prefix').addClass('alert alert-danger');
			return false;
		}else if(test_popup_mobile_number == ''){
			$('#test_sms_err_suc_msg').text('Please enter mobile number').addClass('alert alert-danger');
			return false;
		}else if(test_popup_mobile_number.length < 4 || test_popup_mobile_number.length > 15){
			$('#test_sms_err_suc_msg').text('Please enter valid mobile number').addClass('alert alert-danger');
			return false;
		}else if(test_popup_sender_id == ''){
			$('#test_sms_err_suc_msg').text('Please enter sender id').addClass('alert alert-danger');
			return false;
		}else{
			//check sender id validation
			
			var isValid = isValidSenderId(test_popup_sender_id);

			if (isValid != 1) {
				$('#test_sms_err_suc_msg').text('Invalid sender id. It can be either numeric with a limit of 15 chars or alphanumeric with a limit of 11 chars').addClass('alert alert-danger');
				return false;
			}else{
				if(test_popup_service_provider == ''){
					$('#test_sms_err_suc_msg').text('Please select provider').addClass('alert alert-danger');
					return false;
				}else if(test_msg == ''){
					$('#test_sms_err_suc_msg').text('Please enter message').addClass('alert alert-danger');
					return false;
				}else if(test_msg.indexOf("{url}") == -1){
					$('#test_sms_err_suc_msg').text('{url} is required in message field').addClass('alert alert-danger');
					return false;
				}else if(test_msg.indexOf("{unsubscribe_url}") == -1){
					$('#test_sms_err_suc_msg').text('{unsubscribe_url} is required in message field').addClass('alert alert-danger');
					return false;
				}else{
					$('#test_sms_err_suc_msg').text('Sending SMS... Please wait...').addClass('alert alert-warning');
					
					var test_send_obj = {
						test_popup_prefix : test_popup_prefix, 
						test_popup_mobile_number : test_popup_mobile_number, 
						test_popup_sender_id : test_popup_sender_id, 
						test_popup_service_provider : test_popup_service_provider, 
						test_msg : test_msg,
						domain : domain,
						unsubscribeDomain : unsubscribeDomain
					}; 

					$.ajax({
						url : BASE_URL + 'send_test_sms/send_sms',
						type : 'post',
						data:test_send_obj,
						success:function(res){

							var res = JSON.parse(res);
							console.log('res > > >',res);
							if (res['err'] == 0) {
								$('#test_sms_err_suc_msg').text(res.msg).removeClass('alert-danger alert-warning').addClass('alert alert-success');
							}else{
								$('#test_sms_err_suc_msg').text(res.msg).removeClass('alert-success alert-warning').addClass('alert alert-danger');
							}
						}
					});
				}
			}
		}
	}


	function isValidSenderId(str){

		if ((str.match(/[a-z]/i) && str.length >= 1 && str.length <= 11 ) || ($.isNumeric(str) && str.length >= 1 && str.length <= 15)) {
			return 1;
		}else{
			return 0;
		}
		
	}


	function get_current_date_time(){
		var d = new Date($.now());
		var current_date = d.getFullYear()+"-"+("0" + (d.getMonth() + 1)).slice(-2)+"-"+("0" + (d.getDate())).slice(-2)+"T"+d.getHours()+":"+d.getMinutes();		
		return current_date;
	}


	function get_split_divided_arr(arr_val){
		
		var total_num = arr_val.total_num;
		var divided = arr_val.divided;
		var val = Math.floor(total_num / divided);
		var reminder = total_num % divided;

		var return_arr = [];
		for(var i=0; i < divided; i++) {
		    
		    if(i == 0){
		        return_arr[i] = val;
		    }else{
		        return_arr[i] = return_arr[i - 1] + val;
		        
		        if(i == (divided - 1)){
		            return_arr[i] += reminder;
		        }
		    }
		    
		}

		return return_arr;

	}
</script>