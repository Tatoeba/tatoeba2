<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TranslationsTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class TranslationsTableTest extends TestCase {
    public $fixtures = array(
        'app.Sentences',
        'app.Links',
        'app.Transcriptions',
        'app.Users'
    );

    function setUp() {
        parent::setUp();
        $this->Translation = TableRegistry::getTableLocator()->get('Translations');
    }

    function tearDown() {
        unset($this->Translation);
        parent::tearDown();
    }

    function testGetTranslationsOf() {
        $result = $this->Translation->getTranslationsOf(5, array());
        $expected = array(
            "问题的根源是，在当今世界，愚人充满了自信，而智者充满了怀疑。",
            "The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.",
            "La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d'assurance, alors que les gens intelligents sont pleins de doute."
        );
        $this->assertEquals($expected, Hash::extract($result, '{n}.text'));
    }

    function _assertFind($sentenceIdsAndTheirExpectedTranslationIds, $langs) {
        $sentenceIds = array_keys($sentenceIdsAndTheirExpectedTranslationIds);
        $result = $this->Translation->getTranslationsOf($sentenceIds, $langs);
        $returned = array_fill_keys(
            $sentenceIds,
            array(0 => array(), 1 => array())
        );
        foreach ($result as $rec) {
            $sentenceId = $rec->sentence_id;
            $returned[$sentenceId][$rec->type][] = $rec->id;
        }
        $this->assertEquals($sentenceIdsAndTheirExpectedTranslationIds, $returned);
    }

    function testFindCheckIds() {
        $checkIf[1] = array(
            0 => array(2, 4, 3), 1 => array(5, 6)
        );
        $this->_assertFind($checkIf, array());
    }

    function testFindWithFilteredDirectTranslation() {
        $checkIf[1] = array(
            array(2), array()
        );
        $this->_assertFind($checkIf, array('cmn'));
    }

    function testFindWithFilteredIndirectTranslation() {
        $checkIf[1] = array(
            array(), array(6)
        );
        $this->_assertFind($checkIf, array('jpn'));
    }

    function testFindWithFilteredMultipleLang() {
        $checkIf[1] = array(
            array(3), array(5)
        );
        $this->_assertFind($checkIf, array('spa', 'deu'));
    }

    function testFindWithoutTranslation() {
        $checkIf[7] = array(
            array(), array()
        );
        $this->_assertFind($checkIf, array());
    }

    function testGetTranslationsOfCheckLangOrder() {
        $result = $this->Translation->getTranslationsOf(1, array());

        $languages = $languages = Hash::extract($result, '{n}.lang');
        $expected = array('cmn', 'deu', 'fra', 'jpn', 'spa');

        $this->assertEquals($expected, $languages);
    }
}
