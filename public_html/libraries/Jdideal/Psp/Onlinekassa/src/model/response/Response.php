<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonMapper;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignedResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

/**
 * This class is used to easily construct sub classes from json.
 * Classes that extend this class are considered responses from the Rabobank OmniKassa.
 */
abstract class Response extends SignedResponse
{
    /**
     * Construct this response from the given json.
     * Also validates the signature with the given signing key.
     *
     * @param string     $json
     * @param SigningKey $signingKey
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public function __construct($json, SigningKey $signingKey)
    {
        if (empty($json)) {
            return;
        }
        $mapper = new JsonMapper();
        $mapper->map(json_decode($json), $this);

        $this->validateSignature($signingKey);
    }
}
