<?php
namespace App\Test\TestCase\Command;

use Cake\Console\Command;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class EditOwnersCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.links',
        'app.reindex_flags',
        'app.sentences',
        'app.users',
        'app.users_languages',
    ];

    const TESTDIR = TMP . 'edit_owners_tests' . DS;

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

    public function testExecute_changesOwner() {
        $ids = [14, 40, 41];
        $path = $this->create_test_file([14, 40, 41]);
        $this->exec("edit_owners admin $path contributor");

        $this->assertExitCode(Command::CODE_SUCCESS);

        foreach ($ids as $id) {
            $sentence = $this->Sentences->get($id);
            $this->assertEquals(4, $sentence->user_id);
        }
    }

    public function successesProvider() {
        // username, ids, owner, number of changes, log
        return [
            'all ids adopted by contributor' =>
                ['contributor', [14, 15, 16], 'contributor', 3],
            'all ids adopted by advanced_contributor' =>
                ['advanced_contributor', [14, 15, 16, 40, 41], 'advanced_contributor', 5],
            'some ids adopted by contributor' =>
                ['contributor', [14, 15, 16, 40, 41, 8], 'contributor', 3, "id 8 - Record not found or could not save changes"],
            'some ids adopted by contributor using admin power' =>
                ['admin', [14, 15, 16, 40, 41, 8], 'contributor', 5, "id 8 - Record not found or could not save changes"],
            'with wrong ids' =>
                ['admin', [14, 999999, 999998], 'contributor', 1, "id 999999 - Record not found or could not save changes"],
            'empty file' => ['admin', [], 'contributor', 0],
        ];
    }

    private function countOwned($ids, $owner) {
        if (empty($ids)) {
            return 0;
        } else {
            $ownerId = $this->Sentences->Users->findByUsername($owner)->first()->id;
            return $this->Sentences
                        ->find()
                        ->where(['id IN' => $ids, 'user_id' => $ownerId])
                        ->count();
        }
    }

    /**
     * @dataProvider successesProvider
     **/
    public function testExecute_severalScenarios($user, $ids, $newOwner, $changes, $log = null) {
        $path = $this->create_test_file($ids);
        $before = $this->countOwned($ids, $newOwner);
        $this->exec("edit_owners $user $path $newOwner");
        $after = $this->countOwned($ids, $newOwner);
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertEquals($changes, $after - $before);
        $count = count($ids);
        if ($count > 0) {
            $this->assertOutputContains("$count row(s) proceeded:");
        } else {
            $this->assertOutputContains("There was nothing to do.");
        }
        if ($log) {
            $this->assertOutputContains($log);
        }
    }

    public function failuresProvider() {
        return [
            'without any required argument' => ['edit_owners'],
            'without file' => ['edit_owners admin'],
            'without owner' => ['edit_owners admin stdin'],
            'with unknown file' => ['edit_owners admin unknown_file contributor'],
            'with unknown owner' => ['edit_owners admin stdin unknown_owner'],
            'as unknown user' => ['edit_owners unknown_user stdin contributor'],
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
