<script type="text/javascript">
    let genderData = <?= $countGenderArr ?>;
    let ageData = <?= $countAgeArr ?>;
    let cityData = <?= $countCityArr ?>;

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {        
        var genderChartdata = google.visualization.arrayToDataTable(genderData);
        var ageChartdata = google.visualization.arrayToDataTable(ageData);
        var cityChartdata = google.visualization.arrayToDataTable(cityData);

        var genderChartoptions = {
            title: 'Gender',
            pieHole: 0.5,
            is3D: false,
            chartArea:{left:40,top:40,width:'100%',height:'100%'},
            legend:{position: 'right', textStyle: {fontSize: 16}},
            titleTextStyle:{fontSize: 16}
        };
        var ageChartoptions = {
            title: 'Age',
            pieHole: 0.5,
            is3D: false,
            chartArea:{left:40,top:40,width:'100%',height:'100%'},
            legend:{position: 'right', textStyle: {fontSize: 16}},
            titleTextStyle:{fontSize: 16}
        };

        var cityChartoptions = {
            title: 'City',
            pieHole: 0.5,
            is3D: false,
            chartArea:{left:40,top:40,width:'100%',height:'100%'},
            legend:{position: 'right', textStyle: {fontSize: 16}},
            titleTextStyle:{fontSize: 16}
        };

        var genderChart = new google.visualization.PieChart(document.getElementById('gender_chart'));
        var ageChart = new google.visualization.PieChart(document.getElementById('age_chart'));
        var cityChart = new google.visualization.PieChart(document.getElementById('city_chart'));

        genderChart.draw(genderChartdata, genderChartoptions);
        ageChart.draw(ageChartdata, ageChartoptions);
        cityChart.draw(cityChartdata, cityChartoptions);
    }
</script>