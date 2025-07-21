<?php

namespace App\Test\TestCase\Event;

use App\Event\NotificationListener;
use App\Model\Entity\PrivateMessage;
use App\Model\Entity\SentenceComment;
use App\Model\Entity\Wall;
use Cake\Event\Event;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

class NotificationListenerTest extends TestCase {

    use EmailTrait;

    public $fixtures = array(
        'app.users',
        'app.sentences',
        'app.sentence_comments',
        'app.walls'
    );

    public function setUp() {
        parent::setUp();
        $this->NL = new NotificationListener();
    }

    public function tearDown() {
        unset($this->NL);
        parent::tearDown();
    }

    private function _message($recpt) {
        return new PrivateMessage([
            'id' => 42,
            'recpt' => $recpt,
            'sender' => 7,
            'date' => '1999-12-31 23:59:59',
            'folder' => 'Inbox',
            'title' => 'Status',
            'content' => "Why are you so\nadvanced? <>",
            'isnonread' => 1,
            'user_id' => $recpt,
            'draft_recpts' => '',
            'sent' => 1,
        ]);
    }

    public function testSendPmNotification() {
        $event = new Event('Model.PrivateMessage.messageSent', $this, [
            'message' => $this->_message(3),
        ]);

        $this->NL->sendPmNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('advanced_contributor@example.com');
        $this->assertMailSentWith('Tatoeba PM - Status', 'subject');
        $this->assertMailContainsHtml("You have received a private message from <strong>kazuki</strong>.");
        $this->assertMailContainsHtml("Why are you so<br />\nadvanced? &lt;&gt;");
    }

    public function testSendPmNotification_doNotSendIfUserSettingsDisabled() {
        $event = new Event('Model.PrivateMessage.messageSent', $this, [
            'message' => $this->_message(7),
        ]);

        $this->NL->sendPmNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendSentenceCommentNotification_toSentenceOwner() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 9,
                'text' => 'This sentence lacks a flag.',
                'user_id' => 4,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('advanced_contributor@example.com');
        $this->assertMailSentWith('Tatoeba - Comment on sentence : This sentences purposely misses its flag.', 'subject');
        $this->assertMailContainsHtml("<strong>contributor</strong> has posted a comment on the sentence 'This sentences purposely misses its flag.'.");
    }

