<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersSentencesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class UsersSentencesTest extends TestCase {
    public $fixtures = array(
        'app.users',
        'app.users_sentences',
        'app.sentences'
    );

    function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        $this->UsersSentences = TableRegistry::getTableLocator()->get('UsersSentences');
    }

    function tearDown() {
        unset($this->UsersSentences);
        parent::tearDown();
    }

    function testSaveSentence_addsSentence() {
        $sentenceId = 1;
        $correctness = 1;
        $userId = 1;

        $this->UsersSentences->saveSentence(
            $sentenceId, $correctness, $userId
        );

        $userSentence = $this->UsersSentences->findBySentenceIdAndUserId(
            $sentenceId, $userId
        )->first()->old_format;
        $expected = array(
            'user_id' => $userId,
            'sentence_id' => $sentenceId,
            'correctness' => $correctness,
        );

        $result = array_intersect_key($userSentence['UsersSentences'], $expected);
        $this->assertEquals($expected, $result);
    }

    function testSaveSentence_editsDirtySentence() {
        $sentenceId = 2;
        $correctness = 1;
        $userId = 1;

        $this->UsersSentences->saveSentence(
            $sentenceId, $correctness, $userId
        );

        $userSentence = $this->UsersSentences->findBySentenceIdAndUserId(
            $sentenceId, $userId
        )->first()->old_format;
        $expected = array(
            'user_id' => $userId,
            'sentence_id' => $sentenceId,
            'correctness' => $correctness,
            'dirty' => false
        );

        $result = array_intersect_key($userSentence['UsersSentences'], $expected);
        $this->assertEquals($expected, $result);
    }

    function testDeleteSentence_succeeds() {
        $sentenceId = 2;
        $userId = 1;

        $result = $this->UsersSentences->deleteSentence(
            $sentenceId, $userId
        );

        $this->assertEquals($result, true);
    }

    function testDeleteSentence_fails() {
        $sentenceId = 100;
        $userId = 100;

        $result = $this->UsersSentences->deleteSentence(
            $sentenceId, $userId
        );

        $this->assertEquals($result, false);
    }

    function testCorrectnessForSentence_succeeds() {
        $result = $this->UsersSentences->correctnessForSentence(2, 1);
        $this->assertEquals(1, $result);
    }

    function testCorrectnessForSentence_fails() {
        $result = $this->UsersSentences->correctnessForSentence(999, 999);
        $this->assertEquals(-2, $result);
    }

    function testGetCorrectnessForSentence_hasResult() {
        $result = $this->UsersSentences->getCorrectnessForSentence(2);
        $this->assertEquals(1, count($result));
    }

    function testGetCorrectnessForSentence_hasNoResult() {
        $result = $this->UsersSentences->getCorrectnessForSentence(999);
        $this->assertEquals(0, count($result));
    }
}
