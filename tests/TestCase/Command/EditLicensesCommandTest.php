<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Console\Command;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class EditLicensesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.sentences',
        'app.reindex_flags',
        'app.transcriptions',
        'app.contributions',
        'app.tags',
        'app.tags_sentences',
        'app.users',
        'app.users_languages',
    ];

    const TESTDIR = TMP . 'edit_licenses_tests' . DS;

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
        $this->Contributions = TableRegistry::getTableLocator()->get('Contributions');
    }

    private function create_test_file($ids) {
        $path = self::TESTDIR . 'input_test';
        $file = new File($path);
        $file->write(implode("\n", $ids));
        $file->close();
        return $path;
    }

    public function testExecute_changesLicense() {
        $path = $this->create_test_file([1, 2]);
        $this->exec("edit_licenses admin $path 'Licensing issue'");

        $this->assertExitCode(Command::CODE_SUCCESS);

        $sentence = $this->Sentences->get(1);
        $this->assertEquals('', $sentence->license);

        $contribution = $this->Contributions->find()->where(['sentence_id' => 2])->last()->toArray();
        $expected = [
            'sentence_id' => 2,
            'type' => 'license',
            'action' => 'update',
            'text' => '',
        ];
        $this->assertArraySubset($expected, $contribution);
    }

    public function scenariosProvider() {
        // username, license, ids, at least one change, ignored sentence
        return [
            'all ids changed to CC0 1.0' =>
                ['admin', 'CC0 1.0', [23, 11, 9], true, false],
            'some ids changed to CC BY 2.0 FR' =>
                ['admin', 'CC BY 2.0 FR', [51, 52, 53], true, "53 ignored: License is already"],
            'with wrong ids' =>
                ['admin', 'CC0 1.0', [1, 99, 402, 5], true, "99 ignored: Record not found"],
            'empty file' => ['admin', 'CC BY 2.0 FR', [], false, false],
            'as non-admin' =>
                ['contributor', 'CC0 1.0', [1, 2], false, "1 ignored: Cannot change license"],
            'all ids changed to licensing issue' => ['admin', 'Licensing issue', [1, 2, 3], true, false],
        ];
    }

    /**
     * @dataProvider scenariosProvider
     **/
    public function testExecute_severalScenarios($user, $newLicense, $ids, $hasOneChange, $containsIgnored) {
        $path = $this->create_test_file($ids);

        $this->exec("edit_licenses $user $path '$newLicense'");

        $this->assertExitCode(Command::CODE_SUCCESS);

        $count = count($ids);
        if ($count > 0) {
            $this->assertOutputContains("$count row(s) proceeded:");
        } else {
            $this->assertOutputContains("There was nothing to do.");
        }

        if ($hasOneChange) {
            $this->assertOutputContains("license changed from");
        } else {
            $this->assertOutputNotContains("license changed from");
        }

        if ($containsIgnored) {
            $this->assertOutputContains($containsIgnored);
        };
    }

    public function testExecute_withoutRequiredArguments() {
        $this->exec("edit_licenses");
        $this->assertExitCode(Command::CODE_ERROR);

        $this->exec("edit_licenses admin");
        $this->assertExitCode(Command::CODE_ERROR);

        $path = $this->create_test_file([]);
        $this->exec("edit_licenses admin $path");
        $this->assertExitCode(Command::CODE_ERROR);
    }

    public function testExecute_withNonexistentFile() {
        $this->exec('edit_licenses admin nonexistentfile "CC0 1.0"');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    public function testExecute_withInvalidLicense() {
        $path = $this->create_test_file([1, 2, 3]);
        $this->exec("edit_licenses admin $path 'invalid'");
        $this->assertExitCode(Command::CODE_ERROR);
        $this->exec("edit_licenses admin $path ''");
        $this->assertExitCode(Command::CODE_ERROR);
    }

    public function testExecute_asUnknownUser() {
        $path = $this->create_test_file([1, 2, 3]);
        $this->exec("edit_licenses unknown_user $path 'CC BY 2.0 FR'");
        $this->assertExitCode(Command::CODE_ERROR);
    }

    public function testExecute_dryRun() {
        $path = $this->create_test_file([1]);
        $sentenceBefore = $this->Sentences->get(1);
        $contributionsBefore = $this->Contributions->find('all')->count();
        $this->exec("edit_licenses -n admin $path 'CC0 1.0'");
        $sentenceAfter = $this->Sentences->get(1);
        $contributionsAfter = $this->Contributions->find('all')->count();
        $this->assertEquals($sentenceBefore, $sentenceAfter);
        $this->assertEquals($contributionsBefore, $contributionsAfter);
    }
}
