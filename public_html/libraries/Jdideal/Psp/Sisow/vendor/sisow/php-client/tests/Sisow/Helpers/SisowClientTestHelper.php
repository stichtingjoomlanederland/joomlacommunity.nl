<?php


namespace Tests\Sisow\Helpers;


use Sisow\SisowClient;

class SisowClientTestHelper
{
    static function getClient(){
        $sisow = new SisowClient();
        $sisow->setApiKey('', '');
        return $sisow;
    }
}