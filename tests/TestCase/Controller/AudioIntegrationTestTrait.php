<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Utility\Filesystem;

/**
 * A trait intended to make audio testing easier.
 */
trait AudioIntegrationTestTrait
{
    private $testAudioDir = TMP.'audio_tests'.DS;

    private function getAudioFilePath($audioId) {
        try {
            return $this->fetchTable('Audios')->get($audioId)->file_path;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->fetchTable('DisabledAudios')->get($audioId)->file_path;
        }
    }

    private function createAudioFile($audioId) {
        $contents = md5($audioId, true);
        $audioPath = $this->getAudioFilePath($audioId);
        mkdir(dirname($audioPath), 0777, true);
        file_put_contents($audioPath, $contents);

        return $contents;
    }

    private function deleteAudioStorageDir() {
        (new Filesystem())->deleteDir($this->testAudioDir);
    }

    private function initAudioStorageDir() {
        Configure::write('Recordings.path', $this->testAudioDir);
        $fs = new Filesystem();
        $fs->deleteDir($this->testAudioDir);
        $fs->mkdir($this->testAudioDir);
    }
}
