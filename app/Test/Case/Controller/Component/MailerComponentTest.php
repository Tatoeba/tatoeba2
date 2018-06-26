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

    public function tearDown() {
        unset($this->Mailer);

        parent::tearDown();
    }

    public function testSendWallReplyNotification_doesntIncludeFooter() {
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
        var_dump($this->Mailer->Email->message());
    }
}
