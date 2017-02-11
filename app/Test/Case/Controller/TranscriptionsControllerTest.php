<?php
/* Transcriptions Test cases generated on: 2014-10-20 00:25:43 : 1413764743*/
App::import('Controller', 'Transcriptions');

App::import('Component', 'Cookie');
App::import('Component', 'Auth');
class TestTranscriptionsController extends TranscriptionsController {
    function beforeFilter() {
        /* Replace the CookieComponent with a mock in order to prevent
           the 'headers already sent' error when a cookie is written.
        */
        $this->Cookie = Mockery::mock('CookieComponent');

        /* Replace the AuthComponent to easily log anyone. */
        $this->Auth = Mockery::mock('AuthComponent');
        if ($this->params['loggedInUserForTest']) {
            $user = $this->params['loggedInUserForTest'];
            $this->Auth->shouldReceive('user')->andReturn($user);
            unset($this->params['loggedInUserForTest']);
        }

        /* Replace Autotranscription to allow syntax errors */
        $autotranscription = Mockery::mock('Autotranscription');
        $autotranscription->shouldReceive('jpn_Jpan_to_Hrkt_validate')->andReturn(true);
        $autotranscription->shouldReceive('jpn_Jpan_to_Hrkt_generate')->andReturn('furi');
        $autotranscription->shouldReceive('jpn_Hrkt_to_Latn_generate')->andReturn('roma');
        $autotranscription->shouldReceive('yue_Hant_to_Latn_generate')->andReturn('yeah');
        $this->Transcription->setAutotranscription($autotranscription);

        parent::beforeFilter();
    }

    function redirect($url = NULL, $status = NULL, $exit = true) {
        /* Avoid redirecting for real since it causes the good old
           'Cannot modify header information' error. */
    }
}

class TranscriptionsControllerTest extends CakeTestCase {
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
        $this->Transcriptions = new TestTranscriptionsController();
        $this->Transcriptions->constructClasses();
        $this->User = ClassRegistry::init('User');
    }

    function endTest($method) {
        unset($this->Transcriptions);
        unset($this->User);
    }

    function _resetAsUser($username, $sentenceId, $script) {
        $user = $this->User->find('first', array(
            'conditions' => array('username' => $username),
            'recursive' => -1,
        ));

        return $this->testAction(
            "/jpn/transcriptions/reset/$sentenceId/$script",
            array(
                'method' => 'post',
                'controller' => 'TestTranscriptions',
                'loggedInUserForTest' => $user,
            )
        );
    }

    function _saveAsUser($username, $sentenceId, $script, $transcrText) {
        $user = $this->User->find('first', array(
            'conditions' => array('username' => $username),
            'recursive' => -1,
        ));
        $data = array('value' => $transcrText);

        return $this->testAction(
            "/jpn/transcriptions/save/$sentenceId/$script",
            array(
                'form' => $data,
                'method' => 'post',
                'controller' => 'TestTranscriptions',
                'loggedInUserForTest' => $user,
            )
        );
    }

    function testGuestCannotEditMachineTranscription() {
        $result = $this->_saveAsUser(null, 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    function testGuestCannotEditHumanTranscription() {
        $result = $this->_saveAsUser(null, 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }

    function testRegularUserCannotEditMachineTranscription() {
        $result = $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    function testOwnerCanEditOwnTranscription() {
        $result = $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    function testNonTranscriptionAuthorCannotEditHumanTranscription() {
        $result = $this->_saveAsUser('contributor', 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    function testSentenceOwnerCanEditTranscriptionMadeBySomeoneElse() {
        $user = $this->User->findByUsername('contributor');
        $saved = $this->Transcriptions->Transcription->save(array(
            'id' => 1,
            'user_id' => $user['User']['id'],
        ));

        $result = $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');

        $this->assertTrue($result);
    }
    function testRegularUserCannotInsertTranscription() {
        $this->Transcriptions->Transcription->deleteAll('1=1');
        $result = $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    function testOwnerCanInsertTranscription() {
        $this->Transcriptions->Transcription->deleteAll('1=1');
        $result = $this->_saveAsUser('kazuki', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    function testAdvancedUserCanEditMachineTranscription() {
        $result = $this->_saveAsUser('advanced_contributor', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    function testAdvancedUserCannotEditHumanTranscription() {
        $result = $this->_saveAsUser('advanced_contributor', 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }

    function testCorpusMaintainerCanEditMachineTranscription() {
        $result = $this->_saveAsUser('corpus_maintainer', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    function testCorpusMaintainerCanEditHumanTranscription() {
        $result = $this->_saveAsUser('corpus_maintainer', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    function testAdminCanEditMachineTranscription() {
        $result = $this->_saveAsUser('admin', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    function testAdminCanEditHumanTranscription() {
        $result = $this->_saveAsUser('admin', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    function testResetDoesResetTranscription() {
        $this->_resetAsUser('kazuki', 6, 'Hrkt');
        $result = $this->Transcriptions->Transcription->find('first', array(
            'conditions' => array('sentence_id' => 6, 'script' => 'Hrkt')
        ));
        $this->assertEquals('furi', $result['Transcription']['text']);
    }

    function testRegularUserCanResetNonExistingTranscription() {
        $this->_resetAsUser('kazuki', 11, 'Latn');
        $result = $this->Transcriptions->Transcription->find('count', array(
            'conditions' => array('sentence_id' => 11, 'script' => 'Latn')
        ));
        $this->assertEquals(1, $result);
    }
}
