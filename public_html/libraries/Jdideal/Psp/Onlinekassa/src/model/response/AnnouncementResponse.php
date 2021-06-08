<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint\Endpoint;

/**
 * With an instance of this class you can retrieve the order statuses at the Rabobank OmniKassa.
 *
 * @see Endpoint::retrieveAnnouncement() To retrieve the order statuses.
 */
class AnnouncementResponse extends Response
{
    /** @var int */
    private $poiId;
    /** @var string */
    private $authentication;
    /** @var string */
    private $expiry;
    /** @var string */
    private $eventName;

    /**
     * @return int
     */
    public function getPoiId()
    {
        return $this->poiId;
    }

    /**
     * @param int $poiId
     */
    public function setPoiId($poiId)
    {
        $this->poiId = $poiId;
    }

    /**
     * @return string
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param string $authentication
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return string
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * @param string $expiry
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [$this->authentication, $this->expiry, $this->eventName, $this->poiId];
    }
}
