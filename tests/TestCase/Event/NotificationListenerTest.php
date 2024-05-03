<?php

namespace App\Test\TestCase\Event;

use App\Event\NotificationListener;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

class NotificationListenerTest extends TestCase {

    use EmailTrait;

    public $fixtures = array(
        'app.Users',
        'app.Sentences',
        'app.SentenceComments',
        'app.Walls'
    );

    public function setUp() {
        parent::setUp();

        $this->Email = $this->getMockBuilder(Email::class)
            ->setMethods(['setTo', 'setSubject', 'send', 'setViewVars'])
            ->getMock();
        foreach (['setTo', 'setSubject', 'setViewVars'] as $method) {
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
        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->Email->expects($this->once())
                    ->method('setTo')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('setSubject')
                    ->with('Tatoeba PM - Status');
        $this->Email->expects($this->once())
                    ->method('send');

        $this->NL->sendPmNotification($event);
    }

    public function testSendPmNotification_noUnwantedHeaderAndFooter() {
        // Use a real Email object instead of a mock, so that we can
        // check the email contents. The debug transport will prevent
        // it from actually sending out emails.
        $this->Email = new Email();
        $this->NL = new NotificationListener($this->Email);

        $event = new Event('Model.PrivateMessage.messageSent', $this, array(
            'message' => $this->_message(),
        ));

        $this->NL->sendPmNotification($event);

        $this->assertMailCount(1);
        $this->assertMailContainsHtml('Why are you so advanced?');
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
                    ->method('setTo')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('setSubject')
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
                    ->method('setTo')
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
                    ->method('setTo')
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
                    ->method('setTo')
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

        $this->Email->expects($this->exactly(4))
                    ->method('setTo')
                    ->withConsecutive(
                        ['admin@example.com'],
                        ['corpus_maintainer@example.com'],
                        ['advanced_contributor@example.com'],
                        ['contributor@example.com']
                    );

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
                    ->method('setTo')
                    ->with('advanced_contributor@example.com');
        $this->Email->expects($this->once())
                    ->method('setSubject')
                    ->with('Tatoeba - Comment on deleted sentence #13');
        $this->Email->expects($this->once())
                    ->method('send');

        $this->NL->sendSentenceCommentNotification($event);
    }

    public function testSendSentenceCommentNotification_hasCorrectViewVars() {
        $comment = [
            'sentence_id' => 9,
            'text' => 'This sentence lacks a flag.',
            'user_id' => 4,
        ];
        $event = new Event('Model.SentenceComment.commentPosted', $this, array(
            'comment' => $comment
        ));
        $this->Email->expects($this->once())
            ->method('setViewVars')
            ->with([
                'author' => 'contributor',
                'commentText' => $comment['text'],
                'sentenceIsDeleted' => false,
                'sentenceText' => 'This sentences purposely misses its flag.',
                'sentenceId' => 9
            ]);
        $this->NL->sendSentenceCommentNotification($event);
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
        $event = new Event('Model.Wall.replyPosted', $this, array(
            'post' => $this->_wallReply(),
        ));

        $this->Email->expects($this->once())
                    ->method('setTo')
                    ->with('admin@example.com');
        $this->Email->expects($this->once())
                    ->method('setSubject')
                    ->with('Tatoeba - kazuki has replied to you on the Wall');
        $this->Email->expects($this->once())
                    ->method('send');

        $this->NL->sendWallReplyNotification($event);
    }

    public function testSendWallReplyNotification_hasCorrectViewVars() {
        $event = new Event('Model.Wall.replyPosted', $this, array(
            'post' => $this->_wallReply(),
        ));
        $expectedLink = 'https://example.net/wall/show_message/3#message_3';
        $this->Email->expects($this->once())
            ->method('setViewVars')
            ->with([
                'author' => 'kazuki',
                'postId' => 3,
                'messageContent' => $this->_wallReply()['content']
            ]);

        $this->NL->sendWallReplyNotification($event);        
    }

    public function testSendWallReplyNotification_doesntSelfNotify() {
        $event = new Event('Model.Wall.replyPosted', $this, array(
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
        $event = new Event('Model.Wall.replyPosted', $this, array(
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
}
