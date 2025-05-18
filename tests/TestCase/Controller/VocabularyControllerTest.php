<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class VocabularyControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.sentences',
        'app.users',
        'app.users_languages',
        'app.users_vocabulary',
        'app.vocabulary',
        'app.wiki_articles',
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
}
