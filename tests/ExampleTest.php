<?php

namespace Inertia\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTester;

class ExampleTest extends CIUnitTestCase
{
    use ControllerTester;

    public function testInertiaResponse()
    {
        $result = $this->withUri('http://example.com')
            ->controller(\Inertia\Controllers\TestController::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
    }
}
