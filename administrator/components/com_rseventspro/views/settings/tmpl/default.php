<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	var map_map;
	var map_marker;
	function map_initialize() {
	var mapDiv = document.getElementById('map_canvas');
	map_map = new google.maps.Map(mapDiv, {
	  center: new google.maps.LatLng(<?php echo $this->config->google_maps_center; ?>),
	  zoom: <?php echo $this->config->google_map_zoom; ?>,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	
	map_marker = new google.maps.Marker({
	  map: map_map,
	  position: new google.maps.LatLng(<?php echo $this->config->google_maps_center; ?>),
	  draggable: true
	});
	
	google.maps.event.addListener(map_marker, 'dragend', function(event) {
        document.getElementById('jform_google_maps_center').value = event.latLng.lat().toFixed(7) + ',' + event.latLng.lng().toFixed(7);
        });
	}

	google.maps.event.addDomListener(window, 'load', map_initialize);
	
	window.addEvent('load', function() {
		var eud = $('jform_user_display').options;
		for (i=0; i < eud.length; i++) {
			<?php if (!$this->social['cb']) { ?>
			if (eud[i].value == 3)
				eud[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['js']) { ?>
			if (eud[i].value == 2)
				eud[i].disabled = true;
			<?php } ?>
		}
		
		var eup = $('jform_user_profile').options;
		for (i=0; i < eup.length; i++) {
			<?php if (!$this->social['cb']) { ?>
			if (eup[i].value == 2)
				eup[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['js']) { ?>
			if (eup[i].value == 1)
				eup[i].disabled = true;
			<?php } ?>
		}
		
		var eeo = $('jform_event_owner').options;
		for (i=0; i < eeo.length; i++) {
			<?php if (!$this->social['cb']) { ?>
			if (eeo[i].value == 3)
				eeo[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['js']) { ?>
			if (eeo[i].value == 2)
				eeo[i].disabled = true;
			<?php } ?>
		}
		
		var eop = $('jform_event_owner_profile').options;
		for (i=0; i < eop.length; i++) {
			<?php if (!$this->social['cb']) { ?>
			if (eop[i].value == 2)
				eop[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['js']) { ?>
			if (eop[i].value == 1)
				eop[i].disabled = true;
			<?php } ?>
		}
		
		var eua = $('jform_user_avatar').options;
		for (i=0; i < eua.length; i++) {
			<?php if (!$this->social['cb']) { ?>
			if (eua[i].value == 'comprofiler')
				eua[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['js']) { ?>
			if (eua[i].value == 'community')
				eua[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['kunena']) { ?>
			if (eua[i].value == 'kunena')
				eua[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['fireboard']) { ?>
			if (eua[i].value == 'fireboard')
				eua[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['k2']) { ?>
			if (eua[i].value == 'k2')
				eua[i].disabled = true;
			<?php } ?>
		}
		
		var eec = $('jform_event_comment').options;
		for (i=0; i < eec.length; i++) {
			<?php if (!$this->social['rscomments']) { ?>
			if (eec[i].value == 2)
				eec[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['jcomments']) { ?>
			if (eec[i].value == 3)
				eec[i].disabled = true;
			<?php } ?>
			<?php if (!$this->social['jomcomment']) { ?>
			if (eec[i].value == 4)
				eec[i].disabled = true;
			<?php } ?>
		}
		
		<?php if (rseventsproHelper::isJ3()) { ?>
		jQuery('#jform_user_display').trigger('liszt:updated');
		jQuery('#jform_user_profile').trigger('liszt:updated');
		jQuery('#jform_event_owner').trigger('liszt:updated');
		jQuery('#jform_event_owner_profile').trigger('liszt:updated');
		jQuery('#jform_user_avatar').trigger('liszt:updated');
		jQuery('#jform_event_comment').trigger('liszt:updated');
		<?php } ?>
		
	});
	
	function fconnect() {
		document.location = 'https://graph.facebook.com/oauth/authorize?client_id=<?php echo $this->config->facebook_appid; ?>&type=user_agent&display=popup&scope=user_events&redirect_uri=http://www.rsjoomla.com/frseventspro/index.php?to=<?php echo base64_encode(JURI::root().'administrator/index.php?option=com_rseventspro&task=settings.savetoken'); ?>';
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=settings'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="span10">
		<?php foreach ($this->layouts as $layout) {
			// add the tab title
			$this->tabs->title('COM_RSEVENTSPRO_CONF_TAB_'.strtoupper($layout), $layout);
			
			// prepare the content
			$content = $this->loadTemplate($layout);
			
			// add the tab content
			$this->tabs->content($content);
		}
		
		// render tabs
		echo $this->tabs->render();
		?>
		<div id="mapContainer" style="display:none;">
			<div id="map_canvas" style="width: 100%; height: 380px; float: left;"></div>
		</div>
		
			<div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			</div>
		</div>
	</div>
</form>
<?php if (rseventsproHelper::ideal()) { ?>
<script type="text/javascript">rs_ideal(document.getElementById('jform_ideal_account').value);</script>
<?php } ?>