<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class TransactionRequestTest extends TestCase
{

    public function testCreate()
    {
        $data = [
            'payment' => 'mistercash',
            'purchaseid' => 'test',
            //'entrancecode' => 'ec',
            'amount' => '50',
            'description' => 'test description',
            'returnurl' => 'https://sisow.nl'
        ];

        $sisow = SisowClientTestHelper::getClient();
        $result = $sisow->transactions->create($data);

        $this->assertTrue($result instanceof Transaction);
        $this->assertEquals('Open', $result->status);
        $this->assertFalse(empty($result->issuerUrl));
    }
}
