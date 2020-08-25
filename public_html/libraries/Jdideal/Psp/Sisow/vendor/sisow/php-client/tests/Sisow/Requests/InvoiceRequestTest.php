<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Invoice;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;
use Tests\Sisow\Helpers\SisowHelper;

class InvoiceRequestTest extends TestCase
{

    public function testCreate()
    {
        $sisow = SisowClientTestHelper::getClient();

        $response = $sisow->transactions->create(SisowHelper::transactionParams());

        $this->assertTrue($response instanceof Transaction);

        $responseInvoice = $sisow->invoices->create($response->transactionId);

        $this->assertTrue($responseInvoice instanceof Invoice);
    }
}
