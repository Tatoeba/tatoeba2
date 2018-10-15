<?php

App::uses('NotificationListener', 'Lib/Event');
App::uses('CakeEvent', 'Event');
App::uses('CakeEmail', 'Network/Email');

class NotificationListenerTest extends CakeTestCase {
    public $fixtures = array(
        'app.user',
        'app.sentence',
        'app.sentence_comment',
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

    private function _message($recpt = 3) {
        return array(
            'id' => 42,
            'recpt' => $recpt,
            'sender' => 7,
            'date' => '1999-12-31 23:59:59',
            'folder' => 'Inbox',
            'title' => 'Status',
            'content' => 'Why are you so advanced?',
            'isnonread' => 1,
            'user_id' => $recpt,
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

    public function testSendPmNotification_noUnwantedHeaderAndFooter() {
        $this->Email = $this->getMock('CakeEmail', null);
        $this->NL = $this->getMock('NotificationListener',
                                   array('getTransport'),
                                   array($this->Email));
        $this->NL->expects($this->any())
                 ->method('getTransport')
                 ->will($this->returnValue('Debug'));

        $event = new CakeEvent('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->NL->sendPmNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertNotContains('CakePHP Framework', $sentMessage);
        $this->assertNotContains('Emails/html', $sentMessage);
    }

    public function testSendPmNotification_doNotSendIfDisabled() {
        Configure::write('Mailer.enabled', false);
        $event = new CakeEvent('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }

    public function testSendPmNotification_doNotSendIfUserSettingsDisabled() {
        $event = new CakeEvent('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(7),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }

    public function testSendSentenceCommentNotification_toSentenceOwner() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 9,
                'text' => 'This sentence lacks a flag.',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('from')
                    ->with(array('tatoeba@example.com' => 'noreply'));
        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('subject')
                    ->with('Tatoeba - Comment on sentence : '
                          .'This sentences purposely misses its flag.');
        $this->Email->expects($this->once())
                    ->method('send');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }
}
