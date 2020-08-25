<?php


namespace Sisow\Requests;


use Sisow\Exceptions\SisowException;

class Ping extends AbstractRequest
{
    /**
     * @return bool
     * @throws SisowException
     */
    public function get(){
        $xmlResponse = $this->execute('PingRequest', null);

        return !empty($xmlResponse->getValue('timestamp'));
    }
}