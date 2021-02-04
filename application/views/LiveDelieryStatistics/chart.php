<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<style>
    .btn-group{
        display: block !important;
    }
    .multiselect{
        min-width: 100%;
        padding: 10px;
        background: #fff;
        color: #000;
        text-align: left;
    }
    .caret{
        float: right;
        margin-top: 7px;
        border-top: 7px solid;
    }
</style>
<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 p-r-0 title-margin-right">
                    <div class="page-header">
                        <div class="page-title">
                            <h1 id="providerName">Live Delivery Statistics</h1>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
                <div class="col-lg-4 p-l-0 title-margin-left">
                    <div class="page-header">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="<?php echo base_url(); ?>">Dashboard</a></li>
                                <li class="active"><?php echo $headerTitle; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /# column -->
            </div>
            <!-- /# row -->
            <section id="main-content">
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card alert">
                            <div class="card-body">
                                <div class="basic-form">
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="sucErrMsg"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label>API Key  (Group-Keyword) *</label>
                                                <select class="form-control" name="country" id="select_apikey">
                                                    <option value="">Select Api Key</option>
                                                        <?php 
                                                        foreach ($apikeys as $apikey) {                                              
                                                        ?>
                                                            <option value="<?php echo $apikey['apikey']; ?>"> <?php echo $apikey['groupName'].'-'.$apikey['keyword'].' ('.$apikey['apikey'].')'; ?></option>
                                                        <?php 
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label>Date</label>
                                                <input class="form-control" type="date" name="deliveryDate" value = "<?php echo @$_GET['deliveryDate']; ?>"  >
                                            </div>  
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success" id="btn_insert">Submit</button>
                                    <a href="<?php echo base_url('repost/addEdit'); ?>" class="btn btn-default" >Reset</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <!-- /# column -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>Chart</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="width:75%;">
                                    <canvas id="canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </section>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#btn_insert').on('click',function(){
            var select_apikey = $('#select_apikey').val();
            if(select_apikey != '') {
                $.ajax({
                    url: '<?php echo base_url(); ?>LiveDelieryStatistics/getMailProviderData',
                    type: 'post',
                    data: {
                        apikey: select_apikey
                    },
                    success: function(response) {

                        
                    }
                });
            } 
        });
        console.log(select_apikey);
    });
		var randomScalingFactor = function(){ return Math.round(Math.random()*70)};
		window.chartColors = {
			blue: 'rgb(3, 169, 245)',
			green: 'rgb(76, 175, 80)'
		};
		var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var config = {
			type: 'bar',
			data: {
				labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
				datasets: [{
					label: 'Queue',
					backgroundColor: window.chartColors.blue,
					borderColor: window.chartColors.blue,
					data: [
						0,
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor()
					],
					fill: false,
				}, {
					label: 'Send',
					fill: false,
					backgroundColor: window.chartColors.green,
					borderColor: window.chartColors.green,
					data: [
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor()
					],
				}]
			},
			options: {
                responsive: true,
                legend: {
                    position: 'top',
                    align: "end"
                },
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
                        barPercentage: 0.5,
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Mail Provider'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Live Delivery'
						}
					}]
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);
		};
		
	</script>


