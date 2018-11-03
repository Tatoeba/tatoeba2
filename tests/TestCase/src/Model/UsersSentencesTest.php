<?php
namespace App\Test\TestCase\Model;

use App\Model\UsersSentences;
use Cake\TestSuite\TestCase;

class UsersSentencesTest extends TestCase {
    public $fixtures = array(
        'app.users',
        'app.users_sentences',
        'app.sentences'
    );

    function setUp() {
        parent::setUp();
        $this->UsersSentences = ClassRegistry::init('UsersSentences');
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
        );
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
        );
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
}
