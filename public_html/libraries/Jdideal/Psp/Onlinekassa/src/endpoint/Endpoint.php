<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\ApiConnector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\Connector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

/**
 * This class exposes the functionality available in the SDK.
 *
 * @api
 */
class Endpoint
{
    /** @var Connector */
    private $connector;
    /** @var SigningKey */
    private $signingKey;

    /**
     * @param Connector  $connector
     * @param SigningKey $signingKey
     *
     * @internal
     */
    protected function __construct(Connector $connector, SigningKey $signingKey)
    {
        $this->signingKey = $signingKey;
        $this->connector = $connector;
    }

    /**
     * Create an instance of the endpoint to connect to the Rabobank OmniKassa.
     *
     * @param string        $baseURL       the base URL pointing towards the Rabobank OmniKassa environment
     * @param SigningKey    $signingKey    the secret key used to sign messages
     * @param TokenProvider $tokenProvider used to store and retrieve token related information to/from
     *
     * @return Endpoint
     */
    public static function createInstance($baseURL, SigningKey $signingKey, TokenProvider $tokenProvider)
    {
        return new Endpoint(ApiConnector::withGuzzle($baseURL, $tokenProvider), $signingKey);
    }

    /**
     * Announce an order.
     *
     * @param MerchantOrder $merchantOrder
     *
     * @return string an URL the customer shall be redirected to
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public function announceMerchantOrder(MerchantOrder $merchantOrder)
    {
        $request = new MerchantOrderRequest($merchantOrder, $this->signingKey);

        $responseAsJson = $this->connector->announceMerchantOrder($request);

        $response = new MerchantOrderResponse($responseAsJson, $this->signingKey);

        return $response->getRedirectUrl();
    }

    /**
     * Retrieve the merchant order status from the given announcement.
     *
     * @param AnnouncementResponse $announcementResponse
     *
     * @return MerchantOrderStatusResponse
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public function retrieveAnnouncement(AnnouncementResponse $announcementResponse)
    {
        $announcementDataAsJson = $this->connector->getAnnouncementData($announcementResponse);

        // When we get different types of announcements, make the switch to handle response message differently.

        return new MerchantOrderStatusResponse($announcementDataAsJson, $this->signingKey);
    }
}
