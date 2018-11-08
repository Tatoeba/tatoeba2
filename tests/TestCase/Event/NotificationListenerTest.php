<?php

namespace App\Test\TestCase\Event;

use App\Lib\Event\NotificationListener;
use App\Network\Email\Email;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\TestSuite\TestCase;

class NotificationListenerTest extends TestCase {
    public $fixtures = array(
        'app.users',
        'app.sentences',
        'app.sentence_comments',
        'app.walls'
    );

    public function setUp() {
        parent::setUp();

        Configure::write('App.fullBaseUrl', 'https://example.net');
        Configure::write('Mailer.enabled',   true);
        Configure::write('Mailer.username', 'tatoeba@example.com');
        Configure::write('Mailer.password', 'terrible_password');

        $this->Email = $this->getMock('Email', array(
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

    /**
     * Do not mock $this->Email but prevent it from sending out emails.
     * Useful to test email contents.
     */
    private function setUpLightMock() {
        $this->Email = new Email();
        $this->NL = $this->getMock('NotificationListener',
                                   array('getTransport'),
                                   array($this->Email));
        $this->NL->expects($this->any())
                 ->method('getTransport')
                 ->will($this->returnValue('Debug'));
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
        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
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
        $this->setUpLightMock();

        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->NL->sendPmNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertNotContains('CakePHP Framework', $sentMessage);
        $this->assertNotContains('Emails/html', $sentMessage);
    }

    public function testSendPmNotification_doNotSendIfDisabled() {
        Configure::write('Mailer.enabled', false);
        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }

    public function testSendPmNotification_doNotSendIfUserSettingsDisabled() {
        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(7),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }

    public function testSendSentenceCommentNotification_toSentenceOwner() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_toOtherCommentsAuthors() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 14,
                'text' => 'I’m adopting!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('corpus_maintainer@example.com');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_toMentionedUsers() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 1,
                'text' => '@corpus_maintainer Any licensing problem with this sentence?',
                'user_id' => 3,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('corpus_maintainer@example.com');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_notToMentionedInvalidUsers() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 17,
                'text' => '@advanced_contributor @advanced_contributor I love you!',
                'user_id' => 4,
            )
        ));

        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('advanced_contributor@example.com');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_toMultipleRecipents() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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
                    ->with('corpus_maintainer@example.com');
        $this->Email->expects($this->at(8))
                    ->method('to')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->at(12))
                    ->method('to')
                    ->with('contributor@example.com');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_onDeletedSentence() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
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

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_includesLinkToComment() {
        $this->setUpLightMock();

        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 9,
                'text' => 'This sentence lacks a flag.',
                'user_id' => 4,
            )
        ));
        $expectedLink = 'https://example.net/sentence_comments/show/9#comments';

        $this->NL->sendSentenceCommentNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertContains($expectedLink, $sentMessage);
    }

    public function testSendSentenceCommentNotification_includesCommentAuthor() {
        $this->setUpLightMock();

        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => array(
                'sentence_id' => 9,
                'text' => 'This sentence lacks a flag.',
                'user_id' => 2,
            )
        ));
        $expectedAuthor = 'corpus_maintainer';

        $this->NL->sendSentenceCommentNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertContains($expectedAuthor, $sentMessage);
    }

    private function _wallReply() {
        return array(
            'id' => 3,
            'owner' => 7,
            'date' => '2018-01-02 03:04:05',
            'modified' => '2018-01-02 03:04:05',
            'parent_id' => 2,
            'content' => 'I see.',
            'lft' => 3,
            'rght' => 4,
        );
    }

    public function testSendWallReplyNotification() {
        $event = new Event('Model.Wall.postPosted', $this, array(
            'post' => $this->_wallReply(),
        ));

        $this->Email->expects($this->once())
                    ->method('from')
                    ->with(array('tatoeba@example.com' => 'noreply'));
        $this->Email->expects($this->once())
                    ->method('to')
                    ->with('admin@example.com');
        $this->Email->expects($this->once())
                    ->method('subject')
                    ->with('Tatoeba - kazuki has replied to you on the Wall');
        $this->Email->expects($this->once())
                    ->method('send');

        $this->NL->sendWallReplyNotification($event);
    }

    public function testSendWallReplyNotification_includesLinkToPost() {
        $this->setUpLightMock();

        $event = new Event('Model.Wall.postPosted', $this, array(
            'post' => $this->_wallReply(),
        ));
        $expectedLink = 'https://example.net/wall/show_message/3#message_3';

        $this->NL->sendWallReplyNotification($event);

        $sentMessage = implode($this->Email->message());
        $this->assertContains($expectedLink, $sentMessage);
    }

    public function testSendWallReplyNotification_doesntSelfNotify() {
        $event = new Event('Model.Wall.postPosted', $this, array(
            'post' => array(
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 2,
                'content' => 'I’m replying to myself!',
                'lft' => 4,
                'rght' => 5,
            ),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendWallReplyNotification($event);
    }

    public function testSendWallReplyNotification_doesNotSendIfUserSettingsDisabled() {
        $event = new Event('Model.Wall.postPosted', $this, array(
            'post' => array(
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'No, I was kidding!',
                'lft' => 4,
                'rght' => 5,
            ),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendWallReplyNotification($event);
    }

    public function testSendWallReplyNotification_doesNotSendOnNewThread() {
        $event = new Event('Model.Wall.postPosted', $this, array(
            'post' => array(
                'id' => 3,
                'owner' => 2,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => NULL,
                'content' => 'Hi everyone!',
                'lft' => 4,
                'rght' => 5,
            ),
        ));

        $this->Email->expects($this->never())
                    ->method('send');

        $this->NL->sendWallReplyNotification($event);
    }
}
