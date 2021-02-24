<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing;

/**
 * This interface describes what each signature data provider must provide to be able to calculate the signature.
 */
interface SignatureDataProvider
{
    /**
     * @return array
     */
    public function getSignatureData();
}
