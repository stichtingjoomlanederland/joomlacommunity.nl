<?php


namespace Sisow\Requests;


use Sisow\Exceptions\SisowException;
use Sisow\Responses\Issuer;

class Directory extends AbstractRequest
{
    /**
     * Get issuers
     *
     * @param $test
     * @return Issuer[]
     * @throws SisowException
     */
    public function get($test = false){
        // do request to Sisow
        $xmlResponse = $this->execute('DirectoryRequest' . ($test ? '?test=true' : ''), null);

        // get simple array
        $issuerArray = $xmlResponse->getKeyValueArray('directory', 'issuer', 'issuerid', 'issuername');

        // generate issuer[] response
        $response = [];

        // loop found issuers and convert them to Issuer object
        if(is_array($issuerArray) && count($issuerArray) > 0){
            foreach($issuerArray as $issuerId => $issuerName){
                $response[] = new Issuer($issuerId, $issuerName);
            }
        }

        // return Issuer[]
        return $response;
    }
}