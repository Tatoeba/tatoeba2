<?php
namespace App\Test\Mailer;

use App\Mailer\UserMailer;
use App\Model\ContentReport;
use App\Model\Entity\SentenceComment;
use App\Model\Entity\User;
use App\Model\Entity\Wall;
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

class UserMailerTest extends TestCase {

    use EmailTrait;

    public $fixtures = ['app.users'];

    protected $email = null;
    protected $mailer = null;

    public function setUp() {
        Configure::write('App.fullBaseUrl', 'https://example.net');
        Configure::write('Tatoeba.communityModeratorEmail', 'moderator@example.net');

        $this->email = new Email([
            'from' => 'sender@example.com',
            'emailFormat' => 'html',
            'transport' => 'debug',
        ]);
        $this->mailer = new UserMailer($this->email);
    }

    public function blockedOrSuspendedProvider () {
        return [
            'suspended user' => [true, 'suspended'],
            'blocked user' => [false, 'changed the level'],
        ];
    }

    /**
     * @dataProvider blockedOrSuspendedProvider
     */
    public function test_blocked_or_suspended_user($isSuspended, $snippet) {
        $user = new User([
            'username' => 'kazuki',
            'id' => 7,
        ]);

        $result = $this->mailer->send(
            'blocked_or_suspended_user',
            [$user, $isSuspended]
        );

        $this->assertMailSentTo('moderator@example.net');
        $this->assertMailSentWith('( ! ) kazuki', 'subject');
        $this->assertMailContainsHtml($snippet);
    }

    public function test_new_password() {
        $user = new User([
            'username' => 'someone',
            'email' => 'someone@example.com',
        ]);

        $result = $this->mailer->send('new_password', [$user, 'secret']);

        $this->assertMailSentTo('someone@example.com');
        $this->assertMailSentFrom('sender@example.com');
        $this->assertMailSentWith('Tatoeba, new password', 'subject');
        $this->assertMailContainsHtml('Your new password: secret');
    }

    public function test_content_report_wall_post() {
        $wallPost = new Wall(['id' => 3, 'content' => 'spam spam spam...']);
        $report = new ContentReport('kazuki', $wallPost, 'this is spam');

        $this->mailer->send('content_report', [$report]);

        $this->assertMailSentTo('moderator@example.net');
        $this->assertMailSentWith('[Content Report] Wall message #3', 'subject');
        $this->assertMailContainsHtml('Member <a href="https://example.net/user/profile/kazuki">kazuki</a> reported a wall post');
        $this->assertMailContainsHtml('https://example.net/wall/show_message/3#message_3');
        $this->assertMailContainsHtml('this is spam');
    }

    public function test_content_report_wall_post_no_details() {
        $wallPost = new Wall(['id' => 3, 'content' => 'spam spam spam...']);
        $report = new ContentReport('kazuki', $wallPost, '');

        $this->mailer->send('content_report', [$report]);

        $this->assertMailContainsHtml('kazuki did not provide any detail.');
    }

    public function test_content_report_wall_post_html_injection() {
        $wallPost = new Wall(['id' => 3, 'content' => 'spam spam spam...']);
        $report = new ContentReport('kazuki', $wallPost, '<img src="http://example.com/oops" />');

        $this->mailer->send('content_report', [$report]);

        $this->assertMailContainsHtml('&lt;img src=&quot;http://example.com/oops&quot; /&gt;');
    }

    public function test_content_report_sentence_comment() {
        $comment = new SentenceComment([
            'id' => 5,
            'content' => 'spam spam spam...',
            'sentence_id' => 19,
        ]);
        $report = new ContentReport('kazuki', $comment, 'please remove spam');

        $this->mailer->send('content_report', [$report]);

        $this->assertMailSentTo('moderator@example.net');
        $this->assertMailSentWith('[Content Report] Comment #5 (on sentence #19)', 'subject');
        $this->assertMailContainsHtml('Member <a href="https://example.net/user/profile/kazuki">kazuki</a> reported a sentence comment');
        $this->assertMailContainsHtml('https://example.net/sentences/show/19#comment-5');
        $this->assertMailContainsHtml('please remove spam');
    }
}
