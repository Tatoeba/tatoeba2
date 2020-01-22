<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class RatingsControllerTest extends IntegrationTestCase
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
            [ '/eng/ratings/of/admin', null, true ],
            [ '/eng/ratings/of/admin', 'contributor', true ],
            [ '/eng/ratings/of/admin/ok', null, true ],
            [ '/eng/ratings/of/admin/ok', 'contributor', true ],
            [ '/eng/ratings/of/admin/ok/cmn', null, true ],
            [ '/eng/ratings/of/admin/ok/cmn', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testRatingsControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/eng/ratings/add_sentence/30/-1', null, false ],
            [ '/eng/ratings/add_sentence/30/-1', 'contributor', true ],
            [ '/eng/ratings/delete_sentence/30', null, false ],
            [ '/eng/ratings/delete_sentence/30', 'contributor', true ], // does not exist
            [ '/eng/ratings/delete_sentence/2', 'admin', true ], // does exist
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testRatingsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
