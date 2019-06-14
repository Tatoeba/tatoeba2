<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class ImportsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.users',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/imports/import_single_sentences', null, '/eng/users/login?redirect=%2Feng%2Fimports%2Fimport_single_sentences' ],
            [ '/eng/imports/import_single_sentences', 'contributor', '/' ],
            [ '/eng/imports/import_single_sentences', 'advanced_contributor', '/' ],
            [ '/eng/imports/import_single_sentences', 'corpus_maintainer', '/' ],
            [ '/eng/imports/import_single_sentences', 'admin', '/eng/sentences/import' ],
            [ '/eng/imports/import_sentences_with_translation', null, '/eng/users/login?redirect=%2Feng%2Fimports%2Fimport_sentences_with_translation' ],
            [ '/eng/imports/import_sentences_with_translation', 'contributor', '/' ],
            [ '/eng/imports/import_sentences_with_translation', 'advanced_contributor', '/' ],
            [ '/eng/imports/import_sentences_with_translation', 'corpus_maintainer', '/' ],
            [ '/eng/imports/import_sentences_with_translation', 'admin', '/eng/sentences/import' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
