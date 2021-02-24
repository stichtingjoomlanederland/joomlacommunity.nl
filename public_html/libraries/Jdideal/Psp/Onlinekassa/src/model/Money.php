<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignatureDataProvider;

class Money implements \JsonSerializable, SignatureDataProvider
{
    /** @var string */
    private $currency;
    /** @var int */
    private $amount;

    /**
     * Construct a Money object with the given currency and the amount in cents.
     *
     * @param string $currency
     * @param int|float $amount in cents
     *
     * @return Money
     */
    public static function fromCents($currency, $amount)
    {
        $money = new self();
        $money->setCurrency($currency);
        $money->setAmount(intval($amount));

        return $money;
    }

    /**
     * Construct a Money object with the given currency and the amount in decimals.
     *
     * @param string $currency
     * @param float  $amount
     *
     * @return Money
     */
    public static function fromDecimal($currency, $amount)
    {
        $roundedAmount = round($amount, 2);

        return self::fromCents($currency, $roundedAmount * 100);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getSignatureData()
    {
        return [$this->currency, $this->amount];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return ['currency' => $this->currency, 'amount' => $this->amount];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->amount / 100).' '.$this->currency;
    }
}
