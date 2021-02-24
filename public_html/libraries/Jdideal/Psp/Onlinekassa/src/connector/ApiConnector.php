<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\GuzzleRESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\RESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\AccessToken;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;

/**
 * The Connector implementation. It is responsible for separating the rest interface from the endpoint of the SDK.
 */
class ApiConnector implements Connector
{
    /** @var RESTTemplate */
    private $restTemplate;
    /** @var TokenProvider */
    private $tokenProvider;
    /** @var AccessToken */
    private $accessToken;

    /**
     * @param RESTTemplate  $restTemplate
     * @param TokenProvider $tokenProvider
     *
     * @internal
     */
    protected function __construct(RESTTemplate $restTemplate, TokenProvider $tokenProvider)
    {
        $this->restTemplate = $restTemplate;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * Construct a Guzzle based ApiConnector.
     *
     * @param string        $baseURL
     * @param TokenProvider $tokenProvider
     *
     * @return ApiConnector
     */
    public static function withGuzzle($baseURL, TokenProvider $tokenProvider)
    {
        $curlTemplate = new GuzzleRESTTemplate($baseURL);

        return new ApiConnector($curlTemplate, $tokenProvider);
    }

    /**
     * Announce an order.
     *
     * @param MerchantOrderRequest $order
     *
     * @return string json response body
     */
    public function announceMerchantOrder(MerchantOrderRequest $order)
    {
        return $this->performAction(function () use (&$order) {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->post('order/server/api/order', $order);
        });
    }

    /**
     * Retrieve the order details from an announcement.
     *
     * @param AnnouncementResponse $announcement
     *
     * @return string json response body
     */
    public function getAnnouncementData(AnnouncementResponse $announcement)
    {
        return $this->performAction(function () use (&$announcement) {
            $this->restTemplate->setToken($announcement->getAuthentication());

            return $this->restTemplate->get('order/server/api/events/results/'.$announcement->getEventName());
        });
    }

    /**
     * Perform a Rabobank OmniKassa related rest action.
     * This first checks the access token and retrieves one if it is invalid, expired or non existing.
     * Then it executes the action.
     *
     * @param callable $action
     *
     * @return mixed result of the action
     */
    private function performAction($action)
    {
        $this->validateToken();

        return $action();
    }

    private function validateToken()
    {
        try {
            if (null === $this->accessToken) {
                $this->accessToken = $this->tokenProvider->getAccessToken();
            }

            if (null === $this->accessToken || $this->isExpired($this->accessToken)) {
                $this->updateToken();
            }
        } catch (\Exception $invalidAccessTokenException) {
            $this->updateToken();
        }
    }

    /**
     * @param AccessToken $token
     *
     * @return bool
     */
    private function isExpired(AccessToken $token)
    {
        $validUntil = $token->getValidUntil();
        $currentDate = new \DateTime('now', new \DateTimeZone('UTC'));
        //Difference in seconds
        $difference = $validUntil->getTimestamp() - $currentDate->getTimestamp();

        return ($difference / $token->getDurationInSeconds()) < 0.05;
    }

    private function updateToken()
    {
        $this->accessToken = $this->retrieveNewToken();
        $this->tokenProvider->setAccessToken($this->accessToken);
    }

    /**
     * @return AccessToken
     */
    private function retrieveNewToken()
    {
        $refreshToken = $this->tokenProvider->getRefreshToken();

        $this->restTemplate->setToken($refreshToken);
        $accessTokenJson = $this->restTemplate->get('gatekeeper/refresh');

        return AccessToken::fromJson($accessTokenJson);
    }
}
