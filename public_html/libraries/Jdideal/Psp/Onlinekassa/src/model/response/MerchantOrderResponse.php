<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

/**
 * Once an order is announced, an instance of this object will be returned.
 * You can use this object to retrieve the redirect URL to which the customer should be redirected.
 */
class MerchantOrderResponse extends Response
{
    /** @var string */
    private $redirectUrl;

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [$this->redirectUrl];
    }
}
