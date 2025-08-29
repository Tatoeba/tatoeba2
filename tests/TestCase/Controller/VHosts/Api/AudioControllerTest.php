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

    public function testDownload_invalidId()
    {
        $this->get("http://api.example.com/unstable/audio/notAnInt/file");
        $this->assertResponseCode(400);
    }

    public function testDownload_ok()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(1);
        $this->get("http://api.example.com/unstable/audio/1/file");
        $this->assertResponseOk();
        $this->assertResponseEquals($audioFileContents);
        $this->assertHeader('Content-Disposition', 'attachment; filename="3-1.mp3"');

        $this->deleteAudioStorageDir();
    }

    public function testDownload_fileMissing()
    {
        $this->initAudioStorageDir();

        $this->get("http://api.example.com/unstable/audio/1/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_nonExistingAudio()
    {
        $this->get("http://api.example.com/unstable/audio/9999999999/file");
        $this->assertResponseCode(404);
    }

    public function testDownload_nonReusableAudio_fromUserAudioLicenseField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(6);
        $this->get("http://api.example.com/unstable/audio/6/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_nonReusableAudio_fromExternalField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(2);
        $this->get("http://api.example.com/unstable/audio/2/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_reusableAudio_fromExternalField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(3);
        $this->get("http://api.example.com/unstable/audio/3/file");
        $this->assertResponseOk();

        $this->deleteAudioStorageDir();
    }
}
