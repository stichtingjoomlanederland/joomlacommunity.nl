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

class SocialLocationProvidersFoursquare extends SocialLocationProviders
{
    protected $queries = array(
        'll' => '',
        'query' => '',
        'client_id' => '',
        'client_secret' => '',
        'm' => 'foursquare',
        'radius' => 800,
        'v' => '20140905',
        'intent' => 'browse'
    );

    protected $url = 'https://api.foursquare.com/v2/venues/search';

    public function __construct()
    {
        // Initialise the client_id and client_secret
        $config = FD::config();
        $client_id = $config->get('location.foursquare.clientid');
        $client_secret = $config->get('location.foursquare.clientsecret');

        // If client_id and client_secret is empty, then we have to mark this as error

        if (empty($client_id)) {
            return $this->setError(JText::_('COM_EASYSOCIAL_LOCATION_PROVIDERS_FOURSQUARE_MISSING_CLIENT_ID'));
        }

        if (empty($client_secret)) {
            return $this->setError(JText::_('COM_EASYSOCIAL_LOCATION_PROVIDERS_FOURSQUARE_MISSING_CLIENT_SECRET'));
        }

        $this->setQuery('client_id', $client_id);
        $this->setQuery('client_secret', $client_secret);
    }

    /**
     * Allows caller to search by locations given the lat and lng
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function setCoordinates($lat, $lng)
    {
        return $this->setQuery('ll', $lat . ',' . $lng);
    }

    /**
     * Allows caller to search for places given a partial address or location
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function setSearch($search = '')
    {
        return $this->setQuery('query', $search);
    }

    public function getResult($queries = array())
    {
        $this->setQueries($queries);

        // If the latitude and longitude isn't set, we need to unset it here.
        if (!$this->queries['ll']) {
            unset($this->queries['ll']);
        }

        $url = $this->buildUrl();

        $connector = FD::connector();
        $connector->setMethod('GET');
        $connector->addUrl($url);

        if (!empty($this->queries['query'])) {
            $this->setQuery('intent', 'global');

            $url = $this->buildUrl();
            $connector->addUrl($url);
        }

        $connector->execute();
        $result = $connector->getResult($url);

        // Stores the list of available venues
        $venues = array();

        if (!$result) {
            return $venues;    
        }

        $result = json_decode($result);

        if (!isset($result->meta) || !isset($result->meta->code)) {
            $this->setError(JText::_('COM_EASYSOCIAL_LOCATION_PROVIDERS_FOURSQUARE_UNKNOWN_ERROR'));

            return $venues;
        }

        // If foursquare returns an error, we should log this down
        if ($result->meta->code != 200) {
            $this->setError($result->meta->errorDetail);

            return $venues;
        }

        // If there is no venues, skip this altogether.
        if (!$result->response->venues) {
            return $venues;
        }

        foreach ($result->response->venues as $item) {

            $venue = new SocialLocationData();
            $venue->latitude = $item->location->lat;
            $venue->longitude = $item->location->lng;
            $venue->address = isset($item->location->address) ? $item->location->address : '';
            $venue->name = $item->name;
            $venue->fulladdress = $venue->address ? $venue->name . ', ' . $venue->address : '';

            $venues[] = $venue;
        }

        return $venues;
    }
}
