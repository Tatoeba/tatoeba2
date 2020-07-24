<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ErrorComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;

class ErrorComponentTest extends TestCase
{
    private $Error;

    private function buildTestErrorComponent() {
        $request = new ServerRequest([
            'environment' => [
                'REMOTE_ADDR' => '93.184.216.340',
                'HTTP_REFERER' => 'https://example.com/to/tatoeba',
            ],
            'url' => '/here/and/there',
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        return new ErrorComponent($registry);
    }

    public function setUp()
    {
        parent::setUp();
        $this->Error = $this->buildTestErrorComponent();
    }

    public function tearDown()
    {
        unset($this->Error);
        parent::tearDown();
    }

    public function testFormat()
    {
        $expectedLog = <<<END_OF_LOG
ouch!
Request URL: /here/and/there
Referer URL: https://example.com/to/tatoeba
Client IP: 93.184.216.340

END_OF_LOG;

        $result = $this->Error->format('ouch!');

        $this->assertEquals($expectedLog, $result);
    }
}
