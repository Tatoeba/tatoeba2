<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersSentencesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\I18n\I18n;
use Cake\I18n\Time;

class UsersSentencesTest extends TestCase {
    public $fixtures = array(
        'app.users',
        'app.users_sentences',
        'app.sentences'
    );

    function setUp() {
        parent::setUp();
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
        )->first()->extract(['user_id', 'sentence_id', 'correctness']);
        $expected = array(
            'user_id' => $userId,
            'sentence_id' => $sentenceId,
            'correctness' => $correctness,
        );

        $this->assertEquals($expected, $userSentence);
    }

    function testSaveSentence_addsSentence_failsInvalidCorrectnessOnCreate() {
        $sentenceId = 1;
        $correctness = 1234;
        $userId = 1;

        $result = $this->UsersSentences->saveSentence(
            $sentenceId, $correctness, $userId
        );
        $this->assertFalse($result);
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
        )->first()->extract(['user_id', 'sentence_id', 'correctness', 'dirty']);
        $expected = array(
            'user_id' => $userId,
            'sentence_id' => $sentenceId,
            'correctness' => $correctness,
            'dirty' => false
        );

        $this->assertEquals($expected, $userSentence);
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

    function testSaveSentence_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');
        $now = new Time('2020-01-02 03:04:05');
        Time::setTestNow($now);

        $this->UsersSentences->saveSentence(1, 1, 4);
        $returned = $this->UsersSentences->findBySentenceIdAndUserId(1, 4)->first();
        $this->assertEquals($now, $returned->created);

        Time::setTestNow();
        I18n::setLocale($prevLocale);
    }
}
