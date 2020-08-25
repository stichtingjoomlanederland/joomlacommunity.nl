<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class StatusRequestTest extends TestCase
{

    public function testGet()
    {
        $data = [
            'payment' => 'mistercash',
            'purchaseid' => 'test',
            'entrancecode' => 'ec',
            'amount' => '50',
            'description' => 'test description',
            'returnurl' => 'https://sisow.nl'
        ];

        $sisow = SisowClientTestHelper::getClient();
        $result = $sisow->transactions->create($data);

        $this->assertTrue($result instanceof Transaction);

        $statusResult = $sisow->transactions->get($result->transactionId);
        $this->assertTrue($statusResult instanceof Transaction);
        $this->assertEquals('Open', $statusResult->status);
    }
}
