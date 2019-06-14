<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
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
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/vocabulary/of/admin', null, true ],
            [ '/eng/vocabulary/of/admin', 'contributor', true ],
            [ '/eng/vocabulary/of/admin/eng', null, true ],
            [ '/eng/vocabulary/add', null, '/eng/users/login?redirect=%2Feng%2Fvocabulary%2Fadd' ],
            [ '/eng/vocabulary/add', 'contributor', true ],
            [ '/eng/vocabulary/add_sentences', null, '/eng/users/login?redirect=%2Feng%2Fvocabulary%2Fadd_sentences' ],
            [ '/eng/vocabulary/add_sentences', 'contributor', true ],
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
            [ '/eng/vocabulary/remove/1', null, false ],
            [ '/eng/vocabulary/remove/1', 'admin', true ], // owner of vocab
            [ '/eng/vocabulary/remove/1', 'contributor', true ], // not owner of vocab
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    private function saveSomething() {
        $this->ajaxPost('/eng/vocabulary/save', [
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
