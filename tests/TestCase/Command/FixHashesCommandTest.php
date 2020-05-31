<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Console\Command;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class FixHashesCommandTest extends TestCase
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

    const TESTDIR = TMP . 'fix_hashes_tests' . DS;

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

    public function testExecute_completeDatabase() {
        $this->exec('fix_hashes Sentences');
        $this->assertOutputContains(
            sprintf('%u rows checked', $this->Sentences->find()->count())
        );
        $this->assertOutputContains('8 rows changed');

        $hash = $this->Sentences->get(3)->hash;
        $modified = $this->Sentences->get(3)->modified;
        $this->assertEquals("2hfhma4\0\0\0\0\0\0\0\0\0", $hash);
        $this->assertEquals("2014-04-15 00:33:18", $modified);

        $hash = $this->Sentences->get(42)->hash;
        $modified = $this->Sentences->get(42)->modified;
        $this->assertEquals("23jek2o\0\0\0\0\0\0\0\0\0", $hash);
        $this->assertEquals("2017-04-09 11:39:02", $modified);
    }

    public function inputProvider() {
        return [
            'only correct ids' => [[23, 11, 24, 9, 32, 1, 39, 43, 7], 3, false],
            'with wrong ids' => [[1, 99, 402, 5], 0, true],
            'empty file' => [[], 0, false]
        ];
    }

    /**
     * @dataProvider inputProvider
     **/
    public function testExecute_withInputOption($ids, $nbrOfChanges, $containsIgnored) {
        $path = self::TESTDIR . 'input_test';
        $file = new File($path);
        $file->write(implode("\n", $ids));
        $file->close();

        $this->exec(format('fix_hashes -i {path} Sentences', ['path' => $path]));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains(format('{n} rows checked', ['n' => count($ids)]));

        if ($nbrOfChanges > 0) {
            $this->assertOutputContains(format('{n} rows changed', ['n' => $nbrOfChanges]));
        } else {
            $this->assertOutputContains('no problems found');
        }

        if ($containsIgnored) {
            $this->assertErrorContains('ignored');
        } else {
            $this->assertErrorEmpty();
        }
    }

    public function testExecute_withNonexistentFile() {
        $this->exec('fix_hashes -i nonexistentfile Sentences');
        $this->assertExitCode(Command::CODE_ERROR);
    }

    private function _getLatestContributor($sentence_id) {
        return $this->Contributions->find()
               ->select(['username' => 'Users.username'])
               ->where(['sentence_id' => $sentence_id])
               ->orderDesc('datetime')
               ->contain('Users')
               ->first()
               ->username;
    }

    public function testExecute_asDefaultUser() {
        $this->exec('fix_hashes Sentences');

        $storedUser = $this->_getLatestContributor(11);
        $this->assertEquals('FixHashesCommand', $storedUser);
    }

    public function testExecute_asProvidedUser() {
        $this->exec('fix_hashes -u admin Sentences');

        $storedUser = $this->_getLatestContributor(11);
        $this->assertEquals('admin', $storedUser);
    }

    public function testExecute_asUnknownUser() {
        $this->exec('fix_hashes -u unknown_user Sentences');
        $this->assertExitCode(Command::CODE_ERROR);
    }
}
