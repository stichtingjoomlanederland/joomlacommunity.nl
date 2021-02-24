<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class houses the different types of VAT categories that can be assigned to a given order item.
 */
class VatCategory
{
    const HIGH = '1';
    const LOW = '2';
    const ZERO = '3';
    const NONE = '4';
}
