<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(empty($this->data->bouncedetails)) return; ?>
<script language="JavaScript" type="text/javascript">
	function drawChart(){
		var dataTable = new google.visualization.DataTable();
		dataTable.addColumn('string');
		dataTable.addColumn('number');
		dataTable.addRows(<?php echo count($this->data->bouncedetails); ?>);

		<?php
		$i = 0;
		$table = '';
		foreach($this->data->bouncedetails as $oneRule => $total ){
			$found = preg_match('#^([A-Z0-9_]*) \[#Uis', $oneRule, $match);
			if($found) $oneRule = str_replace($match[1], JText::_($match[1]), $oneRule);
			$table .= '<tr><td>'.$total.'</td><td>'.$oneRule.'</td></tr>';
		?>
		dataTable.setValue(<?php echo $i ?>, 0, '<?php echo addslashes($oneRule).' ('.$total.')'; ?>');
		dataTable.setValue(<?php echo $i ?>, 1, <?php echo intval($total); ?>);
		<?php 	$i++;
		} ?>

		var vis = new google.visualization.PieChart(document.getElementById('bouncechart'));
		var options = {
			width: '100%', height: 400, is3D: true, legendTextStyle: {color: '#333333'}, legend: 'right'
		};
		vis.draw(dataTable, options);
	}
	google.load("visualization", "1", {packages: ["corechart"]});
	google.setOnLoadCallback(drawChart);
</script>
<div id="acy_content">
	<div id="iframedoc"></div>
	<div id="bouncechart"></div>
	<table id="bouncelist" class="acymailing_table">
		<?php echo $table; ?>
	</table>
</div>
