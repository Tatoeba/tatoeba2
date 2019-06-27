<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class CollectionsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.sentences',
        'app.users',
        'app.users_languages',
        'app.users_sentences',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/collections/of/admin', null, true ],
            [ '/eng/collections/of/admin', 'contributor', true ],
            [ '/eng/collections/of/admin/ok', null, true ],
            [ '/eng/collections/of/admin/ok', 'contributor', true ],
            [ '/eng/collections/of/admin/ok/cmn', null, true ],
            [ '/eng/collections/of/admin/ok/cmn', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testCollectionsControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/eng/collections/add_sentence/30/-1', null, false ],
            [ '/eng/collections/add_sentence/30/-1', 'contributor', true ],
            [ '/eng/collections/delete_sentence/30', null, false ],
            [ '/eng/collections/delete_sentence/30', 'contributor', true ], // does not exist
            [ '/eng/collections/delete_sentence/2', 'admin', true ], // does exist
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testCollectionsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
