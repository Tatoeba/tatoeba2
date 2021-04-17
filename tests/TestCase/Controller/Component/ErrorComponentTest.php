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
    private $controller;

    public function setUp()
    {
        parent::setUp();
        $request = new ServerRequest([
            'environment' => [
                'REMOTE_ADDR' => '93.184.216.340',
                'HTTP_REFERER' => 'https://example.com/to/tatoeba',
            ],
            'url' => '/here/and/there',
        ]);

        $this->controller = $this->getMockBuilder(Controller::class)
            ->setConstructorArgs([$request])
            ->setMethods(['log'])
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->Error = $this->getMockBuilder(ErrorComponent::class)
            ->setConstructorArgs([$registry])
            ->setMethods(['generateNewCode'])
            ->getMock();
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

    public function testTraceErrorWithoutCode_returnsNewCode()
    {
        $code = '2a001c1de4a97';
        $this->Error
            ->expects($this->any())
            ->method('generateNewCode')
            ->will($this->returnValue($code));

        $returned = $this->Error->traceError('ouch!');

        $this->assertEquals($code, $returned);
    }

    public function testTraceErrorWithCode_returnsPassedCode()
    {
        $code = '3852764736a9b';
        $returned = $this->Error->traceError('ouch!', $code);
        $this->assertEquals($code, $returned);
    }

    public function testTraceError_logsWithErrorCode()
    {
        $code = '4c8e2f91e946c';
        $this->Error
            ->expects($this->any())
            ->method('generateNewCode')
            ->will($this->returnValue($code));
        $this->controller
            ->expects($this->once())
            ->method('log')
            ->with($this->stringContains($code));

        $this->Error->traceError('ouch!');
    }
}
