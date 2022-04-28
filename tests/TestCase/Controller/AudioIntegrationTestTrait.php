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

    private function getAudioFilePath($audioId) {
        try {
            return TableRegistry::get('Audios')->get($audioId)->file_path;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return TableRegistry::get('DisabledAudios')->get($audioId)->file_path;
        }
    }

    private function createAudioFile($audioId) {
        $contents = md5($audioId, true);
        $audioPath = $this->getAudioFilePath($audioId);
        mkdir(dirname($audioPath), 0777, true);

        $file = new File($audioPath, true);
        $file->write($contents);
        $file->close();

        return $contents;
    }

    private function deleteAudioStorageDir() {
        $folder = new Folder($this->testAudioDir);
        $folder->delete();
    }

    private function initAudioStorageDir() {
        Configure::write('Recordings.path', $this->testAudioDir);
        $folder = new Folder($this->testAudioDir);
        $folder->delete();
        $folder->create($this->testAudioDir);
    }
}
