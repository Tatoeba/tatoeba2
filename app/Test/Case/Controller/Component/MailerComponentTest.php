<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('MailerComponent', 'Controller/Component');

class MailerComponentTest extends CakeTestCase {

    public function setUp() {
        parent::setUp();

        Configure::write('Mailer.enabled', true);
        // These tests shouldn't really send anything out,
        // but let's set some safe defaults, just in case.
        Configure::write('Mailer.username', 'invalid@example.com');
        Configure::write('Mailer.password', 'invalid');

        $_SERVER['HTTP_HOST'] = 'example.net';

        $Collection = new ComponentCollection();
        $this->Mailer = $this->getMock(
            'MailerComponent',
            array('getTransport'),
            array($Collection)
        );
        $this->Mailer
            ->expects($this->any())
            ->method('getTransport')
            ->will($this->returnValue('Debug'));
    }

    private function behaveAsUser($username) {
        $user = ClassRegistry::init('User')->findByUsername($username);
        CurrentUser::store($user['User']);
    }

    public function tearDown() {
        unset($this->Mailer);

        parent::tearDown();
    }

    public function testSendWallReplyNotification_noUnwantedHeaderAndFooter() {
        $recipient = 'kazuki@example.net';
        $message = array(
            'Wall' => array(
                'content' => 'When it’s done.',
                'owner' => '1',
                'parent_id' => '1',
                'date' => '2014-04-15 16:38:36',
                'id' => '2',
            ),
            'User' => array(
                'image' => '',
                'username' => 'admin',
            ),
        );

        $this->Mailer->sendWallReplyNotification($recipient, $message);

        $sentMessage = implode($this->Mailer->Email->message());
        $this->assertNotContains('CakePHP Framework', $sentMessage);
        $this->assertNotContains('Emails/html', $sentMessage);
    }

    public function testSendSentenceCommentNotification_onDeletedSentence() {
        $this->behaveAsUser('kazuki');
        $sentenceOwner = null;
        $recipient = 'advanced_contributor@example.com';
        $comment = array(
            'sentence_id' => '13',
            'sentence_text' => 'Sentence deleted',
            'text' => 'Thank you for deleting that sentence!',
        );

        $this->Mailer->sendSentenceCommentNotification($recipient, $comment, $sentenceOwner);

        $this->assertEquals('Tatoeba - Comment on deleted sentence #13', $this->Mailer->Email->subject());
        $sentMessage = implode($this->Mailer->Email->message());
        $this->assertContains('<strong>kazuki</strong> has posted a comment on deleted sentence #13', $sentMessage);
    }
}
