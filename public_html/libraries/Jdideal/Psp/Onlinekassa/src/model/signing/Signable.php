<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

/**
 * This class is responsible for providing sub classes with signature related functionality like calculating a signature, setting the calculated signature and retrieving the signature related data.
 */
abstract class Signable implements SignatureDataProvider
{
    const HASH_ALGORITHM = 'sha512';

    /** @var string */
    private $signature;

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * Calculate the signature with the given data and signing key.
     *
     * @param SigningKey $signingKey
     *
     * @return string hex string representation of the calculated signature
     */
    protected function calculateSignature(SigningKey $signingKey)
    {
        $signatureData = $this->getSignatureData();
        $preparedSignatureData = $this->flattenAndJoin($signatureData);

        return hash_hmac(static::HASH_ALGORITHM, $preparedSignatureData, $signingKey->getSigningData());
    }

    private function flattenAndJoin($input)
    {
        $flattenedData = $this->flatten($input);

        return $this->join($flattenedData);
    }

    private function flatten($input)
    {
        return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($input)), false);
    }

    private function join($input)
    {
        return implode(',', $input);
    }
}
