<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$locations = count($this->events); ?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_EVENTS_MAP'); ?></h1>
<?php } ?>

<script type="text/javascript">
	var mapview;
	var geocoderview;
	function initialize_view() {
		geocoderview = new google.maps.Geocoder();
		var mapDiv = document.getElementById('map-canvas');
		mapview = new google.maps.Map(mapDiv, {
		  center: new google.maps.LatLng(<?php echo rseventsproHelper::getConfig('google_maps_center'); ?>),
		  zoom: <?php echo rseventsproHelper::getConfig('google_map_zoom','int'); ?>,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		
		var latlngboundsview = new google.maps.LatLngBounds();
		
		<?php if (!empty($this->events)) { ?>
		<?php foreach ($this->events as $location => $events) { ?>
		<?php if (empty($events)) continue; ?>
		<?php $event = $events[0]; ?>
		<?php $single = count($events) > 1 ? false : true; ?>
		<?php if (empty($event->coordinates) && empty($event->address)) continue; ?>
		
		<?php if (!empty($event->coordinates)) { ?>
		var coordinates_view = '<?php echo $event->coordinates; ?>';
		coordinates_view = coordinates_view.split(',');
		var lat = parseFloat(coordinates_view[0]);
		var lon = parseFloat(coordinates_view[1]);
		var markerv<?php echo $event->id; ?> = createMarker_view(new google.maps.LatLng(lat,lon));
		latlngboundsview.extend(new google.maps.LatLng(lat,lon));
		<?php } else { ?>
		var markerv<?php echo $event->id; ?> = createMarker_view();
		codeAddress_view('<?php echo $event->address; ?>',markerv<?php echo $event->id; ?>);
		<?php } ?>
		
		if (markerv<?php echo $event->id; ?> != false)
		{
			<?php if ($event->allday) { ?>
			var contentString<?php echo $event->id; ?> = '<b><a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo addslashes($event->name); ?></a></b> <br /> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON',true); ?> <?php echo addslashes(rseventsproHelper::date($event->start,$this->config->global_date,true)); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT',true); ?> <a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->lid,$event->lname)); ?>"><?php echo addslashes($event->lname); ?></a> ';
			<?php } else { ?>
			var contentString<?php echo $event->id; ?> = '<b><a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo addslashes($event->name); ?></a></b> <br /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTS',true); ?> <?php echo addslashes(rseventsproHelper::date($event->start,null,true)); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDS',true); ?> <?php echo addslashes(rseventsproHelper::date($event->end,null,true)); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT',true); ?> <a target="_blank" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->lid,$event->lname)); ?>"><?php echo addslashes($event->lname); ?></a> ';
			<?php } ?>
			
			<?php if (!$single) { ?>
			contentString<?php echo $event->id; ?> += '<br /><br /><a style="float:right;" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&location='.rseventsproHelper::sef($event->lid,$event->lname)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_VIEW_OTHER_EVENTS',true); ?></a>';
			<?php } ?>
			
			var infowindow<?php echo $event->id; ?> = new google.maps.InfoWindow({
				content: contentString<?php echo $event->id; ?>
			});
			
			google.maps.event.addListener(markerv<?php echo $event->id; ?>, 'click', function() {
			  infowindow<?php echo $event->id; ?>.open(mapview,markerv<?php echo $event->id; ?>);
			});
			
			google.maps.event.addListener(mapview, 'click', function() {
			  infowindow<?php echo $event->id; ?>.close();
			});
		}
		
		<?php } ?>
		<?php } ?>
		
		<?php if ($locations >= 1) { ?>
		mapview.setCenter(latlngboundsview.getCenter());
		<?php if ($locations != 1) { ?>mapview.fitBounds(latlngboundsview);<?php } ?>
		<?php } ?>
		
		google.maps.event.addListener(mapview, 'click', function(e) {
          pantocenter(e.latLng, mapview);
        });
		
	}
	
	function pantocenter(position,map)
	{
		var currentzoom = map.getZoom();
		if (currentzoom == 2)
		{
			map.panTo(position);
			map.setZoom(5);
		}
	}
	
	function createMarker_view(point)
	{
		new_marker = new google.maps.Marker({
		  map: mapview,
		  position: point,
		  draggable: false
		});
		
		return new_marker;
	}
	
	function codeAddress_view(address,themarker) 
	{
		geocoderview.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var lat = parseFloat(results[0].geometry.location.lat().toFixed(7));
				var lon = parseFloat(results[0].geometry.location.lng().toFixed(7));
				themarker.setPosition(new google.maps.LatLng(lat,lon));
			}
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize_view);
</script>

<?php if ($this->params->get('search',1)) { ?>
<form method="post" action="<?php echo JRoute::_(JURI::getInstance()); ?>" name="adminForm" id="adminForm">
	<div class="rs_search">
		<div class="rs_select_top" id="rs_select_top1">
			<?php echo $this->lists['filter_from']; ?>
		</div>
		
		<div class="rs_select_top" id="rs_select_top2">
			<?php echo $this->lists['filter_condition']; ?>
		</div>
		
		<div class="rs_select_top" style="position: relative;">
			<input type="text" name="search[]" id="rseprosearch" onkeyup="rs_search();" onkeydown="rs_stop();" value="" size="30" autocomplete="off" class="rs_input" />
			<button type="button" onclick="rs_add_filter();" class="rs_search_button"><span id="search_btn"></span></button>
			<ul class="rs_results" id="rs_results"></ul>
		</div>
		<div class="rs_clear"></div>
		
		<div class="rs_filter">
			<ul id="rs_filters">
				<?php if (!empty($this->columns)) { ?>
				<?php for ($i=0; $i<count($this->columns); $i++) { ?>
				<li>
					<span><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
					<span><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
					<strong><?php echo $this->escape($this->values[$i]); ?></strong>
					<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter(<?php echo $i; ?>)"></a>
					<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>" />
					<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>" />
					<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>" />
				</li>
				<?php } ?>
				<li><a href="javascript:void(0)" onclick="rs_clear_filters();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<input type="hidden" name="rs_clear" id="rs_clear" value="0" />
	<input type="hidden" name="rs_remove" id="rs_remove" value="" />
</form>
<?php } else { ?>
<?php if (!empty($this->columns)) { ?>
<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear&from=map'); ?>" class="rs_filter_clear"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>

<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<div id="map-canvas" style="width: 100%; height: 400px"></div>
<?php } ?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		<?php if ($this->params->get('search',1)) { ?>
		new elSelect( {container : 'rs_select_top1'} );
		new elSelect( {container : 'rs_select_top2'} );
		<?php } ?>
	});
</script>