<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

class InvalidSignatureException extends \ErrorException
{
    public function __construct()
    {
        parent::__construct('The signature validation of the response failed. Please contact the Rabobank service team.');
    }
}
