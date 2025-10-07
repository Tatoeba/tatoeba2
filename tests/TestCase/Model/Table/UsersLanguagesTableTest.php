<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersLanguagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\I18n\I18n;
use Cake\I18n\Time;

class UsersLanguagesTableTest extends TestCase {
    public $fixtures = array(
        'app.users',
        'app.users_languages',
        'app.languages',
        'app.sentences',
        'app.reindex_flags',
        'app.links',
    );
    public $autoFixtures = false;

    function setUp() {
        parent::setUp();
        $this->UsersLanguages = TableRegistry::getTableLocator()->get('UsersLanguages');
        $this->loadFixtures('Users', 'UsersLanguages', 'Languages');
    }

    function tearDown() {
        unset($this->UsersLanguages); 
        parent::tearDown();
    }

    function testGetLanguageInfo_succeeds() {
        $langInfo = $this->UsersLanguages->getLanguageInfo(1);
        $expected = array(
            'language_code' => 'jpn',
            'by_user_id' => 4
        );

        $this->assertEquals($expected, $langInfo);
    }

    function testGetLanguageInfo_fails() {
        $langInfo = $this->UsersLanguages->getLanguageInfo(100);

        $this->assertEquals(null, $langInfo);
    }

    function testSaveUserLanguage_addsLanguage() {
        $lang = 'eng';
        $level = 1;
        $details = 'Teach me please';
        $currentUserId = 4;
        $data = array(
            'language_code' => $lang,
            'level' => $level,
            'details' => $details
        );
        $expected = array(
            'language_code' => $lang,
            'level' => $level, 
            'details' => $details,
            'of_user_id' => $currentUserId,
            'by_user_id' =>  $currentUserId
        );
        $userLanguage = $this->UsersLanguages->saveUserLanguage($data, $currentUserId)
            ->extract(['language_code', 'level', 'details', 'of_user_id', 'by_user_id']);

        $this->assertEquals($expected, $userLanguage);
    } 

    function testSaveUserLanguage_cannotMessUpWithDates() {
        $currentUserId = 4;
        $data = array(
            'language_code' => 'eng',
            'level' => 1,
            'details' => '',
            'created' => '1990-01-01 00:00:00',
            'modified' => '2000-01-01 00:00:00',
        );
        $userLanguage = $this->UsersLanguages->saveUserLanguage($data, $currentUserId);

        $this->assertNotEquals($data['created'], $userLanguage->created);
        $this->assertNotEquals($data['modified'], $userLanguage->modified);
    }

