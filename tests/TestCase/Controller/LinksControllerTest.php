<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class LinksControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.contributions',
        'app.links',
        'app.reindex_flags',
        'app.sentences',
        'app.users',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/links/add/2/3', null, '/en/users/login?redirect=%2Fen%2Flinks%2Fadd%2F2%2F3' ],
            [ '/en/links/add/2/3', 'contributor', '/' ],
            [ '/en/links/add/2/3', 'advanced_contributor', '/en/sentences/show/2' ],
            [ '/en/links/add/2/3', 'corpus_maintainer', '/en/sentences/show/2' ],
            [ '/en/links/add/2/3', 'admin', '/en/sentences/show/2' ],
            [ '/en/links/delete/1/2', null, '/en/users/login?redirect=%2Fen%2Flinks%2Fdelete%2F1%2F2' ],
            [ '/en/links/delete/1/2', 'contributor', '/' ],
            [ '/en/links/delete/1/2', 'advanced_contributor', '/en/sentences/show/1' ],
            [ '/en/links/delete/1/2', 'corpus_maintainer', '/en/sentences/show/1' ],
            [ '/en/links/delete/1/2', 'admin', '/en/sentences/show/1' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
