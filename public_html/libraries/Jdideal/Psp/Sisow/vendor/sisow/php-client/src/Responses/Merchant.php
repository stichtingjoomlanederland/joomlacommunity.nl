<?php


namespace Sisow\Responses;


class Merchant
{
    /**
     * Merchant is active
     * @bool
     */
    public $active;

    /**
     * Merchant ID
     * @string
     */
    public $merchantId;

    /**
     * Available payment methods
     * @array
     */
    public $payments;
}