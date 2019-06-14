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
            [ '/eng/links/add/2/3', null, '/eng/users/login?redirect=%2Feng%2Flinks%2Fadd%2F2%2F3' ],
            [ '/eng/links/add/2/3', 'contributor', '/' ],
            [ '/eng/links/add/2/3', 'advanced_contributor', '/eng/sentences/show/2' ],
            [ '/eng/links/add/2/3', 'corpus_maintainer', '/eng/sentences/show/2' ],
            [ '/eng/links/add/2/3', 'admin', '/eng/sentences/show/2' ],
            [ '/eng/links/delete/1/2', null, '/eng/users/login?redirect=%2Feng%2Flinks%2Fdelete%2F1%2F2' ],
            [ '/eng/links/delete/1/2', 'contributor', '/' ],
            [ '/eng/links/delete/1/2', 'advanced_contributor', '/eng/sentences/show/1' ],
            [ '/eng/links/delete/1/2', 'corpus_maintainer', '/eng/sentences/show/1' ],
            [ '/eng/links/delete/1/2', 'admin', '/eng/sentences/show/1' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
