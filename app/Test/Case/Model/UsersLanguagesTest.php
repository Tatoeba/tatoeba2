<?php
App::uses('UsersLanguages', 'Model');

class UsersLanguagesTest extends CakeTestCase {
    public $fixtures = array(
        'app.user',
        'app.users_language',
        'app.language'
    );

    function setUp() {
        parent::setUp();
        $this->UsersLanguages = ClassRegistry::init('UsersLanguages');
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
        $userLanguage = $this->UsersLanguages->saveUserLanguage($data, $currentUserId);
        $result = array_intersect_key($userLanguage['UsersLanguages'], $expected);

        $this->assertEquals($expected, $result);
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
        $userLanguage = $this->UsersLanguages->saveUserLanguage($data, $currentUserId);
        $result = array_intersect_key($userLanguage['UsersLanguages'], $expected);

        $this->assertEquals($expected, $result);
    } 

    function testSaveUserLanguage_fails() {
        $data = array(
            'id' => 20,
            'level' => 2
        );
        $result1 = $this->UsersLanguages->saveUserLanguage($data, 1);
        $data = array(
            'language_code' => 'und',
            'level' => 5
        );
        $result2 = $this->UsersLanguages->saveUserLanguage($data, 1);

        $result = array(
            'savingNonExistingId' => $result1,
            'savingUndefinedLanguage' => $result2
        );
        $expected = array(
            'savingNonExistingId' => array(),
            'savingUndefinedLanguage' => array(),
        );

        $this->assertEquals($expected, $result);
    }

    function testDeleteUserLanguage_succeeds() {
        $result = $this->UsersLanguages->deleteUserLanguage(1, 4);

        $this->assertEquals(true, $result);
    }

    function testDeleteUserLanguage_fails() {
        $result1 = $this->UsersLanguages->deleteUserLanguage(1, 1);
        $result2 = $this->UsersLanguages->deleteUserLanguage(2, 4);

        $result = array(
            'result1' => $result1,
            'result2' => $result2
        );
        $expected = array(
            'result1' => false,
            'result2' => false
        );

        $this->assertEquals($expected, $result);
    }
}
