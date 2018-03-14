ed.require(['edq', 'chartjs'], function($, Subscription) {

    var assignedData = {
        labels: <?php echo $assignedPostDate; ?>,
        datasets: [
            {
                label: "<?php echo JText::_('COM_EASYDISCUSS_MY_POST_ASSIGNED') ?>",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#eee",
                pointHighlightFill: "#eee",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: <?php echo $assignedPostHistory; ?>
            }
        ]
    };

    var assignedOptions ={
        tooltipTemplate: "[%if (label){%][%=label%]: [%}%][%= value %] <?php echo JText::_('COM_EASYDISCUSS_CHART_MYPOST_POSTS');?>",
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>",
        responsive: true,
        maintainAspectRatio: false
    }

    var ctx = document.getElementById("assign-chart").getContext("2d");
    var myLineChart = new Chart(ctx).Line(assignedData, assignedOptions);

    //--------------------------------------------------------------------------
    // display a doughnut chart for show total assiged post                   ||  
    //--------------------------------------------------------------------------
    var totalAssignedData = [
        {
            // if that is no any post assigned, it will show 1 for appear the pie
            value: <?php echo $totalAssignedData ? $totalAssignedData : '1'; ?>,
            color:"#ccc",
            label: "<?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_ASSIGNED') ?>"
        }
    ]

    var totalAssignedOptions ={
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>",
        responsive : true,
        maintainAspectRatio: false,
        segmentShowStroke : false,
        percentageInnerCutout: 94,
        showTooltips : false 
    }    

    var ctx = document.getElementById("total-assigned-post-area").getContext("2d");
    var totalAssignedDoughnutChart = new Chart(ctx).Doughnut(totalAssignedData, totalAssignedOptions);

    //--------------------------------------------------------------------------
    // display a doughnut chart for show total solved post                    ||  
    //--------------------------------------------------------------------------
    var totalSolvedData = [
        {
            value: <?php echo $totalResolvedData ? $totalResolvedData : '1'; ?>,
            color:"#ccc",
            label: "<?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_RESOLVED') ?>"
        }
    ]

    var totalSolvedOptions ={
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>",
        responsive: true,
        maintainAspectRatio: false,
        segmentShowStroke : false,
        percentageInnerCutout: 94,
        showTooltips : false 
    }    

    var ctx = document.getElementById("total-solved-post-area").getContext("2d");
    var totalSolvedDoughnutChart = new Chart(ctx).Doughnut(totalSolvedData, totalSolvedOptions);

    //--------------------------------------------------------------------------
    // display a doughnut chart for show current complete post                || 
    //--------------------------------------------------------------------------    
    var totalCompleteData = [
        {
            value: <?php echo $completedPercentage ? $completedPercentage : $emptyAssignedPostPercentage; ?>,
            color:"#39b54a",
            label: "<?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_COMPLETED') ?>"
        },
        {
            value: <?php echo $unresolvedPercentage; ?>,
            color: "#dd5036",
            label: "<?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_UNRESOLVED') ?>"
        }
    ]

    var totalCompleteOptions ={
        legendTemplate : "<ul class=\"[%=name.toLowerCase()%]-legend\">[% for (var i=0; i<datasets.length; i++){%]<li><span style=\"background-color:[%=datasets[i].strokeColor%]\"></span>[%if(datasets[i].label){%][%=datasets[i].label%][%}%]</li>[%}%]</ul>",
        responsive: true,
        maintainAspectRatio: false,
        segmentShowStroke : false,
        percentageInnerCutout: 94,
        showTooltips : false
    }    

    var ctx = document.getElementById("total-completed-post-area").getContext("2d");
    var totalCompleteDoughnutChart = new Chart(ctx).Doughnut(totalCompleteData, totalCompleteOptions);
});