<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class RefundRequestTest extends TestCase
{
    public function testRefund(){
        $trxId = '';

        $sisow = SisowClientTestHelper::getClient();
        $result = $sisow->transactions->refund($trxId, 10);

        $this->assertFalse(empty($result));
    }
}
