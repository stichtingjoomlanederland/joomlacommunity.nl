<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/textbox.php';

class RSFormProFieldGMaps extends RSFormProFieldTextbox
{
	static $mapScript = false;

	// backend preview
	public function getPreviewInput() {
		$caption 	= $this->getProperty('CAPTION','');
		$codeIcon	= RSFormProHelper::getIcon('gmaps');
		$html = '<td>'.$caption.'</td><td>'.$codeIcon.'</td>';
		return $html;
	}
	
	// functions used for rendering in front view
	public function getFormInput() {
		$id				= $this->getId();
		$componentId	= $this->getProperty('componentId');
		$mapWidth  		= $this->getProperty('MAPWIDTH', '450px');
		$mapHeight 		= $this->getProperty('MAPHEIGHT', '300px');
		$geoLocation 	= $this->getProperty('GEOLOCATION', 'NO');
		
		// Get the textbox input
		$textbox = parent::getFormInput();
		
		$html = '<div'.
				' id="rsform-map'.$this->componentId.'"'.
				' class="rsformMaps"'.
				' style="width: '.$this->escape($mapWidth).'; height: '.$this->escape($mapHeight).';"></div>'.
				'<br />';
		
		if ($geoLocation) {
			$html .= '<span style="position:relative;">'.
					 $textbox.
					 '<ul'.
					 ' id="rsform_geolocation'.$this->componentId.'"'.
					 ' class="rsform-map-geolocation"'.
					 ' style="display: none;"></ul>'.
					 '</span>';
		} else {
			$html .= $textbox;
		}
		
		// add the gmaps script
		$this->generateMap();

		return $html;
	}
	
	// @desc Overridden here because we need autocomplete set to off
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (!isset($attr['autocomplete'])) {
			$attr['autocomplete'] = 'off';
		}
		
		return $attr;
	}

	public function generateMap()
	{
		$id			= $this->getProperty('componentId');
		$zoom 		= (int) $this->getProperty('MAPZOOM', 2);
		$center 	= $this->getProperty('MAPCENTER', '39.5500507,-105.7820674');
		$geo		= $this->getProperty('GEOLOCATION', 'NO');
		$address	= $this->getProperty('MAPRESULT', 'ADDRESS');
		$name 		= $this->getProperty('NAME');
		$mapType 	= $this->getProperty('MAPTYPE', 'ROADMAP');
		
		$script		= '';
		
		$script .= "\n".'var rsformmap'.$id.'; var geocoder; var rsformmarker'.$id.';'."\n";
		$script .= 'function rsfp_initialize_map'.$id.'() {'."\n";
		$script .= "\t".'geocoder = new google.maps.Geocoder();'."\n";
		$script .= "\t".'var rsformmapDiv'.$id.' = document.getElementById(\'rsform-map'.$id.'\');'."\n";
		$script .= "\t".'rsformmap'.$id.' = new google.maps.Map(rsformmapDiv'.$id.', {'."\n";
		$script .= "\t\t".'center: new google.maps.LatLng('.$center.'),'."\n";
		$script .= "\t\t".'zoom: '.$zoom.','."\n";
		$script .= "\t\t".'mapTypeId: google.maps.MapTypeId.'.$mapType.','."\n";
		$script .= "\t\t".'streetViewControl: false'."\n";
		$script .= "\t".'});'."\n\n";
		$script .= "\t".'rsformmarker'.$id.' = new google.maps.Marker({'."\n";
		$script .= "\t\t".'map: rsformmap'.$id.','."\n";
		$script .= "\t\t".'position: new google.maps.LatLng('.$center.'),'."\n";
		$script .= "\t\t".'draggable: true'."\n";
		$script .= "\t".'});'."\n\n";
		$script .= "\t".'google.maps.event.addListener(rsformmarker'.$id.', \'drag\', function() {'."\n";
		$script .= "\t\t".'geocoder.geocode({\'latLng\': rsformmarker'.$id.'.getPosition()}, function(results, status) {'."\n";
		$script .= "\t\t\t".'if (status == google.maps.GeocoderStatus.OK) {'."\n";
		$script .= "\t\t\t\t".'if (results[0]) {'."\n";
		
		if ($address == 'ADDRESS')
			$script .= "\t\t\t\t\t".'document.getElementById(\''.$name.'\').value = results[0].formatted_address;'."\n";
		else
			$script .= "\t\t\t\t\t".'document.getElementById(\''.$name.'\').value = rsformmarker'.$id.'.getPosition().toUrlValue();'."\n";
		
		$script .= "\t\t\t\t".'}'."\n";
		$script .= "\t\t\t".'}'."\n";
		$script .= "\t\t".'});'."\n";
		$script .= "\t".'});'."\n";
		
		$currentValue = $this->getValue();
		if (!empty($currentValue)) {
			if ($address == 'ADDRESS') {
				$script .= "\n\t".'geocoder.geocode({\'address\': document.getElementById(\''.$name.'\').value}, function(results, status) {'."\n";
				$script .= "\t\t".'if (status == google.maps.GeocoderStatus.OK) {'."\n";
				$script .= "\t\t\t".'rsformmap'.$id.'.setCenter(results[0].geometry.location);'."\n";
				$script .= "\t\t\t".'rsformmarker'.$id.'.setPosition(results[0].geometry.location);'."\n";
				$script .= "\t\t".'}'."\n";
				$script .= "\t".'});'."\n";
			} else {
				$script .= "\t".'if (document.getElementById(\''.$name.'\') && document.getElementById(\''.$name.'\').value && document.getElementById(\''.$name.'\').value.length > 0 && document.getElementById(\''.$name.'\').value.indexOf(\',\') > -1) {'."\n";
				$script .= "\t\t".'rsformCoordinates'.$id.' = document.getElementById(\''.$name.'\').value.split(\',\');'."\n";
				$script .= "\t\t".'formPosition'.$id.' = new google.maps.LatLng(parseFloat(rsformCoordinates'.$id.'[0]),parseFloat(rsformCoordinates'.$id.'[1]));'."\n";
				$script .= "\t\t".'rsformmap'.$id.'.setCenter(formPosition'.$id.');'."\n";
				$script .= "\t\t".'rsformmarker'.$id.'.setPosition(formPosition'.$id.');'."\n";
				$script .= "\t}\n";
			}
		}
		
		
		$script .= '}'."\n";
		$script .= 'google.maps.event.addDomListener(window, \'load\', rsfp_initialize_map'.$id.');'."\n\n";
		
		if ($geo) {
			$isAdress = $address == 'ADDRESS';
			$script .= 'window.addEventListener("load", function(){'."\n";
			$script .= "\t".'rsfp_addEvent(document.getElementById(\''.$name.'\'),\'keyup\', function() { '."\n";
			$script .= "\t\t".'rsfp_geolocation(this.value,'.$id.',\''.$name.'\',rsformmap'.$id.',rsformmarker'.$id.',geocoder, '.(int) $isAdress.');'."\n";
			$script .= "\t".'});'."\n";
			$script .= '});'."\n";
		}
		
		// Add the Google Maps API JS
		if (!RSFormProFieldGMaps::$mapScript) {
			$this->addCustomTag('<script src="https://maps.google.com/maps/api/js?key='.urlencode(RSFormProHelper::getConfig('google.api_key')).'" type="text/javascript"></script>');
			// Do not load the script for every map field
			RSFormProFieldGMaps::$mapScript = true;
		}
		// Add the custom script after the maps.js is loaded in the dom
		$this->addCustomTag('<script type="text/javascript">'.$script.'</script>');
	}
}