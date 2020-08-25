<?php


namespace Sisow\Requests;


use Sisow\Exceptions\SisowException;
use Sisow\Helpers\XmlHelper;
use Sisow\SisowClient;

abstract class AbstractRequest
{
    private $client;

    /**
     * AbstractRequest constructor.
     * @param SisowClient $client
     */
    function __construct(SisowClient $client){
        $this->client = $client;
    }

    /**
     * @param $endpoint
     * @param $data
     * @return XmlHelper
     * @throws SisowException
     */
    protected function execute($endpoint, $data){
        return $this->client->apiRequest($endpoint, $data);
    }

    /**
     * @return mixed
     */
    protected function getMerchantId(){
        return $this->client->getMerchantId();
    }

    /**
     * @return mixed
     */
    protected function getMerchantKey(){
        return $this->client->getMerchantKey();
    }

    /**
     * @return mixed
     */
    protected function getShopId(){
        return $this->client->getShopId();
    }
}