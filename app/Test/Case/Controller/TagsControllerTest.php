<?php
App::import('Controller', 'Tags');

App::import('Component', 'Cookie');
App::import('Component', 'Auth');
class TestTagsController extends TagsController {
    function beforeFilter() {
        /* Replace the CookieComponent with a mock in order to prevent
           the 'headers already sent' error when a cookie is written.
        */
        $this->Cookie = Mockery::mock();

        /* Replace the AuthComponent to easily log anyone. */
        $this->Auth = Mockery::mock();
        if ($this->params['loggedInUserForTest']) {
            $user = $this->params['loggedInUserForTest'];
            $this->Auth->shouldReceive('user')->andReturn($user);
            unset($this->params['loggedInUserForTest']);
        }

        parent::beforeFilter();
    }

    function redirect($url = NULL, $status = NULL, $exit = true) {
        /* Avoid redirecting for real since it causes the good old
           'Cannot modify header information' error. */
    }
}

class TagsControllerTest extends CakeTestCase {
    public $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.contribution',
        'app.favorites_user',
        'app.group',
        'app.language',
        'app.link',
        'app.reindex_flag',
        'app.sentence',
        'app.sentence_comment',
        'app.sentence_annotation',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.user',
        'app.users_language',
        'app.wall',
        'app.wall_thread',
    );

    function setUp() {
        Configure::write('Acl.database', 'test_suite');
    }

    function startTest($method) {
        $this->Tags = new TestTagsController();
        $this->Tags->constructClasses();
        $this->User = ClassRegistry::init('User');
    }

    function endTest($method) {
        unset($this->Tags);
        unset($this->User);
    }

    function _removeAsUser($username, $tagId, $sentenceId) {
        $user = $this->User->find('first', array(
            'conditions' => array('username' => $username),
            'recursive' => -1,
        ));
        $beforeCount = $this->Tags->Tag->TagsSentence->find('count', array(
            'conditions' => array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ),
        ));
        $this->testAction(
            "/jpn/tags/remove_tag_from_sentence/$tagId/$sentenceId",
            array(
                'method' => 'get',
                'controller' => 'TestTags',
                'loggedInUserForTest' => $user,
            )
        );
        $afterCount = $this->Tags->Tag->TagsSentence->find('count', array(
            'conditions' => array(
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ),
        ));
        return $afterCount - $beforeCount;
    }

    function testGuestDoesntRemoveTag() {
        $delta = $this->_removeAsUser(null, 2, 2);
        $this->assertEquals(0, $delta);
    }

    function testRegularUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    function testAdvancedUserAuthorDoesRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    function testDifferentAdvancedUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    function testCorpusMaintainerDoesRemoveTag() {
        $delta = $this->_removeAsUser('corpus_maintainer', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    function testAdminDoesRemoveTag() {
        $delta = $this->_removeAsUser('admin', 2, 2);
        $this->assertEquals(-1, $delta);
    }
}
