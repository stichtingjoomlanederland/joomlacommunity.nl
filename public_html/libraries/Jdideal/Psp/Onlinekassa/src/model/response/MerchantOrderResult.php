<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignatureDataProvider;

/**
 * This object contains information, like status and paid amount, of an order.
 */
class MerchantOrderResult implements SignatureDataProvider
{
    /** @var int */
    private $poiId;
    /** @var string */
    private $merchantOrderId;
    /** @var string */
    private $omnikassaOrderId;
    /** @var string */
    private $orderStatus;
    /** @var string */
    private $orderStatusDateTime;
    /** @var string */
    private $errorCode;
    /** @var Money */
    private $paidAmount;
    /** @var Money */
    private $totalAmount;

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
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @param string $merchantOrderId
     */
    public function setMerchantOrderId($merchantOrderId)
    {
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @return string
     */
    public function getOmnikassaOrderId()
    {
        return $this->omnikassaOrderId;
    }

    /**
     * @param string $omnikassaOrderId
     */
    public function setOmnikassaOrderId($omnikassaOrderId)
    {
        $this->omnikassaOrderId = $omnikassaOrderId;
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param string $orderStatus
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return string
     */
    public function getOrderStatusDateTime()
    {
        return $this->orderStatusDateTime;
    }

    /**
     * @param string $orderStatusDateTime
     */
    public function setOrderStatusDateTime($orderStatusDateTime)
    {
        $this->orderStatusDateTime = $orderStatusDateTime;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return Money
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param Money $paidAmount
     */
    public function setPaidAmount(Money $paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return Money
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param Money $totalAmount
     */
    public function setTotalAmount(Money $totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [
            $this->merchantOrderId,
            $this->omnikassaOrderId,
            $this->poiId,
            $this->orderStatus,
            $this->orderStatusDateTime,
            $this->errorCode,
            $this->paidAmount->getSignatureData(),
            $this->totalAmount->getSignatureData(),
        ];
    }
}
