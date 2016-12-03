
// Load the Visualization API and the piechart package.
google.load( 'visualization' , '1.0' , {
	packages : [ 'corechart' ]
});

// Set callback
google.setOnLoadCallback(drawCategoryChart);

function drawCategoryChart()
{
	var data 	= new google.visualization.DataTable();

	data.addColumn( 'string', 'Category' );
	data.addColumn( 'number', 'Posts' );

	data.addRows([
		<?php for ($i = 0; $i < count($categories); $i++) { ?>
			<?php $total = $categories[$i]->getPostCount(); ?>
			['<?php echo $this->escape(JText::_($categories[$i]->title, true));?> (<?php echo $total;?>)', <?php echo $total;?>]
			<?php if (next($categories) !== false) { ?>, <?php } ?>
		<?php } ?>
	]);

	var chart 	= new google.visualization.PieChart(document.getElementById('categoryChart'));

	chart.draw(data, {
		width: '100%',
		height: 250
	});
}