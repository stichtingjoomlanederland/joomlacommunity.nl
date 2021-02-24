<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignedResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class PaymentCompletedResponse extends SignedResponse
{
    /** @var string */
    protected $orderID;
    /** @var string */
    protected $status;

    /**
     * @param string $orderID
     * @param string $status
     * @param string $signature
     */
    private function __construct($orderID, $status, $signature)
    {
        $this->orderID = $orderID;
        $this->status = $status;
        $this->setSignature($signature);
    }

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Creates a new PaymentCompletedResponse instance.
     * It sanitizes the input and validates the signature resulting in a valid instance or the value FALSE.
     *
     * @param string     $orderID
     * @param string     $status
     * @param string     $signature
     * @param SigningKey $signingKey
     *
     * @return bool|PaymentCompletedResponse returns FALSE if the signature is invalid, otherwise the instance is returned
     */
    public static function createInstance($orderID, $status, $signature, SigningKey $signingKey)
    {
        //Sanitize input
        $orderID = preg_replace('/[^0-9A-Za-z]/', '', $orderID);
        $status = preg_replace('/[^A-Z_]/', '', $status);
        $signature = preg_replace('/[^0-9a-f]/', '', $signature);

        $instance = new PaymentCompletedResponse($orderID, $status, $signature);
        try {
            $instance->validateSignature($signingKey);
        } catch (InvalidSignatureException $invalidSignatureException) {
            return false;
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [$this->orderID, $this->status];
    }
}
