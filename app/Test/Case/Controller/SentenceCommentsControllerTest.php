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
        $expectedArg = array(
            'sentence_id' => '1',
            'sentence_text' => 'The fundamental cause of the problem is that in'
                              .' the modern world, idiots are full of confidence,'
                              .' while the intelligent are full of doubt.',
            'text' => 'Very well said!',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('kazuki@example.net', $expectedArg, 'kazuki@example.net');

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => array(
                    'sentence_id' => '1',
                    'text' => 'Very well said!',
                ),
            )
        ));
    }

    public function testSave_onOrphanSentence() {
        $this->logInAs('contributor');
        $expectedArg = array(
            'sentence_id' => '14',
            'sentence_text' => 'An orphan sentence.',
            'text' => 'Okay, I’m going to adopt it.',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('kazuki@example.net', $expectedArg, null);

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => array(
                    'sentence_id' => '14',
                    'text' => 'Okay, I’m going to adopt it.',
                ),
            )
        ));
    }

    public function testSave_onDeletedSentence() {
        $this->logInAs('kazuki');
        $expectedArg = array(
            'sentence_id' => '13',
            'sentence_text' => false,
            'text' => 'Thank you for deleting that sentence!',
        );

        $this->controller->Mailer
            ->expects($this->once())
            ->method('sendSentenceCommentNotification')
            ->with('advanced_contributor@example.com', $expectedArg, null);

        $this->testAction('/eng/sentence_comments/save', array(
            'data' => array(
                'SentenceComment' => array(
                    'sentence_id' => '13',
                    'text' => 'Thank you for deleting that sentence!',
                ),
            )
        ));
    }
}
