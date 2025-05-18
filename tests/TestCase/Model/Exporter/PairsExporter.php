<?php
namespace App\Test\TestCase\Exporter;

use App\Model\Exporter\PairsExporter;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class PairsExporterTest extends TestCase
{
    public $fixtures = [
        'app.Links',
        'app.Users',
        'app.Sentences',
    ];

    public function testQuery() {
        $expected = [
            [ 4, "La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d'assurance, alors que les gens intelligents sont pleins de doute.", 6, "その問題の根本原因は、現代の世界において、賢明な人々が猜疑心に満ちている一方で、愚かな人々が自信過剰であるということである。" ],
            [ 55, 'Je peux utiliser ton téléphone ?', 57, '電話使ってもいいかな。' ],
        ];
        $options = [
            'from' => 'fra',
            'to'   => 'jpn',
            'fields' => ['id', 'text', 'trans_id', 'trans_text'],
            'format' => 'tsv',
        ];
        $PE = new PairsExporter($options, null);

        $result = $PE->getQuery()->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testQueryReverse() {
        $expected = [
            [ 6, "その問題の根本原因は、現代の世界において、賢明な人々が猜疑心に満ちている一方で、愚かな人々が自信過剰であるということである。", 4, "La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d'assurance, alors que les gens intelligents sont pleins de doute." ],
            [ 57, '電話使ってもいいかな。', 55, 'Je peux utiliser ton téléphone ?' ],
        ];
        $options = [
            'from' => 'jpn',
            'to'   => 'fra',
            'fields' => ['id', 'text', 'trans_id', 'trans_text'],
            'format' => 'tsv',
        ];
        $PE = new PairsExporter($options, null);

        $result = $PE->getQuery()->toArray();

        $this->assertEquals($expected, $result);
    }

    private function _disableCallbacks($class) {
        foreach($class->eventManager()->matchingListeners('') as $event => $values) {
            $class->eventManager()->off($event);
        }
    }

    public function testQuery_withGhostLinks() {
        $Links = TableRegistry::get('Links');
        $this->_disableCallbacks($Links);
        $Links->save($Links->newEntity([
            'sentence_id' => 4,
            'sentence_lang' => 'fra',
            'translation_id' => 123456,
            'translation_lang' => 'jpn',
        ]));
        $Links->save($Links->newEntity([
            'sentence_id' => 123445,
            'sentence_lang' => 'fra',
            'translation_id' => 6,
            'translation_lang' => 'jpn',
        ]));

        $this->testQuery();
    }
}
