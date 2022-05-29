<?php
namespace App\Test\TestCase\Controller\VHosts\Audio;

use App\Controller\VHosts\Audio\MainController;
use App\Test\TestCase\Controller\AudioIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class MainControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AudioIntegrationTestTrait;

    public $fixtures = [
        'app.Audios',
        'app.DisabledAudios',
        'app.Sentences',
        'app.ReindexFlags',
        'app.Links',
        'app.Languages',
    ];

    public function setUp()
    {
        $this->initAudioStorageDir();
    }

    public function tearDown()
    {
        $this->deleteAudioStorageDir();
    }

    public function testUnknownController()
    {
        $this->get("http://audio.example.com/hi/there");
        $this->assertResponseCode(404);
    }

    public function testNoController()
    {
        $this->get("http://audio.example.com/");
        $this->assertResponseCode(404);
    }

    public function testLegacyAudioLink_ok()
    {
        $audioFileContents = $this->createAudioFile(1);
        $this->get("http://audio.example.com/sentences/spa/3.mp3");
        $this->assertResponseOk();
        $this->assertResponseEquals($audioFileContents);
        $this->assertContentType('audio/mpeg');
    }

    public function testLegacyAudioLink_sentence_without_audio()
    {
        $this->get("http://audio.example.com/sentences/cmn/2.mp3");
        $this->assertResponseCode(404);
    }

    public function testLegacyAudioLink_no_such_sentence()
    {
        $this->get("http://audio.example.com/sentences/eng/99999999999.mp3");
        $this->assertResponseCode(404);
    }

    public function testLegacyAudioLink_wrong_language()
    {
        $audioFileContents = $this->createAudioFile(1);
        $this->get("http://audio.example.com/sentences/eng/3.mp3");
        $this->assertResponseCode(404);
    }

    public function testLegacyAudioLink_audio_disabled()
    {
        $audioId = 1;
        $audioFileContents = $this->createAudioFile($audioId);
        $audios = TableRegistry::get('Audios');
        $audio = $audios->get($audioId);
        $audio->enabled = false;
        $audios->save($audio);

        $this->get("http://audio.example.com/sentences/spa/3.mp3");
        $this->assertResponseCode(404);
    }
}
