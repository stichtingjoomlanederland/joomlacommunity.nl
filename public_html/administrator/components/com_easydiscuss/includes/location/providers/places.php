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

FD::import('admin:/includes/location/provider');

class SocialLocationProvidersPlaces extends SocialLocationProviders
{
    protected $queries = array(
        'location' => '',
        'radius' => 800,
        'key' => '',
        'query' => '',
        'keyword' => ''
    );

    public function __construct()
    {
        $key = FD::config()->get('location.places.api');

        if (empty($key)) {
            return $this->setError(JText::_('COM_EASYSOCIAL_LOCATION_PROVIDERS_PLACES_MISSING_APIKEY'));
        }

        $this->setQuery('key', $key);
    }

    public function setCoordinates($lat, $lng)
    {
        return $this->setQuery('location', $lat . ',' . $lng);
    }

    public function setSearch($search = '')
    {
        $this->setQuery('keyword', $search);
        $this->setQuery('query', $search);

        return $this;
    }

    public function getResult($queries = array())
    {
        $this->setQueries($queries);

        // There is 2 parts to this
        // nearbysearch
        // textsearch

        $nearbysearchUrl = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
        $textsearchUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

        $nearbysearchOptions = array(
            'location' => $this->queries['location'],
            'radius' => $this->queries['radius'],
            'key' => $this->queries['key'],
            'keyword' => $this->queries['keyword']
        );

        $textsearchOptions = array(
            'query' => $this->queries['query'],
            'key' => $this->queries['key']
        );

        $connector = FD::connector();
        $connector->setMethod('GET');
        $connector->addUrl($nearbysearchUrl . '?' . http_build_query($nearbysearchOptions));
        if (!empty($this->queries['query'])) {
            $connector->addUrl($textsearchUrl . '?' . http_build_query($textsearchOptions));
        }
        $connector->execute();

        $results = $connector->getResults();

        $venues = array();

        foreach ($results as $result) {
            $obj = json_decode($result->contents);

            foreach ($obj->results as $row) {
                $obj = new SocialLocationData;
                $obj->latitude = $row->geometry->location->lat;
                $obj->longitude = $row->geometry->location->lng;
                $obj->name = $row->name;
                $obj->address = isset($row->formatted_address) ? $row->formatted_address : '';
                $obj->fulladdress = !empty($obj->address) ? $obj->name . ', ' . $obj->address : '';

                $venues[$row->id] = $obj;
            }
        }

        $venues = array_values($venues);

        return $venues;
    }
}
