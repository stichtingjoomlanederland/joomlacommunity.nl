<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

/**
 * This class is responsible for calculating and setting the signature for the request it represents.
 */
abstract class SignedRequest extends Signable
{
    /**
     * Calculates the signature and sets it for this request.
     *
     * @param SigningKey $signingKey
     */
    public function calculateAndSetSignature(SigningKey $signingKey)
    {
        $this->setSignature($this->calculateSignature($signingKey));
    }
}
