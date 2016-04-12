<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<h2><?php echo JText::_('COM_RSFIREWALL_ATTACKS_BLOCKED_PAST_MONTH'); ?></h2>
<div id="com-rsfirewall-logs-chart" style="height: 400px;"></div>

<script type="text/javascript">
// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(RSFirewalldrawChart);

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function RSFirewalldrawChart() {
	// Create the data table.
	var data = new google.visualization.DataTable();
	data.addColumn('date', 		'');
	data.addColumn('number', 	'<?php echo JText::_('COM_RSFIREWALL_LEVEL_LOW', true); ?>');
	data.addColumn('number', 	'<?php echo JText::_('COM_RSFIREWALL_LEVEL_MEDIUM', true); ?>');
	data.addColumn('number', 	'<?php echo JText::_('COM_RSFIREWALL_LEVEL_HIGH', true); ?>');
	data.addColumn('number', 	'<?php echo JText::_('COM_RSFIREWALL_LEVEL_CRITICAL', true); ?>');

	<?php foreach ($this->lastMonthLogs as $date => $item) { ?>
	data.addRow([new Date(<?php echo $date; ?>), <?php echo $item['low']; ?>, <?php echo $item['medium']; ?>, <?php echo $item['high']; ?>, <?php echo $item['critical']; ?>]);
	<?php } ?>
	
	// Set chart options	
	var options = {
		'colors': ['green', 'orange', 'red', 'black'],
		'backgroundColor': 'transparent', // try to make it transparent
		'legend': {'position': 'top'},
		'chartArea': {
			'left': 0,
			'width': '100%'
		}
	};

	// Instantiate and draw our chart, passing in some options.
	
	var chart = new google.visualization.LineChart(document.getElementById('com-rsfirewall-logs-chart'));
	chart.draw(data, options);
	
	RSFirewall.$(window).resize(function() {
		chart.draw(data, options);
	});
}
</script>
<style type="text/css">
svg { width: 100%; }
</style>