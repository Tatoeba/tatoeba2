<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class FavoritesControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.favorites_users',
        'app.users',
        'app.users_languages',
        'app.private_messages',
        'app.sentences',
        'app.transcriptions',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/favorites/of_user/kazuki', null, true ],
            [ '/eng/favorites/of_user/kazuki', 'contributor', true ],
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
            [ '/eng/favorites/add_favorite/1', null, false ],
            [ '/eng/favorites/add_favorite/1', 'contributor', true ],
            [ '/eng/favorites/add_favorite/1/true', null, false ],
            [ '/eng/favorites/add_favorite/1/true', 'contributor', true ],
            [ '/eng/favorites/remove_favorite/1', null, false ],
            [ '/eng/favorites/remove_favorite/1', 'contributor', true ], // does not exist
            [ '/eng/favorites/remove_favorite/1/true', null, false ],
            [ '/eng/favorites/remove_favorite/1/true', 'contributor', true ], // does not exist
            [ '/eng/favorites/remove_favorite/4', 'kazuki', true ],
            [ '/eng/favorites/remove_favorite/4/true', 'kazuki', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
