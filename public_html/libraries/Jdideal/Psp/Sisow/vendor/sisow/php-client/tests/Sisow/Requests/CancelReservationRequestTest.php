<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Sisow\Responses\Transaction;
use Tests\Sisow\Helpers\SisowClientTestHelper;
use Tests\Sisow\Helpers\SisowHelper;

class CancelReservationRequestTest extends TestCase
{

    public function testCreate()
    {
        $sisow = SisowClientTestHelper::getClient();

        // create transaction
        $response = $sisow->transactions->create(SisowHelper::transactionParams());

        // valid response?
        $this->assertTrue($response instanceof Transaction);
        $this->assertEquals('Reservation', $response->status);

        // cancel transaction
        $responseInvoice = $sisow->transactions->cancel($response->transactionId);

        // validate response
        $this->assertTrue($responseInvoice instanceof Transaction);
        $this->assertEquals('Cancelled', $responseInvoice->status);
    }
}
