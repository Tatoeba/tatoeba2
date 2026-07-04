<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class FavoritesControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use TatoebaControllerTestTrait;

    public array $fixtures = [
        'app.FavoritesUsers',
        'app.Users',
        'app.UsersLanguages',
        'app.PrivateMessages',
        'app.Sentences',
        'app.Transcriptions',
        'app.WikiArticles',
    ];

    public static function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/favorites/of_user/kazuki', null, true ],
            [ '/en/favorites/of_user/kazuki', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public static function ajaxAccessesProvider() {
        return [
            [ '/en/favorites/add_favorite/1', null, false ],
            [ '/en/favorites/add_favorite/1', 'contributor', true ],
            [ '/en/favorites/add_favorite/1/true', null, false ],
            [ '/en/favorites/add_favorite/1/true', 'contributor', true ],
            [ '/en/favorites/remove_favorite/1', null, false ],
            [ '/en/favorites/remove_favorite/1', 'contributor', true ], // does not exist
            [ '/en/favorites/remove_favorite/1/true', null, false ],
            [ '/en/favorites/remove_favorite/1/true', 'contributor', true ], // does not exist
            [ '/en/favorites/remove_favorite/4', 'kazuki', true ],
            [ '/en/favorites/remove_favorite/4/true', 'kazuki', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
