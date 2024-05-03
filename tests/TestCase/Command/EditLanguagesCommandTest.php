<?php
namespace App\Test\TestCase\Command;

use Cake\Console\Command;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class EditLanguagesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.Audios',
        'app.DisabledAudios',
        'app.Contributions',
        'app.Languages',
        'app.Links',
        'app.ReindexFlags',
        'app.Sentences',
        'app.Tags',
        'app.TagsSentences',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.UsersSentences',
    ];

    const TESTDIR = TMP . 'edit_languages_tests' . DS;

    public static function setUpBeforeClass() {
        new Folder(self::TESTDIR, true, 0755);
    }

    public static function tearDownAfterClass() {
        $folder = new Folder(self::TESTDIR);
        $folder->delete();
    }

    public function setUp() {
        parent::setUp();
        $this->UseCommandRunner();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
    }

    private function create_test_file($ids) {
        $path = self::TESTDIR . 'input_test';
        $file = new File($path);
        $file->write(implode("\n", $ids));
        $file->close();
        return $path;
    }

    public function testExecute_changesLanguage() {
        $path = $this->create_test_file([1, 2]);
        $this->exec("edit_languages admin $path fra");

        $this->assertExitCode(Command::CODE_SUCCESS);

        $sentence = $this->Sentences->get(2);
        $this->assertEquals('fra', $sentence->lang);
    }

    public function successesProvider() {
        // username, ids, language, number of changes
        return [
            'all ids changed to fra' =>
                ['admin', [1, 2, 5], 'fra', 3],
            'some ids changed to fra' =>
                ['admin', [1, 3, 5, 8], 'fra', 2],
            'with wrong ids' =>
                ['admin', [1, 99, 999], 'fra', 1],
            'empty file' => ['admin', [], 'fra', 0],
        ];
    }

    private function countLanguage($ids, $lang) {
        if (empty($ids)) {
            return 0;
        } else {
            return $this->Sentences
                        ->find()
                        ->where(['id IN' => $ids, 'lang' => $lang])
                        ->count();
        }
    }

    /**
     * @dataProvider successesProvider
     **/
    public function testExecute_severalScenarios($user, $ids, $newLanguage, $changes) {
        $path = $this->create_test_file($ids);
        $before = $this->countLanguage($ids, $newLanguage);
        $this->exec("edit_languages $user $path $newLanguage");
        $after = $this->countLanguage($ids, $newLanguage);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertEquals($changes, $after - $before);
    }

    public function failuresProvider() {
        return [
            'without any required argument' => ['edit_languages'],
            'without file' => ['edit_languages admin'],
            'without language' => ['edit_languages admin stdin'],
            'with unknown file' => ['edit_languages admin unknown_file eng'],
            'with invalid language' => ['edit_languages admin stdin invalid'],
            'as unknown user' => ['edit_languages unknown_user stdin eng'],
            'as non-moderator' => ['edit_languages contributor stdin eng'],
        ];
    }

    /**
     * @dataProvider failuresProvider
     */
    public function testExecute_failures($command) {
        $this->exec($command);
        $this->assertExitCode(Command::CODE_ERROR);
    }
}
