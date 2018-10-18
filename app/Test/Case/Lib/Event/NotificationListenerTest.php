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

    public function testSendSentenceCommentNotification_toOtherCommentsAuthors() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 14,
                'text' => 'I’m adopting!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('corpus_maintainer@example.com');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_toMentionedUsers() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 1,
                'text' => '@corpus_maintainer Any licensing problem with this sentence?',
                'user_id' => 3,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('corpus_maintainer@example.com');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_notToMentionedInvalidUsers() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 1,
                'text' => 'mentioning @invalid @users',
                'user_id' => 3,
            )
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_notToSelfMentioned() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 1,
                'text' => 'mentioning myself: @advanced_contributor',
                'user_id' => 3,
            )
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_ignoresUsersWithNotificationsDisabled() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 4,
                'text' => '@kazuki Yay!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_ignoresCommentPoster() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 14,
                'text' => 'Come on, someone adopt this sentence!',
                'user_id' => 2,
            )
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_doesNotDoubleSend() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 17,
                'text' => '@advanced_contributor @advanced_contributor I love you!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_toMultipleRecipents() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 19,
                'text' => '@advanced_contributor and @corpus_maintainer I love you guys!',
                'user_id' => 7,
            )
        ));

/*
        // use this code instead after we upgrade to CakePHP 3
        $this->Email->expects($this->exactly(4))
                    ->method('to')
                    ->withConsecutive(array(
                        array('admin@example.com'),
                        array('advanced_contributor@example.com'),
                        array('corpus_maintainer@example.com'),
                        array('contributor@example.com'),
                    ));
*/
        $this->Email->expects($this->at(0))
                    ->method('to')
                    ->with('admin@example.com');
        $this->Email->expects($this->at(4))
                    ->method('to')
                    ->with('contributor@example.com');
        $this->Email->expects($this->at(8))
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->at(12))
                    ->method('to')
                    ->with('corpus_maintainer@example.com');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_onDeletedSentence() {
        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 13,
                'text' => 'I deleted this sentence!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('subject')
                    ->with('Tatoeba - Comment on deleted sentence #13');
        $this->Email->expects($this->once())
                    ->method('send');
        $_SERVER['HTTP_HOST'] = 'example.net';

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_includesLinkToComment() {
        $this->Email = $this->getMock('CakeEmail', null);
        $this->NL = $this->getMock('NotificationListener',
                                   array('getTransport'),
                                   array($this->Email));
        $this->NL->expects($this->any())
                 ->method('getTransport')
                 ->will($this->returnValue('Debug'));

        $event = new CakeEvent('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 9,
                'text' => 'This sentence lacks a flag.',
                'user_id' => 4,
            )
        ));
        $_SERVER['HTTP_HOST'] = 'example.net';
        $expectedLink = 'https://example.net/sentence_comments/show/9#comments';

        $this->NL->sendSentenceCommentNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertContains($expectedLink, $sentMessage);
    }
}
