<?php

namespace Tests\Sisow\Requests;

use PHPUnit\Framework\TestCase;
use Tests\Sisow\Helpers\SisowClientTestHelper;

class PingTest extends TestCase
{

    public function testGet()
    {
        $sisow = SisowClientTestHelper::getClient();
        $result = $sisow->ping->get();

        $this->assertTrue($result);
    }
}
