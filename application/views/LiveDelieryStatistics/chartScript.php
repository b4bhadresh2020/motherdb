<script type="text/javascript">
    var BASEPATH = "<?php echo base_url(); ?>";

    $(document).ready(function(){
        var myChart;
        window.chartColors = {
            blue: 'rgb(3, 169, 245)',
            green: 'rgb(76, 175, 80)'
        };
        $('#btn_insert').on('click',function(){
            var select_apikey = $('#select_apikey').val();
            var delivery_date = $('#deliveryDate').val();

            if(select_apikey == '') {
                $('#sucErrMsg').text('Please select API Key').addClass('alert alert-danger');
            } else {
                $.ajax({
                    url: BASEPATH + 'LiveDelieryStatistics/getMailProviderData',
                    type: 'post',
                    data: {
                        apikey: select_apikey,
                        deliveryDate: delivery_date
                    },
                    success: function(response) {
                        $('.no-result').hide();
                        var provider = JSON.parse(response);
                        var provider_name = [];
                        var queue_live_delivery = [];
                        var send_live_delivery  = [];

                        $.each(provider.providerDetail, function (key, value) {
                            provider_name.push(value.listname);
                        }); 
                        $.each(provider.live_delivery, function (key, value) {
                            queue_live_delivery.push(value.queue_record);
                            send_live_delivery.push(value.send_record);
                        }); 

                        if(myChart) {
                            myChart.destroy();
                        }
                        var config = {
                            type: 'bar',
                            data: {
                                labels: provider_name,
                                datasets: [{
                                    label: 'Queue',
                                    backgroundColor: window.chartColors.blue,
                                    borderColor: window.chartColors.blue,
                                    data: queue_live_delivery,
                                    fill: false,
                                }, {
                                    label: 'Send',
                                    fill: false,
                                    backgroundColor: window.chartColors.green,
                                    borderColor: window.chartColors.green,
                                    data: send_live_delivery,
                                }]
                            },
                            options: {
                                responsive: true,
                                legend: {
                                    position: 'top',
                                    align: "end"
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

                        var ctx = document.getElementById('canvas').getContext('2d');
                        myChart = new Chart(ctx, config);
                    }
                });
            } 
        });
    });
</script>