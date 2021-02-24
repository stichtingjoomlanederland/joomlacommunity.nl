<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class houses the different types of payment brands that can be included in the MerchantOrder to restrict
 * the payment brands that the consumer can choose from.
 */
class PaymentBrand
{
    const IDEAL = 'IDEAL';
    const AFTERPAY = 'AFTERPAY';
    const PAYPAL = 'PAYPAL';
    const MASTERCARD = 'MASTERCARD';
    const VISA = 'VISA';
    const BANCONTACT = 'BANCONTACT';
    const MAESTRO = 'MAESTRO';
    const V_PAY = 'V_PAY';

    /**
     * The CARDS type comprises MASTERCARD, VISA, BANCONTACT, MAESTRO and V_PAY.
     */
    const CARDS = 'CARDS';
}
