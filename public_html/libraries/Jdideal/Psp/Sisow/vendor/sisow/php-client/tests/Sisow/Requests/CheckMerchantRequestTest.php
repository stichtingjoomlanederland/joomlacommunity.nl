<?php

namespace Test\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Merchant;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class CheckMerchantRequestTest extends TestCase
{

    public function testGet()
    {
        $sisow = SisowClientTestHelper::getClient();
        $response = $sisow->merchants->get();
        $this->assertTrue($response instanceof Merchant);
    }
}
