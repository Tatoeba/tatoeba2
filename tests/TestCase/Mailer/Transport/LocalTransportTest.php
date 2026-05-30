<?php
namespace App\Test\TestCase\Mailer\Transport;

use App\Mailer\Transport\LocalTransport;
use App\Test\TestCase\TestLog;
use Cake\Log\Log;
use Cake\Mailer\Message;
use Cake\Network\Exception\SocketException;
use Cake\TestSuite\TestCase;

class LocalTransportTest extends TestCase {

    private $LocalTransport;
    private $msg;

    public function setUp(): void {
        parent::setUp();
        $this->LocalTransport = $this->getMockBuilder(LocalTransport::class)
            ->setMethods(['_parent'])
            ->getMock();

        $this->msg = new Message();
        $this->msg
            ->setTo('you@example.com')
            ->setFrom('us@example.com')
            ->setSubject('test')
            ->setBody(['text' => 'a message']);
    }

    public function testSendingWorks() {
        $this->LocalTransport->expects($this->once())
            ->method('_parent');

        $result = $this->LocalTransport->send($this->msg);
        $this->assertTextContains('you@example.com', $result['headers']);
        $this->assertTextContains('a message', $result['message']);
    }

    public function testFailureWithoutLogging() {
        $this->LocalTransport->expects($this->once())
            ->method('_parent')
            ->willThrowException(new SocketException());

        $result = $this->LocalTransport->send($this->msg);
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

        $loggingTransport->send($this->msg);
        $logMessage = $logger->getLogMessage();
        $this->assertTextContains('SocketException', $logMessage);
        $this->assertTextContains('us@example.com', $logMessage);

        Log::drop('unsent');
        Log::setConfig('unsent', $oldConfig);
    }
}
