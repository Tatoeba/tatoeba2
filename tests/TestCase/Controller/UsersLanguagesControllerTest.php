<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;
use Helmich\JsonAssert\JsonAssertions;

class UsersLanguagesControllerTest extends IntegrationTestCase
{
    use JsonAssertions;
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.Users',
        'app.UsersLanguages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
           [ '/en/users_languages/delete/1', null, '/en/users/login?redirect=%2Fen%2Fusers_languages%2Fdelete%2F1' ],
           [ '/en/users_languages/delete/1', 'contributor', '/en/user/profile/contributor' ],
           [ '/en/users_languages/delete/1', 'kazuki',      '/en/user/profile/kazuki' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    private function add_language($langCode) {
        $this->post('/en/users_languages/save', [
            'language_code' => $langCode,
            'level' => '1',
            'details' => '',
        ]);
    }

    private function add_language_angular($langCode) {
        $this->addHeader('Accept', 'application/json');
        $this->ajaxPost('/en/users_languages/save', [
            'language_code' => $langCode,
            'level' => '1',
            'details' => '',
        ]);
    }

    private function edit_language() {
        $this->post('/en/users_languages/save/1', [
            'id' => '1',
            'level' => '2',
            'details' => 'I just leveled up!',
        ]);
    }

    public function testSaveNew_asGuest_nonAngular() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->add_language('cmn');
        $this->assertRedirect('/en/users/login');
    }

    public function testSaveNew_asGuest_angular() {
        $this->add_language_angular('cmn');
        $this->assertResponseCode(403);
    }

    public function testSaveNew_asMember_nonAngular() {
        $this->logInAs('contributor');
        $this->add_language('cmn');
        $this->assertRedirect('/en/user/profile/contributor');
    }

    public function testSaveNew_asMember_angular() {
        $this->logInAs('contributor');

        $this->add_language_angular('cmn');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $expected = [
            '$' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.languages' => new \PHPUnit\Framework\Constraint\Count(3),
            '$.languages[0].id' => 2,
            '$.languages[0].of_user_id' => 4,
            '$.languages[0].language_code' => 'fra',
            '$.languages[0].level' => 5,
            '$.languages[0].details' => '',
            '$.languages[0].name' => 'French',
            '$.languages[1].id' => 1,
            '$.languages[2].id' => 7,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSaveNew_lang_und_nonAngular() {
        $this->logInAs('contributor');
        $this->add_language('und');
        $this->assertRedirect('/en/user/language');
        $this->assertFlashMessage('You cannot save this language.');
    }

    public function testSaveNew_lang_und_angular() {
        $this->logInAs('contributor');

        $this->add_language_angular('und');

        $this->assertResponseCode(400);
        $this->assertContentType('application/json');
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.message', 'You cannot save this language.');
    }

    public function testSaveNew_lang_empty() {
        $this->logInAs('contributor');
        $this->add_language('');
        $this->assertRedirect('/en/user/language');
    }

    public function testSaveNew_lang_alreadyAdded_nonAngular() {
        $this->logInAs('contributor');
        $this->add_language('jpn');
        $this->assertRedirect('/en/user/language');
        $this->assertFlashMessage('This language has already been added to your profile.');
    }

    public function testSaveNew_lang_alreadyAdded_angular() {
        $this->logInAs('contributor');

        $this->add_language_angular('jpn');

        $this->assertResponseCode(400);
        $this->assertContentType('application/json');
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.message', 'This language has already been added to your profile.');
    }

    public function testSaveExisting_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->edit_language();
        $this->assertRedirect('/en/users/login');
    }

    public function testSaveExisting_asMember() {
        $this->logInAs('contributor');
        $this->edit_language();
        $this->assertRedirect('/en/user/profile/contributor');
    }

    public function testSaveExisting_ofOtherUser() {
        $this->logInAs('kazuki');
        $this->edit_language();
        $this->assertRedirect('/en/user/language');
    }
}
