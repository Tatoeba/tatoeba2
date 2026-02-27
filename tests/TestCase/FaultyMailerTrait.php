<?php

namespace App\Test\TestCase;

trait FaultyMailerTrait
{
    private function enableFaultyMailer() {
        $faultyTransport = $this
             ->getMockBuilder(\Cake\Mailer\Transport\DebugTransport::class)
             ->setMethods(['send'])
             ->getMock();
        $faultyTransport
             ->method('send')
             ->willThrowException(
                 new \Cake\Network\Exception\SocketException('Connection timed out')
             );
        \Cake\Mailer\TransportFactory::getRegistry()->set('debug', $faultyTransport);
    }
}
