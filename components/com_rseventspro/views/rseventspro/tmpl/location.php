<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if (rseventsproHelper::getConfig('enable_google_maps') && !empty($this->row->coordinates)) { ?>
<script type="text/javascript">
	var map_location;
	var marker_location;
	<?php if(rseventsproHelper::getConfig('google_map_directions')) { ?>
	var rendererOptions = {draggable: true};
	var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);;
	var directionsService = new google.maps.DirectionsService();
	<?php } ?>
	
	function initialize() {
		var mapDiv = document.getElementById('map-canvas');
		map_location = new google.maps.Map(mapDiv, {
		  center: new google.maps.LatLng(<?php echo $this->row->coordinates ? $this->escape($this->row->coordinates) : rseventsproHelper::getConfig('google_maps_center'); ?>),
		  zoom: <?php echo rseventsproHelper::getConfig('google_map_zoom') ?>,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		marker_location = new google.maps.Marker({
		  map: map_location,
		  title: '<?php echo addslashes($this->row->name); ?>',
		  position: new google.maps.LatLng(<?php echo $this->escape($this->row->coordinates); ?>),
		  draggable: false
		});
		
		var contentString = '<b><?php echo addslashes($this->row->name); ?></b> <br /> <?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS',true); ?>: <?php echo addslashes($this->row->address); ?> <?php if (!empty($this->row->url)) echo '<br /><a target="_blank" href="'.addslashes($this->row->url).'">'.addslashes($this->row->url).'</a>'; ?>';
		
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		<?php if(rseventsproHelper::getConfig('google_map_directions')) { ?>
		directionsDisplay.setMap(map_location);
		directionsDisplay.setPanel(document.getElementById('rs_directions'));
		<?php } ?>

		
		google.maps.event.addListener(marker_location, 'click', function() {
		  infowindow.open(map_location,marker_location);
		});
		
		google.maps.event.addListener(map_location, 'click', function() {
		  infowindow.close();
		});
	}
	<?php if(rseventsproHelper::getConfig('google_map_directions')) { ?>
	function calcRoute() 
	{
		var request = {
			origin: document.getElementById('from_direction').value,
			destination: new google.maps.LatLng(<?php echo $this->escape($this->row->coordinates); ?>),
			travelMode: google.maps.TravelMode.DRIVING
		};
		
		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				directionsDisplay.setDirections(response);
			}
		});
	}
	<?php } ?>
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php } ?>


<h1><?php echo $this->row->name; ?></h1>
<b><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS'); ?> </b> <?php echo $this->row->address; ?>

<?php if ($this->row->url) { ?>
<br /><b><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_URL'); ?> </b> <a href="<?php echo $this->row->url; ?>"><?php echo $this->row->url; ?></a>
<?php } ?>

<br /> <br />
<?php echo rseventsproHelper::removereadmore($this->row->description); ?>
<br />
<?php echo rseventsproHelper::gallery('location',$this->row->id); ?>
<br />

<?php if (rseventsproHelper::getConfig('enable_google_maps') && !empty($this->row->coordinates)) { ?>
<div id="map-canvas" style="width: 100%; height: 400px"></div>
<?php if (rseventsproHelper::getConfig('google_map_directions')) { ?>
<div style="margin:15px 0;">
	<h3><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS'); ?></h3>
	<label for="from_direction"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_FROM'); ?></label>
	<input type="text" size="25" id="from_direction" name="from_direction" value="" onchange="calcRoute();" />
	<button id="rsegetdir" type="button" onclick="calcRoute();" class="button btn"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS'); ?></button>
</div>
<div class="rs_clear"></div>
<div id="rs_directions"></div>
<?php }	?>
<?php }	?>
<div class="rs_clear"></div>
<a href="javascript:history.go(-1);"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a>