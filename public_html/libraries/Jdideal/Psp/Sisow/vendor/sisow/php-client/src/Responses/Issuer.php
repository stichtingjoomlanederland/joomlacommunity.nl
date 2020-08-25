<?php


namespace Sisow\Responses;


class Issuer
{
    public $id;
    public $name;

    public function __construct($issuerId, $issuerName)
    {
        $this->id = $issuerId;
        $this->name = $issuerName;
    }
}