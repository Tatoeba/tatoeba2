<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class AudioControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.languages',
        'app.private_messages',
        'app.sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.wiki_articles',
    ];

    private $testAudioDir = TMP.'audio_tests'.DS;

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
            [ '/en/audio/download/1', null, 404 ],            # missing file
            [ '/en/audio/download/999999999999', null, 404 ], # unknown audio
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testAudioControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    private function createAudioFile($audioId, $contents) {
        $audios = TableRegistry::get('Audios');
        $audio = $audios->get($audioId);
        mkdir(dirname($audio->file_path), 0777, true);

        $file = new File($audio->file_path, true);
        $file->write($contents);
        $file->close();

        return $contents;
    }

    public function testAudioDownload_ok() {
        Configure::write('Recordings.path', $this->testAudioDir);
        $folder = new Folder($this->testAudioDir);
        $folder->delete();
        $folder->create($this->testAudioDir);

        $someBinaryData = "\x96\xa1\x03\xb9\x95";
        $this->createAudioFile(1, $someBinaryData);
        $this->get('/en/audio/download/1');
        $this->assertResponseOk();
        $this->assertResponseEquals($someBinaryData);
        $this->assertHeader('Content-Disposition', 'attachment; filename="3-1.mp3"');

        Configure::write('Recordings.path', $this->testAudioDir);
        $folder = new Folder($this->testAudioDir);
        $folder->delete();
        $folder->create($this->testAudioDir);
    }
}
