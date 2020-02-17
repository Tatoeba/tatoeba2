<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Console\Command;

class FixHashesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.sentences',
        'app.reindex_flags',
        'app.transcriptions',
        'app.contributions',
        'app.tags',
        'app.tags_sentences'
    ];

    public function setUp() {
        parent::setUp();
        $this->UseCommandRunner();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
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
        $path = stream_get_meta_data(tmpfile())['uri'];
        file_put_contents($path, implode("\n", $ids));
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
}