    public function testSendSentenceCommentNotification_toOtherCommentsAuthors() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 14,
                'text' => 'I’m adopting!',
                'user_id' => 4,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('corpus_maintainer@example.com');
    }

    public function testSendSentenceCommentNotification_toMentionedUsers() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 1,
                'text' => '@corpus_maintainer Any licensing problem with this sentence?',
                'user_id' => 3,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('corpus_maintainer@example.com');
    }

    public function testSendSentenceCommentNotification_notToMentionedInvalidUsers() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 1,
                'text' => 'mentioning @invalid @users',
                'user_id' => 3,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendSentenceCommentNotification_notToSelfMentioned() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 1,
                'text' => 'mentioning myself: @advanced_contributor',
                'user_id' => 3,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendSentenceCommentNotification_ignoresUsersWithNotificationsDisabled() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 4,
                'text' => '@kazuki Yay!',
                'user_id' => 4,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendSentenceCommentNotification_ignoresCommentPoster() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 14,
                'text' => 'Come on, someone adopt this sentence!',
                'user_id' => 2,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendSentenceCommentNotification_doesNotDoubleSend() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 17,
                'text' => '@advanced_contributor @advanced_contributor I love you!',
                'user_id' => 4,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('advanced_contributor@example.com');
    }

    public function testSendSentenceCommentNotification_toMultipleRecipents() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 19,
                'text' => '@advanced_contributor and @corpus_maintainer I love you guys!',
                'user_id' => 7,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(4);
        $this->assertMailSentToAt(0, 'admin@example.com');
        $this->assertMailSentToAt(1, 'corpus_maintainer@example.com');
        $this->assertMailSentToAt(2, 'advanced_contributor@example.com');
        $this->assertMailSentToAt(3, 'contributor@example.com');
    }

    public function testSendSentenceCommentNotification_onDeletedSentence() {
        $event = new Event('Model.SentenceComment.commentPosted', $this, [
            'comment' => new SentenceComment([
                'sentence_id' => 13,
                'text' => 'I deleted this sentence!',
                'user_id' => 4,
            ]),
        ]);

        $this->NL->sendSentenceCommentNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentWith('Tatoeba - Comment on deleted sentence #13', 'subject');
        $this->assertMailContainsHtml("<strong>contributor</strong> has posted a comment on the deleted sentence #13.");
    }

    public function testSendWallReplyNotification_replyToOP() {
        $post = new Wall([
            'id' => 3,
            'owner' => 7,
            'date' => '2018-01-02 03:04:05',
            'modified' => '2018-01-02 03:04:05',
            'parent_id' => 2,
            'content' => "I see.\nGood luck! <>",
            'lft' => 3,
            'rght' => 4,
        ]);
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => $post,
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('admin@example.com');
        $this->assertMailSentWith('Tatoeba - kazuki has replied to you on the Wall', 'subject');
        $this->assertMailContainsHtml("I see.<br />\nGood luck! &lt;&gt;");
    }

    public function testSendWallReplyNotification_toMentionedUser() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'Mentioning @contributor here',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('contributor@example.com');
        $this->assertMailSentWith('Tatoeba - admin mentioned you on the Wall', 'subject');
        $this->assertMailContainsHtml("Mentioning @contributor here");
    }

    public function testSendWallReplyNotification_bothReplyToOPAndMentionOP() {
        $post = new Wall([
            'id' => 3,
            'owner' => 7,
            'date' => '2018-01-02 03:04:05',
            'modified' => '2018-01-02 03:04:05',
            'parent_id' => 2,
            'content' => "Both notifying @admin and replying to them",
            'lft' => 3,
            'rght' => 4,
        ]);
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => $post,
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(1);
    }

    public function testSendWallReplyNotification_replyToSelfDoesntSelfNotify() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 2,
                'content' => 'I’m replying to myself!',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallReplyNotification_mentionSelfDoesntSelfNotify() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'Mentioning @admin myself',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallReplyNotification_doesntNotifyOPIfUserSettingDisabled() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'No, I was kidding!',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallReplyNotification_doesntNotifyOPIfUserSettingDisabled_evenIfMentioned() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'Replying to @kazuki who has notifications turned off',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallReplyNotification_doesntNotifyMentionedUserIfUserSettingDisabled() {
        $event = new Event('Model.Wall.replyPosted', $this, [
            'post' => new Wall([
                'id' => 3,
                'owner' => 1,
                'date' => '2018-01-02 03:04:05',
                'modified' => '2018-01-02 03:04:05',
                'parent_id' => 1,
                'content' => 'Mentioning @kazuki who has turned off notifications',
                'lft' => 4,
                'rght' => 5,
            ]),
        ]);

        $this->NL->sendWallReplyNotification($event);

        $this->assertMailCount(0);
    }

    private function _new_thread($authorId, $content) {
        return new Wall([
            'owner' => $authorId,
            'date' => '2018-01-02 03:04:05',
            'modified' => '2018-01-02 03:04:05',
            'content' => $content,
            'lft' => 7,
            'rght' => 8,
        ]);
    }

    public function testSendWallNewThreadNotification_noNotification() {
        $event = new Event('Model.Wall.newThread', $this, [
            'post' => $this->_new_thread(7, "New post"),
        ]);

        $this->NL->sendNewThreadNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallNewThreadNotification_withMention() {
        $event = new Event('Model.Wall.newThread', $this, [
            'post' => $this->_new_thread(7, "New post\nwith mention to @admin <>"),
        ]);

        $this->NL->sendNewThreadNotification($event);

        $this->assertMailCount(1);
        $this->assertMailSentTo('admin@example.com');
        $this->assertMailSentWith('Tatoeba - kazuki mentioned you on the Wall', 'subject');
        $this->assertMailContainsHtml("New post<br />\nwith mention to @admin &lt;&gt;");
    }

    public function testSendWallNewThreadNotification_doesNotDoubleSend() {
        $event = new Event('Model.Wall.newThread', $this, [
            'post' => $this->_new_thread(7, "New post\nwith two mentions to @admin @admin <>"),
        ]);

        $this->NL->sendNewThreadNotification($event);

        $this->assertMailCount(1);
    }

    public function testSendWallNewThreadNotification_doesNotNotifyIfUserSettingDisabled() {
        $event = new Event('Model.Wall.newThread', $this, [
            'post' => $this->_new_thread(1, 'Mentioning @kazuki who has turned off notifications'),
        ]);

        $this->NL->sendNewThreadNotification($event);

        $this->assertMailCount(0);
    }

    public function testSendWallNewThreadNotification_doesntSelfNotify() {
        $event = new Event('Model.Wall.newThread', $this, [
            'post' => $this->_new_thread(1, 'I am mentioning @admin myself!'),
        ]);

        $this->NL->sendNewThreadNotification($event);

        $this->assertMailCount(0);
    }
}
