<?php
App::uses('Controller', 'Transcriptions');

class TranscriptionsControllerTest extends ControllerTestCase {
    public $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.transcription',
        'app.user',
        'app.users_language',
        'app.sentence',
    );

    public function setUp() {
        $this->controller = $this->generate('Transcriptions');

        /* Replace Autotranscription to allow syntax errors */
        $autotranscription = $this->getMock('Autotranscription', array(
            'jpn_Jpan_to_Hrkt_validate',
            'jpn_Jpan_to_Hrkt_generate',
            'jpn_Hrkt_to_Latn_generate',
            'yue_Hant_to_Latn_generate',
        ));
        $autotranscription
            ->expects($this->any())
            ->method('jpn_Jpan_to_Hrkt_validate')
            ->will($this->returnValue(true));
        $autotranscription
            ->expects($this->any())
            ->method('jpn_Jpan_to_Hrkt_generate')
            ->will($this->returnValue('furi'));
        $autotranscription
            ->expects($this->any())
            ->method('jpn_Hrkt_to_Latn_generate')
            ->will($this->returnValue('roma'));
        $autotranscription
            ->expects($this->any())
            ->method('yue_Hant_to_Latn_generate')
            ->will($this->returnValue('yeah'));
        $this->controller->Transcription->setAutotranscription($autotranscription);

        // Make sure no user is logged in when running from /test.php
        $this->controller->Auth->Session->destroy();
    }

    public function endTest($method) {
        $this->controller->Auth->Session->destroy();
        unset($this->controller);
    }

    private function logInAs($username) {
        $user = $this->controller->User->findByUsername($username);
        $this->controller->Auth->login($user['User']);
    }

    private function _resetAsUser($username, $sentenceId, $script) {
        $this->logInAs($username);

        return $this->testAction(
            "/jpn/transcriptions/reset/$sentenceId/$script",
            array(
                'method' => 'post',
            )
        );
    }

    private function _saveAsUser($username, $sentenceId, $script, $transcrText) {
        if ($username) {
            $this->logInAs($username);
        }
        $data = array('value' => $transcrText);

        return $this->testAction(
            "/jpn/transcriptions/save/$sentenceId/$script",
            array(
                'data' => $data,
                'method' => 'post',
            )
        );
    }

    public function testGuestCannotEditMachineTranscription() {
        $result = $this->_saveAsUser(null, 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    public function testGuestCannotEditHumanTranscription() {
        $result = $this->_saveAsUser(null, 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }

    public function testRegularUserCannotEditMachineTranscription() {
        $result = $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    public function testOwnerCanEditOwnTranscription() {
        $result = $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    public function testNonTranscriptionAuthorCannotEditHumanTranscription() {
        $result = $this->_saveAsUser('contributor', 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    public function testSentenceOwnerCanEditTranscriptionMadeBySomeoneElse() {
        $user = $this->controller->User->findByUsername('contributor');
        $saved = $this->controller->Transcription->save(array(
            'id' => 1,
            'user_id' => $user['User']['id'],
        ));

        $result = $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');

        $this->assertTrue($result);
    }
    public function testRegularUserCannotInsertTranscription() {
        $this->controller->Transcription->deleteAll('1=1');
        $result = $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }
    public function testOwnerCanInsertTranscription() {
        $this->controller->Transcription->deleteAll('1=1');
        $result = $this->_saveAsUser('kazuki', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    public function testAdvancedUserCanEditMachineTranscription() {
        $result = $this->_saveAsUser('advanced_contributor', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    public function testAdvancedUserCannotEditHumanTranscription() {
        $result = $this->_saveAsUser('advanced_contributor', 6, 'Hrkt', 'something new');
        $this->assertFalse($result);
    }

    public function testCorpusMaintainerCanEditMachineTranscription() {
        $result = $this->_saveAsUser('corpus_maintainer', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    public function testCorpusMaintainerCanEditHumanTranscription() {
        $result = $this->_saveAsUser('corpus_maintainer', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    public function testAdminCanEditMachineTranscription() {
        $result = $this->_saveAsUser('admin', 10, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }
    public function testAdminCanEditHumanTranscription() {
        $result = $this->_saveAsUser('admin', 6, 'Hrkt', 'something new');
        $this->assertTrue($result);
    }

    public function testResetDoesResetTranscription() {
        $this->_resetAsUser('kazuki', 6, 'Hrkt');
        $result = $this->controller->Transcription->find('first', array(
            'conditions' => array('sentence_id' => 6, 'script' => 'Hrkt')
        ));
        $this->assertEquals('furi', $result['Transcription']['text']);
    }

    public function testRegularUserCanResetNonExistingTranscription() {
        $this->_resetAsUser('kazuki', 11, 'Latn');
        $result = $this->controller->Transcription->find('count', array(
            'conditions' => array('sentence_id' => 11, 'script' => 'Latn')
        ));
        $this->assertEquals(1, $result);
    }
}
