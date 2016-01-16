<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'location.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	
	<?php if (!rseventsproHelper::isJ3()) { ?>
	window.addEvent('domready', function() {
		$$('.rschosen').chosen({
			disable_search_threshold : 10
		});
	});
	<?php } ?>
</script>

<?php if ($this->config->enable_google_maps) { ?>
<script type="text/javascript">
	var map; var geocoder; var marker;
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapDiv = document.getElementById('map-canvas');
		
		// Create the map object
		map = new google.maps.Map(mapDiv, {
				center: new google.maps.LatLng(<?php echo $this->item->coordinates ? $this->item->coordinates : $this->config->google_maps_center; ?>),
				zoom: <?php echo (int) $this->config->google_map_zoom; ?>,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				streetViewControl: false
		});

		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo $this->item->coordinates ? $this->item->coordinates : $this->config->google_maps_center; ?>),
			draggable: true
		});
		
		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) 
					{
						document.getElementById('jform_address').value = results[0].formatted_address;
						document.getElementById('jform_coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}
	
	// Get coordinates for the inputed address
	function codeAddress() {
		var address = document.getElementById('jform_address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				$('jform_coordinates').value = results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7);
			} else {
				alert('<?php echo JText::_('COM_RSEVENTSPRO_LOCATION_NOT_FOUND',true); ?>');
			}
		});
	}
	
	// Initialize google map
	google.maps.event.addDomListener(window, 'load', initialize);
	
	// Search for addresses	
	function getLocations(term) {
		var content = $('rsepro_results');
		address = $('jform_address').getSize();
		
		$('rsepro_results').setStyle('width', address.x - 21);
		$('rsepro_results').style.display = 'none';
		$$('#rsepro_results li').each(function(el) {
				el.dispose();
			});
		
		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK') {
					results.each(function(item) {
						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});
						
						thea.addEvent('click', function() {
							$('jform_address').value = item.formatted_address;
							$('jform_coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
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
	
	window.addEvent('domready', function() {
		$('jform_address').addEvent('keyup', function() {
			getLocations(this.value);
		}).addEvent('blur', function() {
			clearLocations();
		});
	});
</script>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=location&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span7 rswidth-50 rsfltlft">
			<?php $input = $this->config->enable_google_maps ? ' <button type="button" onclick="codeAddress();" class="btn button">'.JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT').'</button>' : ''; ?>
			<?php $list = '<ul id="rsepro_results" style="display:none;"></ul>'; ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('url'), $this->form->getInput('url')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('address'), $this->form->getInput('address').$list.$input); ?>
			<?php if (rseventsproHelper::isGallery()) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('gallery_tags'), $this->form->getInput('gallery_tags')); ?>
			<?php } ?>
			<?php if ($this->config->enable_google_maps) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('coordinates'), $this->form->getInput('coordinates')); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			
			<?php echo $this->form->getInput('description'); ?>
		</div>
		
		<div class="span5 rsfltrgt rswidth-50">
			<?php if ($this->config->enable_google_maps) { ?>
			<div style="margin-left:60px;">
				<div id="map-canvas" style="width: 100%; height: 400px"></div>
			</div>
			<?php } else { ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_google_maps','config'), $this->form->getInput('enable_google_maps','config')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			<div style="margin-top:5px; text-align:center;">
				<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/map.png" alt="" />
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>