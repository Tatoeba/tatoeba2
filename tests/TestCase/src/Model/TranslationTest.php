<?php
namespace App\Test\TestCase\Model;

App::import('Model', 'Translation');

class TranslationTest extends CakeTestCase {
    public $fixtures = array(
        'app.sentence',
        'app.link',
        'app.transcription',
        'app.user',
    );

    function startTest($method) {
        $this->Translation = ClassRegistry::init('Translation');
    }

    function endTest($method) {
        unset($this->Translation);
        ClassRegistry::flush();
    }

    function testFindCheckAllFields() {
        $this->Translation->unbindModel(
            array('hasMany' => array('Transcription'))
        );
        $result = $this->Translation->getTranslationsOf(5, array());
        $expected = array(
                array(
                    'Translation' => array(
                        'id' => "2",
                        'text' => "问题的根源是，在当今世界，愚人充满了自信，而智者充满了怀疑。",
                        'user_id' => "7",
                        'lang' => "cmn",
                        'correctness' => "0",
                        'script' => 'Hans',
                        'type' => '0',
                        'sentence_id' => '5',
                    ),
                ),
                array(
                    'Translation' => array(
                        'id' => "1",
                        'text' => "The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.",
                        'user_id' => "7",
                        'lang' => "eng",
                        'correctness' => "0",
                        'script' => null,
                        'type' => '1',
                        'sentence_id' => '5',
                    ),
                ),
                array(
                    'Translation' => array(
                        'id' => "4",
                        'text' => "La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d'assurance, alors que les gens intelligents sont pleins de doute.",
                        'user_id' => "7",
                        'lang' => "fra",
                        'correctness' => "0",
                        'script' => null,
                        'type' => '1',
                        'sentence_id' => '5',
                    ),
                ),
        );
        $this->assertEqual($expected, $result);
    }

    function _assertFind($sentenceIdsAndTheirExpectedTranslationIds, $langs) {
        $sentenceIds = array_keys($sentenceIdsAndTheirExpectedTranslationIds);
        $result = $this->Translation->getTranslationsOf($sentenceIds, $langs);
        $returned = array_fill_keys(
            $sentenceIds,
            array(0 => array(), 1 => array())
        );
        foreach ($result as $rec) {
            $rec = $rec['Translation'];
            $sentenceId = $rec['sentence_id'];
            $returned[$sentenceId][$rec['type']][] = $rec['id'];
        }
        $this->assertEqual($sentenceIdsAndTheirExpectedTranslationIds, $returned);
    }

    function testFindCheckIds() {
        $checkIf[1] = array(
            0 => array(2, 4, 3), 1 => array(5, 6)
        );
        $this->_assertFind($checkIf, array());
    }

    function testFindCheckSeveralIds() {
        $checkIf[1] = array(
            array(2, 4, 3), array(5, 6)
        );
        $checkIf[2] = array(
            array(5, 1, 4), array(6, 3)
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

        $languages = $languages = Set::classicExtract($result, '{n}.Translation.lang');
        $expected = array('cmn', 'deu', 'fra', 'jpn', 'spa');

        $this->assertEquals($expected, $languages);
    }
}
