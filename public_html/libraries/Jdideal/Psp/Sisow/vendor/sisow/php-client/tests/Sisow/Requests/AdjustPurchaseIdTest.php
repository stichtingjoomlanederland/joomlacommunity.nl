<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class AdjustPurchaseIdTest extends TestCase
{

    public function testEdit()
    {
        $transactionId = '';
        $oldPurchaseId = '';
        $newPurchaseId = '';

        $sisow = SisowClientTestHelper::getClient();
        $result = $sisow->transactions->edit($transactionId, $oldPurchaseId, $newPurchaseId);

        $this->assertTrue($result instanceof Transaction);
        $this->assertEquals('testnew', $result->purchaseId);
    }
}
