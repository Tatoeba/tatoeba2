<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class ImportsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.users',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/imports/import_single_sentences', null, '/en/users/login?redirect=%2Fen%2Fimports%2Fimport_single_sentences' ],
            [ '/en/imports/import_single_sentences', 'contributor', '/' ],
            [ '/en/imports/import_single_sentences', 'advanced_contributor', '/' ],
            [ '/en/imports/import_single_sentences', 'corpus_maintainer', '/' ],
            [ '/en/imports/import_single_sentences', 'admin', '/en/sentences/import' ],
            [ '/en/imports/import_sentences_with_translation', null, '/en/users/login?redirect=%2Fen%2Fimports%2Fimport_sentences_with_translation' ],
            [ '/en/imports/import_sentences_with_translation', 'contributor', '/' ],
            [ '/en/imports/import_sentences_with_translation', 'advanced_contributor', '/' ],
            [ '/en/imports/import_sentences_with_translation', 'corpus_maintainer', '/' ],
            [ '/en/imports/import_sentences_with_translation', 'admin', '/en/sentences/import' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
