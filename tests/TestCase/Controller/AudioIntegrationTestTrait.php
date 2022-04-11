<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;

/**
 * A trait intended to make audio testing easier.
 */
trait AudioIntegrationTestTrait
{
    private $testAudioDir = TMP.'audio_tests'.DS;

    private function createAudioFile($audioId) {
        $contents = md5($audioId, true);
        $audios = TableRegistry::get('Audios');
        $audio = $audios->get($audioId);
        mkdir(dirname($audio->file_path), 0777, true);

        $file = new File($audio->file_path, true);
        $file->write($contents);
        $file->close();

        return $contents;
    }

    private function initAudioStorageDir() {
        Configure::write('Recordings.path', $this->testAudioDir);
        $folder = new Folder($this->testAudioDir);
        $folder->delete();
        $folder->create($this->testAudioDir);
    }
}
