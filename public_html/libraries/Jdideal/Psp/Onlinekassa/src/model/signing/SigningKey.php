<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

/**
 * This class exists to prevent the signing key data to be printed or logged.
 */
class SigningKey
{
    /** @var string */
    private $signingData;

    /**
     * @param string $signingData
     */
    public function __construct($signingData)
    {
        $this->signingData = $signingData;
    }

    /**
     * @return string
     */
    public function getSigningData()
    {
        return $this->signingData;
    }
}
