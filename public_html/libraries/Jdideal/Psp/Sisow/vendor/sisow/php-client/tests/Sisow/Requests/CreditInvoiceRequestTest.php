<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Invoice;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;
use Tests\Sisow\Helpers\SisowHelper;

class CreditInvoiceRequestTest extends TestCase
{

    public function testCredit()
    {
        $sisow = SisowClientTestHelper::getClient();

        $response = $sisow->transactions->create(SisowHelper::transactionParams());

        $this->assertTrue($response instanceof Transaction);

        $responseInvoice = $sisow->invoices->create($response->transactionId);

        $this->assertTrue($responseInvoice instanceof Invoice);

        $creditInvoice = $sisow->invoices->credit($response->transactionId);

        $this->assertTrue($creditInvoice instanceof Invoice);
    }
}
