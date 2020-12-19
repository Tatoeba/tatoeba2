<?php
namespace App\Test\Mailer;

use App\Mailer\UserMailer;
use App\Model\Entity\User;
use Cake\Mailer\Email;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

class UserMailerTest extends TestCase {

    use EmailTrait;

    public $fixtures = ['app.users'];

    protected $email = null;
    protected $mailer = null;

    public function setUp() {
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

        $this->assertMailSentTo('tatoeba-community-admins@googlegroups.com');
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
}
