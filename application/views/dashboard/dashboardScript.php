<script type="text/javascript">

$(document).ready(function(){

	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	})

})

var BASE_URL = '<?php echo base_url(); ?>';

$(document).ready(function(){
	$('#dashboardStat').html("");
	$.ajax({
		url: BASE_URL + 'AdminHome/getAllCountries/',
		type:"GET",
		success:function(countries){
			var countries = JSON.parse(countries);
			for(var i=0;i<countries.length;i++){
				//get country wise all records
				$.ajax({
					url: BASE_URL + 'AdminHome/getDashboarsStat/',
					type:"POST",
					data:{
						country : countries[i].country
					},
					success:function(response){
						$('#dashboardStat').append(response);
					}
				});
			}
		}

	});
});

</script>
