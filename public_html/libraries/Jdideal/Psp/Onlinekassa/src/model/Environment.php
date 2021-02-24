<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class contains the ROFE API urls per environment.
 */
abstract class Environment
{
    const PRODUCTION = 'https://betalen.rabobank.nl/omnikassa-api/';
    const SANDBOX = 'https://betalen.rabobank.nl/omnikassa-api-sandbox/';
}
