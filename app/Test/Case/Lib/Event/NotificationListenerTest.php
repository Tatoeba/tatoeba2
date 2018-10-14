<?php

App::uses('NotificationListener', 'Lib/Event');
App::uses('CakeEvent', 'Event');
App::uses('CakeEmail', 'Network/Email');

class NotificationListenerTest extends CakeTestCase {
    public $fixtures = array(
        'app.user',
    );

    public function setUp() {
        parent::setUp();

        Configure::write('Mailer.enabled',   true);
        Configure::write('Mailer.username', 'tatoeba@example.com');
        Configure::write('Mailer.password', 'terrible_password');

        $this->Email = $this->getMock('CakeEmail', array(
            'from', 'to', 'subject', 'send'
        ));
        foreach (array('from', 'to', 'subject') as $method) {
            $this->Email->expects($this->any())
                        ->method($method)
                        ->will($this->returnSelf());
        }

        $this->NL = new NotificationListener($this->Email);
    }

    public function tearDown() {
        unset($this->NL);
        parent::tearDown();
    }

    private function _message() {
        return array(
            'id' => 42,
            'recpt' => 3,
            'sender' => 7,
            'date' => '1999-12-31 23:59:59',
            'folder' => 'Inbox',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'isnonread' => 1,
            'user_id' => 3,
            'draft_recpts' => '',
            'sent' => 1,
        );
    }

    public function testSendPmNotification() {
        $event = new CakeEvent('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->Email->expects($this->once())
                    ->method('from')
                    ->with(array('tatoeba@example.com' => 'noreply'));
        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('subject')
                    ->with('Tatoeba PM - Status');
        $this->Email->expects($this->once())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }
}
