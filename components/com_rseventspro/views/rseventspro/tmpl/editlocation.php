<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.editor');?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_EDIT_LOCATION',$this->row->name); ?></h1>

<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<script type="text/javascript">
	var map;
	var geocoder;
	var marker;
	
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapDiv = document.getElementById('map-canvas');
		
		// Create the map object
		map = new google.maps.Map(mapDiv, {
			center: new google.maps.LatLng(<?php echo $this->row->coordinates ? $this->row->coordinates : rseventsproHelper::getConfig('google_maps_center'); ?>),
			zoom: <?php echo rseventsproHelper::getConfig('google_map_zoom','int') ?>,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo $this->row->coordinates ? $this->row->coordinates : rseventsproHelper::getConfig('google_maps_center'); ?>),
			draggable: true
		});
	
		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						document.getElementById('address').value = results[0].formatted_address;
						document.getElementById('coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}
	
	function codeAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				$('coordinates').value = results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7);
			} else {
				alert('<?php echo JText::_('COM_RSEVENTSPRO_LOCATION_NOT_FOUND',true); ?>');
			}
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);
	
	// Search for addresses	
	function getLocations(term) {		
		var content = $('rsepro_results');
		addressSize = $('address').getSize();
		
		$('rsepro_results').setStyle('width', addressSize.x);
		$('rsepro_results').style.display = 'none';
		$$('#rsepro_results li').each(function(el) {
				el.dispose();
			});
		
		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK')
				{
					results.each(function(item) {			
						
						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});
						
						thea.addEvent('click', function() {
							$('address').value = item.formatted_address;
							$('coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
							var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(7), item.geometry.location.lng().toFixed(7));
							marker.setPosition(location);
							map.setCenter(location);
							$('rsepro_results').style.display = 'none';
						});
						
						thea.inject(theli);
						theli.inject(content);
						
					});
				
					$('rsepro_results').style.display = '';
				}
			});
		}
	}
	
	function clearLocations() {
		setTimeout( function () {
			$('rsepro_results').style.display = 'none';
		},1000);
	}
</script>
<?php } ?>

<?php if (rseventsproHelper::isGallery() && !rseventsproHelper::isJ3()) { ?>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('.rschosen').chosen({
		disable_search_threshold : 10
	});
});
</script>
<?php } ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editlocation'); ?>" method="post" name="locationForm" id="locationForm">
	<div>
		<label for="name"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_NAME'); ?></label>
		<input type="text" id="name" name="jform[name]" value="<?php echo $this->escape($this->row->name); ?>" class="rs_edit_inp_small" />
	</div>
	<div>
		<label for="url"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_URL'); ?></label>
		<input type="text" id="url" name="jform[url]" value="<?php echo $this->escape($this->row->url); ?>" class="rs_edit_inp_small" />
	</div>
	<div>
		<label for="address"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS'); ?></label>
		<input type="text" autocomplete="off" id="address" name="jform[address]" value="<?php echo $this->escape($this->row->address); ?>" class="rs_edit_inp_small" onkeyup="getLocations(this.value)" onblur="clearLocations();" />
		<ul id="rsepro_results" style="display:none;"></ul>
		<button type="button" style="border:medium none;height:30px;" onclick="codeAddress()"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT'); ?></button>
	</div>
	<?php if (rseventsproHelper::isGallery()) { ?>
	<div class="rs_clear"></div>
	<div>
		<label for="gallery_tags" style="margin-top: 10px;"><?php echo JText::_('COM_RSEVENTSPRO_GALLERY_TAGS'); ?></label>
		<div class="rs_gallery_tags">
			<select name="jform[gallery_tags][]" id="gallery_tags" multiple="multiple" class="rs200 rschosen">
				<?php echo JHtml::_('select.options',rseventsproHelper::getGalleryTags(),'value','text', $this->row->gallery_tags); ?>
			</select>
		</div>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	<div>
		<label for="description"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_DESCRIPTION'); ?></label>
		<div style="float:left;width: 90%">
			<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[description]',$this->row->description,'80%', '70%', 20, 7, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
		</div>
	</div>
	<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
	<div>&nbsp;</div>
	<div id="map-canvas" style="width:100%;height: 400px"></div>
	<div>&nbsp;</div>
	<div>
		<label for="coordinates"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_COORDINATES'); ?></label>
		<input type="text" id="coordinates" name="jform[coordinates]" value="<?php echo $this->escape($this->row->coordinates); ?>" class="rs_edit_inp_small" />
	</div>
	<?php } ?>
	<div style="text-align:right;">
		<button type="button" class="button btn btn-primary" onclick="document.locationForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savelocations" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->row->id; ?>" />
</form>