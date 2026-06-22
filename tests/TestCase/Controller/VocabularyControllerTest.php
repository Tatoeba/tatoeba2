<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class VocabularyControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.PrivateMessages',
        'app.Sentences',
        'app.Users',
        'app.UsersLanguages',
        'app.UsersVocabulary',
        'app.Vocabulary',
        'app.WikiArticles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/vocabulary/of/admin', null, true ],
            [ '/en/vocabulary/of/admin', 'contributor', true ],
            [ '/en/vocabulary/of/admin/eng', null, true ],
            [ '/en/vocabulary/add', null, '/en/users/login?redirect=%2Fen%2Fvocabulary%2Fadd' ],
            [ '/en/vocabulary/add', 'contributor', true ],
            [ '/en/vocabulary/add_sentences', null, '/en/users/login?redirect=%2Fen%2Fvocabulary%2Fadd_sentences' ],
            [ '/en/vocabulary/add_sentences', 'contributor', true ],
            [ '/en/vocabulary/add_sentences/eng', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/en/vocabulary/remove/1', null, false ],
            [ '/en/vocabulary/remove/1', 'admin', true ], // owner of vocab
            [ '/en/vocabulary/remove/1', 'contributor', true ], // not owner of vocab
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    private function saveSomething() {
        $this->ajaxPost('/en/vocabulary/save', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
    }

    public function testSave_asGuest() {
        $this->enableCsrfToken();
        $this->saveSomething();
        $this->assertResponseError();
    }

    public function testSave_asMember() {
        $this->logInAs('contributor');
        $this->saveSomething();
        $this->assertResponseOk();
    }

    public function testEdit_asGuest() {
        $this->enableCsrfToken();
        $this->ajaxPost('/en/vocabulary/edit/1', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseError();
    }

    public function testEdit_asMember_onlyAddedBySelf() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/vocabulary/edit/1', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseOk();
    }

    public function testEdit_asMember_onlyAddedBySelf_duplicate() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/vocabulary/edit/1', [
            'lang' => 'eng',
            'text' => 'added by 2 members',
        ]);
        $this->assertResponseError();
        $this->assertFlashMessage("The vocabulary item 'added by 2 members' already exists for this language.");
    }

    public function testEdit_asMember_onlyAddedByOtherMember() {
        $this->logInAs('kazuki');
        $this->ajaxPost('/en/vocabulary/edit/1', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseError();
    }

    public function testEdit_asMember_nonExisting() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/vocabulary/edit/999999', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseCode(404);
    }

    public function testEdit_asMember_invalidLang() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/vocabulary/edit/1', [
            'lang' => 'invalid',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseCode(400);
    }

    public function testEdit_asMember_addedByOthersToo() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/vocabulary/edit/2', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseCode(403);
    }

    public function testEdit_asCorpusMaintainer_addedByOthersToo() {
        $this->logInAs('corpus_maintainer');
        $this->ajaxPost('/en/vocabulary/edit/2', [
            'lang' => 'fra',
            'text' => 'hélicoptère',
        ]);
        $this->assertResponseOk();
    }
}
