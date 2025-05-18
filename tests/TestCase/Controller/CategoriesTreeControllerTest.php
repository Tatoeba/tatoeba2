<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CategoriesTreeController;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class CategoriesTreeControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.CategoriesTree',
        'app.PrivateMessages',
        'app.Tags',
        'app.Users',
        'app.UsersLanguages',
        'app.WikiArticles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/categories_tree/manage', null, '/en/users/login?redirect=%2Fen%2Fcategories_tree%2Fmanage' ],
            [ '/en/categories_tree/manage', 'contributor', '/' ],
            [ '/en/categories_tree/manage', 'advanced_contributor', true ],
            [ '/en/categories_tree/manage', 'admin', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccessOnProduction($url, $user, $response) {
        Configure::write('Tatoeba.devStylesheet', false);
        Configure::write('debug', false);
        $this->assertAccessUrlAs($url, $user, false);
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccessOnDev($url, $user, $response)
    {
        Configure::write('Tatoeba.devStylesheet', true);
        Configure::write('debug', false);
        $this->assertAccessUrlAs($url, $user, $response);
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccessOnLocal($url, $user, $response)
    {
        Configure::write('Tatoeba.devStylesheet', false);
        Configure::write('debug', true);
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
