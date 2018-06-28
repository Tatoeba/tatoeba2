<?php
App::uses('TagsController', 'Controller');

class TagsControllerTest extends ControllerTestCase {
    public $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.sentence',
        'app.tag',
        'app.tags_sentence',
        'app.user',
        'app.users_language',
    );

    public function setUp() {
        Configure::write('Acl.database', 'test');
        $this->controller = $this->generate('Tags');
    }

    public function endTest($method) {
        $this->controller->Auth->Session->destroy();
        unset($this->controller);
    }

    private function logInAs($username) {
        $user = $this->controller->Tag->User->find('first', array(
            'conditions' => array('username' => $username),
        ));
        $this->controller->Auth->login($user['User']);
    }

    private function _removeAsUser($username, $tagId, $sentenceId) {
        $beforeCount = $this->controller->Tag->TagsSentence->find('count', array(
            'conditions' => array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ),
        ));
        if ($username) {
            $this->logInAs($username);
        }
        $this->testAction(
            "/jpn/tags/remove_tag_from_sentence/$tagId/$sentenceId",
            array(
                'method' => 'get',
            )
        );
        $afterCount = $this->controller->Tag->TagsSentence->find('count', array(
            'conditions' => array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ),
        ));
        return $afterCount - $beforeCount;
    }

    public function testGuestDoesntRemoveTag() {
        $delta = $this->_removeAsUser(null, 2, 2);
        $this->assertEquals(0, $delta);
    }

    public function testRegularUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testAdvancedUserAuthorDoesRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testDifferentAdvancedUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testCorpusMaintainerDoesRemoveTag() {
        $delta = $this->_removeAsUser('corpus_maintainer', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testAdminDoesRemoveTag() {
        $delta = $this->_removeAsUser('admin', 2, 2);
        $this->assertEquals(-1, $delta);
    }
}
