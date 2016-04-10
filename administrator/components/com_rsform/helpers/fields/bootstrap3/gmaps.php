<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/bootstrap3/textbox.php';

class RSFormProFieldBootstrap3GMaps extends RSFormProFieldBootstrap3Textbox
{
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
		
		//add the gmaps script
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
}