<?php
App::uses('SentenceCommentsController', 'Controller');

class SentenceCommentsControllerTest extends ControllerTestCase {

    public $fixtures = array(
        'app.aco',
        'app.aro',
        'app.aros_aco',
        'app.contribution',
        'app.sentence_comment',
        'app.sentence',
        'app.user',
    );

    public function setUp() {
        Configure::write('Acl.database', 'test');
    }

    public function startTest($method) {
        $this->controller = $this->generate('SentenceComments', array(
            'components' => array('Mailer' => array('sendSentenceCommentNotification'))
        ));
    }

    public function tearDown() {
        unset($this->controller);
    }

    private function logInAs($username) {
        $user = $this->controller->User->findByUsername($username);
        $this->controller->Auth->login($user['User']);
    }

    public function testSave_sendsNotificationToOwner() {
        $this->logInAs('contributor');
        $comment = array(
            'sentence_id' => '1',
            'sentence_text' => 'The fundamental cause of the problem is that in'
                              .' the modern world, idiots are full of confidence,'
                              .' while the intelligent are full of doubt.',
            'text' => 'Very well said!',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('kazuki@example.net', $comment, 'kazuki@example.net');

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => $comment,
            )
        ));
    }

    public function testSave_sendsNotifWithRealSentenceText() {
        $this->logInAs('contributor');
        $comment = array(
            'sentence_id' => '1',
            'sentence_text' => 'The fundamental cause of the problem is that in'
                              .' the modern world, idiots are full of confidence,'
                              .' while the intelligent are full of doubt.',
            'text' => 'Very well said!',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('kazuki@example.net', $comment, 'kazuki@example.net');

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => array(
                    'sentence_id' => '1',
                    'sentence_text' => 'Some random text sent by the client',
                    'text' => 'Very well said!',
                )
            )
        ));
    }

    public function testSave_onDeletedSentence() {
        $this->logInAs('kazuki');
        $comment = array(
            'sentence_id' => '13',
            'sentence_text' => 'Sentence deleted',
            'text' => 'Thank you for deleting that sentence!',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('advanced_contributor@example.com', $comment, null);

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => $comment,
            )
        ));
    }
}
