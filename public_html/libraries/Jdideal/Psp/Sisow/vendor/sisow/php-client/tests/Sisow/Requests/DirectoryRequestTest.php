<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class DirectoryRequestTest extends TestCase
{

    public function testGet()
    {
        $sisow = SisowClientTestHelper::getClient();
        $issuers = $sisow->issuers->get(false);
        $this->assertTrue(count($issuers) > 1);

        $issuers = $sisow->issuers->get(true);
        $this->assertCount(1, $issuers);
    }
}
