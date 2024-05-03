<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class TranscriptionsControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = array(
        'app.PrivateMessages',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.Sentences',
        'app.WikiArticles',
    );

    public function setUp() {
        parent::setUp();
        $this->enableCsrfToken();
    }

    public function controllerSpy($event, $controller = null) {
        parent::controllerSpy($event, $controller);

        /* Replace Autotranscription to allow syntax errors */
        $autotranscription = $this->getMockBuilder(Autotranscription::class)
            ->setMethods([
                'jpn_Jpan_to_Hrkt_validate',
                'jpn_Jpan_to_Hrkt_generate',
                'jpn_Hrkt_to_Latn_generate',
                'yue_Hant_to_Latn_generate',
            ])
            ->getMock();

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

        $this->_controller->Transcriptions
            ->setAutotranscription($autotranscription);
    }

    private function assertRedirectedToLoginPage() {
        $this->assertRedirect('/ja/users/login');
    }

    private function _resetAsUser($username, $sentenceId, $script) {
        $this->logInAs($username);
        $this->post("/ja/transcriptions/reset/$sentenceId/$script");
    }

    private function _saveAsUser($username, $sentenceId, $script, $transcrText) {
        if ($username) {
            $this->logInAs($username);
        }

        $this->post(
            "/ja/transcriptions/save/$sentenceId/$script",
            [ 'value' => $transcrText ]
        );
    }

    public function testGuestCannotEditMachineTranscription() {
        $this->_saveAsUser(null, 10, 'Hrkt', 'something new');
        $this->assertRedirectedToLoginPage();
    }
    public function testGuestCannotEditHumanTranscription() {
        $this->_saveAsUser(null, 6, 'Hrkt', 'something new');
        $this->assertRedirectedToLoginPage();
    }

    public function testRegularUserCannotEditMachineTranscription() {
        $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertResponseCode(400);
    }
    public function testOwnerCanEditOwnTranscription() {
        $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }
    public function testNonTranscriptionAuthorCannotEditHumanTranscription() {
        $this->_saveAsUser('contributor', 6, 'Hrkt', 'something new');
        $this->assertResponseCode(400);
    }
    public function testSentenceOwnerCanEditTranscriptionMadeBySomeoneElse() {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername('contributor')->first();
        $transcr = TableRegistry::get('Transcriptions');
        $tr = $transcr->get(1);
        $tr->user_id = $user->id;
        $saved = $transcr->save($tr);

        $this->_saveAsUser('kazuki', 6, 'Hrkt', 'something new');

        $this->assertResponseSuccess();
    }
    public function testRegularUserCannotInsertTranscription() {
        $transcr = TableRegistry::get('Transcriptions');
        $transcr->deleteAll('1=1');
        $this->_saveAsUser('contributor', 10, 'Hrkt', 'something new');
        $this->assertResponseCode(400);
    }
    public function testOwnerCanInsertTranscription() {
        $transcr = TableRegistry::get('Transcriptions');
        $transcr->deleteAll('1=1');
        $this->_saveAsUser('kazuki', 10, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }

    public function testAdvancedUserCanEditMachineTranscription() {
        $this->_saveAsUser('advanced_contributor', 10, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }
    public function testAdvancedUserCannotEditHumanTranscription() {
        $this->_saveAsUser('advanced_contributor', 6, 'Hrkt', 'something new');
        $this->assertResponseCode(400);
    }

    public function testCorpusMaintainerCanEditMachineTranscription() {
        $this->_saveAsUser('corpus_maintainer', 10, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }
    public function testCorpusMaintainerCanEditHumanTranscription() {
        $this->_saveAsUser('corpus_maintainer', 6, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }

    public function testAdminCanEditMachineTranscription() {
        $this->_saveAsUser('admin', 10, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }
    public function testAdminCanEditHumanTranscription() {
        $this->_saveAsUser('admin', 6, 'Hrkt', 'something new');
        $this->assertResponseSuccess();
    }

    public function testResetDoesResetTranscription() {
        $this->_resetAsUser('kazuki', 6, 'Hrkt');

        $result = $this->_controller->Transcriptions->find()
            ->where(['Transcriptions.sentence_id' => 6, 'Transcriptions.script' => 'Hrkt'])
            ->first();
        $this->assertEquals('furi', $result->text);
    }

    public function testRegularUserCanResetNonExistingTranscription() {
        $this->_resetAsUser('kazuki', 11, 'Latn');

        $result = $this->_controller->Transcriptions->find()
            ->where(['Transcriptions.sentence_id' => 11, 'Transcriptions.script' => 'Latn'])
            ->count();
        $this->assertEquals(1, $result);
    }

    public function testControllerAccess() {
        $this->assertAccessUrlAs('/en/transcriptions/of/kazuki', null, true);
        $this->assertAccessUrlAs('/en/transcriptions/of/kazuki', 'contributor', true);
        $this->assertAjaxAccessUrlAs('/en/transcriptions/view/6', null, true);
        $this->assertAjaxAccessUrlAs('/en/transcriptions/view/6', 'contributor', true);
    }
}
