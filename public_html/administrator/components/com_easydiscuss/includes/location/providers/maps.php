<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussLocationMaps
{
	protected $queries = array(
								'latlng' => '',
								'address' => '',
								'key' => ''
						);

	public $url = 'https://maps.googleapis.com/maps/api/geocode/json';

	public function setCoordinates($lat, $lng)
	{
		return $this->setQuery('latlng', $lat . ',' . $lng);
	}

	public function setSearch($search = '')
	{
		return $this->setQuery('address', $search);
	}

	public function geocode($address)
	{
		$address = urlencode($address);

		$config = ED::config();
		$key = $config->get('main_location_gmaps_key');

		// form the url
		$url = $this->url . '?key=' . $key;
		if ($config->get("main_location_language")) {
			$url .= '&language=' . $config->get("main_location_language");
		}
		$url .= '&address=' . $address;

		$connector = ED::connector();
		$connector->setMethod('GET');
		$connector->addUrl($url);
		$connector->execute();

		$result = $connector->getResult();

		$result = json_decode($result);

		$venues = array();

		foreach ($result->results as $row) {
			$obj = new stdClass();
			$obj->latitude = $row->geometry->location->lat;
			$obj->longitude = $row->geometry->location->lng;
			$obj->name = $row->address_components[0]->long_name;
			$obj->address = $row->formatted_address;
			$obj->formatted_address = $obj->address;
			$obj->fulladdress = $row->formatted_address;
			$obj->reloadmap = true;

			$venues[] = $obj;
		}

		return $venues;
	}
}
