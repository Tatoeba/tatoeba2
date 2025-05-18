<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class UsersLanguagesControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.users',
        'app.users_languages',
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

    public function testSaveNew_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->add_language('cmn');
        $this->assertResponseCode(403);
    }

    public function testSaveNew_asMember() {
        $this->logInAs('contributor');
        $this->add_language('cmn');
        $this->assertRedirect('/en/user/profile/contributor');
    }

    public function testSaveNew_lang_und() {
        $this->logInAs('contributor');
        $this->add_language('und');
        $this->assertRedirect('/en/user/language');
    }

    public function testSaveNew_lang_empty() {
        $this->logInAs('contributor');
        $this->add_language('');
        $this->assertRedirect('/en/user/language');
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
