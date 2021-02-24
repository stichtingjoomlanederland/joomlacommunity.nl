<?php

namespace Sisow;

use Sisow\Exceptions\SisowException;
use Sisow\Helpers\XmlHelper;
use Sisow\Requests\Merchants;
use Sisow\Requests\Directory;
use Sisow\Requests\Invoices;
use Sisow\Requests\Ping;
use Sisow\Requests\Transactions;

class SisowClient
{
    private $merchantId;
    private $merchantKey;
    private $shopId;

    private $apiUrl = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/';
    /**
     * @var Merchants
     */
    public $merchants;
    /**
     * @var Directory
     */
    public $issuers;
    /**
     * @var Transactions
     */
    public $transactions;
    /**
     * @var Invoices
     */
    public $invoices;
    /**
     * @var Ping
     */
    public $ping;

    function __construct() {
        $this->merchants = new Merchants($this);
        $this->issuers = new Directory($this);
        $this->transactions = new Transactions($this);
        $this->invoices = new Invoices($this);
        $this->ping = new Ping($this);
    }

    function setApiKey($merchantId, $merchantKey, $shopId = ''){
        $this->merchantId = $merchantId;
        $this->merchantKey = $merchantKey;
        $this->shopId = $shopId;
    }

    /**
     * @param $endpoint
     * @param $data
     * @return XmlHelper
     * @throws SisowException
     */
    function apiRequest($endpoint, $data){
        // array to string
        $postData = !is_array($data) || count($data) == 0 ? '' : http_build_query($data);

        // init curl
        $ch = curl_init();

        // set options
        curl_setopt($ch, CURLOPT_URL,$this->apiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $sisowResponse = curl_exec($ch);

        curl_close ($ch);

        // do we have an response?
        if(empty($sisowResponse)){
            throw new SisowException('Sisow Exception - no response', 3401); // empty response
        } else if(!simplexml_load_string($sisowResponse)) {
            throw new SisowException('Sisow Exception - no valid XML', 3402); // no proper XML
        } else {
            $xml = new XmlHelper($sisowResponse);
            $errorCode = $xml->getValue('errorcode');
            $errorMessage = $xml->getValue('errormessage');

            // validate if we have an error
            if(!empty($errorCode) || !empty($errorMessage)){
                throw new SisowException($errorMessage, substr($errorCode, 2));
            }

            // return XmlHelper
            return $xml;
        }
    }

    function getMerchantId(){
        return $this->merchantId;
    }

    function getMerchantKey(){
        return $this->merchantKey;
    }

    function getShopId(){
        return $this->shopId;
    }
}