ed.require(['edq', 'chartjs'], function($) {

    var data = {
        labels: <?php echo $postsTicks; ?>,
        datasets: [
            {
                label: "My Created Posts",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: <?php echo $postsCreated; ?>
            }
        ]
    };

    var options ={
        tooltipTemplate: "[%if (label){%][%=label%]: [%}%][%= value %] <?php echo JText::_('COM_EASYDISCUSS_CHART_MYPOST_POSTS');?>",
        responsive: true,
        maintainAspectRatio: false,
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>"
    }

    var ctx = document.getElementById("chart-area").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data, options);

    var legend = myLineChart.generateLegend();

    $('#js-legend').append(legend);
});