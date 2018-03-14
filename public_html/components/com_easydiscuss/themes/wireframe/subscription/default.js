ed.require(['edq', 'site/src/subscription', 'chartjs'], function($) {

    // Find anchor links inside the tab
    var filters = $('[data-filter-anchor]');

    filters.on('click', function(event) {
        event.preventDefault();

        $(this).route();
    });

    var data = {
        labels: <?php echo $label; ?>,
        datasets: [
            {
                label: "<?php echo JText::_('COM_EASYDISCUSS_MY_POSTS_SUBSCRIPTION') ?>",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: <?php echo $postDataSet; ?>
            },
            {
                label: "<?php echo JText::_('COM_EASYDISCUSS_MY_CATEGORIES_SUBSCRIPTION') ?>",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: <?php echo $categoryDataSet; ?>
            }
        ]
    };

    var options ={
        multiTooltipTemplate: "[%= datasetLabel %] - [%= value %]",
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span class=\"chartjs-legend-label\" style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>",
        responsive: true,
        maintainAspectRatio: false
    }

    var ctx = document.getElementById("chart-area").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data, options);

    var legend = myLineChart.generateLegend();

    $('#js-legend').append(legend);
});