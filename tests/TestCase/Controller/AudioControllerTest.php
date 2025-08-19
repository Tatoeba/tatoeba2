<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\AudioIntegrationTestTrait;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class AudioControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;
    use AudioIntegrationTestTrait;

    public $fixtures = [
        'app.audios',
        'app.contributions',
        'app.disabled_audios',
        'app.languages',
        'app.private_messages',
        'app.sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.wiki_articles',
        'app.reindex_flags',
        'app.links',
        'app.queued_jobs',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/audio/import', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fimport' ],
            [ '/en/audio/import', 'spammer', '/' ],
            [ '/en/audio/import', 'inactive', '/' ],
            [ '/en/audio/import', 'contributor', '/' ],
            [ '/en/audio/import', 'advanced_contributor', '/' ],
            [ '/en/audio/import', 'corpus_maintainer', '/' ],
            [ '/en/audio/import', 'admin', true ],
            [ '/en/audio/index', null, true ],
            [ '/en/audio/index', 'contributor', true ],
            [ '/en/audio/index/fra', null, true ],
            [ '/en/audio/index/fra', 'contributor', true ],
            [ '/en/audio/of/contributor', null, true ],
            [ '/en/audio/of/contributor', 'contributor', true ],
            [ '/en/audio/save_settings', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fsave_settings' ],
            [ '/en/audio/save_settings', 'contributor', '/en/audio/of/contributor' ],
            [ '/en/audio/download/999999999999', null, 404 ], # unknown audio
            [ '/en/audio/save/1', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fsave%2F1' ],
            [ '/en/audio/save/1', 'contributor', '/' ],
            [ '/en/audio/save/1', 'corpus_maintainer', '/' ],
            [ '/en/audio/save/1', 'admin', 400 ], // 400 because it's supposed to be POST only
            [ '/en/audio/delete/1', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fdelete%2F1' ],
            [ '/en/audio/delete/1', 'contributor', '/' ],
            [ '/en/audio/delete/1', 'corpus_maintainer', '/' ],
            [ '/en/audio/delete/1', 'admin', 400 ], // 400 because it's supposed to be POST only
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testAudioControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    private function addSentencesWithAudio($nbSentences) {
        $sentences = TableRegistry::getTableLocator()->get('Sentences');
        $kazukiUserId = 7;
        $newSentences = [];
        for ($i = 1; $i <= $nbSentences; $i++) {
            $newSentences[] = [
                'lang' => 'eng',
                'text' => "Ay ay ay $i.",
                'user_id' => $kazukiUserId,
                'audios' => [
                    ['user_id' => $kazukiUserId],
                ],
            ];
        }
        $entities = $sentences->newEntities($newSentences, [
            // 'validate' => false is to avoid triggering
            // "sentence_id required on create" validation rule
            'associated' => ['Audios' => ['validate' => false]]
        ]);
        $sentences->saveMany($entities);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_asGuest() {
        $defaultNbPerPage = (new \App\Controller\AudioController())->paginate['limit'];
        $nbSentences = $this->addSentencesWithAudio($defaultNbPerPage + 1);
        $expectedLastPage = 2;

        $this->get("/en/audio/index?page=9999999");
        $this->assertRedirect("/en/audio/index?page=$expectedLastPage");

        $this->get("/en/audio/index/eng?page=9999999");
        $this->assertRedirect("/en/audio/index/eng?page=$expectedLastPage");

        $this->get("/en/audio/of/kazuki?page=9999999");
        $this->assertRedirect("/en/audio/of/kazuki?page=$expectedLastPage");
    }

    public function testAudioDownload_missingFile() {
        $this->initAudioStorageDir();

        $this->get('/en/audio/download/1');
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testAudioDownload_ok() {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(1);
        $this->get('/en/audio/download/1');
        $this->assertResponseOk();
        $this->assertResponseEquals($audioFileContents);
        $this->assertHeader('Content-Disposition', 'attachment; filename="3-1.mp3"');

        $this->deleteAudioStorageDir();
    }

    public function testAudioSave_asAdmin_ok() {
        $this->logInAs('admin');
        $this->ajaxPost('/ja/audio/save/1', json_encode(['enabled' => true, 'author' => 'kazuki']));
        $this->assertResponseOk();
    }

    public function testAudioSave_asAdmin_invalid() {
        $this->logInAs('admin');
        $this->ajaxPost('/ja/audio/save/9999999999', json_encode(['enabled' => true, 'author' => 'kazuki']));
        $this->assertResponseCode(404);
    }

    protected function assertAdminDeletesAudio($id) {
        $this->initAudioStorageDir();
        $this->createAudioFile($id);
        $path = $this->getAudioFilePath($id);
        $this->assertFileExists($path);

        $this->logInAs('admin');
        $this->ajaxPost('/ja/audio/delete/'.$id);
        $this->assertResponseOk();
        $this->assertFileNotExists($path);

        $this->deleteAudioStorageDir();
    }

    public function testAudioDelete_enabledAudio_asAdmin_ok() {
        $this->assertAdminDeletesAudio(1);
    }

    public function testAudioDelete_disabledAudio_asAdmin_ok() {
        $this->assertAdminDeletesAudio(4);
    }

    public function testAudioDelete_asAdmin_invalid() {
        $this->logInAs('admin');
        $this->ajaxPost('/ja/audio/delete/9999999999');
        $this->assertResponseCode(404);
    }
}
