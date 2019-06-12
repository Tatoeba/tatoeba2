<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class AutocompletionsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.users',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function ajaxAccessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/autocompletions/request/foobar', null, true ],
            [ '/eng/autocompletions/request/foobar', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
