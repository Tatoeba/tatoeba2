<?php
namespace App\Test\TestCase\Command;

use App\Command\FixLinksTableLangsCommand;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class FixLinksTableLangsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public $fixtures = [
        'app.Sentences',
        'app.Links',
    ];

    public function setUp() {
        parent::setUp();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
        $this->Links = TableRegistry::getTableLocator()->get('Links');
        $this->useCommandRunner();
    }

    public function testExecute_wrongLangGetsUpdated() {
        $id = 56;
        $sentence = $this->Sentences->get($id);
        $oldLang1 = $this->Links->find()
                                ->where(['translation_id' => $id])
                                ->first()
                                ->translation_lang;
        $oldLang2 = $this->Links->find()
                                ->where(['sentence_id' => $id])
                                ->first()
                                ->sentence_lang;

        $this->exec('fix_links_table_langs');

        $newLang1 = $this->Links->find()
                                ->where(['translation_id' => $id])
                                ->first()
                                ->translation_lang;
        $newLang2 = $this->Links->find()
                                ->where(['sentence_id' => $id])
                                ->first()
                                ->sentence_lang;
        $this->assertNotEquals($oldLang1, $newLang1);
        $this->assertNotEquals($oldLang2, $newLang2);
        $this->assertEquals($sentence->lang, $newLang1);
        $this->assertEquals($sentence->lang, $newLang2);
    }

    public function testExecute_correctLangDoesNotGetUpdated() {
        $id = 56;
        $conditions = [ 'not' => [
            'OR' => [
                'translation_id' => $id,
                'sentence_id'    => $id,
            ]
        ]];
        $otherLinksBefore = $this->Links
                                 ->find()
                                 ->where($conditions)
                                 ->all()
                                 ->toArray();

        $this->exec('fix_links_table_langs');

        $otherLinksAfter = $this->Links
                                ->find()
                                ->where($conditions)
                                ->all()
                                ->toArray();
        $this->assertEquals($otherLinksBefore, $otherLinksAfter);
    }

    public function testExecute_logs() {
        $this->exec('fix_links_table_langs');

        $log = [
            'Processing field sentence_lang...',
            'sentence_lang: found 1 sentence(s) having incorrect language.',
            'sentence_lang: fixed 1 row(s) in links table.',
            'Processing field translation_lang...',
            'translation_lang: found 1 sentence(s) having incorrect language.',
            'translation_lang: fixed 1 row(s) in links table.',
        ];
        foreach ($log as $line) {
            $this->assertOutputContains($line);
        }
    }
}
