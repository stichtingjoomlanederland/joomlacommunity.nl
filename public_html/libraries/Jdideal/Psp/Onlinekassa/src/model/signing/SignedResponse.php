<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

/**
 * This class is responsible for validating the signatures.
 */
abstract class SignedResponse extends Signable
{
    /**
     * Validate the signature of this response.
     *
     * @param SigningKey $signingKey the merchant signing key
     *
     * @throws InvalidSignatureException An exception is thrown in case the signature validation fails
     */
    protected function validateSignature(SigningKey $signingKey)
    {
        $responseSignature = $this->getSignature();
        $expectedSignature = $this->calculateSignature($signingKey);

        if (!hash_equals($expectedSignature, $responseSignature)) {
            throw new InvalidSignatureException();
        }
    }
}
