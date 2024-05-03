<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LanguagesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LanguagesTableTest extends TestCase {
    public $fixtures = array(
        'app.Languages',
    );

    function setUp() {
        parent::setUp();
        $this->Languages = TableRegistry::getTableLocator()->get('Languages');
    }

    function tearDown() {
        unset($this->Languages);
        parent::tearDown();
    }

    function testGetSentencesStatistics() {
        $result = $this->Languages->getSentencesStatistics(5);
        $this->assertEquals(5, count($result));
        $this->assertLessThanOrEqual($result[0]->sentences, $result[4]->sentences);
    }

    function testGetAudioStats() {
        $stats = $this->Languages->getAudioStats();
        $result = Hash::combine($stats, '{n}.lang', '{n}.total');
        $expected = ['spa' => 1, 'fra' => 2];
        $this->assertEquals($expected, $result);
    }

    function testGetNativeSpeakersStatistics() {
        $result = $this->Languages->getNativeSpeakersStatistics();
        $this->assertEquals(13, count($result));
    }

    function testGetUsersLanguagesStatistics() {
        $result = $this->Languages->getNativeSpeakersStatistics();
        $this->assertEquals(13, count($result));
    }

    function testIncrementCountForLanguage() {
        $lang = 'eng';
        $before = $this->Languages->find()
            ->where(['code' => $lang])
            ->first();
        $this->Languages->incrementCountForLanguage($lang);
        $after = $this->Languages->find()
            ->where(['code' => $lang])
            ->select(['sentences'])
            ->first();
        $this->assertEquals(1, $after->sentences - $before->sentences);
    }

    function testDecrementCountForLanguage() {
        $lang = 'eng';
        $before = $this->Languages->find()
            ->where(['code' => $lang])
            ->first();
        $this->Languages->decrementCountForLanguage($lang);
        $after = $this->Languages->find()
            ->where(['code' => $lang])
            ->select(['sentences'])
            ->first();
        $this->assertEquals(-1, $after->sentences - $before->sentences);
    }

    function testGetTotalSentencesNumber() {
        $expected = 56;
        $n = $this->Languages->getTotalSentencesNumber();
        $this->assertEquals($expected, $n);
    }

    function testGetMilestonedStatistics() {
        $milestones = [10, 5, 0];

        $result = $this->Languages->getMilestonedStatistics($milestones);

        foreach ($milestones as $m) {
            $this->assertArrayHasKey($m, $result);
        }
        foreach ($result as $milestone => $langs) {
            foreach ($langs as $lang) {
                $this->assertGreaterThanOrEqual($milestone, $lang->sentences);
            }
        }
    }
}
