<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class rseventsproMapHelper
{
	public static function loadMap($params) {
		$config = rseventsproHelper::getConfig();
		$script = array();

		// Load map files
		self::loadDependencies($config);

		switch($config->map) {
			case 'google':
				$script[] = "jQuery(function($) {";
				$script[] = "\tjQuery('#".$params['id']."').rsjoomlamap({";
				
				if (isset($params['address']))				$script[] = "\t\taddress: '".$params['address']."',";
				if (isset($params['coordinates']))			$script[] = "\t\tcoordinates: '".$params['coordinates']."',";
				if (isset($params['locationCoordonates']))	$script[] = "\t\tlocationCoordonates: '".$params['locationCoordonates']."',";
				if (isset($params['pinpointBtn']))			$script[] = "\t\tpinpointBtn: '".$params['pinpointBtn']."',";
				if (isset($params['directionsBtn']))		$script[] = "\t\tdirectionsBtn: '".$params['directionsBtn']."',";
				if (isset($params['directionsPanel']))		$script[] = "\t\tdirectionsPanel: '".$params['directionsPanel']."',";
				if (isset($params['directionsFrom']))		$script[] = "\t\tdirectionsFrom: '".$params['directionsFrom']."',";
				if (isset($params['directionNoResults']))	$script[] = "\t\tdirectionNoResults: '".$params['directionNoResults']."',";
				if (isset($params['zoom']))					$script[] = "\t\tzoom: ".$params['zoom'].",";
				if (isset($params['center']))				$script[] = "\t\tcenter: '".$params['center']."',";
				if (isset($params['markerDraggable']))		$script[] = "\t\tmarkerDraggable: ".$params['markerDraggable'].",";
				if (isset($params['resultsWrapperClass']))	$script[] = "\t\tresultsWrapperClass: '".$params['resultsWrapperClass']."',";
				if (isset($params['resultsClass']))			$script[] = "\t\tresultsClass: '".$params['resultsClass']."',";
				if (isset($params['radiusSearch']))			$script[] = "\t\tradiusSearch: '".$params['radiusSearch']."',";
				if (isset($params['radiusLocationId']))		$script[] = "\t\tradiusLocationId: '".$params['radiusLocationId']."',";
				if (isset($params['radiusValueId']))		$script[] = "\t\tradiusValueId: '".$params['radiusValueId']."',";
				if (isset($params['radiusUnitId']))			$script[] = "\t\tradiusUnitId: '".$params['radiusUnitId']."',";
				if (isset($params['radiusLoaderId']))		$script[] = "\t\tradiusLoaderId: '".$params['radiusLoaderId']."',";
				if (isset($params['radiusBtnId']))			$script[] = "\t\tradiusBtnId: '".$params['radiusBtnId']."',";
				if (isset($params['use_geolocation']))		$script[] = "\t\tuse_geolocation: ".(int) $params['use_geolocation'].",";
				if (isset($params['circleColor']))			$script[] = "\t\tcircleColor: '".$params['circleColor']."',";
				if (isset($params['markers']))				$script[] = "\t\tmarkers: ".json_encode($params['markers']).",";
				
				$script[] = "\t});";
				$script[] = "});";
			break;

			case 'openstreetmapbox':
			case 'openstreetthunderforest':
			case 'openstreetstamen':
			case 'openstreetesri':
				$attr		 = self::getAttributes($config);
				$tileType    = $attr['tileType'];
				$accessToken = $attr['accessToken'];
				$attribution = $attr['attribution'];
				$tileLayer   = $attr['tileLayer'];

				$script[] = "jQuery(function($) {";
				$script[] = "\tjQuery('#".$params['id']."').rsjoomlaomap({";
				
				if (isset($params['address']))				$script[] = "\t\taddress: '".$params['address']."',";
				if (isset($params['coordinates']))			$script[] = "\t\tcoordinates: '".$params['coordinates']."',";
				if (isset($params['locationCoordonates']))	$script[] = "\t\tlocationCoordonates: '".$params['locationCoordonates']."',";
				if (isset($params['pinpointBtn']))			$script[] = "\t\tpinpointBtn: '".$params['pinpointBtn']."',";
				if (isset($params['zoom']))					$script[] = "\t\tzoom: ".$params['zoom'].",";
				if (isset($params['markerDraggable']))		$script[] = "\t\tmarkerDraggable: ".$params['markerDraggable'].",";
				if (isset($params['resultsWrapperClass']))	$script[] = "\t\tresultsWrapperClass: '".$params['resultsWrapperClass']."',";
				if (isset($params['resultsClass']))			$script[] = "\t\tresultsClass: '".$params['resultsClass']."',";
				if (isset($params['radiusSearch']))			$script[] = "\t\tradiusSearch: '".$params['radiusSearch']."',";
				if (isset($params['radiusLocationId']))		$script[] = "\t\tradiusLocationId: '".$params['radiusLocationId']."',";
				if (isset($params['radiusValueId']))		$script[] = "\t\tradiusValueId: '".$params['radiusValueId']."',";
				if (isset($params['radiusUnitId']))			$script[] = "\t\tradiusUnitId: '".$params['radiusUnitId']."',";
				if (isset($params['radiusLoaderId']))		$script[] = "\t\tradiusLoaderId: '".$params['radiusLoaderId']."',";
				if (isset($params['radiusBtnId']))			$script[] = "\t\tradiusBtnId: '".$params['radiusBtnId']."',";
				if (isset($params['use_geolocation']))		$script[] = "\t\tuse_geolocation: ".(int) $params['use_geolocation'].",";
				if (isset($params['circleColor']))			$script[] = "\t\tcircleColor: '".$params['circleColor']."',";
				if (isset($params['markers']))				$script[] = "\t\tmarkers: ".json_encode($params['markers']).",";
				
				$script[] = "\t\ttileLayer: '".$tileLayer."',";
				$script[] = "\t\ttileType: '".self::legacy($tileType)."',";
				$script[] = "\t\tattribution: '".$attribution."',";
				$script[] = "\t\taccessToken: '".$accessToken."'";
				
				$script[] = "\t});";
				$script[] = "});";
			break;
		}

		// Add the script declaration.
		if (!empty($script)) {
			JFactory::getDocument()->addScriptDeclaration(implode("\n",$script));
		}
	}

	protected static function getAttributes($config) {
		$attributes = array(
			'openstreetmapbox'    => array(
				'tileType'        => $config->openstreet_mapbox_tile_type,
				'accessToken'     => $config->openstreet_mapbox_access_token,
				'tileLayer'       => 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
				'attribution'     => 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery  &copy; <a href="https://www.mapbox.com/">Mapbox</a>'
			),
			'openstreetthunderforest' => array(
				'tileType'        => '',
				'accessToken'     => $config->openstreet_thunderforest_api_key,
				'tileLayer'       => 'https://tile.thunderforest.com/'.$config->openstreet_thunderforest_tile_type.'/{z}/{x}/{y}.png?apikey={accessToken}',
				'attribution'     => 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, Maps &copy; <a href="http://www.thunderforest.com">Thunderforest</a>'
			),
			'openstreetstamen'            => array(
				'tileType'        => '',
				'accessToken'     => '',
				'tileLayer'       => 'https://stamen-tiles.a.ssl.fastly.net/'.$config->openstreet_stamen_tile_type.'/{z}/{x}/{y}.'.($config->openstreet_stamen_tile_type == 'toner' ? 'png' : 'jpg'),
				'attribution'     => '<a href="http://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> | Map tiles by <a href="http://stamen.com/">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://www.openstreetmap.org/copyright">ODbL</a>.'
			),
			'openstreetesri'            => array(
				'tileType'        => '',
				'accessToken'     => '',
				'tileLayer'       => 'https://server.arcgisonline.com/ArcGIS/rest/services/'.$config->openstreet_esri_tile_type.'/MapServer/tile/{z}/{y}/{x}',
				'attribution'     => 'Tiles &copy; Esri -- {tile_tile_attribution}. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://www.openstreetmap.org/copyright">ODbL</a>.'
			)
		);

		if ($config->map == 'openstreetesri') {
			switch($config->openstreet_esri_tile_type) {
				case 'World_Street_Map':
					$attributes['openstreetesri']['attribution'] = str_replace('{tile_tile_attribution}', 'Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012', $attributes['openstreetesri']['attribution']);
				break;
				case 'World_Topo_Map':
					$attributes['openstreetesri']['attribution'] = str_replace('{tile_tile_attribution}', 'Source: Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community', $attributes['openstreetesri']['attribution']);
				break;
				case 'World_Imagery':
					$attributes['openstreetesri']['attribution'] = str_replace('{tile_tile_attribution}', 'Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community', $attributes['openstreetesri']['attribution']);
				break;
			}
		}

		return (isset($attributes[$config->map]) ? $attributes[$config->map] : '');
	}

	protected static function loadDependencies($config) {
		static $loaded = array();

		if (!isset($loaded[$config->map])) {
			if ($config->map == 'google') {
				JHtml::script('https://maps.google.com/maps/api/js?libraries=geometry&language='.JFactory::getLanguage()->getTag().($config->google_map_api ? '&key='.$config->google_map_api : ''));
				JHtml::script('com_rseventspro/jquery.map.js', array('relative' => true, 'version' => 'auto'));
			} else {
				// Leaflet stylesheet
				JHtml::stylesheet('com_rseventspro/leaflet.css', array('relative' => true, 'version'=> 'auto'));
				
				// Leafelt
				JHtml::script('https://unpkg.com/leaflet@1.4.0/dist/leaflet.js', array());
				
				// Esri
				JHtml::script('https://unpkg.com/esri-leaflet@2.2.4/dist/esri-leaflet.js', array());
				JHtml::script('https://unpkg.com/esri-leaflet-geocoder@2.2.13/dist/esri-leaflet-geocoder.js', array());
				
				JHtml::script('com_rseventspro/jquery.map.os.js', array('relative' => true, 'version' => 'auto'));
			}
		}
	}
	
	protected static function legacy($type) {
		if ($type == 'mapbox.streets') {
			return 'mapbox/streets-v11';
		} elseif ($type == 'mapbox.dark') {
			return 'mapbox/dark-v10';
		} elseif ($type == 'mapbox.light') {
			return 'mapbox/light-v10';
		} elseif ($type == 'mapbox.outdoors') {
			return 'mapbox/outdoors-v11';
		} elseif ($type == 'mapbox.satellite') {
			return 'mapbox/satellite-streets-v11';
		}
		
		return $type;
	}
	
	public static function markers($locations) {
		$markers = array(); 
		
		if ($locations) {
			foreach ($locations as $location => $events) {
				if (empty($events)) continue;
				$event = $events[0];
				if (empty($event->coordinates) && empty($event->address)) continue;
				$single = count($events) > 1 ? false : true;
				
				$marker = array(
					'title' => $event->name,
					'position' => $event->coordinates,
					'address' => $event->address,
					'content' => rseventsproHelper::locationContent($event, $single)
				);
				
				if ($event->marker) { 
					$marker['icon'] = rseventsproHelper::showMarker($event->marker);
				}
				
				$markers[] = $marker;
			}
		}
		
		return $markers;
	}
}