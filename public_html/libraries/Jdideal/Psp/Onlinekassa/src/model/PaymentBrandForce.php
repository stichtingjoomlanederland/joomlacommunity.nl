<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class provides constants for the paymentBrandForce field of a MerchantOrder. This field is used in
 * combination with the paymentBrand field.
 * When paymentBrandForce is set to FORCE_ONCE then the supplied paymentBrand is only enforced in the customer's first
 * payment attempt. If the payment was not successful then the consumer is allowed to select an alternative
 * payment brand in the Hosted Payment Pages.
 * When paymentBrandForce is set to FORCE_ALWAYS then the consumer is not allowed to select an alternative
 * payment brand, the customer is restricted to use the provided paymentBrand for all payment attempts.
 */
class PaymentBrandForce
{
    const FORCE_ONCE = 'FORCE_ONCE';
    const FORCE_ALWAYS = 'FORCE_ALWAYS';
}
