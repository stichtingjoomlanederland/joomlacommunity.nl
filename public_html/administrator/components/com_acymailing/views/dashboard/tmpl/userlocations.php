<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!empty($this->geoloc_details)){ ?>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script language="JavaScript" type="text/javascript">
		var mapOptions = {
			legend: 'none',
			height: 400,
			displayMode: 'markers',
			colorAxis: {colors: ['', '#a8a12c']},
			sizeAxis: {minSize: 2, maxSize: 10, minValue: 1, maxValue: 15},
			enableRegionInteractivity: 'true',
			backgroundColor: 'transparent',
			region: '<?php echo $this->geoloc_region; ?>'
		};
		google.load('visualization', '1', {'packages': ['geochart']});
		google.setOnLoadCallback(drawMarkersMap);
		var chart;
		var data;
		function drawMarkersMap() {
			data = new google.visualization.DataTable();
			data.addColumn('string', 'Address');
			data.addColumn('number', 'Color');
			data.addColumn('number', 'Size');
			data.addColumn({type: 'string', role: 'tooltip'});
			<?php
			$myData = array();
			foreach($this->geoloc_city as $key => $city){
				$toolTipTxt = str_replace("'", "\'", JText::_('GEOLOC_NB_USERS')) . ': ' . $this->geoloc_details[$key];
				$lineData = "['" . str_replace("'", "\'",$this->geoloc_addresses[$key]).  "', 1, " . $this->geoloc_details[$key] . ", '" . $toolTipTxt . "']";
				array_push($myData, $lineData);
			}
			echo "data.addRows([" . implode(", ", $myData) . "]);";
			?>
			chart = new google.visualization.GeoChart(document.getElementById('mapGeoloc_div'));
			chart.draw(data, mapOptions);
		}
		;

	</script>
	<h1 class="acy_graphtitle"> <?php echo JText::sprintf('ACY_SUBSCRIBERS_LOCATIONS', $this->nbUsersToGet) ?> </h1>
	<div id="mapGeoloc_div"></div>
<?php } ?>