    function testSaveUserLanguage_editsLanguage() {
        $id = 1;
        $level = 2;
        $currentUserId = 4;
        $data = array(
            'id' => $id,
            'level' => $level
        );
        $expected = array(
            'id' => $id,
            'language_code' => 'jpn',
            'level' => $level,
            'of_user_id' => $currentUserId,
            'by_user_id' =>  $currentUserId
        );
        $userLanguage = $this->UsersLanguages->saveUserLanguage($data, $currentUserId)
            ->extract(['id', 'language_code', 'level', 'of_user_id', 'by_user_id']);

        $this->assertEquals($expected, $userLanguage);
    } 

    
    function testSaveUserLanguage_failsBecauseUnknownId() {
        $data = array(
            'id' => 20,
            'level' => 2
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    } 

    function testSaveUserLanguage_failsBecauseUndefinedLanguage() {       
        $data = array(
            'language_code' => 'und',
            'level' => 5
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    }

    function testSaveUserLanguage_failsBecauseInvalidLanguage() {
        $data = array(
            'language_code' => '000',
            'details' => 'Details here.',
            'level' => 5
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    }

    function testSaveUserLanguage_failsBecauseLevelTooHigh() {
        $data = array(
            'language_code' => 'eng',
            'details' => 'Details here.',
            'level' => '6',
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    }

    function testSaveUserLanguage_failsBecauseLevelTooLow() {
        $data = array(
            'language_code' => 'eng',
            'details' => 'Details here.',
            'level' => '-1',
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    }

    function testSaveUserLanguage_failsBecauseLevelInvalid() {
        $data = array(
            'language_code' => 'eng',
            'details' => 'Details here.',
            'level' => 'invalid',
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertEmpty($result);
    }

    function testSaveUserLanguage_succeedsLevelUnknown() {
        $data = array(
            'language_code' => 'eng',
            'details' => 'Details here.',
            'level' => null
        );
        $result = $this->UsersLanguages->saveUserLanguage($data, 1);

        $this->assertNotEmpty($result);
    }

    function testDeleteUserLanguage_succeeds() {
        $result = $this->UsersLanguages->deleteUserLanguage(1, 4);

        $this->assertEquals(true, $result);
    }

    function testDeleteUserLanguage_failsBecauseUserNotAllowed()
    {
        $result = $this->UsersLanguages->deleteUserLanguage(1, 1);
        $this->assertFalse($result);
    }

    function testDeleteUserLanguage_failsBecauseUnknownId() {
        $result = $this->UsersLanguages->deleteUserLanguage(9999999, 4);
        $this->assertFalse($result);
    }

    function testGetLanguageInfoOfUser_succeeds() {
        $lang = 'jpn';
        $userId = 4;
        $result = $this->UsersLanguages->getLanguageInfoOfUser($lang, $userId);
        $this->assertEquals(1, $result->id);
    }

    function testGetLanguageInfoOfUser_failsBecauseWrongLang() {
        $lang = 'eng';
        $userId = 4;
        $result = $this->UsersLanguages->getLanguageInfoOfUser($lang, $userId);
        $this->assertEquals(null, $result);
    }

    function testGetLanguageInfoOfUser_failsBecauseWrongUser() {
        $lang = 'jpn';
        $userId = 1;
        $result = $this->UsersLanguages->getLanguageInfoOfUser($lang, $userId);
        $this->assertEquals(null, $result);
    }

    function testGetNumberOfUsersForEachLanguage() {
        $result = $this->UsersLanguages->getNumberOfUsersForEachLanguage();
        $this->assertEquals(2, count($result));
    }

    function testSaveUserLanguage_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');
        $now = new Time('2020-01-02 03:04:05');
        Time::setTestNow($now);

        $added = $this->UsersLanguages->saveUserLanguage(
            ['language_code' => 'npi', 'details' => ''],
            100
        );
        $returned = $this->UsersLanguages->get($added->id);
        $this->assertEquals($now, $returned->created);
        $this->assertEquals($now, $returned->modified);

        Time::setTestNow();
        I18n::setLocale($prevLocale);
    }

    function assertSentencesFlaggedForReindex($expected) {
        $result = TableRegistry::getTableLocator()->get('ReindexFlags')
            ->find('list', ['valueField' => 'sentence_id'])
            ->select(['sentence_id'])
            ->order('sentence_id')
            ->toList();
        $this->assertEquals($expected, $result);
    }

    function testSaveUserLanguage_reindexSentencesOnLevelDrop() {
        $this->loadFixtures();
        $nativeJpn = $this->UsersLanguages->get(3);
        $nativeJpn->level = 4;

        $this->UsersLanguages->save($nativeJpn);

        // should reindex sentences 6, 10, 56 and 57
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 4, 6, 10, 55, 56, 57, 65]);
    }

    function testSaveUserLanguage_reindexSentencesOnLevelUp() {
        $this->loadFixtures();
        $learnerFra = $this->UsersLanguages->get(5);
        $learnerFra->level = 5;

        $this->UsersLanguages->save($learnerFra);

        // should reindex sentences 4, 8, 12 and 35
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 3, 4, 5, 6, 8, 10, 12, 35]);
    }

    function testSaveUserLanguage_noReindexSentencesOnLearnerLevelChange() {
        $this->loadFixtures();
        $learnerFra = $this->UsersLanguages->get(5);
        $learnerFra->level = 3;

        $this->UsersLanguages->save($learnerFra);

        $this->assertSentencesFlaggedForReindex([]);
    }

    function testSaveUserLanguage_reindexSentencesOnLanguageDeletion() {
        $this->loadFixtures();
        $nativeJpn = $this->UsersLanguages->get(3);

        $this->UsersLanguages->delete($nativeJpn);

        // should reindex sentences 6, 10, 56 and 57
        // along with all direct and indirect translations
        $this->assertSentencesFlaggedForReindex([1, 2, 4, 6, 10, 55, 56, 57, 65]);
    }

    function testSaveUserLanguage_noReindexSentencesOnLearningLanguageDeletion() {
        $this->loadFixtures();
        $learnerFra = $this->UsersLanguages->get(5);

        $this->UsersLanguages->delete($learnerFra);

        $this->assertSentencesFlaggedForReindex([]);
    }
}
