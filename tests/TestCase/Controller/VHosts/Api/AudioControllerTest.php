<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Test\TestCase\Controller\AudioIntegrationTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class AudioControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use AudioIntegrationTestTrait;

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Users',
    ];

    public function testDownload_ok()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(1);
        $this->get("http://api.example.com/unstable/audio/download/1");
        $this->assertResponseOk();
        $this->assertResponseEquals($audioFileContents);
        $this->assertHeader('Content-Disposition', 'attachment; filename="3-1.mp3"');

        $this->deleteAudioStorageDir();
    }

    public function testDownload_fileMissing()
    {
        $this->initAudioStorageDir();

        $this->get("http://api.example.com/unstable/audio/download/1");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_nonExistingAudio()
    {
        $this->get("http://api.example.com/unstable/audio/download/9999999999");
        $this->assertResponseCode(404);
    }

    public function testDownload_nonReusableAudio()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(6);
        $this->get("http://api.example.com/unstable/audio/download/6");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }
}
