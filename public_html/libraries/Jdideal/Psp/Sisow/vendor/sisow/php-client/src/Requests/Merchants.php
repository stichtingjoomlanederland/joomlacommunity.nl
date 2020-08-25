<?php


namespace Sisow\Requests;


use Sisow\Exceptions\SisowException;
use Sisow\Responses\Merchant;

class Merchants extends AbstractRequest
{
    /**
     * Get Merchant Information
     *
     * @return Merchant
     * @throws SisowException
     */
    public function get(){
        $response = $this->execute('CheckMerchantRequest', [
            'merchantid' => $this->getMerchantId(),
            'sha1' => sha1($this->getMerchantId() . $this->getMerchantKey())
        ]);

        // validate signature
        if($response->getValue('sha1') != sha1($this->getMerchantId() . $this->getMerchantKey())){
            throw new SisowException('Invalid response!');
        }

        // return merchant information
        $checkMerchantResponse = new Merchant();
        $checkMerchantResponse->active = $response->getValue('active') == 'true';
        $checkMerchantResponse->merchantId = $response->getValue('merchantid');
        $checkMerchantResponse->payments = $response->getArray('payments', 'payment');
        return $checkMerchantResponse;
    }
}