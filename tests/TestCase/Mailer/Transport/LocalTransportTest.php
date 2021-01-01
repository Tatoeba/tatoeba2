<?php
namespace App\Test\TestCase\Mailer\Transport;

use App\Mailer\Transport\LocalTransport;
use App\Test\TestCase\TestLog;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Network\Exception\SocketException;
use Cake\TestSuite\TestCase;

class LocalTransportTest extends TestCase {

    public function setUp() {
        parent::setUp();
        $this->LocalTransport = $this->getMockBuilder(LocalTransport::class)
            ->setMethods(['_parent'])
            ->getMock();

        $this->email = $this->getMockBuilder(Email::class)
            ->setConstructorArgs([[
                'to' => 'you@example.com',
                'from' => 'us@example.com',
                'subject' => 'test',
            ]])
            ->setMethods(['message'])
            ->getMock();
        $this->email
             ->method('message')
             ->willReturn(['a message']);
    }

    public function testSendingWorks() {
        $this->LocalTransport->expects($this->once())
            ->method('_parent');

        $result = $this->LocalTransport->send($this->email);
        $this->assertTextContains('you@example.com', $result['headers']);
        $this->assertTextContains('a message', $result['message']);
    }

    public function testFailureWithoutLogging() {
        $this->LocalTransport->expects($this->once())
            ->method('_parent')
            ->willThrowException(new SocketException());

        $result = $this->LocalTransport->send($this->email);
        $this->assertTextContains('you@example.com', $result['headers']);
        $this->assertTextContains('a message', $result['message']);
    }

    public function testFailureWithLogging() {
        $logger = new TestLog(['scopes' => ['unsent']]);
        $oldConfig = Log::getConfig('unsent');
        Log::drop('unsent');
        Log::setConfig('unsent', $logger);

        $loggingTransport = $this->getMockBuilder(LocalTransport::class)
            ->setConstructorArgs([['log' => true]])
            ->setMethods(['_parent'])
            ->getMock();
        $loggingTransport->expects($this->once())
            ->method('_parent')
            ->willThrowException(new SocketException('SocketException'));

        $loggingTransport->send($this->email);
        $logMessage = $logger->getLogMessage();
        $this->assertTextContains('SocketException', $logMessage);
        $this->assertTextContains('us@example.com', $logMessage);

        Log::drop('unsent');
        Log::setConfig('unsent', $oldConfig);
    }
}
